<?php
/**
 * RegistrationValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationAnswerBehavior', 'Registrations.Model/Behavior');

/**
 * Email Behavior
 *
 * @package  Registrations\Registrations\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class RegistrationAnswerEmailBehavior extends RegistrationAnswerBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = RegistrationsComponent::TYPE_EMAIL;

/**
 * answerMaxLength 登録が登録フォームが許す最大長を超えていないかの確認
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 登録データに対応する項目
 * @param int $max 最大長
 * @return bool
 */
	public function answerMaxLength(&$model, $data, $question, $max) {
		if ($question['question_type'] != $this->_myType) {
			return true;
		}
		return Validation::maxLength($data['answer_value'], $max);
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
	public function answerEmailValidation(&$model, $data, $question, $allAnswers) {
		if ($question['question_type'] != $this->_myType) {
			return true;
		}
		return Validation::email($data['answer_value']);
	}

/**
 * 確認用メールアドレスが一致するか
 *
 * @param Model &$model Model
 * @param array $data Validation対象データ
 * @param array $question 登録データに対する項目
 * @param array $allAnswers 入力された登録すべて
 * @return bool
 */
	public function answerEmailConfirmValidation(&$model, $data, $question, $allAnswers) {
		if ($question['question_type'] != $this->_myType) {
			return true;
		}
		$questionKey = $question['key'];
		$confirmData = $allAnswers[$questionKey][0]['answer_value_again'];
		return ($confirmData == $data['answer_value']);
	}
}

