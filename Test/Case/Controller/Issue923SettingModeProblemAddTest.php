<?php
/**
 * Issue923SettingModeProblemAddTest
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * Issue923SettingModeProblemAddTest
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Pages\Test\Case\Controller\PagesEditController
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Issue923SettingModeProblemAddTest extends NetCommonsControllerTestCase {

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
	protected $_controller = 'registration_add';

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
		//'plugin.files.upload_file',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//$this->Registration = ClassRegistry::init('Registrations.Registration');
		//$this->Registration->Behaviors->unload('AuthorizationKey');

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
 * add()アクションテスト
 *
 * @return void
 */
	public function testAdd() {
		$data = $this->__getData();
		//セッティングモードでないときは、メール設定タブは非表示

		$this->_controller = 'registration_add';

		$url = [
			'controller' => 'registration_add',
			'action' => 'add',
			'block_id' => $data['Block']['id'],
			'frame_id' => $data['Frame']['id'],
			//'key' => 'registration_2',
		];

		$this->_testGetAction($url,
			[
				'method' => 'assertNotContains', 'expected' => __d('blocks', 'Block settings')
			], null, 'view');

		//セッティングモードONならブロック設定タブが表示される
		$url['q_mode'] = 'setting';
		$this->_testGetAction($url,
			[
				'method' => 'assertContains', 'expected' => __d('blocks', 'Block settings')
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
			'ActionRegistrationAdd' => array(
				'create_option' => 'create',
				'title' => 'New Registration Title',
			),

			//'WorkflowComment' => array(
			//	'comment' => 'WorkflowComment save test'
			//),
		);
		return $data;
	}

}