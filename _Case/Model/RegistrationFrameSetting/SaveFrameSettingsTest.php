<?php
/**
 * RegistrationFrameSetting::saveFrameSetting()のテスト
 *
 * @property RegistrationFrameSetting $RegistrationFrameSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationFrameSetting::saveFrameSetting()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationFrameSetting
 */
class SaveFrameSettingTest extends NetCommonsSaveTest {

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
		'plugin.authorization_keys.authorization_keys'
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
	protected $_methodName = 'saveFrameSettings';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
		$mock = $this->getMockForModel('Registrations.RegistrationFrameDisplayRegistration', array('validateFrameDisplayRegistration'));
		$mock->expects($this->any())
			->method('validateFrameDisplayRegistration')
			->will($this->returnValue(true));
	}

/**
 * テストDataの取得
 *
 * @param int $displayType display type
 * @return array
 */
	protected function _getData($displayType = RegistrationsComponent::DISPLAY_TYPE_SINGLE) {
		$data = array(
			'RegistrationFrameSetting' => array(
				'display_type' => $displayType,
				'display_num_per_page' => '10',
				'sort_type' => 'Registration.modified DESC',
				'frame_key' => 'frame_3',
			),
			'List' => array(
				'RegistrationFrameDisplayRegistration' => array(
					array('is_display' => '0', 'registration_key' => 'registration_2'),
					array('is_display' => '1', 'registration_key' => 'registration_4'),
					array('is_display' => '1', 'registration_key' => 'registration_6')
				)
			),
			'Single' => array(
				'RegistrationFrameDisplayRegistration' => array(
					'registration_key' => 'registration_2',
				)
			)
		);
		return $data;
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		return array(
			array($this->_getData(RegistrationsComponent::DISPLAY_TYPE_SINGLE)),
			array($this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST)),
		);
	}

/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderSaveOnExceptionError() {
		return array(
			array(
				$this->_getData(RegistrationsComponent::DISPLAY_TYPE_SINGLE),
				'Registrations.RegistrationFrameSetting',
				'save'),
		);
	}
/**
 * SaveのValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *
 * @return void
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->_getData(RegistrationsComponent::DISPLAY_TYPE_SINGLE);
		return array(
			array($data, 'Registrations.RegistrationFrameSetting'),
		);
	}

/**
 * RegistrationFrameDisplayRegistrationのExceptionErrorテスト
 *
 * @return void
 */
	public function testRegistrationFrameDisplayRegistrationValidationError() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->$model->Registration = $this->getMockForModel(
			'Registrations.Registration', array('find'));
		$this->$model->Registration->expects($this->any())
			->method('find')
			->will($this->returnValue(10));

		$this->$model->RegistrationFrameDisplayRegistration = $this->getMockForModel(
			'Registrations.RegistrationFrameDisplayRegistration',
			array(
				'validateFrameDisplayRegistration',
			));
		$this->$model->RegistrationFrameDisplayRegistration->expects($this->once())
			->method('validateFrameDisplayRegistration')
			->will($this->returnValue(false));

		$data = $this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST);
		$data['List']['RegistrationFrameDisplayRegistration'] = array();
		$result = $this->$model->$method($data);
		$this->assertFalse($result);
	}

/**
 * RegistrationFrameDisplayRegistrationのExceptionErrorテスト
 *
 * @return void
 */
	public function testRegistrationFrameDisplayRegistrationSaveError() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->$model->Registration = $this->getMockForModel(
			'Registrations.Registration', array('find'));
		$this->$model->Registration->expects($this->any())
			->method('find')
			->will($this->returnValue(10));

		$this->$model->RegistrationFrameDisplayRegistration = $this->getMockForModel(
			'Registrations.RegistrationFrameDisplayRegistration',
			array(
				'validateFrameDisplayRegistration',
				'saveFrameDisplayRegistration'
			));
		$this->$model->RegistrationFrameDisplayRegistration->expects($this->once())
			->method('validateFrameDisplayRegistration')
			->will($this->returnValue(true));
		$this->$model->RegistrationFrameDisplayRegistration->expects($this->once())
			->method('saveFrameDisplayRegistration')
			->will($this->returnValue(false));

		$this->setExpectedException('InternalErrorException');

		$data = $this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST);
		$data['List']['RegistrationFrameDisplayRegistration'] = array();
		$this->$model->$method($data);
	}
}
