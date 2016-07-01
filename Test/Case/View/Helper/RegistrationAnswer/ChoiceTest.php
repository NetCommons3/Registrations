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
App::uses('NetCommonsHtmlHelper', 'NetCommons.View/Helper');
App::uses('RegistrationAnswerHelper', 'Registrations.View/Helper');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Summary for RegistrationAnswerHelper Test Case
 */
class RegistrationAnswerHelperChoiceTest extends NetCommonsCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$this->NetCommonsHtml = new NetCommonsHtmlHelper($View);
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
 * Test RegistrationAnswer->singleChoice()
 *
 * @return void
 */
	public function testSingleChoice() {
		$expected = <<< EOT
<input type="hidden" name="data[RegistrationAnswer][qKey1][0][answer_value]" value="" id="RegistrationAnswerQKey10AnswerValue" />
<div class="input radio">
	<div class="radio">
		<label>
			<input type="hidden" name="data[RegistrationAnswer][qKey1][0][answer_value]" id="RegistrationAnswerQKey10AnswerValue_" value=""/>
			<input type="radio" name="data[RegistrationAnswer][qKey1][0][answer_value]" id="RegistrationAnswerQKey10AnswerValueCKey1選択肢1" value="|cKey1:選択肢1"/>
			選択肢1
		</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="data[RegistrationAnswer][qKey1][0][answer_value]" id="RegistrationAnswerQKey10AnswerValueCKey2選択肢2" value="|cKey2:選択肢2"/>
			選択肢2
		</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="data[RegistrationAnswer][qKey1][0][answer_value]" id="RegistrationAnswerQKey10AnswerValueCKey3その他" value="|cKey3:その他"/>
			その他
			<input name="data[RegistrationAnswer][qKey1][0][other_answer_value]" class="form-control" type="text" id="RegistrationAnswerQKey10OtherAnswerValue"/>
		</label>
	</div>
</div>
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_SELECTION,
			'is_choice_horizon' => false,
			'RegistrationChoice' => array(
				array(
					'key' => 'cKey1',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 0,
					'choice_label' => '選択肢1',
					'choice_value' => '選択肢1',
				),
				array(
					'key' => 'cKey2',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 1,
					'choice_label' => '選択肢2',
					'choice_value' => '選択肢2',
				),
				array(
					'key' => 'cKey3',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT,
					'choice_sequence' => 2,
					'choice_label' => 'その他',
					'choice_value' => 'その他',
				)
			)
		);

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);

		$expected = preg_replace('/class="radio/', 'class="radio-inline', $expected);
		$question['is_choice_horizon'] = true;
		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

/**
 * Test RegistrationAnswer->multipleChoice()
 *
 * @return void
 */
	public function testMultipleChoice() {
		$expected = <<< EOT
<input type="hidden" name="data[RegistrationAnswer][qKey1][0][answer_value]" value="" id="RegistrationAnswerQKey10AnswerValue"/>
<div class="checkbox nc-checkbox">
	<input type="checkbox" name="data[RegistrationAnswer][qKey1][0][answer_value][]" value="cKey1:選択肢1" id="RegistrationAnswerQKey10AnswerValueCKey1選択肢1" />
	<label for="RegistrationAnswerQKey10AnswerValueCKey1選択肢1">
		選択肢1
	</label>
</div>
<div class="checkbox nc-checkbox">
	<input type="checkbox" name="data[RegistrationAnswer][qKey1][0][answer_value][]" value="cKey2:選択肢2" id="RegistrationAnswerQKey10AnswerValueCKey2選択肢2" />
	<label for="RegistrationAnswerQKey10AnswerValueCKey2選択肢2">
		選択肢2
	</label>
</div>
<div class="checkbox nc-checkbox">
	<input type="checkbox" name="data[RegistrationAnswer][qKey1][0][answer_value][]" value="cKey3:その他" id="RegistrationAnswerQKey10AnswerValueCKey3その他" />
	<label for="RegistrationAnswerQKey10AnswerValueCKey3その他">
		その他
	</label>
</div>
<div class="checkbox-inline">
<input name="data[RegistrationAnswer][qKey1][0][other_answer_value]" class="form-control" type="text" id="RegistrationAnswerQKey10OtherAnswerValue"/>
</div>
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_MULTIPLE_SELECTION,
			'is_choice_horizon' => false,
			'RegistrationChoice' => array(
				array(
					'key' => 'cKey1',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 0,
					'choice_label' => '選択肢1',
					'choice_value' => '選択肢1',
				),
				array(
					'key' => 'cKey2',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 1,
					'choice_label' => '選択肢2',
					'choice_value' => '選択肢2',
				),
				array(
					'key' => 'cKey3',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT,
					'choice_sequence' => 2,
					'choice_label' => 'その他',
					'choice_value' => 'その他',
				)
			)
		);

		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);

		$expected = preg_replace('/class="checkboxnc-checkbox/', 'class="checkbox-inlinenc-checkbox', $expected);
		$question['is_choice_horizon'] = true;
		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test RegistrationAnswer->singleList()
 *
 * @return void
 */
	public function testSingleList() {
		$expected = <<< EOT
<div class="form-inline">
	<select name="data[RegistrationAnswer][qKey1][0][answer_value]" class="form-control" id="RegistrationAnswerQKey10AnswerValue">
		<option value="">%s</option>
		<option value="|cKey1:選択肢1">選択肢1</option>
		<option value="|cKey2:選択肢2">選択肢2</option>
		<option value="|cKey3:選択肢3">選択肢3</option>
	</select>
</div>
EOT;
		$expected = sprintf($expected, __d('registrations', 'Please choose one'));
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_SINGLE_SELECT_BOX,
			'RegistrationChoice' => array(
				array(
					'key' => 'cKey1',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'choice_sequence' => 0,
					'choice_label' => '選択肢1',
					'choice_value' => '選択肢1',
				),
				array(
					'key' => 'cKey2',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'choice_sequence' => 1,
					'choice_label' => '選択肢2',
					'choice_value' => '選択肢2',
				),
				array(
					'key' => 'cKey3',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'choice_sequence' => 2,
					'choice_label' => '選択肢3',
					'choice_value' => '選択肢3',
				)
			)
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