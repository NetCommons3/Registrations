<?php
/**
 * RegistrationOwnAnswerComponent::getOwnAnsweredKeys()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * RegistrationOwnAnswerComponent::getOwnAnsweredKeys()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Controller\Component\RegistrationOwnAnswerComponent
 */
class RegistrationOwnAnswerComponentgetOwnAnsweredKeysTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.registrations.registration',
		'plugin.registrations.registration_setting',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'registrations';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Registrations', 'TestRegistrations');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * getOwnAnswerKeys()のテスト
 *
 * @return void
 */
	public function testGetOwnAnswerKeys() {
		//テストコントローラ生成
		$this->generateNc('TestRegistrations.TestRegistrationsOwnAnswerComponent');

		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		//テスト実行
		$this->_testGetAction(
			'/test_registrations/test_registration_own_answer_component/index',
			array('method' => 'assertNotEmpty'),
			null,
			'view');

		$result = $this->controller->RegistrationsOwnAnswer->getOwnAnsweredKeys();

		//チェック
		$this->assertEqual($result, array(
			'registration_20',
			'registration_22',
			'registration_24',
			'registration_4',
		));
		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * getOwnAnswerKeys()のテスト
 *
 * @return void
 */
	public function testGetOwnAnswerKeysNoLogin() {
		//テストコントローラ生成
		$this->generateNc('TestRegistrations.TestRegistrationsOwnAnswerComponent');

		$blockId = Current::read('Block.id');
		$this->controller->Session->expects($this->any())
			->method('read')
			->will(
				$this->returnValueMap([
					['Registrations.ownAnsweredKeys.' . $blockId,
						'registration_99,registration_100']
				]));

		//テスト実行
		$this->_testGetAction(
			'/test_registrations/test_registration_own_answer_component/index',
			array('method' => 'assertNotEmpty'),
			null,
			'view');

		$result = $this->controller->RegistrationsOwnAnswer->getOwnAnsweredKeys();

		//チェック
		$this->assertEqual($result, array(
			'registration_99',
			'registration_100'
		));
	}
}
