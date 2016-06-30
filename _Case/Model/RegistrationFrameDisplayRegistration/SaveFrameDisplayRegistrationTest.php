<?php
/**
 * RegistrationFrameDisplayRegistration::saveFrameDisplayRegistration()のテスト
 *
 * @property RegistrationFrameDisplayRegistration $RegistrationFrameDisplayRegistration
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
 * RegistrationFrameDisplayRegistration::saveFrameDisplayRegistration()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationFrameDisplayRegistration
 */
class RegistrationSaveFrameDisplayRegistrationTest extends NetCommonsSaveTest {

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
	protected $_modelName = 'RegistrationFrameDisplayRegistration';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveFrameDisplayRegistration';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
		$this->_mockForAny(
			$this->_modelName,
			'Registrations.Registration',
			'getBaseCondition', array());
	}
/**
 * Mockセット
 *
 * @param string $model モデル名
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @param mixed $return 戻り値
 * @return void
 */
	protected function _mockForAny($model, $mockModel, $mockMethod, $return) {
		list($mockPlugin, $mockModel) = pluginSplit($mockModel);

		if (is_string($mockMethod)) {
			$mockMethod = array($mockMethod);
		}
		$mockClassName = get_class($this->$model->$mockModel);
		if (substr($mockClassName, 0, strlen('Mock_')) !== 'Mock_') {
			$this->$model->$mockModel = $this->getMockForModel(
				$mockPlugin . '.' . $mockModel, $mockMethod, array('plugin' => $mockPlugin)
			);
		}
		foreach ($mockMethod as $method) {
			$this->$model->$mockModel->expects($this->any())
				->method($method)
				->will($this->returnValue($return));
		}
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
				'display_num_per_page' => 10,
				'sort_type' => 'Registration.modified DESC',
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
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//チェック用データ取得
		$before = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array('frame_key' => Current::read('Frame.key')),
		));

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertNotEmpty($result);

		//登録データ取得
		$actual = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array('frame_key' => Current::read('Frame.key')),
			'order' => array('registration_key asc'),
		));
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created_user');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.id');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.frame_key');

		if ($data['RegistrationFrameSetting']['display_type'] == RegistrationsComponent::DISPLAY_TYPE_SINGLE) {
			$expected[0] = Hash::extract($data, 'Single');
		} else {
			$expected = $before;
			foreach ($data['List']['RegistrationFrameDisplayRegistration'] as $value) {
				if ($value['is_display']) {
					$registration = Hash::extract($expected, '{n}.' . $this->$model->alias . '[registration_key=' . $value['registration_key'] . ']');
					if (! $registration) {
						$expected[] = array('RegistrationFrameDisplayRegistration' => array('registration_key' => $value['registration_key']));
					}
				} else {
					$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '[registration_key=' . $value['registration_key'] . ']');
				}
			}
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.created');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.created_user');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.modified');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.modified_user');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.id');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.frame_key');
			$expected = Hash::sort($expected, '{n}.' . $this->$model->alias . '.registration_key');
		}

		$this->assertEquals($expected, $actual);
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
				'Registrations.RegistrationFrameDisplayRegistration',
				'save'),
			array(
				$this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST),
				'Registrations.RegistrationFrameDisplayRegistration',
				'deleteAll'),
			array(
				$this->_getData(RegistrationsComponent::DISPLAY_TYPE_LIST),
				'Frames.Frame',
				'updateAll'),
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
			array($data, 'Registrations.RegistrationFrameDisplayRegistration'),
		);
	}
/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ
 *
 * @return void
 */
	public function dataProviderValidationError() {
		return array(
			array($this->_getData(), 'Single', null,
				__d('net_commons', 'Invalid request.')),
		);
	}

}
