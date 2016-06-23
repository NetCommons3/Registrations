<?php
/**
 * RegistrationFrameSetting::validate()のテスト
 *
 * @property RegistrationFrameSetting $RegistrationFrameSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationFrameSetting::validate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationFrameSetting
 */
class ValidateRegistrationFrameSettingTest extends NetCommonsValidateTest {

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
	protected $_modelName = 'RegistrationFrameSetting';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'validateFrameDisplayRegistration';

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
		return array(
			array($this->_getData(RegistrationsComponent::DISPLAY_TYPE_SINGLE), 'display_type', '',
				__d('net_commons', 'Invalid request.')),
			array($this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST), 'display_type', '',
				__d('net_commons', 'Invalid request.')),
			array($this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST), 'display_num_per_page', 'a999999',
				__d('net_commons', 'Invalid request.')),
			array($this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST), 'sort_type', 12,
				__d('net_commons', 'Invalid request.')),
		);
	}
}
