<?php
/**
 * RegistrationAnswerSummary::saveAnswerStatus()のテスト
 *
 * @property RegistrationAnswerSummary $RegistrationAnswerSummary
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');
App::uses('RegistrationFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationPageFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationQuestionFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationChoiceFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationAnswerSummaryFixture', 'Registrations.Test/Fixture');

/**
 * RegistrationAnswerSummary::saveAnswerStatus()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationAnswerSummary
 */
class SaveAnswerStatusTest extends NetCommonsModelTestCase {

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
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.site_manager.site_setting',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationAnswerSummary';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveAnswerStatus';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';

		$model = $this->_modelName;
		$mailQueueMock = $this->getMock('MailQueueBehavior',
			['setAddEmbedTagValue', 'afterSave']);
		$mailQueueMock->expects($this->any())
			->method('setAddEmbedTagValue')
			->will($this->returnValue(true));
		$mailQueueMock->expects($this->any())
			->method('afterSave')
			->will($this->returnValue(true));

		// ClassRegistoryを使ってモックを登録。
		// まずremoveObjectしないとaddObjectできないのでremoveObjectする
		ClassRegistry::removeObject('MailQueueBehavior');
		// addObjectでUploadBehaviorでMockが使われる
		ClassRegistry::addObject('MailQueueBehavior', $mailQueueMock);

		// このloadではモックがロードされる
		$this->$model->Behaviors->load('MailQueue');

		// Registrationモデルをモックに
		$registration['Registration'] = (new RegistrationFixture())->records[0];
		$questionFixture = new RegistrationQuestionFixture();
		$registration['RegistrationPage'][0]['RegistrationQuestion'] = $questionFixture->getQuestions(
			1,
			1,
			2,
			1
		);
		$registrationMock = $this->getMockForModel('Registrations.Registration', ['find']);
		$registrationMock->expects($this->any())
			->method('find')
			->will($this->returnValue($registration));
	}

/**
 * テストSummaryDataの取得
 *
 * @param int $id summary id
 * @return array
 */
	private function __getSummary($id) {
		$data = array();
		$fixture = new RegistrationAnswerSummaryFixture();
		$rec = Hash::extract($fixture->records, '{n}[id=' . $id . ']');
		$data['RegistrationAnswerSummary'] = $rec[0];
		return $data;
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @param int $status status
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data, $status) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($data, $status);
		$this->assertNotEmpty($result);

		//idのチェック
		if (isset($data[$this->$model->alias]['id'])) {
			$id = $data[$this->$model->alias]['id'];
		} else {
			$id = $this->$model->getLastInsertID();
		}

		//登録データ取得
		$actual = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $id
			),
		));
		if (Hash::check($data, $this->$model->alias . '.{n}.id')) {
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		} else {
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created_user');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		}
		$actual = $actual[0];

		$data[$this->$model->alias]['answer_status'] = $status;
		$expected = Hash::remove($data, $this->$model->alias . '.created');
		$expected = Hash::remove($expected, $this->$model->alias . '.created_user');
		$expected = Hash::remove($expected, $this->$model->alias . '.modified');
		$expected = Hash::remove($expected, $this->$model->alias . '.modified_user');

		$actual = Hash::remove($actual, $this->$model->alias . '.answer_time');
		$actual = Hash::remove($actual, $this->$model->alias . '.session_value');
		$actual = Hash::remove($actual, $this->$model->alias . '.serial_number');
		$expected = Hash::remove($expected, $this->$model->alias . '.answer_time');

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
			array($this->__getSummary(2), RegistrationsComponent::ACTION_ACT),
		);
	}
/**
 * SaveのValidationErrorテスト
 *
 * @param array $data 登録データ
 * @param int $status status
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnValidationError
 * @return void
 */
	public function testSaveOnValidationError($data, $status, $mockModel, $mockMethod = 'validates') {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);
		$result = $this->$model->$method($data, $status);
		$this->assertFalse($result);
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
		return array(
			array($this->__getSummary(2), RegistrationsComponent::ACTION_ACT, 'Registrations.RegistrationAnswerSummary'),
		);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param array $data 登録データ
 * @param int $status status
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($data, $status, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($data, $status);
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
	public function dataProviderSaveOnExceptionError() {
		return array(
			array($this->__getSummary(2), RegistrationsComponent::ACTION_ACT, 'Registrations.RegistrationAnswerSummary', 'save'),
		);
	}

}
