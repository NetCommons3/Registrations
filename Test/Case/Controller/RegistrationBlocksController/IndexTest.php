<?php
/**
 * RegistrationBlocksController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BlocksControllerTest', 'Blocks.TestSuite');

/**
 * RegistrationBlocksController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Qustionnaires\Test\Case\Controller\RegistrationBlocksController
 */
class RegistrationBlocksControllerIndexTest extends BlocksControllerTest {

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
	protected $_controller = 'registration_blocks';

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
		'plugin.registrations.block4registrations',
		'plugin.registrations.blocks_language4registrations',
		'plugin.registrations.frame4registrations',
		'plugin.registrations.frame_public_language4registrations',
		'plugin.registrations.frames_language4registrations',
	);

/**
 * Edit controller name
 *
 * @var string
 */
	protected $_editController = 'registration_blocks';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->Registration = ClassRegistry::init('Registrations.Registration');
		$this->Registration->Behaviors->unload('AuthorizationKey');
	}

/**
 * index()のテスト
 *
 * 登録フォームは追加、編集用のコントローラを独自に持っているためここにオーバーライド
 * @return void
 */
	public function testIndex() {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実施
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
			'frame_id' => $frameId,
		);
		$result = $this->_testNcAction($url, array('method' => 'get'));

		//チェック
		//--追加ボタンチェック
		$addLink = $url;
		$addLink['controller'] = 'registration_add';
		$addLink['action'] = 'add';
		$addLink['block_id'] = $blockId;
		$addLink['q_mode'] = 'setting';
		$this->assertRegExp(
			'/<a href=".*?' . preg_quote(NetCommonsUrl::actionUrl($addLink), '/') . '.*?".*?>/', $result
		);

		//--編集ボタンチェック
		$blockId = '2';
		$editLink = $url;
		$editLink['controller'] = 'registration_edit';
		$editLink['action'] = 'edit_question';
		$editLink['block_id'] = $blockId;
		$editLink['key'] = 'registration_2';
		$editLink['q_mode'] = 'setting';
		$this->assertRegExp(
			'/<a href=".*?' . preg_quote(NetCommonsUrl::actionUrl($editLink), '/') . '.*?".*?>/', $result
		);

		//--カレントブロックラジオボタン
		//$this->assertInput('input', 'data[Frame][block_id]', null, $result);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * index()のブロックなしテスト
 *
 * 登録フォームはブロック一覧ではなく、コンテンツ一覧を出しているため独自実装が必要となる
 * @return void
 */
	public function testIndexNoneBlock() {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実施
		$frameId = '18';
		$url = array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
			'frame_id' => $frameId,
		);
		$result = $this->_testNcAction($url, array('method' => 'get'), null, 'viewFile');

		//チェック
		$this->assertTextEquals($result, 'not_found');

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * index()のページネーションテスト
 *
 * @param int $page ページ番号
 * @param bool $isFirst 最初のページかどうか
 * @param bool $isLast 最後のページかどうか
 * @dataProvider dataProviderPaginator
 * @return void
 */
	public function testIndexPaginator($page, $isFirst, $isLast) {
		TestAuthGeneral::login($this);

		//テスト実施
		$frameId = '6';
		$url = array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
			'frame_id' => $frameId,
			'limit' => 10,
		);
		if (! $isFirst) {
			$url[] = 'page:' . $page;
		}
		$result = $this->_testNcAction($url, array('method' => 'get'));

		//チェック
		$this->assertRegExp(
			'/' . preg_quote('<ul class="pagination">', '/') . '/', $result
		);
		if ($isFirst) {
			$this->assertNotRegExp('/<li><a.*?rel="first".*?<\/a><\/li>/', $result);
		} else {
			$this->assertRegExp('/<li><a.*?rel="first".*?<\/a><\/li>/', $result);
		}
		$this->assertRegExp('/<li class="active"><a>' . $page . '<\/a><\/li>/', $result);
		if ($isLast) {
			$this->assertNotRegExp('/<li><a.*?rel="last".*?<\/a><\/li>/', $result);
		} else {
			$this->assertRegExp('/<li><a.*?rel="last".*?<\/a><\/li>/', $result);
		}

		TestAuthGeneral::logout($this);
	}

/**
 * ページネーションDataProvider
 *
 * ### 戻り値
 *  - page: ページ番号
 *  - isFirst: 最初のページかどうか
 *  - isLast: 最後のページかどうか
 *
 * @return array
 */
	public function dataProviderPaginator() {
		//$page, $isFirst, $isLast
		$data = array(
			array(1, true, false),
			array(2, false, false),
			array(3, false, true),
		);
		return $data;
	}

}
