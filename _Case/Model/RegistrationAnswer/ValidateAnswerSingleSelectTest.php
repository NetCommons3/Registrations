<?php
/**
 * RegistrationAnswer::validate()のテスト
 *
 * @property RegistrationAnswer $RegistrationAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationAnswerValidateTest', 'Registrations.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationAnswer::validate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationAnswer
 */
class ValidateAnswerSingleSelectTest extends RegistrationAnswerValidateTest {

/**
 * __getData
 *
 * @param string $qKey 項目キー
 * @param int $summaryId サマリID
 * @return array
 */
	private function __getData($qKey, $summaryId) {
		$answerData = array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'answer_value' => '|choice_2:choice label1',
				'registration_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
		return $answerData;
	}

/**
 * testValidationErrorのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderValidationError() {
		$data = $this->__getData('qKey_1', 3);
		// 通常の項目
		$normalQuestion = $this->_getQuestion(2);
		// 解答必須項目
		$requireQuestion = Hash::merge($normalQuestion, array('is_require' => RegistrationsComponent::REQUIRES_REQUIRE));
		// その他登録がある項目
		$otherQuestion = Hash::merge($normalQuestion, array('RegistrationChoice' => array(array('other_choice_type' => 1))));
		return array(
			array($data, 3, $normalQuestion, 'answer_value', 'aaa',
				__d('registrations', 'Invalid choice')),
			array($data, 3, $requireQuestion, 'answer_value', '',
				__d('registrations', 'Input required')),
			array($data, 3, $otherQuestion, 'other_answer_value', '',
				__d('registrations', 'Please enter something, if you chose the other item')),
		);
	}
}
