<?php
/**
 * RegistrationAnswerSummary Model
 *
 * @property Registration $Registration
 * @property RegistrationAnswer $RegistrationAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');
App::uses('NetCommonsTime', 'NetCommons.Utility');
App::uses('NetCommonsUrl', 'NetCommons.Utility');
App::uses('MailSettingFixedPhrase', 'Mails.Model');

/**
 * Summary for RegistrationAnswerSummary Model
 */
class RegistrationAnswerSummary extends RegistrationsAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		// 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'Registration.title',
			),
			'keyField' => 'id',
			'typeKey' => MailSettingFixedPhrase::ANSWER_TYPE,
			),
		'Mails.MailQueueDelete',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'registration_key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Registration' => array(
			'className' => 'Registrations.Registration',
			'foreignKey' => 'registration_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'RegistrationAnswer' => array(
			'className' => 'Registrations.RegistrationAnswer',
			'foreignKey' => 'registration_answer_summary_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

/**
 * saveAnswerStatus
 * 登録状態を書き換える
 *
 * @param array $summary summary data
 * @param int $status status
 * @throws InternalErrorException
 * @return bool
 */
	public function saveAnswerStatus($summary, $status) {
		$summary['RegistrationAnswerSummary']['answer_status'] = $status;

		if ($status == RegistrationsComponent::ACTION_ACT) {
			// サマリの状態を完了にして確定する
			$summary['RegistrationAnswerSummary']['answer_time'] = (new NetCommonsTime())->getNowDatetime();
			// メールのembed のURL設定を行っておく
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'registration_blocks',
				'action' => 'index',
				Current::read('Block.id'),
				'frame_id' => Current::read('Frame.id'),
			), true);
			$this->setAddEmbedTagValue('X-URL', $url);

			// 本人にもメールする設定でメールアドレス欄があったら、一番最初のメールアドレス宛にメールする
			$condition = $this->Registration->getBaseCondition();
			$registration = $this->Registration->find('first', ['conditions' => $condition]);

			// X-SUBJECT設定
			$this->setAddEmbedTagValue('X-SUBJECT', $registration['Registration']['title']);

			// 登録された項目の取得
			// RegistrationAnswerに登録データの取り扱いしやすい形への整備機能を組み込んであるので、それを利用したかった
			// AnswerとQuestionがJOINされた形でFindしないと整備機能が発動しない
			// そうするためにはrecursive=2でないといけないわけだが、recursive=2にするとRoleのFindでSQLエラーになる
			// 仕方ないのでこの形式で処理を行う
			// 単純にRegistrationAnswerSummary.idでFindすると、LEFT JOIN の関係で同じ項目が複数でてきてしまう。
			$questionIds = Hash::extract(
				$registration['RegistrationPage'],
				'{n}.RegistrationQuestion.{n}.id');
			$answers = $this->RegistrationAnswer->find('all', array(
				'fields' => array('RegistrationAnswer.*', 'RegistrationQuestion.*'),
				'conditions' => array(
					'registration_answer_summary_id' => $summary[$this->alias]['id'],
					'RegistrationQuestion.id' => $questionIds
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'registration_questions',
						'alias' => 'RegistrationQuestion',
						'type' => 'LEFT',
						'conditions' => array(
							'RegistrationAnswer.registration_question_key = RegistrationQuestion.key',
						)
					)
				)
			));

			// X-DATA展開
			$xData = $this->_makeXData($summary, $answers);
			$this->setAddEmbedTagValue('X-DATA', $xData);

			// TO_ADDRESSESには表示しない（ルーム配信のみ表示）末尾定型文を追加（登録フォーム回答）
			$this->setSetting(MailQueueBehavior::MAIL_QUEUE_SETTING_MAIL_BODY_AFTER,
				__d('registrations', 'Registration.mail.after.body'));

			if ($registration['Registration']['is_regist_user_send']) {
				// 本人にもメールする
				foreach ($registration['RegistrationPage'][0]['RegistrationQuestion'] as $index => $question) {
					if ($question['question_type'] == RegistrationsComponent::TYPE_EMAIL) {
						// メール項目あり

						// メアドをregistration_answersから取得
						$registUserMail = $answers[$index]['RegistrationAnswer']['answer_value'];
						// 送信先にset
						$this->setSetting(
							MailQueueBehavior::MAIL_QUEUE_SETTING_TO_ADDRESSES,
							[$registUserMail]
						);
						// ループから抜ける
						break;
					}
				}
			}
		} else {
			// 完了時以外はメールBehaviorを外す
			$this->Behaviors->unload('Mails.MailQueue');
		}
		$this->begin();
		try {
			$this->set($summary);
			if (! $this->validates()) {
				$this->rollback();
				return false;
			}
			if (! $this->save($summary, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}
/**
 * getProgressiveSummary
 * 登録中サマリを取得する
 *
 * @param string $registrationKey registration key
 * @param int $summaryId summary id
 * @return array
 */
	public function getProgressiveSummary($registrationKey, $summaryId = null) {
		$conditions = array(
			'answer_status !=' => RegistrationsComponent::ACTION_ACT,
			'registration_key' => $registrationKey,
			'user_id' => Current::read('User.id'),
		);
		if (! is_null($summaryId)) {
			$conditions['RegistrationAnswerSummary.id'] = $summaryId;
		}
		$summary = $this->find('first', array(
			'conditions' => $conditions,
			'recursive' => -1,
			'order' => 'RegistrationAnswerSummary.created DESC'	// 最も新しいものを一つ選ぶ
		));
		return $summary;
	}
/**
 * forceGetProgressiveAnswerSummary
 * get answer summary record if there is no summary , then create
 *
 * @param array $registration registration
 * @param int $userId user id
 * @param string $sessionId session id
 * @return array summary
 * @throws InternalErrorException
 */
	public function forceGetProgressiveAnswerSummary($registration, $userId, $sessionId) {
		$this->begin();
		try {
			$answerTime = 1;
			if ($userId) {
				$maxTime = $this->find('first', array(
					'fields' => array('MAX(answer_number) AS max_answer_time'),
					'conditions' => array(
						'registration_key' => $registration['Registration']['key'],
						'user_id' => $userId
					)
				));
				if ($maxTime) {
					$answerTime = $maxTime[0]['max_answer_time'] + 1;
				}
			}
			// 完全にこのコード上で作成しているものであるのでvalidatesでのチェックは行っていない
			$this->create();

			$testStatus = RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM;
			if ($registration['Registration']['status'] != WorkflowComponent::STATUS_PUBLISHED) {
				$testStatus = RegistrationsComponent::TEST_ANSWER_STATUS_TEST;
			}

			// サマリ新規作成時は一時保存なので、メール送信させない
			$this->Behaviors->unload('Mails.MailQueue');

			if (! $this->save(array(
				'answer_status' => RegistrationsComponent::ACTION_NOT_ACT,
				'test_status' => $testStatus,
				'answer_number' => $answerTime,
				'registration_key' => $registration['Registration']['key'],
				'session_value' => $sessionId,
				'user_id' => $userId,
			))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		$summary = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $this->id
			)
		));
		return $summary;
	}

/**
 * getResultCondition
 *
 * @param int $registration Registration
 * @return array
 */
	public function getResultCondition($registration) {
		// 指定された登録フォームを集計するときのサマリ側の条件を返す
		$baseConditions = array(
			'RegistrationAnswerSummary.answer_status' => RegistrationsComponent::ACTION_ACT,
			'RegistrationAnswerSummary.registration_key' => $registration['Registration']['key']
		);
		//公開時は本番時登録のみ、テスト時(=非公開時)は本番登録＋テスト登録を対象とする。
		if ($registration['Registration']['status'] == WorkflowComponent::STATUS_PUBLISHED) {
			$baseConditions['RegistrationAnswerSummary.test_status'] =
				RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM;
		}
		return $baseConditions;
	}

/**
 * getAggrigates
 * 集計処理の実施
 *
 * @param array $registration 登録フォーム情報
 * @return void
 */
	public function getAggregate($registration) {
		$this->RegistrationAnswer = ClassRegistry::init('Registrations.RegistrationAnswer', true);
		// 項目データのとりまとめ
		//$questionsは、registration_question_keyをキーとし、registration_question配下が代入されている。
		$questions = Hash::combine($registration,
			'RegistrationPage.{n}.RegistrationQuestion.{n}.key',
			'RegistrationPage.{n}.RegistrationQuestion.{n}');

		// 集計データを集める際の基本条件
		$baseConditions = $this->getResultCondition($registration);

		//項目毎に集計
		foreach ($questions as &$question) {
			if (RegistrationsComponent::isOnlyInputType($question['question_type'])) {
				continue;
			}
			if ($question['is_result_display'] != RegistrationsComponent::EXPRESSION_SHOW) {
				//集計表示をしない、なので飛ばす
				continue;
			}
			// 戻り値の、この項目の合計登録数を記録しておく。
			// skip ロジックがあるため、単純にsummaryのcountじゃない..
			$questionConditions = $baseConditions + array(
					'RegistrationAnswer.registration_question_key' => $question['key'],
				);
			$question['answer_total_cnt'] = $this->RegistrationAnswer->getAnswerCount($questionConditions);

			if (RegistrationsComponent::isMatrixInputType($question['question_type'])) {
				$this->__aggregateAnswerForMatrix($question, $questionConditions);
			} else {
				$this->__aggregateAnswerForNotMatrix($question, $questionConditions);
			}
		}
		return $questions;
	}

/**
 * __aggregateAnswerForMatrix
 * matrix aggregate
 *
 * @param array &$question 登録フォーム項目(集計結果を配列追加して返します)
 * @param array $questionConditions get aggregate base condition
 * @return void
 */
	private function __aggregateAnswerForMatrix(&$question, $questionConditions) {
		$rowCnt = 0;
		$cols = Hash::extract(
			$question['RegistrationChoice'],
			'{n}[matrix_type=' . RegistrationsComponent::MATRIX_TYPE_COLUMN . ']');

		foreach ($question['RegistrationChoice'] as &$c) {
			if ($c['matrix_type'] == RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
				foreach ($cols as $col) {
					$searchWord = sprintf(
						'%%%s%s%s%%',
						RegistrationsComponent::ANSWER_DELIMITER,
						$col['key'],
						RegistrationsComponent::ANSWER_VALUE_DELIMITER);
					$conditions = $questionConditions + array(
							'RegistrationAnswer.matrix_choice_key' => $c['key'],
							'RegistrationAnswer.answer_value LIKE ' => $searchWord,
						);
					$cnt = $this->RegistrationAnswer->getAnswerCount($conditions);
					$c['aggregate_total'][$col['key']] = $cnt;
				}
				$rowCnt++;
			}
		}
		$question['answer_total_cnt'] /= $rowCnt;
	}

/**
 * __aggregateAnswerForNotMatrix
 * not matrix aggregate
 *
 * @param array &$question 登録フォーム項目(集計結果を配列追加して返します)
 * @param array $questionConditions get aggregate base condition
 * @return void
 */
	private function __aggregateAnswerForNotMatrix(&$question, $questionConditions) {
		foreach ($question['RegistrationChoice'] as &$c) {
			$searchWord = sprintf(
				'%%%s%s%s%%',
				RegistrationsComponent::ANSWER_DELIMITER,
				$c['key'],
				RegistrationsComponent::ANSWER_VALUE_DELIMITER);
			$conditions = $questionConditions + array(
					'RegistrationAnswer.answer_value LIKE ' => $searchWord,
				);
			$cnt = $this->RegistrationAnswer->getAnswerCount($conditions);
			$c['aggregate_total']['aggregate_not_matrix'] = $cnt;
		}
	}

/**
 * deleteTestAnswerSummary
 * when registration is published, delete test answer summary
 *
 * @param int $key registration key
 * @param int $status publish status
 * @return bool
 */
	public function deleteTestAnswerSummary($key, $status) {
		if ($status != WorkflowComponent::STATUS_PUBLISHED) {
			return true;
		}
		$this->deleteAll(array(
			'registration_key' => $key,
			'test_status' => RegistrationsComponent::TEST_ANSWER_STATUS_TEST), true);
		return true;
	}

/**
 * deleteAnswerSummary
 * when registration is published, delete answer summary
 *
 * @param int $key registration key
 * @return bool
 */
	public function deleteAnswerSummary($key) {
		$this->deleteAll(array(
			'registration_key' => $key,
			), true);
		return true;
	}

/**
 * メール送信のX-DATAタグ用文字列の生成
 *
 * @param array $summary RegistrationAnswerSummmaryデータ
 * @param array $answers RegistrationAnswerデータ（複数）
 * @return array|string X-DATA
 */
	protected function _makeXData($summary, $answers) {
		$xData = array();
		$xData[] = __d('registrations', 'RegistrationAnswerSummary ID') . ':' .
			$summary['RegistrationAnswerSummary']['id'];
		foreach ($answers as $answer) {
			// answer_valuesがあるときは選択式
			$xDataString = $answer['RegistrationQuestion']['question_value'] . ':';

			if (Hash::check($answer, 'RegistrationAnswer.answer_values')) {
				// 選択式
				$otherAnswer = Hash::get($answer,
					'RegistrationAnswer.other_answer_value');
				if ($otherAnswer) {
					// 「その他」を取り除いて代わりにその他に入力されたテキストを追加
					array_pop($answer['RegistrationAnswer']['answer_values']);
					$answer['RegistrationAnswer']['answer_values'][] =
						$otherAnswer;
				}
				$xDataString .= implode("\n", $answer['RegistrationAnswer']['answer_values']);
			} else {
				$xDataString .= $answer['RegistrationAnswer']['answer_value'];
			}
			$xData[] = $xDataString;
		}
		$xData = implode("\n", $xData);
		return $xData;
	}

}
