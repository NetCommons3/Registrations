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

App::uses('WorkflowControllerEditTest', 'Workflow.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationEditController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Controller\RegistrationEditController
 */
class RegistrationEditControllerEditQuestionTest extends WorkflowControllerEditTest {

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
	protected $_controller = 'registration_edit';

/**
 * test Action name
 *
 * @var string
 */
	protected $_myAction = 'edit_question';

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
				'room_id' => '1',
				'plugin_key' => $this->plugin,
			),
			'Registration' => array(
				'key' => $registrationKey,
				'status' => WorkflowComponent::STATUS_IN_DRAFT,
				'title' => 'EditTestTitle',
				'title_icon' => 'ok.svg',
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
			//'WorkflowComment' => array(
			//	'comment' => 'WorkflowComment save test'
			//),
		);
		return $data;
	}

/**
 * editアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGet() {
		$data = $this->__getData();

		$results = array();

		//ログインなし
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_42'),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		return $results;
	}

/**
 * editアクションのGETテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByCreatable() {
		$data = $this->__getData();

		$results = array();

		//作成権限のみ
		//--他人の記事の編集
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_42'),
			'assert' => null,
			'exception' => 'BadRequestException'
		);
		//--自分の記事の編集(一度も公開していない)
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_44'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//--自分の記事の編集(一度も公開していない)=編集用「質問追加」ボタンがあるはず
		$results[2] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_44'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('registrations', 'Add Question')),
		);
		// 存在してない登録フォームを指定
		$results[3] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_99999'),
			'assert' => null, 'exception' => 'BadRequestException', 'return' => 'json'
		);
		//新規作成
		$results[4] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//--自分の記事の編集(公開すみ)
		$results[5] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_10'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//--自分の記事の編集(公開すみ)＝追加ボタンがない
		$results[6] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_18'),
			'assert' => array('method' => 'assertNotContains', 'expected' => __d('registrations', 'Add Question')),
		);
		return $results;
	}

/**
 * editアクションのGETテスト(編集権限、公開権限なし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByEditable() {
		$data = $this->__getData();
		$base = 0;
		$results = array();

		//編集権限あり
		//--コンテンツあり 自分の記事（編集できるコンテンツ
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_42'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		/* 本来はここで表示ページの要素の妥当性をチェックするのだと思われるが
		 * 登録フォームはAngularでページ要素を展開しているため、NetCommons通常テスト確認メソッドが使えない
		 * ごく一部に限って確認を行うことにする
		 */

		// ページタブ,ページ追加リンク,質問追加ボタン,質問LI、質問種別選択,質問削除ボタン, 選択肢追加ボタン, 選択肢削除ボタン、キャンセルボタン、次へボタンの存在の確認
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][page_sequence]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][key]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][question_sequence]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][key]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][is_result_display]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][result_display_type]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][is_require]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][question_value]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][question_type]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'textarea', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][description]', 'value' => null),
		)));
		//10
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][is_choice_random]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][is_skip]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][choice_label]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][choice_value]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][matrix_type]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][other_choice_type]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][key]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][graph_color]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		)));
		//--コンテンツなし...編集対象データを指定せずに編集画面へ行くと不正リクエストエラー
		$results[count($results)] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'action' => $this->_myAction, 'key' => null),
			'assert' => null,
			'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * editアクションのGETテスト(公開権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByPublishable() {
		$data = $this->__getData();
		$results = array();

		//フレームID指定なしテスト
		$results[0] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//フレームID指定なしでも画面の内容がちゃんと表示されていることを確認している
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_2'),
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => null),
		)));
		// いったん公開して、その後の一時保存データに対して編集している
		// その場合でも質問変更は不可能だ
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_2'),
			'assert' => array('method' => 'assertNotContains', 'expected' => __d('registrations', 'Add Question')),
		)));

		return $results;
	}

/**
 * editアクションのPOSTテスト用DataProvider
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
	public function dataProviderEditPost() {
		$data = $this->__getData();
		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'registration_44'),
				'exception' => 'ForbiddenException'
			),
			//作成権限のみ
			//--他人の記事
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'registration_40'),
				'exception' => 'BadRequestException'
			),
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'registration_40'),
				'exception' => 'BadRequestException', 'return' => 'json'
			),
			//--自分の記事(一度も公開していない)
			array(
				'data' => $this->__getData('registration_44'), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
			//--自分の記事(公開)
			array(
				'data' => $this->__getData('registration_12'), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
			//編集権限あり
			//--新規作成
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
			//フレームID指定なし 新規作成テスト
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
			//--自分の記事(公開)
			array(
				'data' => $this->__getData('registration_4'), 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
		);
	}

/**
 * editアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderEditValidationError() {
		$data = $this->__getData();

		$result = array(
			'data' => $data,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
		);

		return array(
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'RegistrationPage.0.RegistrationQuestion.0.is_require',
					'value' => 'aa',
					'message' => 'question.errorMessages.isRequire',
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'RegistrationPage.0.RegistrationQuestion.0.question_value',
					'value' => '',
					'message' => 'question.errorMessages.questionValue',
					//'message' => __d('registrations', 'Please input question text.') Angularで展開するためエラーメッセージ実体がphpunitの試験段階だとわからない
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'RegistrationPage.0.RegistrationQuestion.0.question_type',
					'value' => 'aa',
					'message' => 'question.errorMessages.questionType',
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'RegistrationPage.0.RegistrationQuestion.0.is_choice_random',
					'value' => 'aa',
					'message' => 'question.errorMessages.isChoiceRandom',
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'RegistrationPage.0.RegistrationQuestion.0.is_skip',
					'value' => 'aa',
					'message' => 'question.errorMessages.isSkip',
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'RegistrationPage.0.RegistrationQuestion.0.RegistrationChoice.0.choice_label',
					'value' => '',
					'message' => 'choice.errorMessages.choiceLabel',
				)
			)),
		);
	}
}