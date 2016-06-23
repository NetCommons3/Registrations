<?php
/**
 * RegistrationFrameDisplayRegistration::validateFrameDisplayRegistration()のテスト
 *
 * @property RegistrationFrameDisplayRegistration $RegistrationFrameDisplayRegistration
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
 * RegistrationFrameDisplayRegistration::validateFrameDisplayRegistration()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationFrameDisplayRegistration
 */
class RegistrationValidateFrameDisplayRegistrationTest extends NetCommonsModelTestCase {

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
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationFrameDisplayRegistration';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'validateFrameDisplayRegistration';

/**
 * Validatesのテスト
 *
 * @param array $data 登録データ
 * @param string $field フィールド名
 * @param string $value セットする値
 * @param string $message エラーメッセージ
 * @param array $overwrite 上書きするデータ
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidationError($data, $field, $value, $message, $overwrite = array()) {
		$model = $this->_modelName;

		//validate処理実行
		$result = $this->$model->validateFrameDisplayRegistration($data);
		$this->assertFalse($result);

		if ($message) {
			if ($data['RegistrationFrameSetting']['display_type'] == RegistrationsComponent::DISPLAY_TYPE_SINGLE) {
				$this->assertEquals($this->$model->validationErrors[$field][0], $message);
			} else {
				// FUJI 問題のあるレコードIndexを固定的に設定しているのは問題か
				$this->assertEquals($this->$model->validationErrors[2][$field][0], $message);
			}
		}
	}
/**
 * _getData
 *
 * @param int $displayType 表示形式
 * @return array
 */
	protected function _getData($displayType) {
		$data = array(
			'RegistrationFrameSetting' => array(
				'display_type' => $displayType,
				'display_num_per_page' => 10,
				'sort_type' => 0,
			),
			'List' => array(
				'RegistrationFrameDisplayRegistration' => array(
					array('is_display' => 1, 'registration_key' => 'registration_2'),
					array('is_display' => 1, 'registration_key' => 'registration_4'),
					array('is_display' => 1, 'registration_key' => '')
				)
			),
			'Single' => array(
				'RegistrationFrameDisplayRegistration' => array(
					'registration_key' => '',
				)
			)
		);
		return $data;
	}
/**
 * validateFrameDisplayRegistrationのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderValidationError() {
		$errData = Hash::remove($this->_getData(RegistrationsComponent::DISPLAY_TYPE_SINGLE), 'Single.RegistrationFrameDisplayRegistration');

		return array(
			array($this->_getData(RegistrationsComponent::DISPLAY_TYPE_SINGLE), 'registration_key', '',
				__d('net_commons', 'Invalid request.')),
			array($errData, 'registration_key', '', ''),
			array($this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST), 'registration_key', '',
				__d('net_commons', 'Invalid request.')),
		);
	}
}
