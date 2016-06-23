<?php
/**
 * RegistrationAnswerSummaryCsv::getAnswerSummaryCsv()のテスト
 *
 * @property RegistrationAnswerSummaryCsv $RegistrationAnswerSummaryCsv
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationAnswerSummaryCsv::getAnswerSummaryCsv()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationAnswerSummaryCsv
 */
class GetAnswerSummaryCsvTest extends NetCommonsGetTest {

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
		'plugin.registrations.registration_setting',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationAnswerSummaryCsv';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getAnswerSummaryCsv';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->RegistrationAnswerSummary = ClassRegistry::init('Registrations.RegistrationAnswerSummary');
		$this->RegistrationAnswer = ClassRegistry::init('Registrations.RegistrationAnswer');
		$this->RegistrationAnswerSummary->deleteAll(array('answer_status' => 2));
		$this->RegistrationAnswer->deleteAll(array('NOT' => array('registration_answer_summary_id' => null)));
		$this->RegistrationAnswer->Behaviors->unload('RegistrationAnswerSingleChoice');
		$this->RegistrationAnswer->Behaviors->unload('RegistrationAnswerMultipleChoice');
		$this->RegistrationAnswer->Behaviors->unload('RegistrationAnswerSingleList');
		$this->RegistrationAnswer->Behaviors->unload('RegistrationAnswerMatrixSingleChoice');
		$this->RegistrationAnswer->Behaviors->unload('RegistrationAnswerMatrixMultipleChoice');
		$this->RegistrationAnswerSummary->Behaviors->unload('Mails.MailQueue');
		// ダミーデータを入れます
		$summaryData = array(
			array('registration_4', ''),
			array('registration_4', ''),
			array('registration_4', 1),
			array('registration_12', ''),
			array('registration_12', 1),
			array('registration_22', ''),
			array('registration_22', ''),
			array('registration_22', ''),
		);
		$loopCt = count($summaryData);
		for ($i = 0; $i < $loopCt; $i++) {
			$registrationKey = $summaryData[$i][0];
			$id = $this->_insertAnswerSummary($registrationKey, $summaryData[$i][1]);

			if ($registrationKey == 'registration_4') {
				// single choice
				$this->_insertAnswer($id, 'qKey_3', '|choice_4:choice label1');
				// multi choice
				if ($i % 2 == 0) {
					$this->_insertAnswer($id, 'qKey_5', '|choice_7:choice label4|choice_8:choice label5');
				} else {
					$this->_insertAnswer($id, 'qKey_5', '|choice_7:choice label4');
				}
				// text
				$this->_insertAnswer($id, 'qKey_7', 'テキストの登録ですよ');
				// matrix single
				$this->_insertAnswer($id, 'qKey_9', '|choice_12:choice label12', 'choice_9');
				$this->_insertAnswer($id, 'qKey_9', '|choice_11:choice label11', 'choice_10');
				// matrix multi
				if ($i % 2 == 0) {
					$this->_insertAnswer($id, 'qKey_11', '|choice_16:choice label16', 'choice_13');
					$this->_insertAnswer($id, 'qKey_11', '|choice_15:choice label15', 'choice_14');
				} else {
					$this->_insertAnswer($id, 'qKey_11', '|choice_15:choice label15|choice_16:choice label16', 'choice_13');
					$this->_insertAnswer($id, 'qKey_11', '|choice_15:choice label15|choice_16:choice label16', 'choice_14');
				}
				// date
				$this->_insertAnswer($id, 'qKey_13', '2016-03-01');
			} elseif ($registrationKey == 'registration_12') {
				$this->_insertAnswer($id, 'qKey_27', '|choice_27:choice label27');
			} elseif ($registrationKey == 'registration_22') {
				// マトリクスで登録蟻の場合と i=6のときは登録がなかったことにしたい
				if ($i == 5) {
					$this->_insertAnswer($id, 'qKey_41', '|choice_35:choice label35', 'choice_33');
					$this->_insertAnswer($id, 'qKey_41', '|choice_36:choice label36', 'choice_34');
				} elseif ($i == 6) {
					$this->_insertAnswer($id, 'qKey_41', '', 'choice_33');
					$this->_insertAnswer($id, 'qKey_41', '', 'choice_34');
				} else {
					$this->_insertAnswer($id, 'qKey_99', '', 'choice_33');
					$this->_insertAnswer($id, 'qKey_99', '', 'choice_34');
				}
			}
		}
		$this->RegistrationAnswer->Behaviors->load('RegistrationAnswerSingleChoice');
		$this->RegistrationAnswer->Behaviors->load('RegistrationAnswerMultipleChoice');
		$this->RegistrationAnswer->Behaviors->load('RegistrationAnswerSingleList');
		$this->RegistrationAnswer->Behaviors->load('RegistrationAnswerMatrixSingleChoice');
		$this->RegistrationAnswer->Behaviors->load('RegistrationAnswerMatrixMultipleChoice');
	}

/**
 * _insertAnswerSummary
 *
 * @param int $registrationKey 登録フォームKey
 * @param int $userId 登録した人ID
 *
 * @return int summary id
 */
	protected function _insertAnswerSummary($registrationKey, $userId) {
		$summary = array(
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-29 00:00:00',
			'registration_key' => $registrationKey,
			'user_id' => $userId,
			'created_user' => $userId
		);
		$this->RegistrationAnswerSummary->create();
		$this->RegistrationAnswerSummary->save($summary);
		$id = $this->RegistrationAnswerSummary->getLastInsertID();
		return $id;
	}
/**
 * _insertAnswer
 *
 * @param int $summaryId サマリID
 * @param sring $qKey Question key
 * @param string $value answer value
 * @param string $cKey choice key
 *
 * @return void
 */
	protected function _insertAnswer($summaryId, $qKey, $value, $cKey = '') {
		$answer = array(
			'answer_value' => $value,
			'registration_answer_summary_id' => $summaryId,
			'registration_question_key' => $qKey,
			'matrix_choice_key' => $cKey,
			'other_answer_value' => 'その他の登録',
		);
		$this->RegistrationAnswer->create();
		$this->RegistrationAnswer->save($answer, false);
	}
/**
 * _getRegistration
 *
 * @param int $id 質問ID
 * @return array
 */
	protected function _getRegistration($id) {
		$fixtureRegistration = new RegistrationFixture();
		$fixturePage = new RegistrationPageFixture();
		$fixtureQuestion = new RegistrationQuestionFixture();
		$fixtureChoice = new RegistrationChoiceFixture();

		$data = array();
		$rec = Hash::extract($fixtureRegistration->records, '{n}[id=' . $id . ']');
		$data['Registration'] = $rec[0];

		$rec = Hash::extract($fixturePage->records, '{n}[registration_id=' . $data['Registration']['id'] . ']');
		$rec = Hash::extract($rec, '{n}[language_id=2]');
		$data['RegistrationPage'] = $rec;

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
 * getAnswerSummaryCsv
 *
 * @param int $registrationId registration id
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetAnswerSummaryCsv($registrationId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$registration = $this->_getRegistration($registrationId);

		//テスト実行
		$result = $this->$model->$method($registration, 1000, 0);
		$result = Hash::remove($result, '{n}.1');

		$expected = Hash::remove($expected, '{n}.1');
		//チェック
		$this->assertEquals($expected, $result);
	}
/**
 * getDefaultChoiceのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(array(
				__d('registrations', 'Respondent'), __d('registrations', 'Answer Date'), __d('registrations', 'Number'),
				'1-1. Question_1',
				'2-1. Question_2',
				'3-1. Question_3',
				'4-1-1. Question_4:choice label9',
				'4-1-2. Question_4:choice label10',
				'5-1-1. Question_5:choice label13',
				'5-1-2. Question_5:choice label14',
				'6-1. Question_6',
				'7-1. Question_7',
			),	// header
			array(
				'Guest', '2016-03-01 01:01:01', '1',
				'choice label1',
				'choice label4|その他の登録',
				'テキストの登録ですよ',
				'choice label12',
				'choice label11',
				'choice label16',
				'choice label15',
				'2016-03-01',
				''
			),	// data1
			array(
				'Guest', '2016-03-01 01:01:01', '1',
				'choice label1',
				'choice label4',
				'テキストの登録ですよ',
				'choice label12',
				'choice label11',
				'choice label15|choice label16',
				'choice label15|choice label16',
				'2016-03-01',
				''
			),	// data2
			array(
				'System Administrator', '2016-03-01 01:01:01', '1',
				'choice label1',
				'choice label4|その他の登録',
				'テキストの登録ですよ',
				'choice label12',
				'choice label11',
				'choice label16',
				'choice label15',
				'2016-03-01',
				''
		));
		$expect2 = array(array(
				__d('registrations', 'Respondent'), __d('registrations', 'Answer Date'), __d('registrations', 'Number'),
				'1-1. Question_1',
		));
		$expect3 = array(array(
				__d('registrations', 'Respondent'), __d('registrations', 'Answer Date'), __d('registrations', 'Number'),
				'1-1. Question_1',
				'2-1. Question_1',
				'3-1. Question_1',
			),	// header
			array(
				__d('registrations', 'Anonymity'), '2016-03-01 01:01:01', '1',
				'choice label27',
				'',
				'',
			),	// data2
			array(
				__d('registrations', 'Anonymity'), '2016-03-01 01:01:01', '1',
				'choice label27',
				'',
				'',
		));
		$expect4 = array(array(
				__d('registrations', 'Respondent'), __d('registrations', 'Answer Date'), __d('registrations', 'Number'),
				'1-1-1. Question_1:choice label33',
				'1-1-2. Question_1:choice label34',
			),	// header
			array(
				'Guest', '2016-03-01 01:01:01', '1',
				'choice label35',
				'その他の登録:choice label36',
			),	// data2
			array(
				'Guest', '2016-03-01 01:01:01', '1',
				'', //登録なし
				'その他の登録:',
			),	// data3 空登録
			array(// data4 異常登録
				'Guest', '2016-03-01 01:01:01', '1',
				'',
				'',
		));
		return array(
			array('4', $expect),
			array('2', $expect2),
			array('12', $expect3),
			array('22', $expect4),
		);
	}

}