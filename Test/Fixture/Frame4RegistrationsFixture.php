<?php
/**
 * Block4RegistrationsFixture.php
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */


class Block4RegistrationsFixture extends BlockFixture {

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
	public $records = array(
		[
			'id' => 11,
			'language_id' => 2,
			'room_id' => 1,
			'key' => 'block_11',
			'name' => 'Block name 11',
			'public_type' => 1,
		],
	);
}
