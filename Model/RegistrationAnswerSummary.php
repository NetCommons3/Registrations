<?php
/**
 * RegistrationAnswerSummary Model
 *
 * @property Registration $Registration
 * @property User $User
 * @property RegistrationAnswer $RegistrationAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Summary for RegistrationAnswerSummary Model
 */
class RegistrationAnswerSummary extends RegistrationsAppModel {

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
 * getNowSummaryOfThisUser 指定された登録フォームIDと指定ユーザーに合致する登録フォーム登録を取得する
 *
 * @param int $registrationKey 登録フォームKey
 * @param int $userId ユーザID （指定しない場合は null)
 * @param string $sessionId セッションID
 * @return array
 */
	public function getNowSummaryOfThisUser($registrationKey, $userId, $sessionId) {
		if ($userId) {
			$conditions = array(
				'answer_status' => RegistrationsComponent::ACTION_ACT,
				'registration_key' => $registrationKey,
				'user_id' => $userId
			);
		} else {
			$conditions = array(
				'answer_status' => RegistrationsComponent::ACTION_ACT,
				'registration_key' => $registrationKey,
				'session_value' => $sessionId
			);
		}

		$summary = $this->find('all', array(
			'conditions' => $conditions
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
 * @throws $ex
 * @return array summary
 */
	public function forceGetProgressiveAnswerSummary($registration, $userId, $sessionId) {
		$this->begin();
		try {
			$this->create();
			if (! $this->save(array(
				'answer_status' => RegistrationsComponent::ACTION_NOT_ACT,
				'test_status' => ($registration['Registration']['status'] != WorkflowComponent::STATUS_PUBLISHED) ? RegistrationsComponent::TEST_ANSWER_STATUS_TEST : RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => 1,
				'registration_key' => $registration['Registration']['key'],
				'session_value' => $sessionId,
				'user_id' => $userId,
			))) {
				$this->rollback();
				return false;
			}
			//$summary = array();
			//$summary['RegistrationAnswerSummary']['id'] = $this->id;
			//return $summary;
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		$summary = $this->findById($this->id);
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
			$baseConditions['RegistrationAnswerSummary.test_status'] = RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM;
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
		// 質問データのとりまとめ
		//$questionsは、registration_question_keyをキーとし、registration_question配下が代入されている。
		$questions = Hash::combine($registration,
			'RegistrationPage.{n}.RegistrationQuestion.{n}.key',
			'RegistrationPage.{n}.RegistrationQuestion.{n}');

		// 集計データを集める際の基本条件
		$baseConditions = $this->getResultCondition($registration);

		//質問毎に集計
		foreach ($questions as &$question) {
			if ($question['is_result_display'] != RegistrationsComponent::EXPRESSION_SHOW) {
				//集計表示をしない、なので飛ばす
				continue;
			}
			// 戻り値の、この質問の合計登録数を記録しておく。
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
 * @param array &$question 登録フォーム質問(集計結果を配列追加して返します)
 * @param array $questionConditions get aggregate base condition
 * @return void
 */
	private function __aggregateAnswerForMatrix(&$question, $questionConditions) {
		$rowCnt = 0;
		$cols = Hash::extract($question['RegistrationChoice'], '{n}[matrix_type=' . RegistrationsComponent::MATRIX_TYPE_COLUMN . ']');
		foreach ($question['RegistrationChoice'] as &$c) {
			if ($c['matrix_type'] == RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
				foreach ($cols as $col) {
					$conditions = $questionConditions + array(
							'RegistrationAnswer.matrix_choice_key' => $c['key'],
							'RegistrationAnswer.answer_value LIKE ' => '%' . RegistrationsComponent::ANSWER_DELIMITER . $col['key'] . RegistrationsComponent::ANSWER_VALUE_DELIMITER . '%',
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
 * @param array &$question 登録フォーム質問(集計結果を配列追加して返します)
 * @param array $questionConditions get aggregate base condition
 * @return void
 */
	private function __aggregateAnswerForNotMatrix(&$question, $questionConditions) {
		foreach ($question['RegistrationChoice'] as &$c) {
			$conditions = $questionConditions + array(
					'RegistrationAnswer.answer_value LIKE ' => '%' . RegistrationsComponent::ANSWER_DELIMITER . $c['key'] . RegistrationsComponent::ANSWER_VALUE_DELIMITER . '%',
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

}
