<?php
/**
 * RegistrationAddController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationAddController', 'Registrations.Controller');
App::uses('WorkflowControllerAddTest', 'Workflow.TestSuite');

/**
 * FaqQuestionsController Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller\FaqQuestionsController
 */
class RegistrationAddControllerAddTest extends WorkflowControllerAddTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.registrations.registration',
		'plugin.registrations.block_setting_for_registration',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
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
	protected $_controller = 'registration_add';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		//ログアウト Web UI でテスト中にログインしてるとテストがログイン済みとして実行されるようなので
		TestAuthGeneral::logout($this);

		parent::setUp();
		$this->Registration = ClassRegistry::init('Registrations.Registration');
		$this->Registration->Behaviors->unload('AuthorizationKey');
		$this->ActionRegistrationAdd = ClassRegistry::init('Registrations.ActionRegistrationAdd');
	}

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __getData() {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			//'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
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
		);

		return $data;
	}
/**
 * テストDataの取得
 *
 * @return array
 */
	private function __getDataPastReuse() {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			//'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
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
				'create_option' => 'reuse',
				'past_registration_id' => '32',
			),
		);

		return $data;
	}

/**
 * addアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGet() {
		$data = $this->__getData();
		$results = array();

		//ログインなし
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		return $results;
	}

/**
 * addアクションのGETテスト(作成権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGetByCreatable() {
		$data = $this->__getData();
		$results = array();

		//作成権限あり
		$base = 0;
		// 正しいフレームIDとブロックID
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// フレームIDのhidden-inputがあるか
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		)));
		// ブロックIDのhidden-inputがあるか
		//array_push($results, Hash::merge($results[$base], array(
		//	'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		//)));
		// 作成方法選択肢オプションがあるか
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[ActionRegistrationAdd][create_option]', 'value' => null),
		)));
		// タイトル入力テキストがあるか
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'text', 'name' => 'data[ActionRegistrationAdd][title]', 'value' => null),
		)));
		// 過去再利用の絞込テキスト入力とhiddenがあることを確認する
		// 本当は過去の登録フォーム一覧が表示されることも確認せねばならないが、それはAngularで展開しているのでphpunitでは確認できないため省略
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'text', 'name' => 'data[ActionRegistrationAdd][past_search]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[ActionRegistrationAdd][past_registration_id]', 'value' => null),
		)));
		// テンプレートファイル読み込みがあるか
		//array_push($results, Hash::merge($results[$base], array(
		//	'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[ActionRegistrationAdd][template_file]', 'value' => null),
		//)));

		//フレームID指定なしテスト （ありえないはず。 by RyujiAMANO）
		//array_push($results, Hash::merge($results[$base], array(
		//	'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
		//	'assert' => array('method' => 'assertNotEmpty'),
		//)));
		//array_push($results, Hash::merge($results[$base], array(
		//	'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
		//	'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => null),
		//)));

		return $results;
	}

/**
 * addアクションのPOSTテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddPost() {
		$data = $this->__getData();

		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
				'exception' => 'ForbiddenException'
			),
			//作成権限あり
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			),
			array(
				'data' => $this->__getDataPastReuse(), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			),
			//フレームID指定なしテスト
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			),
		);
	}

/**
 * addアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderAddValidationError() {
		$data = $this->__getData();
		$result = array(
			'data' => $data,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
		);
		$dataPastReuse = $this->__getDataPastReuse();
		$resultPastReuse = array(
			'data' => $dataPastReuse,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
		);
		//$dataTemplate = $this->__getData();
		//$dataTemplate['ActionRegistrationAdd']['create_option'] = 'template';
		//$resultTemplate = array(
		//	'data' => $dataTemplate,
		//	'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
		//);

		return array(
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'ActionRegistrationAdd.create_option',
					'value' => null,
					'message' => sprintf(__d('registrations', 'Please choose create option.'))
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'ActionRegistrationAdd.title',
					'value' => '',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('registrations', 'Title'))
				)
			)),
			Hash::merge($resultPastReuse, array(
				'validationError' => array(
					'field' => 'ActionRegistrationAdd.past_registration_id',
					'value' => '',
					'message' => sprintf(__d('registrations', 'Please select past registration.'))
				)
			)),
			Hash::merge($resultPastReuse, array(
				'validationError' => array(
					'field' => 'ActionRegistrationAdd.past_registration_id',
					'value' => '9999999',
					'message' => sprintf(__d('registrations', 'Please select past registration.'))
				)
			)),
			//Hash::merge($resultTemplate, array(
			//	'validationError' => array(
			//		'field' => 'ActionRegistrationAdd.template_file',
			//		'value' => null,
			//		'message' => sprintf(__d('registrations', 'file upload error.'))
			//	)
			//)),
		);
	}
}
