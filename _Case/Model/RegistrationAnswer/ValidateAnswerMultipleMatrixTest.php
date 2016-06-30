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

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('RegistrationAnswerValidateTest', 'Registrations.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');
App::uses('RegistrationQuestionFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationChoiceFixture', 'Registrations.Test/Fixture');

/**
 * RegistrationAnswer::validate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationAnswer
 */
class ValidateAnswerMultipleMatrixTest extends RegistrationAnswerValidateTest {

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
				'registration_question_key' => $qKey,
				'matrix_choice_key' => 'choice_13',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => array('|choice_15:choice label15', '|choice_16:choice label16'),
			),
			array(
				'registration_answer_summary_id' => $summaryId,
				'registration_question_key' => $qKey,
				'matrix_choice_key' => 'choice_14',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => array('|choice_15:choice label15', '|choice_16:choice label16'),
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
		$data = $this->__getData('qKey_11', 4);
		// 通常の項目
		$normalQuestion = $this->_getQuestion(12);
		// 解答必須項目
		$requireQuestion = Hash::merge($normalQuestion, array('is_require' => RegistrationsComponent::REQUIRES_REQUIRE));
		// その他登録がある項目
		$otherQuestion = Hash::merge($normalQuestion, array('RegistrationChoice' => array(array('other_choice_type' => 1))));

		$extraAnswer = array(
			'registration_answer_summary_id' => 4,
			'registration_question_key' => 'qKey_11',
			'matrix_choice_key' => 'choice_1000',
			'id' => '',
			'other_answer_value' => '',
			'answer_value' => array('|choice_15:choice label15', '|choice_16:choice label16'),
		);

		return array(
			// 選択肢にないものを答える
			array(Hash::merge($data, array(array('answer_value' => array('aaa', '|choice_16:choice label16')))), 4, $normalQuestion, null, null,
				__d('registrations', 'Invalid choice')),
			array(Hash::merge($data, array(2 => $extraAnswer)), 4, $normalQuestion, null, null,
				__d('registrations', 'Invalid choice')),
			// 必須登録なのに登録なし チェックボックスに何もチェックがないときはhiddenの空文字が来る
			array(Hash::merge($data, array(array('answer_value' => '')), array('answer_value' => '')), 4, $requireQuestion, null, null,
				__d('registrations', 'Input required')),
			// その他を書かない
			array(Hash::merge($data, array()), 4, $otherQuestion, null, null,
				__d('registrations', 'Please enter something in other item')),
			// マトリクスなのに1つだけ答えてあと放置
			array(Hash::merge($data, array(array('answer_value' => ''))), 4, $normalQuestion, null, null,
				__d('registrations', 'Please answer about all rows.')),
		);
	}
}
