<?php
/**
 * RegistrationAnswersController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * RegistrationAnswersController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\AuthorizationKeys\Test\Case\Controller
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class RegistrationAnswersControllerPostTest extends NetCommonsControllerTestCase {

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
		'plugin.registrations.registration_answer',
		'plugin.authorization_keys.authorization_keys',
		'plugin.registrations.block4registrations',
		'plugin.registrations.blocks_language4registrations',
		'plugin.registrations.frame4registrations',
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
		$this->generateNc(Inflector::camelize($this->_controller));
	}

/**
 * アクションのPOSTテスト
 * KeyAuthへPost
 *
 * @return void
 */
	public function testKeyAuthPost() {
		$controller = $this->generate('Registrations.RegistrationAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'Registrations.Registrations',
				'Registrations.RegistrationsOwnAnswer',
				'AuthorizationKeys.AuthorizationKey'
			)
		));
		$data = array(
			'data' => array(
				'Frame' => array('id' => 19),
				'Block' => array('id' => 11),
				'AuthorizationKeys' => array('key' => 'test')
			)
		);
		$controller->AuthorizationKey->expects($this->any())
			->method('check')
			->will(
				$this->returnValue(true));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->_testPostAction('post', $data, array('action' => 'key_auth', 'frame_id' => 19, 'block_id' => 11, 'key' => 'registration_6'));
		$result = $this->headers['Location'];

		$this->assertTextContains('registration_6', $result);

		TestAuthGeneral::logout($this);
	}

/**
 * アクションのPOSTテスト
 * KeyAuthへPost
 *
 * @return void
 */
	public function testKeyAuthPostNG() {
		$controller = $this->generate('Registrations.RegistrationAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'Registrations.Registrations',
				'Registrations.RegistrationsOwnAnswer',
				'AuthorizationKeys.AuthorizationKey'
			)
		));
		$data = array(
			'data' => array(
				'Frame' => array('id' => 20),
				'Block' => array('id' => 11),
				'AuthorizationKeys' => array('key' => 'test')
			)
		);
		$controller->AuthorizationKey->expects($this->any())
			->method('check')
			->will(
				$this->returnValue(false));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$result = $this->_testPostAction('post', $data, array('action' => 'key_auth', 'frame_id' => 19, 'block_id' => 11, 'key' => 'registration_6'));

		// 認証キーcomponentをMockにしてるからエラーメッセージが入らない
		// 同じ画面を表示していることでエラー画面になっていると判断する
		$this->assertTextContains('/registrations/registration_answers/key_auth/', $result);

		TestAuthGeneral::logout($this);
	}

/**
 * アクションのPOSTテスト
 * ImgAuthへPost
 *
 * @return void
 */
	public function testImgAuthPost() {
		$controller = $this->generate('Registrations.RegistrationAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'Registrations.Registrations',
				'Registrations.RegistrationsOwnAnswer',
				'AuthorizationKeys.AuthorizationKey',
				'VisualCaptcha.VisualCaptcha'
			)
		));
		$data = array(
			'data' => array(
				'Frame' => array('id' => 20),
				'Block' => array('id' => 12),
				'VisualCaptcha' => array('test' => 'test')	// Mock使うんでなんでもよい
			)
		);
		$controller->VisualCaptcha->expects($this->any())
			->method('check')
			->will(
				$this->returnValue(true));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->_testPostAction('post', $data, array('action' => 'img_auth', 'frame_id' => 20, 'block_id' => 12, 'key' => 'registration_8'));
		$result = $this->headers['Location'];

		$this->assertTextContains('registration_8', $result);

		TestAuthGeneral::logout($this);
	}

/**
 * アクションのPOSTテスト
 * ImgAuthへPost
 *
 * @return void
 */
	public function testImgAuthPostNG() {
		$controller = $this->generate('Registrations.RegistrationAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'Registrations.Registrations',
				'Registrations.RegistrationsOwnAnswer',
				'AuthorizationKeys.AuthorizationKey',
				'VisualCaptcha.VisualCaptcha'
			)
		));
		$data = array(
			'data' => array(
				'Frame' => array('id' => 20),
				'Block' => array('id' => 12),
				'VisualCaptcha' => array('test' => 'test')	// Mock使うんでなんでもよい
			)
		);
		$controller->VisualCaptcha->expects($this->any())
			->method('check')
			->will(
				$this->returnValue(false));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$result = $this->_testPostAction('post', $data, array('action' => 'img_auth', 'frame_id' => 20, 'block_id' => 12, 'key' => 'registration_8'));

		// componentをMockにしてるからエラーメッセージが入らない
		// 同じ画面を表示していることでエラー画面になっていると判断する
		$this->assertTextContains('/registrations/registration_answers/img_auth/', $result);

		TestAuthGeneral::logout($this);
	}

/**
 * アクションのPOSTテスト
 * 登録Post
 *
 * @param array $data 投入データ
 * @param int $role ロール
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderAnswerPost
 * @return void
 */
	public function testAnswerPost($data, $role, $urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		if (isset($role)) {
			TestAuthGeneral::login($this, $role);
		}

		//テスト実施
		$urlOptions = Hash::merge(array('action' => 'view'), $urlOptions);
		$result = $this->_testPostAction('post', $data, $urlOptions, $exception, $return);

		//正常の場合、リダイレクト
		if (! $exception) {
			if ($assert == 'confirm') {
				$header = $this->controller->response->header();
				$this->assertNotEmpty($header['Location']);
			} elseif ($assert == 'err') {
				$this->assertTextContains('Question_1', $result);
			} else {
				$this->assertTextContains($assert, $result);
			}
		}

		//ログアウト
		if (isset($role)) {
			TestAuthGeneral::logout($this);
		}
	}

/**
 * アクションのPOSTテスト
 * 登録Postデータプロバイダ
 *
 * @return void
 */
	public function dataProviderAnswerPost() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'RegistrationPage' => array('page_sequence' => 0),
				'RegistrationAnswer' => array(
					'registration_2' => array(
						array(
							'answer_value' => '|choice_2:choice label1',
							'registration_question_key' => 'qKey_1')
					))
			)
		);
		$errData = $data;
		$errData['data']['RegistrationAnswer']['registration_2'][0]['answer_value'] = '|choice_800:nainainai';
		//$skipData = array(
		//	'data' => array(
		//		'Frame' => array('id' => 21),
		//		'Block' => array('id' => 13),
		//		'RegistrationPage' => array('page_sequence' => 0),
		//		'RegistrationAnswer' => array(
		//			'registration_4' => array(
		//				array(
		//					'answer_value' => '|choice_6:choice label3',
		//					'registration_question_key' => 'qKey_3')
		//			)))
		//);
		//$skipNoSelectData = $skipData;
		//$skipNoSelectData['data']['RegistrationAnswer']['registration_4'][0]['answer_value'] = '';

		return array(
			array(
				'data' => $data,
				'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => 6, 'block_id' => 2, 'key' => 'registration_2'),
				'assert' => 'confirm'),
			array(
				'data' => $errData,
				'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => 6, 'block_id' => 2, 'key' => 'registration_2'),
				'assert' => 'err'),
			//array(
			//	'data' => $skipData,
			//	'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			//	'urlOptions' => array('frame_id' => 21, 'block_id' => 13, 'key' => 'registration_4'),
			//	'assert' => 'name="data[RegistrationPage][page_sequence]" value="4"'),
			//array(
			//	'data' => $skipNoSelectData,
			//	'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			//	'urlOptions' => array('frame_id' => 21, 'block_id' => 13, 'key' => 'registration_4'),
			//	'assert' => 'name="data[RegistrationPage][page_sequence]" value="1"'),
		);
	}

/**
 * アクションのPOSTテスト
 * ConfirmhへPost
 *
 * @return void
 */
	public function testConfirmPost() {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		$data = array(
			'data' => array(
				'Frame' => array('id' => 26),
				'Block' => array('id' => 18),
			)
		);
		$this->_testPostAction('post', $data, array('action' => 'confirm', 'frame_id' => 26, 'block_id' => 18, 'key' => 'registration_12'));
		$result = $this->headers['Location'];
		$this->assertTextContains('thanks', $result);
		TestAuthGeneral::logout($this);
	}

}