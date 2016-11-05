<?php
/**
 * RegistrationEditController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerDeleteTest', 'Workflow.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationEditController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Controller\RegistrationEditController
 */
class RegistrationEditControllerDeleteTest extends WorkflowControllerDeleteTest {

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
	protected $_controller = 'registration_edit';

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
			->will($this->returnValueMap([['Registrations.registrationEdit.' . 'testSession', true]]));
		$this->controller->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap([['Registrations.registrationEdit.' . 'testSession', $this->__getData()]]));
	}

/**
 * テストDataの取得
 *
 * @param string $registrationKey 項目ID
 * @return array
 */
	private function __getData($registrationKey = 'registration_2') {
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
			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test'
			),
		);
		return $data;
	}

/**
 * deleteアクションのGETテスト用DataProvider
 *
 * ### 戻り値
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderDeleteGet() {
		$data = $this->__getData();
		$results = array();

		// 管理者がかいて未公開データを
		// 未ログインの人が取り出そうと
		$results[0] = array('role' => null,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'registration_42'),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		// 一般がかいて未公開データを
		// 未ログインの人が取り出そうと
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'registration_38'),
			'assert' => null, 'exception' => 'BadRequestException'
		)));
		// 管理者がかいて公開データを
		// 編集者が取り出そうと
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'assert' => null, 'exception' => 'BadRequestException'
		)));
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'assert' => null, 'exception' => 'BadRequestException'
		)));
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'assert' => null, 'exception' => 'BadRequestException', 'return' => 'json'
		)));

		return $results;
	}

/**
 * deleteアクションのPOSTテスト用DataProvider
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
	public function dataProviderDeletePost() {
		$data = $this->__getData();

		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['Registration']['key']),
				'exception' => 'ForbiddenException'
			),
			//作成権限のみ
			//--他人の記事
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['Registration']['key']),
				'exception' => 'BadRequestException'
			),
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['Registration']['key']),
				'exception' => 'BadRequestException', 'return' => 'json'
			),
			//--自分の記事＆一度も公開されていない
			array(
				'data' => $this->__getData('registration_38'), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'registration_38'),
			),
			//--自分の記事＆一度公開している
			array(
				'data' => $this->__getData('registration_12'), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'registration_12'),
				'exception' => 'BadRequestException'
			),
			//編集権限あり
			//--公開していない
			array(
				'data' => $this->__getData('registration_36'), 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'registration_36'),
			),
			//--公開している
			array(
				'data' => $this->__getData('registration_6'), 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'registration_6'),
				'exception' => 'BadRequestException'
			),
			//公開権限あり
			//フレームID指定なしテスト
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => $data['Registration']['key']),
			),
		);
	}

/**
 * deleteアクションのExceptionErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - mockModel: Mockのモデル
 *  - mockMethod: Mockのメソッド
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderDeleteExceptionError() {
		$data = $this->__getData();

		return array(
			array(
				'mockModel' => 'Registrations.Registration', 'mockMethod' => 'deleteRegistration', 'data' => $data,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['Registration']['key']),
				'exception' => 'BadRequestException'
			),
			array(
				'mockModel' => 'Registrations.Registration', 'mockMethod' => 'deleteRegistration', 'data' => $data,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['Registration']['key']),
				'exception' => 'BadRequestException', 'return' => 'json'
			),
		);
	}

}
