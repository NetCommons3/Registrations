<?php
/**
 * RegistrationAnswerSummary::getProgressiveSummary()のテスト
 *
 * @property RegistrationAnswerSummary $RegistrationAnswerSummary
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
 * RegistrationAnswerSummary::getProgressiveSummary()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationAnswerSummary
 */
class GetProgressiveSummaryTest extends NetCommonsGetTest {

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
	protected $_modelName = 'RegistrationAnswerSummary';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getProgressiveSummary';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['User']['id'] = 3;
	}
/**
 * getProgressiveSummary
 *
 * @param string $registrationKey registration key
 * @param int $summaryId summary id
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetProgressiveSummary($registrationKey, $summaryId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($registrationKey, $summaryId);

		//チェック
		if ($result) {
			$this->assertEquals($result[$this->$model->alias]['id'], $expected);
		} else {
			$this->assertEquals($result, $expected);
		}
	}
/**
 * getProgressiveSummaryのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		return array(
			array('registration_2', 3, 3),
			array('registration_12', 1, array()),
			array('registration_12', 2, array())
		);
	}
}
