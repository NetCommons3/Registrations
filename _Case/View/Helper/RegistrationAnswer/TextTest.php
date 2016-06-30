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
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Summary for RegistrationAnswerHelper Test Case
 */
class RegistrationAnswerHelperTextTest extends NetCommonsCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$this->RegistrationAnswer = new RegistrationAnswerHelper($View);
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
 * Test RegistrationAnswer->singleText()
 *
 * @return void
 */
	public function testSingleTextNormal() {
		$expected = <<< EOT
		<div class="form-inline">
			<input name="data[RegistrationAnswer][qKey1][0][answer_value]" class="form-control" type="text" id="RegistrationAnswerQKey10AnswerValue"/>
		</div>
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_TEXT,
			'is_range' => 0,
		);

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test RegistrationAnswer->singleText()
 *
 * @return void
 */
	public function testSingleTextRange() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_TEXT,
			'is_range' => 1,
			'min' => 5,
			'max' => 10,
			'question_type_option' => 0
		);

		$expected = <<< EOT
		<div class="form-inline">
			<input name="data[RegistrationAnswer][qKey1][0][answer_value]" class="form-control" type="text" id="RegistrationAnswerQKey10AnswerValue"/>
		</div>
		<span class="help-block">
EOT;
		$expected .= sprintf(__d('registrations', 'Please enter between %s letters and %s letters'), $question['min'], $question['max']) . '</span>';
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test RegistrationAnswer->singleText()
 *
 * @return void
 */
	public function testSingleTextNumericRange() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_TEXT,
			'is_range' => 1,
			'min' => 5,
			'max' => 10,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_NUMERIC
		);

		$expected = <<< EOT
		<div class="form-inline">
			<input name="data[RegistrationAnswer][qKey1][0][answer_value]" class="form-control" type="text" id="RegistrationAnswerQKey10AnswerValue"/>
		</div>
		<span class="help-block">
EOT;
		$expected .= sprintf(__d('registrations', 'Please enter a number between %s and %s'), $question['min'], $question['max']) . '</span>';
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

/**
 * Test RegistrationAnswer->singleText()
 *
 * @return void
 */
	public function testSingleTextReadonly() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_TEXT,
			'is_range' => 1,
			'min' => 5,
			'max' => 10,
			'question_type_option' => RegistrationsComponent::TYPE_OPTION_NUMERIC
		);

		$expected = <<< EOT
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$actual = $this->RegistrationAnswer->answer($question, true);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

/**
 * Test RegistrationAnswer->textArea()
 *
 * @return void
 */
	public function testTextArea() {
		$expected = <<< EOT
			<textarea name="data[RegistrationAnswer][qKey1][0][answer_value]" div="form-inline" class="form-control" rows="5" id="RegistrationAnswerQKey10AnswerValue"></textarea>
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_TEXT_AREA,
		);

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);

		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $this->_getComElement());
		$actual = $this->RegistrationAnswer->answer($question, true);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

}