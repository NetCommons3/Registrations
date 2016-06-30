<?php
/**
 * RegistrationExport::getExportData()のテスト
 *
 * @property RegistrationExport $RegistrationExport
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
 * RegistrationExport::getExportData()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationExport
 */
class RegistrationExportGetExportDataTest extends NetCommonsGetTest {

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
		'plugin.m17n.language',
		'plugin.registrations.registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_setting',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationExport';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getExportData';

/**
 * getExportData
 *
 * @param string $registrationKey 収集対象の登録フォームキー
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetExportData($registrationKey, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($registrationKey);

		//チェック
		if (is_bool($expected)) {
			$this->assertEquals($result, $expected);
		} else {
			foreach ($expected as $expect) {
				$this->assertTrue(Hash::check($result, $expect), $expect . ' is not found');
			}
		}
	}

/**
 * getExportDataのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			//'version', travis ではここがうまくかない FUJI
			'Registrations.{n}.Registration[language_id=1]',
			'Registrations.{n}.Registration[language_id=2]',
		);
		return array(
			array('registration_2', false),
			array('registration_6', $expect),
		);
	}
}
