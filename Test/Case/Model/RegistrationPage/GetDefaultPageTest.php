<?php
/**
 * RegistrationPage::getDefaultPage()のテスト
 *
 * @property RegistrationPage $RegistrationPage
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
 * RegistrationPage::getDefaultPage()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationPage
 */
class RegistrationGetDefaultPageTest extends NetCommonsGetTest {

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
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationPage';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getDefaultPage';

/**
 * getDefaultPageのテスト
 *
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetDefaultPage($expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method();
		// Questionは省く
		$result = Hash::remove($result, 'RegistrationQuestion');

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getDefaultPageのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			'page_title' => __d('registrations', 'First Page'),
			'route_number' => 0,
			'page_sequence' => 0,
			'key' => '',
		);
		return array(
			array($expect),
		);
	}

}
