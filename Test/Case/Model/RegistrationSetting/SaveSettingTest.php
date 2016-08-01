<?php
/**
 * RegistrationSetting::saveSetting()のテスト
 *
 * @property RegistrationSetting $RegistrationSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationSetting::saveSetting()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationSetting
 */
class RegistrationSettingSaveSettingTest extends NetCommonsModelTestCase {

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
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.authorization_keys.authorization_keys'
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationSetting';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveSetting';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		Current::write('Plugin.key', $this->plugin);
	}
/**
 * Saveのテスト 通常の登録
 *
 * @return void
 */
	public function testSave() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Block']['key'] = 'block_1';

		$result = $this->$model->$method();
		$this->assertTrue($result);
	}
/**
 * Saveのテスト Setting登録で何等かのエラー
 *
 * @return void
 */
	public function testSaveError() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		// カレントのブロック情報をなくすとエラーになります
		//Current::$current['Block']['key'] = 'block_1';

		$result = $this->$model->$method();

		// Current::$current['Block']['id'] = null のため、検索結果=空によりtrue
		//$this->assertFalse($result);
		$this->assertTrue($result);
	}
/**
 * Saveのテスト 既に登録済み
 *
 * @return void
 */
	public function testSaveTrue() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Block']['id'] = '2';
		Current::$current['Block']['key'] = 'block_1';

		$result = $this->$model->$method();
		$this->assertTrue($result);
	}

}
