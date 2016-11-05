<?php
/**
 * RegistrationPage::getNextPage()のテスト
 *
 * @property RegistrationPage $RegistrationPage
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');
App::uses('RegistrationFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationPageFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationQuestionFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationChoiceFixture', 'Registrations.Test/Fixture');

/**
 * RegistrationPage::getNextPage()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationPage
 */
class RegistrationGetNextPageTest extends NetCommonsGetTest {

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
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationPage';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getNextPage';

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
		Current::$current['Frame']['room_id'] = '2';
		Current::$current['Frame']['plugin_key'] = 'registrations';
		Current::$current['Frame']['language_id'] = '2';
	}

/**
 * テストDataの取得
 *
 * @param string $id registrationId
 * @param string $status
 * @return array
 */
	private function __getData($id = 3, $status = '1') {
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
 * getNextPageのテスト
 *
 * @param array $registration registration
 * @param int $nowPageSeq current page sequence number
 * @param array $nowAnswers now answer
 * @param mix $expected 期待値
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetNextPage($registration, $nowPageSeq, $nowAnswers, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($registration, $nowPageSeq, $nowAnswers);

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getNextPageのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$registration = $this->__getData(4);
		$noPageQuest = Hash::remove($registration, 'RegistrationPage');
		$errPageSeqQuest = $registration;
		$errPageSeqQuest['RegistrationPage'][0]['RegistrationQuestion'][0]['RegistrationChoice'][2]['skip_page_sequence'] = 10;
		$answer1 = array(
			'qKey_3' => array(
				array(
					'answer_value' => '|choice_6:choice label3',
					'registration_question_key' => 'qKey_3'
				)
			)
		);
		$answer2 = array(
			'qKey_3' => array(
				array(
					'answer_value' => '|choice_5:choice label2',
					'registration_question_key' => 'qKey_3'
				)
			)
		);
		return array(
			array($registration, 0, $answer1, 4),
			array($registration, 0, $answer2, false),
			array($noPageQuest, 0, $answer1, false),
			array($errPageSeqQuest, 0, $answer1, false),
		);
	}

}
