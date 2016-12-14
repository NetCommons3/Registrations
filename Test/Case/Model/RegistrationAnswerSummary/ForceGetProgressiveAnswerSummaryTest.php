<?php
/**
 * RegistrationAnswerSummary::forceGetProgressiveAnswerSummary()のテスト
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
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

/**
 * RegistrationAnswerSummary::forceGetProgressiveAnswerSummary()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationAnswerSummary
 */
class ForceGetProgressiveAnswerSummaryTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'forceGetProgressiveAnswerSummary';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * getのテスト
 *
 * @param int $registration 登録フォームデータ
 * @param int $userId user id
 * @param string $sessionId session id
 * @param mix $expected
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($registration, $userId, $sessionId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($registration, $userId, $sessionId);

		$this->assertEquals($expected['answer_status'], $result[$this->$model->alias]['answer_status']);
		$this->assertEquals($expected['test_status'], $result[$this->$model->alias]['test_status']);
		$this->assertEquals($expected['answer_number'], $result[$this->$model->alias]['answer_number']);
		$this->assertEquals($expected['answer_time'], $result[$this->$model->alias]['answer_time']);
		$this->assertEquals($registration['Registration']['key'], $result[$this->$model->alias]['registration_key']);
		$this->assertEquals($userId, $result[$this->$model->alias]['user_id']);
	}

/**
 * getのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		$registration = array(
			'Registration' => array(
				'key' => 'registration_4',
				'status' => WorkflowComponent::STATUS_PUBLISHED
			)
		);
		$registrationDraft = array(
			'Registration' => array(
				'key' => 'registration_4',
				'status' => WorkflowComponent::STATUS_IN_DRAFT
			)
		);
		$registrationMulti = array(
			'Registration' => array(
				'key' => 'registration_12',
				'status' => WorkflowComponent::STATUS_PUBLISHED
			)
		);
		return array(
			array($registration, 4, '', array(
				'answer_status' => RegistrationsComponent::ACTION_NOT_ACT,
				'test_status' => RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => 1,
				'answer_time' => ''
			)),
			array($registrationDraft, 4, '', array(
				'answer_status' => RegistrationsComponent::ACTION_NOT_ACT,
				'test_status' => RegistrationsComponent::TEST_ANSWER_STATUS_TEST,
				'answer_number' => 1,
				'answer_time' => ''
			)),
			array($registrationMulti, 3, '', array(
				'answer_status' => RegistrationsComponent::ACTION_NOT_ACT,
				'test_status' => RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => 2,
				'answer_time' => ''
			)),
		);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param int $registration 登録フォームデータ
 * @param int $userId user id
 * @param string $sessionId session id
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($registration, $userId, $sessionId, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($registration, $userId, $sessionId);
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
		$registration = array(
			'Registration' => array(
				'key' => 'registration_4',
				'status' => WorkflowComponent::STATUS_PUBLISHED
			)
		);
		return array(
			array($registration, 4, '', 'Registrations.RegistrationAnswerSummary', 'save'),
		);
	}

}
