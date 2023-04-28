<?php
/**
 * RegistrationAnswerController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * RegistrationAnswerController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Controller\RegistrationAsnwerController
 */
class RegistrationAnswerControllerViewTest extends WorkflowControllerViewTest {

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
		'plugin.registrations.block4registrations',
		'plugin.registrations.blocks_language4registrations',
		'plugin.registrations.frame4registrations',
		'plugin.registrations.frame_public_language4registrations',
		'plugin.registrations.frames_language4registrations',
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
	protected $_controller = 'registration_answers';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Registration = ClassRegistry::init('Registrations.Registration');
		$this->Registration->Behaviors->unload('AuthorizationKey');
		$this->controller->Session->expects($this->any())
			->method('check')
			->will(
				$this->returnValueMap([
					['Registration.auth_ok.registration_10', true]
			]));
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderView() {
		$results = array();

		//ログインなし
		//--コンテンツあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'registration_2'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		);
		$results[1] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'linkExist' => false, 'action' => 'edit', 'url' => array('controller' => 'registration_edit')),
		));
		//$results[2] = Hash::merge($results[0], array( // 存在しない
		//	'urlOptions' => array('key' => 'registration_999', 'block_id' => 1000),
		//	'assert' => null,
		//	'exception' => 'BadRequestException',
		//));
		$results[3] = Hash::merge($results[0], array( // 未公開
			'urlOptions' => array('key' => 'registration_36', 'block_id' => 38),
			'assert' => array('method' => 'assertEmpty'),
			//'exception' => 'BadRequestException',
		));
		$results[4] = Hash::merge($results[0], array( // 非会員NG
			'urlOptions' => array('key' => 'registration_6', 'block_id' => 11),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to answer this registration.')),
		));
		$results[5] = Hash::merge($results[0], array( // 未来
			'urlOptions' => array('key' => 'registration_14', 'block_id' => 20),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to answer this registration.')),
		));
		$results[6] = Hash::merge($results[0], array( // 過去
			'urlOptions' => array('key' => 'registration_20', 'block_id' => 26),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to answer this registration.')),
		));

		// test mode 画面へ
		$results[7] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'test_mode', 'key' => 'registration_2'),
			'assert' => array('method' => 'assertTextNotContains', 'expected' => __d('registrations', 'Test Mode')),
		);
		// thanks画面 登録が終わっていない画面は見られない
		$results[8] = Hash::merge($results[0], array( // 未公開
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'thanks', 'key' => 'registration_2'),
			'assert' => array('method' => 'assertNotEmpty'),
			'expected' => 'BadRequestException',
			'return' => 'json'
		));
		// 認証キー 画面へ行こうとしてはじかれる
		$results[9] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'key_auth', 'key' => 'registration_2'),
			'assert' => array('method' => 'assertTextNotContains', 'expected' => '/registrations/registration_answers/key_auth/'),
		);
		// 画像認証 画面へ行こうとしてはじかれる
		$results[10] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'img_auth', 'key' => 'registration_2'),
			'assert' => array('method' => 'assertTextNotContains', 'expected' => '/registrations/registration_answers/img_auth/'),
		);
		// 繰り返しなしのテストは非会員では厳しいので省略
		return $results;
	}

/**
 * viewアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByCreatable() {
		$results = array();
		//作成権限のみ(一般が書いた記事＆一度公開している)
		$results[0] = array(
			'urlOptions' => array('frame_id' => '24', 'block_id' => '16', 'key' =>
				'registration_10'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		);
		// 自分が書いた＆未公開
		$results[1] = Hash::merge($results[0], array(
			'urlOptions' => array('block_id' => '2', 'frame_id' => '48', 'key' =>
				'registration_38'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 人が書いた＆未公開
		$results[2] = Hash::merge($results[0], array( // 未公開
			'urlOptions' => array('block_id' => '38', 'frame_id' => '46', 'key' =>
				'registration_36'),
			'assert' => array('method' => 'assertEmpty'),
			//'expected' => 'BadRequestException',
		));
		// 非会員NG みれる
		$results[4] = Hash::merge($results[0], array( // 非会員NG
			'urlOptions' => array('block_id' => '11', 'frame_id' => '19', 'key' =>
				'registration_6'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 人が書いた未来
		$results[5] = Hash::merge($results[0], array( // 未来
			'urlOptions' => array('block_id' => '20', 'frame_id' => '28', 'key' =>
				'registration_14'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to answer this registration.')),
		));
		// 自分が書いた未来
		$results[6] = Hash::merge($results[0], array( // 未来
			'urlOptions' => array('block_id' => '24', 'frame_id' => '32', 'key' =>
				'registration_18'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 繰り返し登録NGで未登録
		$results[7] = Hash::merge($results[0], array(
			'urlOptions' => array('block_id' => '18', 'frame_id' => '26', 'key' =>
				'registration_12'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 登録してないのに確認画面は見られない
		$results[8] = Hash::merge($results[0], array(
			'urlOptions' => array('block_id' => '18', 'frame_id' => '26', 'action' => 'confirm', 'key' => 'registration_12'),
			'assert' => array('method' => 'assertNotEmpty'),
			'expected' => 'BadRequestException',
			'return' => 'json'
		));
		// 人が書いた過去 省略
		// 自分が書いた過去 省略
		return $results;
	}

/**
 * viewアクションのテスト(編集権限、公開権限なし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByEditable() {
		$results = array();

		//編集権限あり（chef_userが書いた記事一度も公開していない）
		//--コンテンツあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => '58', 'block_id' => '50', 'key' =>
				'registration_48'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 繰り返しNGで登録ずみ
		$results[1] = Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => '26', 'block_id' => '18', 'key' =>
				'registration_12'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to answer this registration.')),
		));

		$results[2] = Hash::merge($results[0], array(	//画像認証
			'urlOptions' => array('frame_id' => '20', 'block_id' => '12', 'key' =>
				'registration_8'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 登録が終わっている登録フォームは見られる
		//$results[3] = array(
		//	'urlOptions' => array('frame_id' => '26', 'block_id' => '18', 'action' => 'thanks', 'key' => 'registration_12'),
		//	'assert' => array(
		//		'method' => 'assertActionLink',
		//		'linkExist' => true,
		//		'action' => 'view', 'url' => array('frame_id' => '26', 'block_id' => '18', 'controller' => 'registration_answer_summaries', 'key' => 'registration_12')),
		//);
		return $results;
	}

/**
 * viewアクション(編集ボタンの確認)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewGetByPublishable
 * @return void
 */
	public function testEditGetByPublishable($urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		$this->testView($urlOptions, $assert, $exception, $return);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * viewアクション　編集長は何でも見ることができるので
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewGetByPublishable() {
		//公開中の記事
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'registration_2'),
			'assert' => null
		);
		$results[1] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'test_mode', 'key' => 'registration_2'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'This registration is being temporarily stored . You can registration test before performed in this page . If you want to modify or change the registration , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner .')),
		);
		// 確認前までの状態になっていたらconfirm登録フォームは見られる
		$results[2] = array(
			'urlOptions' => array('frame_id' => '26', 'block_id' => '18', 'action' => 'confirm', 'key' => 'registration_12'),
			'assert' => array('method' => 'assertInput', 'type' => 'submit', 'name' => 'confirm_registration', 'value' => null),
		);
		// shuffl
		//$results[3] = array(
		//	'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'registration_4'),
		//	'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'This registration is being temporarily stored . You can registration test before performed in this page . If you want to modify or change the registration , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner .')),
		//);
		return $results;
	}

/**
 * viewアクション　シャッフルされた選択肢を取り出すためだけの試験
 *
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	//public function testGetShuffle() {
	//	$controller = $this->generate('Registrations.RegistrationAnswers', array(
	//		'components' => array(
	//			'Auth' => array('user'),
	//			'Session',
	//			'Security',
	//			'NetCommons.Permission',
	//			'Registrations.Registrations',
	//			'Registrations.RegistrationsOwnAnswer',
	//			'AuthorizationKeys.AuthorizationKey',
	//			'VisualCaptcha.VisualCaptcha'
	//		)
	//	));
	//	//テスト実施
	//	$controller->Session->expects($this->any())
	//		->method('check')
	//		->will($this->returnValue(true));
	//
	//	$url = array(
	//		'plugin' => $this->plugin,
	//		'controller' => $this->_controller,
	//		'action' => 'view',
	//		'frame_id' => 6,
	//		'block_id' => 2,
	//		'key' => 'registration_4'
	//	);
	//	$assert = array('method' => 'assertTextContains', 'expected' => __d('registrations', 'This registration is being temporarily stored . You can registration test before performed in this page . If you want to modify or change the registration , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner .'));
	//
	//	//ログイン
	//	TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);
	//	$this->_testGetAction($url, $assert, null, 'view');
	//	//ログアウト
	//	TestAuthGeneral::logout($this);
	//}

}
