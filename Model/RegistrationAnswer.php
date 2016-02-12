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
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

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
					'rule' => array('answerMaxLength', $question, RegistrationsComponent::REGISTRATION_MAX_ANSWER_LENGTH),
					'message' => sprintf(__d('registrations', 'the answer is too long. Please enter under %d letters.', RegistrationsComponent::REGISTRATION_MAX_ANSWER_LENGTH)),
				),
				'answerValidation' => array(
					'rule' => array('answerValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
			),
		);
		parent::beforeValidate($options);

		return true;
	}

/**
 * getProgressiveAnswerOfThisSummary
 *
 * @param array $summary registration summary ( one record )
 * @return array
 */
	public function getProgressiveAnswerOfThisSummary($summary) {
		$answers = array();
		if (empty($summary)) {
			return $answers;
		}
		$answer = $this->find('all', array(
			'conditions' => array(
				'registration_answer_summary_id' => $summary['RegistrationAnswerSummary']['id']
			),
			'recursive' => -1
		));
		if (!empty($answer)) {
			foreach ($answer as $ans) {
				$answers[$ans['RegistrationAnswer']['registration_question_key']][] = $ans['RegistrationAnswer'];
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
 * @throws $ex
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
				$targetQuestion = Hash::extract($registration['RegistrationPage'], '{n}.RegistrationQuestion.{n}[key=' . $targetQuestionKey . ']');
				// データ保存
				// Matrixタイプの場合はanswerが配列になっているがsaveでかまわない
				$this->oneTimeValidateFlag = false;	// saveMany中で１回しかValidateしなくてよい関数のためのフラグ
				if (!$this->saveMany($answer, array(
					'registration_answer_summary_id' => $summaryId,
					'question' => $targetQuestion[0],
					'allAnswers' => $data['RegistrationAnswer']))) {
					$validationErrors[$targetQuestionKey] = Hash::filter($this->validationErrors);
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
}