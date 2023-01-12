<?php
/**
 * Block4RegistrationsFixture.php
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('BlocksLanguageFixture', 'Blocks.Test/Fixture');

/**
 * Class BlocksLanguage4RegistrationsFixture
 */
class BlocksLanguage4registrationsFixture extends BlocksLanguageFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'BlocksLanguage';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'blocks_languages';

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
			'block_id' => 11,
			'name' => 'Block name 11',
		],
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPost()
		// @uses RegistrationAnswersControllerPostTest::testImgAuthPostNG()
		[
			'id' => 12,
			'language_id' => 2,
			'block_id' => 12,
			'name' => 'Block name 12',
		],
		// registration_4用
		[
			'id' => 13,
			'language_id' => 1,
			'block_id' => 13,
			'name' => 'Block name 13',
		],
		[
			'id' => 14,
			'language_id' => 2,
			'block_id' => 14,
			'name' => 'Block name 14',
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
				'id' => $id,
				'language_id' => 1,
				'block_id' => $id,
				'name' => 'Block name ' . (string)($id + 1),
			];
			$this->records[] = [
				'id' => $id + 1,
				'language_id' => 2,
				'block_id' => $id + 1,
				'name' => 'Block name ' . (string)$id,
			];
		}
		// 継承元のrecordsとこのFixtureのaddRecordsをマージ。
		//foreach ($this->addRecords as $record) {
		//	$this->records[] = $record;
		//}
		parent::init();
	}

}
