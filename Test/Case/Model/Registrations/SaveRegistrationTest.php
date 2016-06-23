<?php
/**
 * Registration::saveRegistration()のテスト
 *
 * @property Registration $Registration
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('WorkflowSaveTest', 'Workflow.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');
App::uses('RegistrationFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationPageFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationQuestionFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationChoiceFixture', 'Registrations.Test/Fixture');

/**
 * Registration::saveRegistration()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\Registration
 */
class RegistrationSaveRegistrationTest extends WorkflowSaveTest {

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
		'plugin.registrations.registration_setting',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.workflow.workflow_comment',
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
	protected $_methodName = 'saveRegistration';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$model = $this->_modelName;
		$this->$model->Behaviors->unload('AuthorizationKey');
		Current::$current['Frame']['id'] = '6';
		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Frame']['room_id'] = '1';
		Current::$current['Frame']['plugin_key'] = 'registrations';
		Current::$current['Frame']['language_id'] = '2';
		$mailQueueMock = $this->getMock('MailQueueBehavior',
			['setAddEmbedTagValue', 'afterSave']);
		$mailQueueMock->expects($this->any())
		->method('setAddEmbedTagValue')
			->will($this->returnValue(true));
		$mailQueueMock->expects($this->any())
			->method('afterSave')
			->will($this->returnValue(true));

		// ClassRegistoryを使ってモックを登録。
		// まずremoveObjectしないとaddObjectできないのでremoveObjectする
		ClassRegistry::removeObject('MailQueueBehavior');
		// addObjectでUploadBehaviorでMockが使われる
		ClassRegistry::addObject('MailQueueBehavior', $mailQueueMock);

		// このloadではモックがロードされる
		$this->$model->Behaviors->load('MailQueue');

		//新着のビヘイビアをモックに差し替え
		$this->$model->Behaviors->unload('Topics');
		$topicsMock = $this->getMock('TopicsBehavior', ['setTopicValue', 'afterSave']);
		$topicsMock->expects($this->any())
			->method('setTopicValue')
			->will($this->returnValue(true));
		$topicsMock->expects($this->any())
			->method('afterSave')
			->will($this->returnValue(true));

		ClassRegistry::removeObject('TopicsBehavior');
		ClassRegistry::addObject('TopicsBehavior', $topicsMock);
		$this->$model->Behaviors->load('Topics');
	}

/**
 * テストDataの取得
 *
 * @param string $id registrationId
 * @param string $status
 * @return array
 */
	private function __getData($id = 2, $status = '1') {
		$fixtureRegistration = new RegistrationFixture();
		$rec = Hash::extract($fixtureRegistration->records, '{n}[id=' . $id . ']');
		$data['Registration'] = $rec[0];
		$data['Registration']['status'] = $status;

		$fixturePage = new RegistrationPageFixture();
		$rec = Hash::extract($fixturePage->records, '{n}[registration_id=' . $data['Registration']['id'] . ']');
		$data['RegistrationPage'] = $rec;
		$pageId = $rec[0]['id'];

		$fixtureQuestion = new RegistrationQuestionFixture();
		$rec = Hash::extract($fixtureQuestion->records, '{n}[registration_page_id=' . $pageId . ']');
		$data['RegistrationPage'][0]['RegistrationQuestion'] = $rec;
		$questionId = $rec[0]['id'];

		$fixtureChoice = new RegistrationChoiceFixture();
		$rec = Hash::extract($fixtureChoice->records, '{n}[registration_question_id=' . $questionId . ']');
		if ($rec) {
			$data['RegistrationPage'][0]['RegistrationQuestion'][0]['RegistrationChoice'] = $rec;
		}

		$data['Frame']['id'] = 6;
		return $data;
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return array 登録後のデータ
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//チェック用データ取得
		if (isset($data[$this->$model->alias]['id'])) {
			$before = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
			$saveData = Hash::remove($data, $this->$model->alias . '.id');
		} else {
			$saveData = $data;
		}

		//テスト実行
		$result = $this->$model->$method($saveData);
		$this->assertNotEmpty($result);
		$lastInsertId = $this->$model->getLastInsertID();

		//登録データ取得
		$latest = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $lastInsertId),
		));

		$actual = $latest;

		//前のレコードのis_latestのチェック
		if (isset($before)) {
			$after = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
			$this->assertFalse($after[$this->$model->alias]['is_latest']);
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');
		} else {
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created_user');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');

			$data[$this->$model->alias]['key'] = OriginalKeyBehavior::generateKey($this->$model->name, $this->$model->useDbConfig);
			$before[$this->$model->alias] = array();
		}
		// afterFindでDBテーブル構造以外のものがくっついてくるので
		$actual = Hash::remove($actual, 'RegistrationPage');

		$expected[$this->$model->alias] = Hash::merge(
			$before[$this->$model->alias],
			$data[$this->$model->alias],
			array(
				'id' => $lastInsertId,
				'is_active' => true,
				'is_latest' => true
			)
		);
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified');
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified_user');

		$this->assertEquals($expected, $actual);

		return $latest;
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		$data = $this->__getData();
		return array(
			array($data), //編集
		);
	}

/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderSaveOnExceptionError() {
		$data = $this->__getData();
		return array(
			array($data, 'Registrations.Registration', 'save'),
			array($data, 'Registrations.RegistrationPage', 'saveRegistrationPage'),
			array($data, 'Registrations.RegistrationFrameDisplayRegistration', 'saveDisplayRegistration'),
		);
	}

/**
 * SaveのValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *
 * @return void
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->__getData();
		return array(
			array($data, 'Registrations.Registration'),
		);
	}

/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ
 *
 * @return void
 */
	public function dataProviderValidationError() {
		$options = array(
			'title' => '',
		);
		return array(
			array($this->__getData(), $options, 'title', '',
				__d('net_commons', 'Invalid request.')),
		);
	}
}
