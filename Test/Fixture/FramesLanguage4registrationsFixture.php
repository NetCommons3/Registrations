<?php
/**
 * FrameRegistrationsFixture.php
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('FramesLanguageFixture', 'Blocks.Test/Fixture');

/**
 * Class Frame4RegistrationsFixture
 */
class FramesLanguage4registrationsFixture extends FramesLanguageFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'FramesLanguage';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'frames_languages';

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
			'id' => 19,
			'language_id' => 2,
			'frame_id' => 19,
			'name' => 'frame_19',
		],
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPost()
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPostNG()
		[
			// 画像認証テスト用
			'id' => 20,
			'frame_id' => 20,
			'name' => 'frame_20',
		],
		[
			// registration_4用
			'id' => 21,
			'language_id' => 2,
			'frame_id' => 21,
			'name' => 'frame_21',
		],
	);

/**
 * 継承元のrecordsに追加する
 *
 * @return void
 */
	public function init() {
		for ($id = 11; $id <= 56; $id = $id + 2) {
			$this->records[] = [
				'id' => $id + 8, // id19から
				'language_id' => 1,
				'frame_id' => $id + 8, // id19から
				'name' => 'frame_' . (string)($id + 9),
			];
			$this->records[] = [
				'id' => $id + 9, // id20から
				'language_id' => 2,
				'frame_id' => $id + 9, // id20から,
				'name' => 'frame_' . (string)($id + 8),
			];
		}

		// 継承元のrecordsとこのFixtureのaddRecordsをマージ。
		//foreach ($this->addRecords as $record) {
		//	$this->records[] = $record;
		//}
		parent::init();
	}
}
