<?php
/**
 * RegistrationValidateBehavior Test Case
 *
 * @property Registration $Registration
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
/**
 * RegistrationValidateBehavior Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registration\Test\Case\Model
 */
class CheckDateTimeTest extends NetCommonsModelTestCase {

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
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		NetCommonsControllerTestCase::loadTestPlugin($this, 'Registrations', 'TestRegistrations');
		$this->TestRegistrationModel = ClassRegistry::init('TestRegistrations.TestRegistrationModel');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TestRegistrationModel);
		parent::tearDown();
	}

/**
 * test testCheckDateTime
 *
 * @param array $data Postされたデータ
 * @param array $check validateとして渡されるチェックデータ
 * @param bool $expected 期待値
 * @dataProvider dataProviderCheckDateTime
 * @return void
 */
	public function testCheckDateTime($data, $check, $expected) {
		$this->TestRegistrationModel->create();
		$this->TestRegistrationModel->set($data);
		$result = $this->TestRegistrationModel->checkDateTime($check);
		$this->assertEqual($result, $expected);
	}
/**
 * dataProviderCheckDateTime
 *
 * testCheckDateTimeのデータプロバイダ
 * @return array
 */
	public function dataProviderCheckDateTime() {
		$data = array(
			'Registration' => array(
				'answer_start_period' => '2006-06-06 00:12:00'
			)
		);
		return array(
			array($data, array('answer_start_period' => '2006-06-06 00:12:00'), true),
			array($data, array('answer_start_period' => ''), true),
			array($data, array('answer_start_period' => '4567-34-90 44:44:44'), false)
		);
	}
}

