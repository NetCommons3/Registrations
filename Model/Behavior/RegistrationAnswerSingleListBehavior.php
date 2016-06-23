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
 * SingleList Behavior
 *
 * @package  Registrations\Registrations\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class RegistrationAnswerSingleListBehavior extends RegistrationAnswerBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = RegistrationsComponent::TYPE_SINGLE_SELECT_BOX;

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
	protected $_isTypeAnsArrShiftUp = true;

/**
 * choice validate check type
 *
 * @var array
 */
	protected $_choiceValidateType = array(
		RegistrationsComponent::TYPE_SELECTION,
		RegistrationsComponent::TYPE_MULTIPLE_SELECTION,
		RegistrationsComponent::TYPE_SINGLE_SELECT_BOX,
	);

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
		$model->data['RegistrationAnswer']['answer_values'] = array();
		if (isset($model->data['RegistrationAnswer']['answer_value'])) {
			$this->_decomposeAnswerValue($model->data['RegistrationAnswer']['answer_values'],
				$model->data['RegistrationAnswer']['answer_value']);
		}
	}
/**
 * answerValidation 登録内容の正当性
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 登録データに対応する項目
 * @param array $allAnswers 入力された登録すべて
 * @return bool
 */
	public function answerChoiceValidation(&$model, $data, $question, $allAnswers) {
		if (! in_array($question['question_type'], $this->_choiceValidateType)) {
			return true;
		}
		if (! isset($model->data['RegistrationAnswer']['answer_values'])) {
			return true;
		}
		// 項目に設定されている選択肢を配列にまとめる
		$list = Hash::combine($question['RegistrationChoice'], '{n}.id', '{n}.key');

		$ret = true;
		// 選択された選択肢IDすべてについて調査する
		$choiceIds = array_keys($model->data['RegistrationAnswer']['answer_values']);
		foreach ($choiceIds as $choiceId) {
			// 選択されたIDは、ちゃんと用意されている選択肢の中のひとつであるか
			if ($choiceId != '' && !Validation::inList(strval($choiceId), $list)) {
				$ret = false;
				$model->validationErrors['answer_value'][] =
					__d('registrations', 'Invalid choice');
			}
			// チェックされている選択肢が「その他」の項目である場合は
			$choice = Hash::extract($question['RegistrationChoice'], '{n}[key=' . $choiceId . ']');
			if ($choice &&
				$choice[0]['other_choice_type'] != RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				// 具体的なテキストが書かれていないといけない
				if (empty($model->data['RegistrationAnswer']['other_answer_value'])) {
					$ret = false;
					$model->validationErrors['answer_value'][] =
						__d('registrations', 'Please enter something, if you chose the other item');
				}
			}
		}
		return $ret;
	}

}