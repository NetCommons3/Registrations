<?php
/**
 * RegistrationsController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationsController', 'Registrations.Controller');
App::uses('WorkflowControllerIndexTest', 'Workflow.TestSuite');

/**
 * RegistrationsController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Controller\RegistrationsController
 */
class RegistrationsControllerIndexTest extends WorkflowControllerIndexTest {

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
		'plugin.workflow.workflow_comment',
	);

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'registrations';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'registrations';

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __getData() {
		$frameId = '6';
		$blockId = '2';

		$data = array(
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
			),
		);

		return $data;
	}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Registration = ClassRegistry::init('Registrations.Registration');
		$this->Registration->Behaviors->unload('AuthorizationKey');
		$this->RegistrationFrameSetting = ClassRegistry::init('Registrations.RegistrationFrameSetting');
		$this->RegistrationAnswerSummary = ClassRegistry::init('Registrations.RegistrationAnswerSummary');
	}

/**
 * indexアクションのテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndex() {
		$data = $this->__getData();
		$results = array();
		//ソート、表示件数指定なし
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id']),
			'assert' => array('method' => 'assertNull'),
		);

		return $results;
	}

/**
 * indexアクションのテスト(編集権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByEditable() {
		$data = $this->__getData();
		$results = array();

		//編集権限あり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--追加ボタンチェック
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('controller' => 'registration_add'),
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => true, 'url' => array('controller' => 'registration_add')),
		)));
		//フレームあり(コンテンツなし)テスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null),
			'assert' => array('method' => 'assertContains', 'expected' => __d('registrations', 'no registration'))
		)));
		return $results;
	}

/**
 * indexアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByCreatable() {
		$data = $this->__getData();
		$results = array();

		//作成権限あり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--追加ボタンチェック
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'controller' => 'registration_add'),
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => true, 'url' => array()),
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		)));
		return $results;
	}

/**
 * indexアクションのExceptionErrorテスト用DataProvider
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexExceptionError() {
		$data = $this->__getData();

		return array(
			array(
				'urlOptions' => array('frame_id' => $data['Frame']['id']),
				'assert' => array('method' => 'assertNotEmpty'),
				'exception' => 'InternalErrorException',
			),
		);
	}

}