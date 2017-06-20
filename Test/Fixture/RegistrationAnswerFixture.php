<?php
/**
 * RegistrationAnswerFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for RegistrationAnswerFixture
 */
class RegistrationAnswerFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'answer_value' => 'choice label26',
			'registration_answer_summary_id' => 1,
			'registration_question_key' => 'qKey_25'
		),
		array(
			'id' => 2,
			'answer_value' => 'choice label26',
			'registration_answer_summary_id' => 2,
			'registration_question_key' => 'qKey_25'
		),
		array(
			'id' => 3,
			'answer_value' => '|choice_32:choice label32',
			'registration_answer_summary_id' => 8,
			'registration_question_key' => 'qKey_39'
		),
		array(
			'id' => 4,
			'answer_value' => '|choice_35:choice label35',
			'registration_answer_summary_id' => 9,
			'registration_question_key' => 'qKey_41',
			'matrix_choice_key' => 'choice_33'
		),
		array(
			'id' => 5,
			'answer_value' => '|choice_35:choice label35',
			'registration_answer_summary_id' => 9,
			'registration_question_key' => 'qKey_41',
			'matrix_choice_key' => 'choice_34'
		),
		array(
			'id' => 6,
			'answer_value' => '|choice_39:choice label39',
			'registration_answer_summary_id' => 10,
			'registration_question_key' => 'qKey_43',
			'matrix_choice_key' => 'choice_37'
		),
		array(
			'id' => 7,
			'answer_value' => '|choice_39:choice label39',
			'registration_answer_summary_id' => 10,
			'registration_question_key' => 'qKey_43',
			'matrix_choice_key' => 'choice_38'
		),
		array(
			'id' => 8,
			'answer_value' => '|choice_12:choice label12',
			'registration_answer_summary_id' => 11,
			'registration_question_key' => 'qKey_9',
			'matrix_choice_key' => 'choice_9'
		),
		array(
			'id' => 9,
			'answer_value' => '|choice_12:choice label12',
			'registration_answer_summary_id' => 11,
			'registration_question_key' => 'qKey_9',
			'matrix_choice_key' => 'choice_10'
		),
		array(
			'id' => 10,
			'answer_value' => '|choice_27:choice label27',
			'registration_answer_summary_id' => 12,
			'registration_question_key' => 'qKey_27',
			'matrix_choice_key' => ''
		),
		array(
			'id' => 11,
			'answer_value' => '|choice_29:choice label29',
			'registration_answer_summary_id' => 12,
			'registration_question_key' => 'qKey_29',
			'matrix_choice_key' => ''
		),
		array(
			'id' => 12,
			'answer_value' => '|choice_31:choice label31',
			'registration_answer_summary_id' => 12,
			'registration_question_key' => 'qKey_31',
			'matrix_choice_key' => ''
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Registrations') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new RegistrationsSchema())->tables[Inflector::tableize($this->name)];
		parent::init();
	}

}
