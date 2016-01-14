<?php
/**
 * RegistrationChoice::saveRegistrationChoice()のテスト
 *
 * @property RegistrationChoice $RegistrationChoice
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('RegistrationsSaveTest', 'Registrations.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationChoice::saveRegistrationChoice()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationChoice
 */
class RegistrationSaveRegistrationChoiceTest extends RegistrationsSaveTest {

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
	protected $_modelName = 'RegistrationChoice';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveRegistrationChoice';

/**
 * テストDataの取得
 *
 * @param string $faqKey faqKey
 * @return array
 */
	private function __getData() {
		$data = array(
			'RegistrationChoice' => array(array(
				'language_id' => '2',
				'matrix_type' => '0',
				'other_choice_type' => '0',
				'choice_sequence' => '0',
				'choice_label' => 'choice1',
				'choice_value' => 'choice1val',
				'skip_page_sequence' => null,
				'jump_route_number' => null,
				'graph_color' => '#ff0000',
				'registration_question_id' => '2',
			))
		);
		return $data;
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
		return array(
			array($this->__getData()), //新規
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
		return array(
			array($this->__getData(), 'Registrations.RegistrationChoice', 'save'),
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
		$options = array(
			'choiceIndex' => 0,
			'isSkip' => 0,
		);
		return array(
			array($this->__getData(), $options, 'Registrations.RegistrationChoice'),
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
			'choiceIndex' => 0,
			'isSkip' => 0,
			'pageIndex' => 0,
			'maxPageIndex' => 0,
		);
		$skipOptions = array(
			'choiceIndex' => 0,
			'isSkip' => 1,
			'pageIndex' => 0,
			'maxPageIndex' => 0,
		);
		$data = $this->__getData();
		return array(
			array($data, $options, 'choice_label', '',
				__d('registrations', 'Please input choice text.')),
			array($data, $options, 'choice_label', 'has|data:abc',
				__d('registrations', 'You can not use the character of |, : for choice text ')),
			array($data, $options, 'other_choice_type', 'abc',
				__d('net_commons', 'Invalid request.')),
			array($data, $options, 'choice_sequence', '1',
				__d('registrations', 'choice sequence is illegal.')),
			array($data, $options, 'graph_color', 'avvv1',
				__d('registrations', 'First character is "#". And input the hexadecimal numbers by six digits.')),
			array($data, $skipOptions, 'skip_page_sequence', '9',
				__d('registrations', 'Invalid skip page. page does not exist.')),
			array($data, $skipOptions, 'skip_page_sequence', '0',
				__d('registrations', 'Invalid skip page. Please set forward page.')),
			array($data, $skipOptions, 'skip_page_sequence', null,
				__d('registrations', 'Invalid skip page. page does not exist.')),
		);
	}

}
