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
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');
App::uses('RegistrationQuestionFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationChoiceFixture', 'Registrations.Test/Fixture');

/**
 * RegistrationAnswer::validate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationAnswer
 */
class RegistrationAnswerValidateTest extends NetCommonsModelTestCase {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'registrations';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.registrations.registration',
		'plugin.registrations.block_setting_for_registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.site_manager.site_setting',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationAnswer';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveMany';

/**
 * Validatesのテスト
 *
 * @param array $data 登録データ
 * @param int $summaryId サマリID
 * @param array $targetQuestion 項目データ
 * @param string $field フィールド名
 * @param string $value セットする値
 * @param string $message エラーメッセージ
 * @param array $overwrite 上書きするデータ
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidationError(
		$data, $summaryId, $targetQuestion, $field, $value, $message, $overwrite = array()) {
		$model = $this->_modelName;

		if (! is_null($field)) {
			if (is_null($value)) {
				unset($data[0][$field]);
			} else {
				$data[0][$field] = $value;
			}
		}
		$registration = $this->_getRegistration(2);
		$addQuestion = array(
			'RegistrationPage' => array(
				array(
					'RegistrationQuestion' => array($targetQuestion)
				)
			)
		);
		$registration = Hash::merge($registration, $addQuestion);
		$summary = array('RegistrationAnswerSummary' => array('id' => $summaryId));
		$result = $this->$model->saveAnswer(
			array('RegistrationAnswer' => array($targetQuestion['key'] => $data)),
			$registration,
			$summary);
		$this->assertFalse($result);

		if ($message) {
			//$this->assertEquals($validationErrors[0][$field][0], $message);
			// 登録フォームの登録時のエラーメッセージはすべてこのフィールドに集約してるのだった
			$this->assertEquals(
				$this->$model->validationErrors[$targetQuestion['key']][0]['answer_value'][0],
				$message);
		}
	}

/**
 * _getQuestion
 *
 * @param int $id 項目ID
 * @return array
 */
	protected function _getQuestion($id) {
		$fixtureQuestion = new RegistrationQuestionFixture();
		$fixtureChoice = new RegistrationChoiceFixture();
		$rec = Hash::extract($fixtureQuestion->records, '{n}[id=' . $id . ']');
		$question = $rec[0];
		$rec = Hash::extract($fixtureChoice->records, '{n}[registration_question_id=' . $id . ']');
		$question['RegistrationChoice'] = $rec;
		return $question;
	}

/**
 * _getRegistration
 *
 * @param int $id 項目ID
 * @return array
 */
	protected function _getRegistration($id) {
		$fixtureRegistration = new RegistrationFixture();
		$fixturePage = new RegistrationPageFixture();
		$fixtureQuestion = new RegistrationQuestionFixture();
		$fixtureChoice = new RegistrationChoiceFixture();

		$data = array();
		$rec = Hash::extract($fixtureRegistration->records,
			'{n}[id=' . $id . ']');
		$data['Registration'] = $rec[0];

		$rec = Hash::extract($fixturePage->records,
			'{n}[registration_id=' . $data['Registration']['id'] . ']');
		$rec = Hash::extract($rec,
			'{n}[language_id=2]');
		$data['RegistrationPage'] = $rec;

		foreach ($data['RegistrationPage'] as &$page) {
			$pageId = $page['id'];

			$rec = Hash::extract($fixtureQuestion->records,
				'{n}[registration_page_id=' . $pageId . ']');
			$rec = Hash::extract($rec, '{n}[language_id=2]');
			$page['RegistrationQuestion'] = $rec;
			$questionId = $rec[0]['id'];

			$rec = Hash::extract($fixtureChoice->records,
				'{n}[registration_question_id=' . $questionId . ']');
			if ($rec) {
				$page['RegistrationQuestion'][0]['RegistrationChoice'] = $rec;
			}
		}
		return $data;
	}

}
