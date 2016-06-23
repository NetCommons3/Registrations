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
class ValidateAnswerDatetimeTest extends RegistrationAnswerValidateTest {

/**
 * __getData
 *
 * @param string $qKey 質問キー
 * @param int $summaryId サマリID
 * @return array
 */
	private function __getData($qKey, $summaryId) {
		$answerData = array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'answer_value' => '2016-02-25',
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
		$data = $this->__getData('qKey_13', 4);
		// 通常の質問
		$normalQuestion = Hash::merge($this->_getQuestion(14), array('is_range' => 0, 'is_require' => 0));
		// 解答必須質問
		$requireQuestion = Hash::merge($normalQuestion, array('is_require' => RegistrationsComponent::REQUIRES_REQUIRE));
		// 日付タイプで範囲設定
		$rangeQuestion = Hash::merge($normalQuestion, array('question_type_option' => RegistrationsComponent::TYPE_OPTION_DATE, 'is_range' => 1, 'min' => '2015-01-01', 'max' => '2015-12-31'));
		// 時間タイプ
		$timeQuestion = Hash::merge($normalQuestion, array('question_type_option' => RegistrationsComponent::TYPE_OPTION_TIME));
		// 時間タイプで範囲設定
		$timeRangeQuestion = Hash::merge($timeQuestion, array('is_range' => 1, 'min' => '06:15', 'max' => '12:30'));
		// 日時タイプ
		$datetimeQuestion = Hash::merge($normalQuestion, array('question_type_option' => RegistrationsComponent::TYPE_OPTION_DATE_TIME));
		// 日時タイプで範囲設定
		$datetmRangeQuestion = Hash::merge($datetimeQuestion, array('is_range' => 1, 'min' => '2015-07-01 06:15', 'max' => '2015-12-31 12:30'));
		// オプションタイプ不正
		$noOptQuestion = Hash::merge($normalQuestion, array('question_type_option' => RegistrationsComponent::TYPE_OPTION_PHONE_NUMBER));
		return array(
			array($data, 3, $normalQuestion, 'answer_value', 'ABC',
				__d('registrations', 'Please enter a valid date in YY-MM-DD format.')),
			array($data, 3, $normalQuestion, 'answer_value', '2016-13-32',
					__d('registrations', 'Please enter a valid date in YY-MM-DD format.')),
			array($data, 3, $requireQuestion, 'answer_value', '',
				__d('registrations', 'Input required')),
			array($data, 3, $rangeQuestion, 'answer_value', '2016-11-30',
				sprintf(__d('registrations', 'Please enter the answer between %s and %s.'), '2015-01-01', '2015-12-31')),
			array($data, 3, $timeQuestion, 'answer_value', '2854',
				__d('registrations', 'Please enter the time.')),
			array($data, 3, $timeRangeQuestion, 'answer_value', '01:00',
				sprintf(__d('registrations', 'Please enter the answer between %s and %s.'), '06:15', '12:30')),
			array($data, 3, $datetimeQuestion, 'answer_value', '2016-11-30',
				__d('registrations', 'Please enter a valid date and time.')),
			array($data, 3, $datetmRangeQuestion, 'answer_value', '2015-01-01 01:00',
				sprintf(__d('registrations', 'Please enter the answer between %s and %s.'), '2015-07-01 06:15', '2015-12-31 12:30')),
			array($data, 3, $noOptQuestion, 'answer_value', '000',
				__d('net_commons', 'Invalid request.')),
		);
	}
}
