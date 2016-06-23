<?php
/**
 * Registrations::saveExportKey()のテスト
 *
 * @property Registrations $Registrations
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
 * Registrations::saveExportKey()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\Registrations
 */
class RegistrationSaveExportKeyTest extends NetCommonsModelTestCase {

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
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.registrations.registration_setting',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.registration_setting',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'Registration';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveExportKey';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->Frame = ClassRegistry::init('Frames' . '.' . 'Frame');
		$this->Block = ClassRegistry::init('Blocks' . '.' . 'Block');
		$this->RegistrationSetting = ClassRegistry::init('Registrations' . '.' . 'RegistrationSetting');
		$this->RegistrationFrameSetting = ClassRegistry::init('Registrations' . '.' . 'RegistrationFrameSetting');
	}

/**
 * Saveのテスト
 *
 * @return void
 */
	public function testSave() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$registrationId = 1;
		//登録データ取得
		$before = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $registrationId),
		));

		//テスト実行
		$result = $this->$model->$method($registrationId, 'testExportKey');
		$this->assertNotEmpty($result);

		//登録データ取得
		$actual = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $registrationId),
		));

		$this->assertNotEquals($before[$model]['export_key'], $actual[$model]['export_key']);
		$this->assertEqual('testExportKey', $actual[$model]['export_key']);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @return void
 */
	public function testSaveOnExceptionError() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $model, 'saveField');
		$this->setExpectedException('InternalErrorException');
		//テスト実行
		$registrationId = 1;
		$this->$model->$method($registrationId, 'testExportKey');
	}
}
