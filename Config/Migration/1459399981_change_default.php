<?php
class ChangeDefault extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_default';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'registrations' => array(
					'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '非会員の回答を許可するか | 0:許可しない | 1:許可する'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'registrations' => array(
					'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '非会員の回答を許可するか | 0:許可しない | 1:許可する'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
