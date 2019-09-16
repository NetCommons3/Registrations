<?php
/**
 * BlockRolePermissionsController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('RegistrationBlockRolePermissionsController', 'Registrations.Controller');
App::uses('BlockRolePermissionsControllerEditTest', 'Blocks.TestSuite');

/**
 * BlockRolePermissionsController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Controller
 */
class RegistrationBlockRolePermissionsControllerEditTest extends BlockRolePermissionsControllerEditTest {

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
	protected $_controller = 'registration_block_role_permissions';

/**
 * テストDataの取得
 *
 * @param bool $isPost POSTかどうか
 * @return array
 */
	private function __getData($isPost) {
		if ($isPost) {
			$data = array(
				'RegistrationSetting' => array(
					'id' => 2,
					'block_key' => 'block_2',
					'use_workflow' => '1',
					'approval_type' => '1',
				)
			);
		} else {
			$data = array(
				'RegistrationSetting' => array(
					'use_workflow',
					'approval_type',
				)
			);
		}
		return $data;
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - approvalFields コンテンツ承認の利用有無のフィールド
 *  - exception Exception
 *  - return testActionの実行後の結果
 *
 * @return void
 */
	public function dataProviderEditGet() {
		return array(
			array('approvalFields' => $this->__getData(false))
		);
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - data POSTデータ
 *  - exception Exception
 *  - return testActionの実行後の結果
 *
 * @return void
 */
	public function dataProviderEditPost() {
		return array(
			array('data' => $this->__getData(true))
		);
	}

/**
 * editアクションのPOSTテスト(Saveエラー)
 *
 * @param array $data POSTデータ
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditPost
 * @return void
 */
	public function testEditPostSaveError($data, $exception = null, $return = 'view') {
		$data['BlockRolePermission']['content_creatable'][Role::ROOM_ROLE_KEY_GENERAL_USER]['roles_room_id'] = 'aaaa';

		//テスト実施
		$exception = false;
		$result = $this->testEditPost($data, false, $return);

		$approvalFields = $this->__getData(false);
		$this->_assertEditGetPermission($approvalFields, $result);
	}
}
