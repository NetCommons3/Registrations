<?php
/**
 * Registrations::deleteRegistration()のテスト
 *
 * @property Registrations $Registrations
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowDeleteTest', 'Workflow.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Registrations::deleteRegistration()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\Registration
 */
class RegistrationDeleteRegistrationTest extends WorkflowDeleteTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'registrations';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.registrations.registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.registrations.block_setting_for_registration',
		'plugin.authorization_keys.authorization_keys',
		'plugin.workflow.workflow_comment',
		'plugin.site_manager.site_setting',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'Registration';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'deleteRegistration';

/**
 * テストDataの取得
 *
 * @param int $id registration id
 * @param string $registrationKey registration key
 * @return array
 */
	private function __getData($id, $registrationKey) {
		$data = array(
			'Block' => array(
				'id' => '2',
				'key' => 'block_1',
			),
			'Registration' => array(
				'id' => $id,
				'key' => $registrationKey,
			),
		);
		return $data;
	}
/**
 * テストAssociationDataの取得
 *
 * @param string $registrationKey registration key
 * @return array
 */
	private function __getAssociation($registrationKey) {
		$association = array(
			//'RegistrationFrameDisplayRegistration' => array(
			//	'registration_key' => $registrationKey,
			//),
			'RegistrationAnswerSummary' => array(
				'registration_key' => $registrationKey,
			),
		);
		return $association;
	}

/**
 * DeleteのDataProvider
 *
 * #### 戻り値
 *  - data: 削除データ
 *  - associationModels: 削除確認の関連モデル array(model => conditions)
 *
 * @return array
 */
	public function dataProviderDelete() {
		$data = $this->__getData(2, 'registration_2');
		$association = $this->__getAssociation('registration_2');
		return array(
			array(
				$data,
				$association),
		);
	}
/**
 * NoExistDeleteのテスト
 *
 * @param array|string $data 削除データ
 * @param array $associationModels 削除確認の関連モデル array(model => conditions)
 * @dataProvider dataProviderNoExistDataDelete
 * @return void
 */
	public function testNoExistDataDelete($data, $associationModels = null) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		if (! $associationModels) {
			$associationModels = array();
		}

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertTrue($result);

		if (isset($data[$this->$model->alias]['key'])) {
			$keyConditions = array('key' => $data[$this->$model->alias]['key']);
		} elseif (! is_array($data)) {
			$keyConditions = array('key' => $data);
		} else {
			$keyConditions = Hash::flatten($data);
		}

		//チェック
		$count = $this->$model->find('count', array(
			'recursive' => -1,
			'conditions' => $keyConditions,
		));
		$this->assertEquals(0, $count);

		foreach ($associationModels as $assocModel => $conditions) {
			$count = $this->$model->$assocModel->find('count', array(
				'recursive' => -1,
				'conditions' => $conditions,
			));
			$this->assertEquals(0, $count);
		}
	}

/**
 * DeleteのDataProvider
 *
 * #### 戻り値
 *  - data: 削除データ
 *  - associationModels: 削除確認の関連モデル array(model => conditions)
 *
 * @return array
 */
	public function dataProviderNoExistDataDelete() {
		$data = $this->__getData(999999, 'registration_0');
		$association = $this->__getAssociation('registration_0');
		return array(
			array($data, $association),
		);
	}

/**
 * ExceptionErrorのDataProvider
 *
 * #### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderDeleteOnExceptionError() {
		$data = $this->__getData(2, 'registration_2');
		return array(
			array($data, 'Registrations.Registration', 'deleteAll'),
			//array($data, 'Registrations.RegistrationFrameDisplayRegistration', 'deleteAll'),
			array($data, 'Registrations.RegistrationAnswerSummary', 'deleteAll'),
		);
	}

}
