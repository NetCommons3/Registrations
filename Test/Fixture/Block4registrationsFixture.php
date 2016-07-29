<?php
/**
 * Block4RegistrationsFixture.php
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class Block4RegistrationsFixture
 */
class Block4registrationsFixture extends BlockFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Block';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'blocks';

/**
 * Records
 *
 * @var array
 */
	public $addRecords = array(
		// @uses RegistrationAnswersControllerPostTest::testKeyAuthPost()
		// @uses RegistrationAnswersControllerPostTest::testKeyAuthPostNG()
		[
			'id' => 11,
			'language_id' => 2,
			'room_id' => 1,
			'key' => 'block_11',
			'name' => 'Block name 11',
			'public_type' => 1,
		],
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPost()
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPostNG()
		[
			'id' => 12,
			'language_id' => 2,
			'room_id' => 1,
			'key' => 'block_12',
			'name' => 'Block name 12',
			'public_type' => 1,
		],
		// registration_4用
		[
			'id' => 13,
			'language_id' => 1,
			'room_id' => 1,
			'key' => 'block_12',
			'name' => 'Block name 13',
			'public_type' => 1,
		],
		[
			'id' => 14,
			'language_id' => 2,
			'room_id' => 1,
			'key' => 'block_12',
			'name' => 'Block name 14',
			'public_type' => 1,
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
				'id' => $id,
				'language_id' => 1,
				'room_id' => 1,
				'key' => 'block_' . $id + 1,
				'name' => 'Block name ' . $id + 1,
				'public_type' => 1,

			];
			$this->records[] = [
				'id' => $id + 1,
				'language_id' => 2,
				'room_id' => 1,
				'key' => 'block_' . $id,
				'name' => 'Block name ' . $id,
				'public_type' => 1,
			];
		}
		// 継承元のrecordsとこのFixtureのaddRecordsをマージ。
		//foreach ($this->addRecords as $record) {
		//	$this->records[] = $record;
		//}
		parent::init();
	}

}
