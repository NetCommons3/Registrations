<?php
/**
 * RegistrationFrameSetting::getRegistrationFrameSetting()のテスト
 *
 * @property RegistrationFrameSetting $RegistrationFrameSetting
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
 * RegistrationFrameSetting::getRegistrationFrameSetting()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationPage
 */
class RegistrationGetRegistrationFrameSettingTest extends NetCommonsGetTest {

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
	protected $_methodName = 'getRegistrationFrameSetting';

/**
 * getRegistrationFrameSetting
 *
 * @param string $frameKey frameKey フレームキー
 * @param int $sort sort type
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetRegistrationFrameSetting($frameKey, $sort, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$condition = array('frame_key' => $frameKey);
		$db = $this->$model->getDataSource();
		$value = $db->value($sort, 'string');
		$field = array('sort_type' => $value);
		$this->$model->updateAll($field, $condition);
		//テスト実行
		$result = $this->$model->$method($frameKey);
		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getRegistrationFrameSettingのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect0 = array(
			RegistrationsComponent::DISPLAY_TYPE_LIST,
			'10',
			'Registration.modified',
			'DESC',
		);
		$expect1 = $expect0;
		//$expect1[2] = 'Registration.modified DESC';
		$expect2 = $expect0;
		$expect2[2] = 'Registration.created';
		$expect2[3] = 'ASC';
		$expect3 = $expect2;
		$expect3[2] = 'Registration.title';
		$expect4 = $expect2;
		$expect4[2] = 'Registration.answer_end_period';
		return array(
			array('frame_3', 'Registration.modified DESC', $expect1),
			array('frame_3', 'Registration.created ASC', $expect2),
			array('frame_3', 'Registration.title ASC', $expect3),
			array('frame_3', 'Registration.answer_end_period ASC', $expect4),
			array('frame_99999', null, $expect0),
		);
	}

}
