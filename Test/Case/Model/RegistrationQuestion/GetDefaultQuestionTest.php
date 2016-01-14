<?php
/**
 * RegistrationQuestion::getDefaultQuestion()のテスト
 *
 * @property RegistrationQuestion $RegistrationQuestion
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
 * RegistrationQuestion::getDefaultQuestion()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationQuestion
 */
class RegistrationGetDefaultQuestionTest extends NetCommonsGetTest {

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
	protected $_modelName = 'RegistrationQuestion';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getDefaultQuestion';

/**
 * getDefaultQuestionのテスト
 *
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetDefaultQuestion($expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method();
		// Choiceは省く
		$result = Hash::remove($result, 'RegistrationChoice');

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getDefaultQuestionのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			'question_sequence' => 0,
			'question_value' => __d('registrations', 'New Question') . '1',
			'question_type' => RegistrationsComponent::TYPE_SELECTION,
			'is_require' => RegistrationsComponent::USES_NOT_USE,
			'is_skip' => RegistrationsComponent::SKIP_FLAGS_NO_SKIP,
			'is_choice_random' => RegistrationsComponent::USES_NOT_USE,
			'is_range' => RegistrationsComponent::USES_NOT_USE,
			'is_result_display' => RegistrationsComponent::EXPRESSION_SHOW,
			'result_display_type' => RegistrationsComponent::RESULT_DISPLAY_TYPE_BAR_CHART
		);
		return array(
			array($expect),
		);
	}

}
