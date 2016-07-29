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
		'plugin.registrations.registration_setting',
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
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// ログイン無しでテストモードは０になる　　画面には"no registration"のテキストが現れていること
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'answer_status' => 'test'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('registrations', 'no registration')),
		);
		// ログイン無しで未登録は全て出てくる　画面には何らかの表示が現れていること
		$results[2] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'answer_status' => 'unanswered'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// ログイン無しで登録済は０になる　画面には"no registration"のテキストが現れていること
		$results[3] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'answer_status' => 'answered'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('registrations', 'no registration')),
		);
		// 表示件数を増やすことでFixtureデータをすべて表示させる
		// そのうえで試験を行う
		// 未ログインでも公開中なら見える
		$results[4] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_2', 'limit' => null)),
		);
		// 未ログインの場合は一時保存が見えない
		$results[5] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_42', 'limit' => null)),
		);
		// 未ログインの場合は未来公開が見えない
		$results[6] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_18', 'limit' => null)),
		);
		// 未ログインの場合は過去公開は見えない。。。Workflowの共通処理に任せることになるので編集権限なければ出ない
		$results[7] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_24', 'limit' => null)),
		);

		//チェック
		//--追加ボタンチェック(なし)
		$results[8] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => false, 'url' => array('controller' => 'registration_add')),
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
		//記事なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '6'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('registrations', 'no registration'))
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
		)));
		// 公開後に一時保存変更してる見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_2', 'limit' => null))
		)));
		// 公開中なら見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_6', 'limit' => null))
		)));
		// 一時保存も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_42', 'limit' => null))
		)));
		// 未来も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_18', 'limit' => null))
		)));
		// 過去も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_24', 'limit' => null))
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
		// 公開中なら見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_2', 'limit' => null))
		)));
		// 自分のなら承認待ちも見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_28', 'limit' => null))
		)));
		// 自分のなら差し戻しも見える #5
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_10', 'limit' => null))
		)));
		// 自分のなら一時保存も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_38', 'limit' => null))
		)));
		// 自分のなら未来も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_18', 'limit' => null))
		)));
		// 自分のなら過去も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_24', 'limit' => null))
		)));

		// 他人の承認待ちも見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_26', 'limit' => null))
		)));
		// 他人の差し戻しも見えない #10
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_30', 'limit' => null))
		)));
		// 他人の一時保存も見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_36', 'limit' => null))
		)));
		// 他人の未来 集計表示ありだが未来なので見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_16', 'limit' => null))
		)));
		// 他人の過去 発行済みで集計表示ありなので見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'registration_answers', 'key' => 'registration_22', 'limit' => null))
		)));

		return $results;
	}

/**
 * indexアクションのExceptionErrorテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexExceptionError
 * @return void
 * @throws InternalErrorException
 */
	public function testIndexExceptionError($urlOptions, $assert, $exception = null, $return = 'view') {
		$this->generate(
			'Registrations.Registrations',
			[
				'components' => [
					'Paginator'
				]
			]
		);

		//Exception
		$this->controller->Components->Paginator
			->expects($this->once())
			->method('paginate')
			->will($this->returnCallback(function () {
				throw new InternalErrorException();
			}));

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);
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