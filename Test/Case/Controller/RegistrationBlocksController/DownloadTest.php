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

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationBlocksController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Qustionnaires\Test\Case\Controller\RegistrationBlocksController
 */
class RegistrationBlocksControllerDownloadTest extends NetCommonsControllerTestCase {

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
		'plugin.registrations.registration_answer',
		'plugin.authorization_keys.authorization_keys',
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

		//ログイン
		TestAuthGeneral::login($this);

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Registrations', 'TestRegistrations');
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Registrations', 'TestFiles');

		//テストコントローラ生成
		$this->generateNc('TestRegistrations.TestRegistrationBlocks');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * download()のテスト
 *
 * @return void
 */
	public function testDownload() {
		//テスト実施
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_registrations',
			'controller' => 'test_registration_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'registration_2',
			'frame_id' => $frameId
		);
		$this->_testPostAction('post', array(
				'AuthorizationKey' => array(
					'authorization_key' => 'ABC'
				)
			),
		$url);
		//チェック
		$this->assertTextEquals('registration_2.zip', $this->controller->returnValue[0]);
		$this->assertTextEquals('registration_2.csv', $this->controller->returnValue[1]);
		$this->assertTextEquals('ABC', $this->controller->returnValue[2]);
	}

/**
 * download()のgetテスト
 *
 * @return void
 */
	//public function testIndexNoneFrameBlock() {
	//	//テスト実施
	//	// フレーム、ブロック指定なし
	//	$url = array(
	//		'plugin' => 'test_registrations',
	//		'controller' => 'test_registration_blocks',
	//		'action' => 'download',
	//		'key' => 'registration_2',
	//	);
	//
	//	$this->_testPostAction('post', array(
	//		'AuthorizationKey' => array(
	//			'authorization_key' => 'ABC'
	//		)
	//	), $url, 'InternalErrorException');
	//}

/**
 * download()の不正登録フォーム指定テスト
 *
 * 一度も発行されたことのない登録フォームはCSVを入手できない
 * 存在しない登録フォーム
 *
 * @return void
 */
	public function testNoPublish() {
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_registrations',
			'controller' => 'test_registration_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'registration_4',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('registrations', 'Designation of the registration does not exist.'));
		$result = $this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url);
		//$flash = CakeSession::read('Message.flash');
		$this->assertEmpty($result);
	}
/**
 * download()の圧縮パスワードなし指定テスト
 *
 * @return void
 */
	public function testNoPassword() {
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_registrations',
			'controller' => 'test_registration_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'registration_2',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('registrations', 'Setting of password is required always to download answers.'));
		$result = $this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => ''
			)
		), $url);
		$this->assertEmpty($result);
	}
/**
 * download()のファイル作成異常テスト
 *
 * @return void
 */
	public function testException() {
		$mock = $this->getMockForModel('Registrations.RegistrationAnswerSummaryCsv', array('getAnswerSummaryCsv'));
		$mock->expects($this->once())
			->method('getAnswerSummaryCsv')
			->will($this->throwException(new Exception));
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_registrations',
			'controller' => 'test_registration_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'registration_2',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('registrations', 'download error'));
		$this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url);
	}

/**
 * download()の大量試験テスト
 *
 * @return void
 */
	public function testDownloadBigData() {
		$frameId = '26';
		$blockId = '18';
		$url = array(
			'plugin' => 'test_registrations',
			'controller' => 'test_registration_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'registration_12',
			'frame_id' => $frameId
		);
		$this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			),
			'Block' => [
				'id' => $blockId,
			]), $url);
		$this->assertEqual(count($this->controller->returnValue[3]), 3);	// header line + 2 records
	}
}
