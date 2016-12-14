<?php
class AddFieldsForM17n extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_fields_for_m17n';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'registration_choices' => array(
					'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか', 'after' => 'language_id'),
					'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか', 'after' => 'is_origin'),
				),
				'registration_pages' => array(
					'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか', 'after' => 'language_id'),
					'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか', 'after' => 'is_origin'),
				),
				'registration_questions' => array(
					'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか', 'after' => 'language_id'),
					'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか', 'after' => 'is_origin'),
				),
				'registrations' => array(
					'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか', 'after' => 'language_id'),
					'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか', 'after' => 'is_origin'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'registration_choices' => array('is_origin', 'is_translation'),
				'registration_pages' => array('is_origin', 'is_translation'),
				'registration_questions' => array('is_origin', 'is_translation'),
				'registrations' => array('is_origin', 'is_translation'),
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
