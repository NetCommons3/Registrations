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

/**
 * Summary for RegistrationAnswerSummary Model
 */
class RegistrationAnswerSummaryCsv extends RegistrationsAppModel {

/**
 * use table
 *
 * @var array
 */
	public $useTable = 'registration_answer_summaries';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
	);

/**
 * getRegistrationForAnswerCsv
 *
 * @param int $registrationKey registration key
 * @return array registration data
 */
	public function getRegistrationForAnswerCsv($registrationKey) {
		$this->Registration = ClassRegistry::init('Registrations.Registration', true);
		// 指定の登録フォームデータを取得
		$registration = $this->Registration->find('first', array(
			'conditions' => array(
				'Registration.key' => $registrationKey,
				'Registration.is_active' => true,
			),
			'recursive' => -1
		));
		return $registration;
	}

/**
 * getAnswerSummaryCsv 
 *
 * @param array $registration registration data
 * @param int $limit record limit
 * @param int $offset offset
 * @return array
 */
	public function getAnswerSummaryCsv($registration, $limit, $offset) {
		$this->RegistrationAnswer = ClassRegistry::init('Registrations.RegistrationAnswer', true);

		// 指定された登録フォームの登録データをＣｓｖに出力しやすい行形式で返す
		$retArray = array();

		// $offset == 0 のときのみヘッダ行を出す
		if ($offset == 0) {
			$retArray[] = $this->_putHeader($registration);
		}

		// $registrationにはページデータ、質問データが入っていることを前提とする

		// 登録フォームのkeyを取得
		$key = $registration['Registration']['key'];

		// keyに一致するsummaryを取得（テストじゃない、完了している）
		$summaries = $this->find('all', array(
			'conditions' => array(
				'answer_status' => RegistrationsComponent::ACTION_ACT,
				'test_status' => RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM,
				'registration_key' => $key,
			),
			'limit' => $limit,
			'offset' => $offset,
			'order' => 'RegistrationAnswerSummaryCsv.created',
		));
		if (empty($summaries)) {
			return $retArray;
		}

		// summary loop
		foreach ($summaries as $summary) {
			//$answers = $summary['RegistrationAnswer'];
			// 何回もSQLを発行するのは無駄かなと思いつつも
			// RegistrationAnswerに登録データの取り扱いしやすい形への整備機能を組み込んであるので、それを利用したかった
			// このクラスからでも利用できないかと試みたが
			// AnswerとQuestionがJOINされた形でFindしないと整備機能が発動しない
			// そうするためにはrecursive=2でないといけないわけだが、recursive=2にするとRoleのFindでSQLエラーになる
			// 仕方ないのでこの形式で処理を行う
			$answers = $this->RegistrationAnswer->find('all', array(
				'conditions' => array(
					'registration_answer_summary_id' => $summary[$this->alias]['id']
				),
			));
			$retArray[] = $this->_getRows($registration, $summary, $answers);
		}

		return $retArray;
	}

/**
 * _putHeader
 *
 * @param array $registration registration data
 * @return array
 */
	protected function _putHeader($registration) {
		$cols = array();

		// "登録者","登録日","回数"
		$cols[] = __d('registrations', 'Respondent');
		$cols[] = __d('registrations', 'Answer Date');
		$cols[] = __d('registrations', 'Number');

		foreach ($registration['RegistrationPage'] as $page) {
			foreach ($page['RegistrationQuestion'] as $question) {
				if (RegistrationsComponent::isMatrixInputType($question['question_type'])) {
					$choiceSeq = 1;
					foreach ($question['RegistrationChoice'] as $choice) {
						if ($choice['matrix_type'] == RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
							$cols[] = $page['page_sequence'] . '-' . $question['question_sequence'] . '-' . $choiceSeq++ . '. ' . $choice['choice_label'];
						}
					}
				} else {
					$cols[] = $page['page_sequence'] . '-' . $question['question_sequence'] . '. ' . $question['question_value'];
				}
			}
		}
		return $cols;
	}

/**
 * _getRow
 *
 * @param array $registration registration data
 * @param array $summary answer summary
 * @param array $answers answer data
 * @return array
 */
	protected function _getRows($registration, $summary, $answers) {
		// ページ、質問のループから、取り出すべき質問のIDを順番に取り出す
		// question loop
		// 返却用配列にquestionのIDにマッチするAnswerを配列要素として追加、Answerがないときは空文字
		// なお選択肢系のものはchoice_idが登録にくっついているのでそれを削除する
		// MatrixのものはMatrixの行数分返却行の列を加える
		// その他の選択肢の場合は、入力されたその他のテキストを入れる

		$cols = array();

		$cols[] = ($registration['Registration']['is_anonymity']) ? __d('registrations', 'Anonymity') : $summary['TrackableCreator']['username'];
		$cols[] = $summary['RegistrationAnswerSummaryCsv']['modified'];
		$cols[] = $summary['RegistrationAnswerSummaryCsv']['answer_number'];

		foreach ($registration['RegistrationPage'] as $page) {
			foreach ($page['RegistrationQuestion'] as $question) {
				if (RegistrationsComponent::isMatrixInputType($question['question_type'])) {
					foreach ($question['RegistrationChoice'] as $choice) {
						if ($choice['matrix_type'] == RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
							$cols[] = $this->_getMatrixAns($question, $choice, $answers);
						}
					}
				} else {
					$cols[] = $this->_getAns($question, $answers);
				}
			}
		}
		return $cols;
	}

/**
 * _getAns
 *
 * @param array $question question data
 * @param array $answers answer data
 * @return string
 */
	protected function _getAns($question, $answers) {
		$retAns = '';
		// 登録配列データの中から、現在指定された質問に該当するものを取り出す
		$ans = Hash::extract($answers, '{n}.RegistrationAnswer[registration_question_key=' . $question['key'] . ']');
		// 登録が存在するとき処理
		if ($ans) {
			$ans = $ans[0];
			// 単純入力タイプのときは登録の値をそのまま返す
			if (RegistrationsComponent::isOnlyInputType($question['question_type'])) {
				$retAns = $ans['answer_value'];
			} elseif (RegistrationsComponent::isSelectionInputType($question['question_type'])) {
				// choice_id と choice_valueに分けられた登録選択肢配列を得る
				// 選択されていた数分処理
				foreach ($ans['answer_values'] as $choiceKey => $dividedAns) {
					// idから判断して、その他が選ばれていた場合、other_answer_valueを入れる
					$choice = Hash::extract($question['RegistrationChoice'], '{n}[key=' . $choiceKey . ']');
					if ($choice) {
						if ($choice[0]['other_choice_type'] != RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
							$retAns .= $ans['other_answer_value'];
						} else {
							$retAns .= $dividedAns;
						}
						$retAns .= RegistrationsComponent::ANSWER_DELIMITER;
					}
				}
				$retAns = trim($retAns, RegistrationsComponent::ANSWER_DELIMITER);
			}
		}
		return $retAns;
	}

/**
 * _getMatrixAns
 *
 * @param array $question question data
 * @param array $choice question choice data
 * @param array $answers answer data
 * @return string
 */
	protected function _getMatrixAns($question, $choice, $answers) {
		$retAns = '';
		// 登録配列データの中から、現在指定された質問に該当するものを取り出す
		// マトリクスタイプのときは複数存在する（行数分）
		$anss = Hash::extract($answers, '{n}.RegistrationAnswer[registration_question_key=' . $question['key'] . ']');
		if (empty($anss)) {
			return $retAns;
		}
		// その中かから現在指定された選択肢行に該当するものを取り出す
		$ans = Hash::extract($anss, '{n}[matrix_choice_key=' . $choice['key'] . ']');
		// 登録が存在するとき処理
		if ($ans) {
			$ans = $ans[0];
			// idから判断して、その他が選ばれていた場合、other_answer_valueを入れる
			if ($choice['other_choice_type'] != RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				$retAns = $ans['other_answer_value'] . RegistrationsComponent::ANSWER_VALUE_DELIMITER;
			}
			$retAns = implode(RegistrationsComponent::ANSWER_DELIMITER, $ans['answer_values']);
		}
		return $retAns;
	}
}
