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
class RegistrationAnswerHelperMatrixTest extends NetCommonsCakeTestCase {

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
 * _getQuestion method
 *
 * @return array
 */
	protected function _getQuestion() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => RegistrationsComponent::TYPE_MATRIX_SELECTION_LIST,
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
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT,
					'choice_sequence' => 1,
					'choice_label' => '選択肢2',
					'choice_value' => '選択肢2',
				),
				array(
					'key' => 'cKey3',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_COLUMN,
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 2,
					'choice_label' => '列１',
					'choice_value' => '列１',
				),
				array(
					'key' => 'cKey4',
					'matrix_type' => RegistrationsComponent::MATRIX_TYPE_COLUMN,
					'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 3,
					'choice_label' => '列２',
					'choice_value' => '列２',
				)
			)
		);
		return $question;
	}
/**
 * Test RegistrationAnswer->matrix()
 *
 * @return void
 */
	public function testSingleMatrix() {
		$question = $this->_getQuestion();
		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertInput('input', 'registration_answer_summary_id', '', $actual);
		$this->assertInput('input', 'registration_question_key', '', $actual);
		$this->assertInput('input', 'matrix_choice_key', '', $actual);
		$this->assertInput('input', 'id', '', $actual);
		$this->assertInput('input', 'answer_value', '', $actual);
	}

/**
 * Test RegistrationAnswer->matrix()
 *
 * @return void
 */
	public function testMultipleMatrix() {
		$question = $this->_getQuestion();
		$question['question_type'] = RegistrationsComponent::TYPE_MATRIX_MULTIPLE;
		$actual = $this->RegistrationAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertInput('input', 'registration_answer_summary_id', '', $actual);
		$this->assertInput('input', 'registration_question_key', '', $actual);
		$this->assertInput('input', 'matrix_choice_key', '', $actual);
		$this->assertInput('input', 'id', '', $actual);
		$this->assertInput('input', 'answer_value', '', $actual);
	}

/**
 * Assert input tag
 *
 * @param string $tagType タグタイプ(input or textearea or button)
 * @param string $name inputタグのname属性
 * @param string $value inputタグのvalue値
 * @param string $result Result data
 * @return void
 */
	public function assertInput($tagType, $name, $value, $result) {
		$result = str_replace("\n", '', $result);
		$patternName = '.*?name="data\[RegistrationAnswer\].*?\[' . preg_quote($name, '/') . '\]"';

		$this->assertRegExp(
				'/<' . $tagType . $patternName . '.*?>/', $result
		);
	}

}
