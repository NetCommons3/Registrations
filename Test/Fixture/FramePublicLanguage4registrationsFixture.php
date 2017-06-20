<?php
/**
 * FrameRegistrationsFixture.php
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('FramePublicLanguageFixture', 'Frames.Test/Fixture');

/**
 * Class Frame4RegistrationsFixture
 */
class FramePublicLanguage4registrationsFixture extends FramePublicLanguageFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'FramePublicLanguage';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'frame_public_languages';

/**
 * Records
 *
 * @uses RegistrationAnswersControllerPostTest::testKeyAuthPost()
 * @var array
 */
	public $addRecords = array(
		// @see RegistrationAnswersControllerPostTest::testKeyAuthPost()
		// @uses RegistrationAnswersControllerPostTest::testKeyAuthPostNG()
		[
			'frame_id' => 19,
			'language_id' => '0',
			'is_public' => '1',
		],
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPost()
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPostNG()
		[
			// 画像認証テスト用
			'frame_id' => 20,
			'language_id' => '0',
			'is_public' => '1',
		],
		[
			// registration_4用
			'frame_id' => 21,
			'language_id' => '0',
			'is_public' => '1',
		],
	);

/**
 * 継承元のrecordsに追加する
 *
 * @return void
 */
	public function init() {
		for ($id = 11; $id <= 52; $id = $id + 2) {
			$this->records[] = [
				'frame_id' => $id + 8, // id19から
				'language_id' => '0',
				'is_public' => '1',
			];
			$this->records[] = [
				'frame_id' => $id + 9, // id20から
				'language_id' => '0',
				'is_public' => '1',
			];
		}

		// 継承元のrecordsとこのFixtureのaddRecordsをマージ。
		//foreach ($this->addRecords as $record) {
		//	$this->records[] = $record;
		//}
		parent::init();
	}
}
