<?php
/**
 * ValidateFileTest.php
 *
 * @author Japan Science and Technology Agency
 * @author National Institute of Informatics
 * @link http://researchmap.jp researchmap Project
 * @link http://www.netcommons.org NetCommons Project
 * @license http://researchmap.jp/public/terms-of-service/ researchmap license
 * @copyright Copyright 2017, researchmap Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * RegistrationsValidateFileTest
 */
final class RegistrationsValidateFileTest extends \NetCommonsModelTestCase {

/**
 * @var array fixtures プロパティなしだとNC3の各種Fixtureがセットされないので空でも宣言しておく。
 */
	public $fixtures = [];

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		// 常に新規インスタンスでテストしたいので最初に生成済みインスタンスを削除しておく。
		ClassRegistry::removeObject('RegistrationAnswer');
	}

/**
 * removeObject後に再度initすれば新しいインスタンスを取得できることを確認
 *
 * @return void
 */
	public function testRemoveObject() {
		$answerModel = ClassRegistry::init('Registrations.RegistrationAnswer');
		ClassRegistry::removeObject('RegistrationAnswer');
		$answerModel2 = ClassRegistry::init('Registrations.RegistrationAnswer');
		self::assertNotSame($answerModel, $answerModel2);
	}

/**
 * ファイル型でなければファイルのバリデーションはセットされない
 *
 * @return void
 */
	public function testNoFileValidationIfQuestionTypeNotFile() {
		/** @var RegistrationAnswer $answerModel */
		$answerModel = ClassRegistry::init('Registrations.RegistrationAnswer');
		$options = [
			'registration_answer_summary_id' => '1',
			'allAnswers' => null,
			'question' => [
				'question_type' => RegistrationsComponent::TYPE_TEXT,
				'question_value' => null,
			]
		];
		$answerModel->beforeValidate($options);
		$this->assertArrayNotHasKey('answer_value_file', $answerModel->validate);
	}

/**
 * ファイル型だとファイルのバリデーションがセットされる。
 *
 * @return void
 */
	public function testExistsFileValidationIfQuestionTypeIsFile() {
		/** @var RegistrationAnswer $answerModel */
		$answerModel = ClassRegistry::init('Registrations.RegistrationAnswer');
		$options = [
			'registration_answer_summary_id' => '1',
			'allAnswers' => null,
			'question' => [
				'question_type' => RegistrationsComponent::TYPE_FILE,
				'is_require' => false,
				'question_value' => null,
			]
		];
		$answerModel->beforeValidate($options);
		$this->assertArrayHasKey('answer_value_file', $answerModel->validate);
	}

/**
 * テキスト型をバリデーションの次にファイル型をバリデーションするときもファイル型バリデーションがある
 *
 * @return void
 */
	public function testExistsFileValidationIfAfterNotFileType() {
		/** @var RegistrationAnswer $answerModel */
		$answerModel = ClassRegistry::init('Registrations.RegistrationAnswer');
		// テキスト型の回答バリデーション組立
		$options = [
			'registration_answer_summary_id' => '1',
			'allAnswers' => null,
			'question' => [
				'question_type' => RegistrationsComponent::TYPE_TEXT,
				'question_value' => null,
			]
		];
		$answerModel->beforeValidate($options);

		// ファイル型の回答バリデーション組立
		$options = [
			'registration_answer_summary_id' => '1',
			'allAnswers' => null,
			'question' => [
				'question_type' => RegistrationsComponent::TYPE_FILE,
				'is_require' => false,
				'question_value' => null,
			]
		];
		$answerModel->beforeValidate($options);
		$this->assertArrayHasKey('answer_value_file', $answerModel->validate);
	}

/**
 * ファイル型をバリデーションの次にテキスト型をバリデーションするときにファイル型バリデーションがないこと
 *
 * @return void
 */
	public function testNotExistsFileValidationIfAfterFileType() {
		/** @var RegistrationAnswer $answerModel */
		$answerModel = ClassRegistry::init('Registrations.RegistrationAnswer');
		// ファイル型の回答バリデーション組立
		$options = [
			'registration_answer_summary_id' => '1',
			'allAnswers' => null,
			'question' => [
				'question_type' => RegistrationsComponent::TYPE_FILE,
				'is_require' => false,
				'question_value' => null,
			]
		];
		$answerModel->beforeValidate($options);

		// テキスト型の回答バリデーション組立
		$options = [
			'registration_answer_summary_id' => '1',
			'allAnswers' => null,
			'question' => [
				'question_type' => RegistrationsComponent::TYPE_TEXT,
				'question_value' => null,
			]
		];
		$answerModel->beforeValidate($options);
		$this->assertArrayNotHasKey('answer_value_file', $answerModel->validate);
	}

}