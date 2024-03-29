<?php
/**
 * FrameRegistrationsFixture.php
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class Frame4RegistrationsFixture
 */
class Frame4registrationsFixture extends FrameFixture {

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
			'room_id' => '2',
			'box_id' => '3',
			'plugin_key' => 'test_plugin',
			'block_id' => 11,
			'key' => 'frame_19',
		],
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPost()
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPostNG()
		[
			// 画像認証テスト用
			'id' => 20,
			'room_id' => '2',
			'box_id' => '3',
			'plugin_key' => 'test_plugin',
			'block_id' => 12,
			'key' => 'frame_20',
		],
		[
			// registration_4用
			'id' => 21,
			'room_id' => '2',
			'plugin_key' => 'test_plugin',
			'box_id' => '3',
			'block_id' => 13,
			'key' => 'frame_21',
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
				'room_id' => '2',
				'plugin_key' => 'test_plugin',
				'box_id' => '3',
				'block_id' => $id,
				'key' => 'frame_' . (string)($id + 9),
			];
			$this->records[] = [
				'id' => $id + 9, // id20から
				'room_id' => '2',
				'plugin_key' => 'test_plugin',
				'box_id' => '3',
				'block_id' => $id + 1,
				'key' => 'frame_' . (string)($id + 8),
			];
		}

		// 継承元のrecordsとこのFixtureのaddRecordsをマージ。
		//foreach ($this->addRecords as $record) {
		//	$this->records[] = $record;
		//}
		parent::init();
	}
}
