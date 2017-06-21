<?php

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * PagesEditController::add()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\PagesEditController
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Issue923SettingModeProblemEditTest extends NetCommonsControllerTestCase {

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'registrations';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'registration_edit';

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
		'plugin.authorization_keys.authorization_keys',
		'plugin.workflow.workflow_comment',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		TestAuthGeneral::login($this);

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Registrations', 'TestRegistrations');
		Current::isSettingMode(false);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * edit_quuestion()アクションテスト
 *
 * @return void
 */
	public function testEditQuestion() {
		$data = $this->__getData();
		//セッティングモードでないときは、メール設定タブは非表示

		$url = [
			'controller' => 'registration_edit',
			'action' => 'edit_question',
			'block_id' => $data['Block']['id'],
			'frame_id' => $data['Frame']['id'],
			'key' => 'registration_2',
		];
		//Current::isSettingMode(false);
		$this->_testGetAction($url,
			[
				'method' => 'assertNotContains', 'expected' => '/registrations/registration_mail_settings/'
			], null, 'view');

		//セッティングモードONならメール設定タブが表示される
		//Current::isSettingMode(true);
		$url['q_mode'] = 'setting';
		$this->_testGetAction($url,
			[
				'method' => 'assertContains', 'expected' => '/registrations/registration_mail_settings/'
			], null, 'view');
	}

/**
 * edit_quuestion()アクションテスト
 *
 * @return void
 */
	public function testEdit() {
		$data = $this->__getData();
		//セッティングモードでないときは、メール設定タブは非表示

		$url = [
			'controller' => 'registration_edit',
			'action' => 'edit',
			'block_id' => $data['Block']['id'],
			'frame_id' => $data['Frame']['id'],
			'key' => 'registration_2',
		];
		//Current::isSettingMode(false);
		$this->_testGetAction($url,
			[
				'method' => 'assertNotContains', 'expected' => '/registrations/registration_mail_settings/'
			], null, 'view');

		//セッティングモードONならメール設定タブが表示される
		//Current::isSettingMode(true);
		$url['q_mode'] = 'setting';
		$this->_testGetAction($url,
			[
				'method' => 'assertContains', 'expected' => '/registrations/registration_mail_settings/'
			], null, 'view');
	}

/**
 * テストDataの取得
 *
 * @param string $registrationKey キー
 * @return array
 */
	private function __getData($registrationKey = null) {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '2',
				'plugin_key' => $this->plugin,
			),
			'Registration' => array(
				'key' => $registrationKey,
				'status' => WorkflowComponent::STATUS_IN_DRAFT,
				'title' => 'EditTestTitle',
				'title_icon' => 'ok.svg',
				'sub_title' => 'EditTestSubTitle',
				'is_total_show' => 0,
				'answer_timing' => '0',
				'is_key_pass_use' => 0,
				'total_show_timing' => 0,
			),
			'RegistrationPage' => array(
				array(
					'page_title' => __d('registrations', 'First Page'),
					'page_sequence' => 0,
					'route_number' => 0,
					'RegistrationQuestion' => array(
						array(
							'question_sequence' => 0,
							'question_value' => __d('registrations', 'New Question') . '1',
							'question_type' => RegistrationsComponent::TYPE_SELECTION,
							'is_require' => RegistrationsComponent::USES_NOT_USE,
							'is_skip' => RegistrationsComponent::SKIP_FLAGS_NO_SKIP,
							'is_choice_random' => RegistrationsComponent::USES_NOT_USE,
							'is_range' => RegistrationsComponent::USES_NOT_USE,
							'is_result_display' => RegistrationsComponent::EXPRESSION_SHOW,
							'result_display_type' => RegistrationsComponent::RESULT_DISPLAY_TYPE_BAR_CHART,
							'RegistrationChoice' => array(
								array(
									'choice_sequence' => 0,
									'matrix_type' => RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
									'choice_label' => __d('registrations', 'new choice') . '1',
									'other_choice_type' => RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
									'graph_color' => '#FF0000',
									'skip_page_sequence' => RegistrationsComponent::SKIP_GO_TO_END
								)
							)
						)
					)
				)
			),
			//'WorkflowComment' => array(
			//	'comment' => 'WorkflowComment save test'
			//),
		);
		return $data;
	}

}