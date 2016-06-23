<?php
/**
 * RegistrationAnswerSummariesController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * RegistrationAnswerSummariesController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Controller\RegistrationAnswerSummariesController
 */
class RegistrationAnswerSummariesControllerViewTest extends WorkflowControllerViewTest {

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
		'plugin.registrations.registration_answer',
		'plugin.authorization_keys.authorization_keys',
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
	protected $_controller = 'registration_answer_summaries';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
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
		// 登録してないから見られない
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'registration_12'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to see this result.')),
		);
		$results[] = Hash::merge($results[0], array( // 存在しない
			'urlOptions' => array('key' => 'registration_999'),
			'assert' => null,
			'exception' => 'BadRequestException',
		));
		/*
		$results[1] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'linkExist' => false, 'action' => 'edit', 'url' => array('controller' => 'registration_edit')),
		));
		$results[3] = Hash::merge($results[0], array( // 未公開
			'urlOptions' => array('key' => 'registration_36'),
			'assert' => null,
			'exception' => 'BadRequestException',
		));
		$results[4] = Hash::merge($results[0], array( // 非会員NG
			'urlOptions' => array('key' => 'registration_6'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to answer this registration.')),
		));
		$results[5] = Hash::merge($results[0], array( // 未来
			'urlOptions' => array('key' => 'registration_14'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to answer this registration.')),
		));
		$results[6] = Hash::merge($results[0], array( // 過去
			'urlOptions' => array('key' => 'registration_20'),
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
		*/
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
		//作った本人
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'registration_12'),
			'assert' => array('method' => 'assertTextContains', 'expected' => 'nvd3'),
		);
		$results[] = Hash::merge($results[0], array( // 集計表示を許してない
			'urlOptions' => array('key' => 'registration_6'),
			'assert' => null,
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to see this result.')),
		));
		$results[] = Hash::merge($results[0], array( // 集計表示期間外
			'urlOptions' => array('key' => 'registration_8'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to see this result.')),
		));
		$results[] = Hash::merge($results[0], array( // 登録してない
			'urlOptions' => array('key' => 'registration_2'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('registrations', 'you will not be able to see this result.')),
		));
		/*
		// 自分が書いた＆未公開
		$results[1] = Hash::merge($results[0], array(
			'urlOptions' => array('key' => 'registration_38'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 人が書いた＆未公開
		$results[2] = Hash::merge($results[0], array( // 未公開
			'urlOptions' => array('key' => 'registration_36'),
			'assert' => null,
			'expected' => 'BadRequestException',
		));
		*/
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

		//編集権限あり 他人が書いたコンテンツ
		//--コンテンツあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'registration_12'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
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
		//公開中の記事　登録もないけどみれてしまう
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'registration_2'),
			'assert' => null
		);
		$results[] = Hash::merge($results[0], array( // 様々な種別
			'urlOptions' => array('key' => 'registration_4'),
			'assert' => array('method' => 'assertTextContains', 'expected' => 'nvd3'),
		));
		$results[] = Hash::merge($results[0], array( // リストー表形式
			'urlOptions' => array('key' => 'registration_20'),
			'assert' => array('method' => 'assertTextContains', 'expected' => 'table table-striped table-bordered registration-table-vcenter table-responsive'),
		));
		$results[] = Hash::merge($results[0], array( // マトリクスー円
			'urlOptions' => array('key' => 'registration_22'),
			'assert' => array('method' => 'assertTextContains', 'expected' => 'nvd3'),
		));
		$results[] = Hash::merge($results[0], array( // マトリクスー棒
			'urlOptions' => array('key' => 'registration_24'),
			'assert' => array('method' => 'assertTextContains', 'expected' => 'nvd3'),
		));
		$results[] = Hash::merge($results[0], array( // マトリクスー表
			'urlOptions' => array('key' => 'registration_4'),
			'assert' => array('method' => 'assertTextContains', 'expected' => 'table table-striped table-bordered registration-table-vcenter'),
		));
		$results[] = Hash::merge($results[0], array( // 非表示の質問がある
			'urlOptions' => array('key' => 'registration_12'),
			'assert' => array('method' => 'assertTextContains', 'expected' => 'nvd3'),
		));
		return $results;
	}
}
