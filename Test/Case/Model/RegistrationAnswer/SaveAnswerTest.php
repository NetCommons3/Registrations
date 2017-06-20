<?php
/**
 * RegistrationAnswer::saveAnswer()のテスト
 *
 * @property RegistrationAnswer $RegistrationAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');
App::uses('RegistrationFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationPageFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationQuestionFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationChoiceFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationAnswerSummaryFixture', 'Registrations.Test/Fixture');

/**
 * RegistrationAnswer::saveAnswer()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationFrameDisplayRegistration
 */
class SaveAnswerTest extends NetCommonsModelTestCase {

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
		'plugin.registrations.block_setting_for_registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.site_manager.site_setting',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationAnswer';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveAnswer';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
		$this->RegistrationAnswerSummary = ClassRegistry::init(Inflector::camelize($this->plugin) . '.RegistrationAnswerSummary');
	}

/**
 * テストDataの取得
 *
 * @param int $pageSeq page sequence
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @param array $getAnswerData answer data get function
 * @return array
 */
	private function __getData($pageSeq, $qKey, $summaryId, $getAnswerData) {
		$answerData = $this->$getAnswerData($qKey, $summaryId);
		$data = array(
			'Frame' => array('id' => 6),
			'Block' => array('id' => 2),
			'RegistrationPage' => array('page_sequence' => $pageSeq),
			'RegistrationAnswer' => array(
				$qKey => $answerData
			),
		);
		return $data;
	}
/**
 * テストRegistrationDataの取得
 *
 * @param int $id registration id
 * @return array
 */
	private function __getRegistration($id) {
		$data = array();
		$fixtureRegistration = new RegistrationFixture();
		$rec = Hash::extract($fixtureRegistration->records, '{n}[id=' . $id . ']');
		$data['Registration'] = $rec[0];

		$fixturePage = new RegistrationPageFixture();
		$rec = Hash::extract($fixturePage->records, '{n}[registration_id=' . $data['Registration']['id'] . ']');
		$rec = Hash::extract($rec, '{n}[language_id=2]');
		$data['RegistrationPage'] = $rec;

		$fixtureQuestion = new RegistrationQuestionFixture();
		$fixtureChoice = new RegistrationChoiceFixture();

		foreach ($data['RegistrationPage'] as &$page) {
			$pageId = $page['id'];

			$rec = Hash::extract($fixtureQuestion->records, '{n}[registration_page_id=' . $pageId . ']');
			$rec = Hash::extract($rec, '{n}[language_id=2]');
			$page['RegistrationQuestion'] = $rec;
			$questionId = $rec[0]['id'];

			$rec = Hash::extract($fixtureChoice->records, '{n}[registration_question_id=' . $questionId . ']');
			if ($rec) {
				$page['RegistrationQuestion'][0]['RegistrationChoice'] = $rec;
			}
		}
		return $data;
	}
/**
 * テストSummaryDataの取得
 *
 * @param int $id summary id
 * @return array
 */
	private function __getSummary($id) {
		$data = array();
		$fixture = new RegistrationAnswerSummaryFixture();
		$rec = Hash::extract($fixture->records, '{n}[id=' . $id . ']');
		$data['RegistrationAnswerSummary'] = $rec[0];
		return $data;
	}
/**
 * テスト択一選択登録の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getSingleSelect($qKey, $summaryId) {
		return array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'answer_value' => '|choice_2:choice label1',
				'registration_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
	}
/**
 * テスト複数選択登録の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getMultipleSelect($qKey, $summaryId) {
		return array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'answer_value' => array('|choice_7:choice label4', '|choice_8:choice label5'),
				'registration_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => 'so no ta value!'
			)
		);
	}
/**
 * テストテキスト登録の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getText($qKey, $summaryId) {
		return array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'answer_value' => 'Test Answer!!',
				'registration_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
	}
/**
 * テスト日付登録の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getDate($qKey, $summaryId) {
		return array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'answer_value' => '2016-02-25',
				'registration_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
	}
/**
 * テスト日時登録の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getDateTime($qKey, $summaryId) {
		return array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'answer_value' => '2016-02-25 02:02:02',
				'registration_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
	}
/**
 * テストマトリクス択一選択登録の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getMatrix($qKey, $summaryId) {
		return array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'registration_question_key' => $qKey,
				'matrix_choice_key' => 'choice_9',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => '|choice_11:choice label11',
			),
			array(
				'registration_answer_summary_id' => $summaryId,
				'registration_question_key' => $qKey,
				'matrix_choice_key' => 'choice_10',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => '|choice_12:choice label12',
			)
		);
	}
/**
 * テストマトリクス複数選択登録の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getMultipleMatrix($qKey, $summaryId) {
		return array(
			array(
				'registration_answer_summary_id' => $summaryId,
				'registration_question_key' => $qKey,
				'matrix_choice_key' => 'choice_13',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => array('|choice_15:choice label15', '|choice_16:choice label16'),
			),
			array(
				'registration_answer_summary_id' => $summaryId,
				'registration_question_key' => $qKey,
				'matrix_choice_key' => 'choice_14',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => array('|choice_15:choice label15', '|choice_16:choice label16'),
			)
		);
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @param int $registrationId registration id
 * @param int $summaryId summary id
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data, $registrationId, $summaryId) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$registration = $this->__getRegistration($registrationId);
		$summary = $this->__getSummary($summaryId);

		//チェック用データ取得
		if (isset($data[$this->$model->alias]['id'])) {
			$before = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
		}

		//テスト実行
		$result = $this->$model->$method($data, $registration, $summary);
		$this->assertNotEmpty($result);

		//idのチェック
		if (isset($data[$this->$model->alias]['id'])) {
			$id = $data[$this->$model->alias]['id'];
		} else {
			$id = $this->$model->getLastInsertID();
		}

		//登録データ取得
		$actual = $this->$model->find('all', array(
			'recursive' => 0,
			'conditions' => array(
				'RegistrationAnswer.registration_answer_summary_id' => $summaryId,
				'RegistrationQuestion.language_id' => 2,
			),
		));
		$actual = Hash::remove($actual, '{n}.RegistrationQuestion');
		$actual = Hash::remove($actual, '{n}.RegistrationChoice');
		$actual = Hash::remove($actual, '{n}.RegistrationAnswerSummary');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.answer_values');//これは予備情報なので
		if (Hash::check($data, $this->$model->alias . '.{n}.id')) {
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		} else {
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created_user');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');

			$before[$this->$model->alias] = array();
		}

		$qKeys = array_keys($data[$this->$model->alias]);
		$qKey = $qKeys[0];
		$expected = $data['RegistrationAnswer'][$qKey];
		$check = array();
		foreach ($actual as $index => $actualElement) {
			// 新規作成の要望の場合はIDチェックはしない
			if (empty($expected[$index]['id'])) {
				$expected[$index]['id'] = $actualElement['RegistrationAnswer']['id'];
			}
			$check[] = $actualElement['RegistrationAnswer'];
		}

		$this->assertEquals($expected, $check);
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
		// pageSeq, question key, summaryId, getAnswerFunctionName, registrationId, summaryId
		return array(
			array($this->__getData(0, 'qKey_1', 3, '_getSingleSelect'), 2, 3),
			array($this->__getData(1, 'qKey_5', 4, '_getMultipleSelect'), 4, 4),
			array($this->__getData(2, 'qKey_7', 4, '_getText'), 4, 4),
			array($this->__getData(3, 'qKey_9', 4, '_getMatrix'), 4, 4),
			array($this->__getData(4, 'qKey_11', 4, '_getMultipleMatrix'), 4, 4),
			array($this->__getData(5, 'qKey_13', 4, '_getDate'), 4, 4),
			array($this->__getData(6, 'qKey_15', 4, '_getDateTime'), 4, 4),
			array($this->__getData(0, 'qKey_17', 5, '_getText'), 6, 5),
		);
	}

/**
 * SaveのValidationErrorテスト
 *
 * @param array $data 登録データ
 * @param int $registrationId registration id
 * @param int $summaryId summary id
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnValidationError
 * @return void
 */
	public function testSaveOnValidationError($data, $registrationId, $summaryId, $mockModel, $mockMethod = 'validates') {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$registration = $this->__getRegistration($registrationId);
		$summary = $this->__getSummary($summaryId);

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);
		$result = $this->$model->$method($data, $registration, $summary);
		$this->assertFalse($result);
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
		$data = $this->__getData(0, 'qKey_1', 3, '_getSingleSelect');
		return array(
			array($data, 2, 3, 'Registrations.RegistrationAnswer', 'saveMany'),
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
			'pageIndex' => 0,
			'maxPageIndex' => 0,
		);
		return array(
			array($this->__getDataWithQuestion(), $options, 'page_sequence', '2',
				__d('registrations', 'page sequence is illegal.')),
			array($this->__getData(), $options, 'page_sequence', '0',
				__d('registrations', 'please set at least one question.')),
		);
	}
/**
 * ターゲット項目取り出しのExceptionErrorテスト
 *
 * @return void
 */
	public function testSaveOnExceptionError2() {
		//$data,  $registrationId, $summaryId
		$model = $this->_modelName;
		$method = $this->_methodName;
		$data = $this->__getData(0, 'qKey_1', 3, '_getSingleSelect');
		$registration = $this->__getRegistration(2);
		$summary = $this->__getSummary(3);

		$registration = Hash::remove($registration, 'RegistrationPage.{n}.RegistrationQuestion');
		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($data, $registration, $summary);
	}
}
