<?php
/**
 * FrameRegistrationsFixture.php
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */


class Frame4RegistrationsFixture extends FrameFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Frame';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'frames';

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
			'room_id' => 1,
			'box_id' => '3',
			'plugin_key' => 'test_plugin',
			'block_id' => 11,
			'key' => 'frame_19',
			'name' => 'frame_19',
		],
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPost()
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPostNG()
		[
			// 画像認証テスト用
			'id' => 20,
			'language_id' => 2,
			'room_id' => 1,
			'box_id' => '3',
			'plugin_key' => 'test_plugin',
			'block_id' => 12,
			'key' => 'frame_20',
			'name' => 'frame_20',
		],
		[
			// registration_4用
			'id' => 21,
			'language_id' => 2,
			'room_id' => 1,
			'plugin_key' => 'test_plugin',
			'box_id' => '3',
			'block_id' => 13,
			'key' => 'frame_21',
			'name' => 'frame_21',
		],
	);

/**
 * 継承元のrecordsに追加する
 *
 * @return void
 */
	public function init() {
		// 継承元のrecordsとこのFixtureのaddRecordsをマージ。
		foreach ($this->addRecords as $record) {
			$this->records[] = $record;
		}
		parent::init();
	}
}
