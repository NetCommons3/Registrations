<?php
/**
 * ActionRegistrationAdd::_createFromReuse()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * ActionRegistrationAdd::_createFromReuse()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\ActionRegistrationAdd
 */
class ActionRegistrationAddCreateFromReuseTest extends NetCommonsGetTest {

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
		$this->Registration = ClassRegistry::init('Registrations.Registration');
		$this->Registration->Behaviors->unload('AuthorizationKey');
		Current::$current['Block']['id'] = 2;
	}

/**
 * _createFromReuse()のテスト
 *
 * @param array $data POSTデータ
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testCreateFromReuse($data) {
		$this->ActionRegistrationAdd->create();
		$this->ActionRegistrationAdd->set($data);
		// getNewRegistrationを呼ぶことで_createNewが呼ばれる仕組み
		$result = $this->ActionRegistrationAdd->getNewRegistration();
		$this->assertTrue(Hash::check($result, 'Registration[title=registration_4]'));
		for ($i = 0; $i < 7; $i++) {
			$this->assertTrue(Hash::check($result, 'RegistrationPage.' . $i));
			$this->assertTrue(Hash::check($result, 'RegistrationPage.' . $i . '.RegistrationQuestion.0'));
		}
	}
/**
 * testCreateFromReuseのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		return array(
			array(
				array('ActionRegistrationAdd' => array(
					'create_option' => 'reuse',
					'past_registration_id' => '4'
				)),
			),
		);
	}
}
