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
class CheckDateCompTest extends NetCommonsModelTestCase {

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
 * test CheckDateComp
 *
 * @param array $data Postされたデータ
 * @param array $check validateとして渡されるチェックデータ
 * @param string $operator 判断式
 * @param datetime $compare 比較対象日時データ
 * @param bool $expected 期待値
 * @dataProvider dataProviderCheckDateComp
 * @return void
 */
	public function testCheckDateComp($data, $check, $operator, $compare, $expected) {
		$this->TestRegistrationModel->create();
		$this->TestRegistrationModel->set($data);
		$result = $this->TestRegistrationModel->checkDateComp($check, $operator, $compare);
		$this->assertEqual($result, $expected);
	}
/**
 * dataProviderCheckDateComp
 *
 * testCheckDateCompのデータプロバイダ
 * @return array
 */
	public function dataProviderCheckDateComp() {
		$data = array(
			'Registration' => array(
				'answer_start_period' => '2006-06-06 00:12:00',
				'answer_end_period' => '2016-06-06 00:12:00'
			)
		);
		$data1 = Hash::insert($data, 'Registration.answer_start_period', '');
		$data2 = Hash::insert($data, 'Registration.answer_end_period', '');
		$data3 = Hash::insert($data, 'Registration.answer_start_period', '2020-10-10 00:00:00');
		return array(
			array($data, array('answer_start_period' => '2006-06-06 00:12:00'), '<=', 'answer_end_period', true),
			array($data1, array('answer_start_period' => ''), '<=', 'answer_end_period', true),
			array($data2, array('answer_start_period' => '2006-06-06 00:12:00'), '<=', 'answer_end_period', true),
			array($data3, array('answer_start_period' => '2020-10-10 00:00:00'), '<=', 'answer_end_period', false),
		);
	}
}

