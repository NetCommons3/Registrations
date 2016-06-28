<?php
/**
 * RegistrationAnswer Model
 *
 * @property MatrixChoice $MatrixChoice
 * @property RegistrationAnswerSummary $RegistrationAnswerSummary
 * @property RegistrationQuestion $RegistrationQuestion
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');

/**
 * Summary for RegistrationAnswer Model
 */
class RegistrationAnswer extends RegistrationsAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Registrations.RegistrationAnswerSingleChoice',
		'Registrations.RegistrationAnswerMultipleChoice',
		'Registrations.RegistrationAnswerSingleList',
		'Registrations.RegistrationAnswerTextArea',
		'Registrations.RegistrationAnswerText',
		'Registrations.RegistrationAnswerMatrixSingleChoice',
		'Registrations.RegistrationAnswerMatrixMultipleChoice',
		'Registrations.RegistrationAnswerDatetime',
		'Registrations.RegistrationAnswerEmail',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'RegistrationChoice' => array(
			'className' => 'Registrations.RegistrationChoice',
			'foreignKey' => false,
			'conditions' => 'RegistrationAnswer.matrix_choice_key=RegistrationChoice.key',
			'fields' => '',
			'order' => ''
		),
		'RegistrationAnswerSummary' => array(
			'className' => 'Registrations.RegistrationAnswerSummary',
			'foreignKey' => 'registration_answer_summary_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'RegistrationQuestion' => array(
			'className' => 'Registrations.RegistrationQuestion',
			'foreignKey' => false,
			'conditions' => 'RegistrationAnswer.registration_question_key=RegistrationQuestion.key',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		// option情報取り出し
		$summaryId = $options['registration_answer_summary_id'];
		$this->data['RegistrationAnswer']['registration_answer_summary_id'] = $summaryId;
		$question = $options['question'];
		$allAnswers = $options['allAnswers'];

		// Answerモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'registration_answer_summary_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					'allowEmpty' => true,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'registration_question_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					//'message' => 'Your custom message here',
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'answer_value' => array(
				'answerRequire' => array(
					'rule' => array('answerRequire', $question),
					'message' => __d('registrations', 'Input required'),
				),
				'answerMaxLength' => array(
					'rule' => array(
						'answerMaxLength',
						$question, RegistrationsComponent::REGISTRATION_MAX_ANSWER_LENGTH
					),
					'message' => sprintf(
						__d('registrations', 'the answer is too long. Please enter under %d letters.',
							RegistrationsComponent::REGISTRATION_MAX_ANSWER_LENGTH)),
				),
				'answerChoiceValidation' => array(
					'rule' => array('answerChoiceValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
				'answerTextValidation' => array(
					'rule' => array('answerTextValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
				'answerDatetimeValidation' => array(
					'rule' => array('answerDatetimeValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
				'answerMatrixValidation' => array(
					'rule' => array('answerMatrixValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
				'answerEmailValidation' => array(
					'rule' => array('answerEmailValidation', $question, $allAnswers),
					'last' => true,
					'message' => __d('net_commons', 'Failed on validation errors. Please check the input data.'),
				),
				'answerEmailConfirmValidation' => array(
					'rule' => array('answerEmailConfirmValidation', $question, $allAnswers),
					'last' => true,
					'message' => __d('net_commons', 'The input data does not match. Please try again.'),
				),

			),
		);
		parent::beforeValidate($options);

		return true;
	}

/**
 * getProgressiveAnswerOfThisSummary
 *
 * @param array $registration registration data
 * @param array $summary registration summary ( one record )
 * @return array
 */
	public function getProgressiveAnswerOfThisSummary($registration, $summary) {
		$answers = array();
		if (empty($summary)) {
			return $answers;
		}
		// 指定のサマリに該当する登録フォームの項目ID配列を取得
		$questionIds = Hash::extract(
			$registration,
			'RegistrationPage.{n}.RegistrationQuestion.{n}.id');
		$choiceIds = Hash::extract(
			$registration,
			'RegistrationPage.{n}.RegistrationQuestion.{n}.RegistrationChoice.{n}.id');
		// その項目配列を取得条件に加える（間違った項目が入らないよう）
		$answer = $this->find('all', array(
			'conditions' => array(
				'registration_answer_summary_id' => $summary['RegistrationAnswerSummary']['id'],
				'RegistrationQuestion.id' => $questionIds,
				'OR' => array(
					array('RegistrationChoice.id' => $choiceIds),
					array('RegistrationChoice.id' => null),
				)
			),
			//'recursive' => 1
		));
		if (!empty($answer)) {
			foreach ($answer as $ans) {
				$answerIndex = $ans['RegistrationAnswer']['registration_question_key'];
				$answers[$answerIndex][] = $ans['RegistrationAnswer'];
			}
		}
		return $answers;
	}
/**
 * getAnswerCount
 * It returns the number of responses in accordance with the conditions
 *
 * @param array $conditions conditions
 * @return int
 */
	public function getAnswerCount($conditions) {
		$cnt = $this->find('count', array(
			'conditions' => $conditions,
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'registration_answer_summaries',
					'alias' => 'RegistrationAnswerSummary',
					'type' => 'LEFT',
					'conditions' => array(
						'RegistrationAnswerSummary.id = RegistrationAnswer.registration_answer_summary_id',
					)
				)
			)
		));
		return $cnt;
	}

/**
 * saveAnswer
 * save the answer data
 *
 * @param array $data Postされた登録データ
 * @param array $registration registration data
 * @param array $summary answer summary data
 * @throws InternalErrorException
 * @return bool
 */
	public function saveAnswer($data, $registration, $summary) {
		//トランザクションBegin
		$this->begin();
		try {
			$summaryId = $summary['RegistrationAnswerSummary']['id'];
			// 繰り返しValidationを行うときは、こうやってエラーメッセージを蓄積するところ作らねばならない
			// 仕方ないCakeでModelObjectを使う限りは
			$validationErrors = array();
			foreach ($data['RegistrationAnswer'] as $answer) {
				$targetQuestionKey = $answer[0]['registration_question_key'];
				$targetQuestion = Hash::extract(
					$registration['RegistrationPage'],
					'{n}.RegistrationQuestion.{n}[key=' . $targetQuestionKey . ']');
				if (! $targetQuestion) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				// データ保存
				// Matrixタイプの場合はanswerが配列になっているがsaveでかまわない
				// saveMany中で１回しかValidateしなくてよい関数のためのフラグ
				$this->oneTimeValidateFlag = false;
				// Validate、Saveで使用するオプションデータ
				$options = array(
					'registration_answer_summary_id' => $summaryId,
					'question' => $targetQuestion[0],
					'allAnswers' => $data['RegistrationAnswer'],
				);

				if (! $this->saveMany($answer, $options)) {
					$validationErrors[$targetQuestionKey] = $this->__errorMessageUnique(
						$targetQuestion[0],
						Hash::filter($this->validationErrors));
				}
			}
			if (! empty($validationErrors)) {
				$this->validationErrors = Hash::filter($validationErrors);
				$this->rollback();
				return false;
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
 * __errorMessageUnique
 * マトリクスの同じエラーメッセージをまとめる
 *
 * @param array $question question
 * @param array $errors error message
 * @return array
 */
	private function __errorMessageUnique($question, $errors) {
		if (! RegistrationsComponent::isMatrixInputType($question['question_type'])) {
			return $errors;
		}
		$ret = array();
		foreach ($errors as $err) {
			if (! in_array($err, $ret)) {
				$ret[] = $err;
			}
		}
		return $ret;
	}
}
