<?php
/**
 * RegistrationAnswerHelper::singleChoice()のテスト
 *
 * @property RegistrationAnswerHelper $RegistrationAnswerHelper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('View', 'View');
App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');
App::uses('RegistrationAnswerHelper', 'Registrations.View/Helper');
App::uses('NetCommonsHtmlHelper', 'NetCommons.View/Helper');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Summary for RegistrationAnswerHelper Test Case
 */
class RegistrationAnswerHelperDatetimeTest extends NetCommonsCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$this->RegistrationAnswer = new RegistrationAnswerHelper($View);
		$this->NetCommonsHtml = new NetCommonsHtmlHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->RegistrationAnswer);
		parent::tearDown();
	}

/**
 * _getComElement method
 *
 * @return string
 */
	protected function _getComElement() {
		$ret = <<< EOT
<div class="has-error">
</div>
<input type="hidden" name="data[RegistrationAnswer][qKey1][0][registration_answer_summary_id]" id="RegistrationAnswerQKey10RegistrationAnswerSummaryId"/>
<input type="hidden" name="data[RegistrationAnswer][qKey1][0][registration_question_key]" value="qKey1" id="RegistrationAnswerQKey10RegistrationQuestionKey"/>
<input type="hidden" name="data[RegistrationAnswer][qKey1][0][id]" id="RegistrationAnswerQKey10Id"/>
<input type="hidden" name="data[RegistrationAnswer][qKey1][0][matrix_choice_key]" id="RegistrationAnswerQKey10MatrixChoiceKey"/>
EOT;
		return $ret;
	}

/**
 * _getDatetimeBaseElement method
 *
 * @return string
 */
	protected function _getDatetimeBaseElement() {
		$ret = <<< EOT
<div class="row">
	<div class="col-sm-4">
		<div class="date">
			<divclass="inputtext"style="position:relative;position:relative;">
				<input name="data[RegistrationAnswer][qKey1][0][answer_value]" class="form-control"
				datetimepicker="1" datetimepicker-options="%s"
				ng-model="dateAnswer[&#039;qKey1&#039;]" 
				data-toggle="dropdown"
				type="text" id="RegistrationAnswerQKey10AnswerValue"/>
			</div>
		</div>
	</div>
</div>
<span class="help-block">%s</span>
EOT;
		return $ret;
	}

/**
 * Test RegistrationAnswer->dateTimeInput()
 *
 * @return void
 */
	public function testDateTimeInputDate() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_DATE_AND_TIME,
			'is_range' => 0,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_DATE,
		);

		$datetimePickerOpt = htmlspecialchars(json_encode(array(
			'format' => 'YYYY-MM-DD'
		)));
		$help = '';
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', sprintf($this->_getDatetimeBaseElement(), $datetimePickerOpt, $help) . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test RegistrationAnswer->dateTimeInput()
 *
 * @return void
 */
	public function testDateTimeInputDateRage() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_DATE_AND_TIME,
			'is_range' => 1,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_DATE,
			'min' => '2014-08-01',
			'max' => '2016-08-01',
		);

		$datetimePickerOpt = htmlspecialchars(
			json_encode(array(
				'format' => 'YYYY-MM-DD',
				'minDate' => '2014-08-01',
				'maxDate' => '2016-08-01',
				)
			)
		);
		$help = sprintf(__d('registrations', 'Please enter at %s to %s'), date('Y-m-d', strtotime($question['min'])), date('Y-m-d', strtotime($question['max'])));
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', sprintf($this->_getDatetimeBaseElement(), $datetimePickerOpt, $help) . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test RegistrationAnswer->dateTimeInput()
 *
 * @return void
 */
	public function testDateTimeInputTime() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_DATE_AND_TIME,
			'is_range' => 0,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_TIME,
		);

		$datetimePickerOpt = htmlspecialchars(json_encode(array(
			'format' => 'HH:mm'
		)));
		$help = '';
		$expected = sprintf($this->_getDatetimeBaseElement(), $datetimePickerOpt, $help);
		$expected = str_replace('calendar', 'time', $expected);
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test RegistrationAnswer->dateTimeInput()
 *
 * @return void
 */
	public function testDateTimeInputTimeRange() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_DATE_AND_TIME,
			'is_range' => 1,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_TIME,
			'min' => '00:00',
			'max' => '15:15',
		);
		$today = date('Y-m-d');
		$datetimePickerOpt = htmlspecialchars(json_encode(array(
			'format' => 'HH:mm',
			'minDate' => $today . ' 00:00',
			'maxDate' => $today . ' 15:15',
		)));
		$help = sprintf(__d('registrations', 'Please enter at %s to %s'), date('H:i', strtotime($question['min'])), date('H:i', strtotime($question['max'])));
		$expected = sprintf($this->_getDatetimeBaseElement(), $datetimePickerOpt, $help);
		$expected = str_replace('calendar', 'time', $expected);
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

/**
 * Test RegistrationAnswer->dateTimeInput()
 *
 * @return void
 */
	public function testDateTimeInputDatetime() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_DATE_AND_TIME,
			'is_range' => 0,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_DATE_TIME,
		);

		$datetimePickerOpt = htmlspecialchars(json_encode(array(
			'format' => 'YYYY-MM-DD HH:mm'
		)));
		$help = '';
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', sprintf($this->_getDatetimeBaseElement(), $datetimePickerOpt, $help) . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test RegistrationAnswer->dateTimeInput()
 *
 * @return void
 */
	public function testDateTimeInputDatetimeRange() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_DATE_AND_TIME,
			'is_range' => 1,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_DATE_TIME,
			'min' => '2014-08-01 00:00',
			'max' => '2016-08-01 15:00',
		);

		$datetimePickerOpt = htmlspecialchars(
			json_encode(array(
					'format' => 'YYYY-MM-DD HH:mm',
					'minDate' => '2014-08-01 00:00',
					'maxDate' => '2016-08-01 15:00',
				)
			)
		);
		$help = sprintf(__d('registrations', 'Please enter at %s to %s'), date('Y-m-d H:i', strtotime($question['min'])), date('Y-m-d H:i', strtotime($question['max'])));
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', sprintf($this->_getDatetimeBaseElement(), $datetimePickerOpt, $help) . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test RegistrationAnswer->dateTimeInput()
 *
 * @return void
 */
	public function testDateTimeInputDatetimeReadonly() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_DATE_AND_TIME,
			'is_range' => 0,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_DATE_TIME,
		);

		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question, true);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
}