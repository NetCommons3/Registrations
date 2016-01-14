<?php
/**
 * RegistrationValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationAnswerBehavior', 'Registrations.Model/Behavior');

/**
 * MatrixMultipleChoice Behavior
 *
 * @package  Registrations\Registrations\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class RegistrationAnswerMatrixMultipleChoiceBehavior extends RegistrationAnswerMatrixSingleChoiceBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = RegistrationsComponent::TYPE_MATRIX_MULTIPLE;

/**
 * this answer type
 * data in database must be changed to array
 *
 * @var int
 */
	protected $_isTypeAnsChgArr = true;

/**
 * this answer type
 * data array must be shift up for post data array in screen
 *
 * @var int
 */
	protected $_isTypeAnsArrShiftUp = false;

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		$question = $options['question'];
		if ($question['question_type'] != $this->_myType) {
			return;
		}
		$model->data['RegistrationAnswer']['matrix_answer_values'] = array();
		$model->data['RegistrationAnswer']['multi_answer_values'] = '';
		$matrixChoiceId = $model->data['RegistrationAnswer']['matrix_choice_key'];
		if (is_array($model->data['RegistrationAnswer']['answer_value'])) {
			foreach ($model->data['RegistrationAnswer']['answer_value'] as $ans) {
				$this->_decomposeAnswerValue($model->data['RegistrationAnswer']['matrix_answer_values'][$matrixChoiceId], $ans);
				$model->data['RegistrationAnswer']['multi_answer_values'] .= $ans;
			}
		}
		$this->_setupOtherAnswerValue($model, $question);
	}
}