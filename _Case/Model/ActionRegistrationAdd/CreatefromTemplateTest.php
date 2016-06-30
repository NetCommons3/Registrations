<?php
/**
 * ActionRegistrationAdd::_createFromTemplate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TemporaryFolder', 'Files.Utility');
App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * ActionRegistrationAdd::_createFromTemplate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\ActionRegistrationAdd
 */
class ActionRegistrationAddCreateFromTemplateTest extends NetCommonsGetTest {

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
		'plugin.registrations.registration_setting',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'ActionRegistrationAdd';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getNewRegistration';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Registrations', 'TestRegistrations');
		$this->TestActionRegistrationAdd = ClassRegistry::init('TestRegistrations.TestActionRegistrationAdd');
		$this->TestActionRegistrationAddSuccess = ClassRegistry::init('TestRegistrations.TestActionRegistrationAddSuccess');

		NetCommonsCakeTestCase::loadTestPlugin($this, 'Registrations', 'TestFiles');

		$this->Registration = ClassRegistry::init('Registrations.Registration');
		$this->Registration->Behaviors->unload('AuthorizationKey');

		Current::$current['Block']['id'] = 2;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TestActionRegistrationAdd);
		unset($this->TestActionRegistrationAddSuccess);
		parent::tearDown();
	}
/**
 * _createFromTemplate()のテスト
 * Successパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplate() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Registrations/Test/Fixture/TemplateTest.zip', $tmpFolder->path . DS . 'TemplateTest.zip');
		$data = array('ActionRegistrationAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'TemplateTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionRegistrationAddSuccess->create();
		$this->TestActionRegistrationAddSuccess->set($data);
		// getNewRegistrationを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionRegistrationAddSuccess->getNewRegistration();
		if (isset($this->TestActionRegistrationAddSuccess->validationErrors['template_file'])) {
			$this->assertTextEquals($this->TestActionRegistrationAddSuccess->validationErrors['template_file'][0], '');
		}
		$this->assertNotNull($result);
		$this->assertTrue(Hash::check($result, 'Registration[import_key=9f1cd3e7ea0cb15c4d6adbe3cabcdb81a20b339a]'));
		for ($i = 0; $i < 10; $i++) {
			$this->assertTrue(Hash::check($result, 'RegistrationPage.' . $i));
			$this->assertTrue(Hash::check($result, 'RegistrationPage.' . $i . '.RegistrationQuestion.0'));
		}
	}
/**
 * _createFromTemplate()のテスト
 * ファイルアップロードなしできたNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG1() {
		$data = array('ActionRegistrationAdd' => array(
			'create_option' => 'template',
			'template_file' => ''
		));
		$this->TestActionRegistrationAdd->create();
		$this->TestActionRegistrationAdd->set($data);
		// getNewRegistrationを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionRegistrationAdd->getNewRegistration();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * ファイルアップロードエラーが発生したNGパターン
 * 実際には存在しないファイルを指定している
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG2() {
		$data = array('ActionRegistrationAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'no_TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => 'no_TemplateTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionRegistrationAdd->create();
		$this->TestActionRegistrationAdd->set($data);
		// getNewRegistrationを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionRegistrationAdd->getNewRegistration();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * Zip形式じゃないZIPファイルが指定されたNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG3() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Registrations/Test/Fixture/emptyErrorTemplateTest.zip', $tmpFolder->path . DS . 'emptyErrorTemplateTest.zip');
		$data = array('ActionRegistrationAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'emptyErrorTemplateTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionRegistrationAdd->create();
		$this->TestActionRegistrationAdd->set($data);
		// getNewRegistrationを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionRegistrationAdd->getNewRegistration();
		$this->assertNull($result);
	}

/**
 * _createFromTemplate()のテスト
 * fingrPrintが違うNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG4() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Registrations/Test/Fixture/fingerPrintErrorTest.zip', $tmpFolder->path . DS . 'fingerPrintErrorTest.zip');
		$data = array('ActionRegistrationAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'fingerPrintErrorTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionRegistrationAdd->create();
		$this->TestActionRegistrationAdd->set($data);
		// getNewRegistrationを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionRegistrationAdd->getNewRegistration();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * versionが違うNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG5() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Registrations/Test/Fixture/versionErrorTest.zip', $tmpFolder->path . DS . 'versionErrorTest.zip');
		$data = array('ActionRegistrationAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'versionErrorTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionRegistrationAdd->create();
		$this->TestActionRegistrationAdd->set($data);
		// getNewRegistrationを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionRegistrationAdd->getNewRegistration();
		$this->assertNull($result);
	}
}
