<?php
/**
 * ActionRegistrationAdd::checkPastRegistration()のテスト
 *
 * @property ActionRegistrationAdd $ActionRegistrationAdd
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
 * ActionRegistrationAdd::checkPastRegistration()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\ActionRegistrationAdd
 */
class CheckPastRegistrationTest extends NetCommonsGetTest {

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
	protected $_methodName = 'checkPastRegistration';

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
 * testCheckPastRegistration
 *
 * @param array $data POSTデータ
 * @param array $check チェックデータ
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testCheckPastRegistration($data, $check, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->$model->create();
		$this->$model->set($data);
		//テスト実行
		$result = $this->$model->$method($check);
		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * testCheckPastRegistrationのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		//$expect = $this->_getRegistration(4);
		return array(
			array(
				array('ActionRegistrationAdd' => array(
					'create_option' => 'aaaa'
				)),
				array('past_registration_id' => 'aaa'),
				true
			),
			array(
				array('ActionRegistrationAdd' => array(
					'create_option' => 'reuse',
					'past_registration_id' => 'aaa'
				)),
				array('past_registration_id' => 'aaa'),
				false
			),
			array(
				array('ActionRegistrationAdd' => array(
					'create_option' => 'reuse',
					'past_registration_id' => '1'
				)),
				array('past_registration_id' => '1'),
				false
			),
			array(
				array('ActionRegistrationAdd' => array(
					'create_option' => 'reuse',
					'past_registration_id' => '4'
				)),
				array('past_registration_id' => '4'),
				false
			),
			array(
				array('ActionRegistrationAdd' => array(
					'create_option' => 'reuse',
					'past_registration_id' => '6'
				)),
				array('past_registration_id' => '6'),
				true
			),
		);
	}
}
