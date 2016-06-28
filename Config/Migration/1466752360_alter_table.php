<?php
class AlterTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'registration_questions' => array(
					'question_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '項目表示順'),
					'question_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '項目タイプ | 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
'),
					'is_choice_random' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 項目タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない'),
				),
				'registrations' => array(
					'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '非会員の登録を許可するか | 0:許可しない | 1:許可する'),
					'is_repeat_allow' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
				),
			),
			'create_field' => array(
				'registrations' => array(
					'is_limit_number' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'is_page_random'),
					'limit_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'is_limit_number'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'registration_questions' => array(
					'question_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '項目表示順'),
					'question_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '項目タイプ | 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
'),
					'is_choice_random' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 項目タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない'),
				),
				'registrations' => array(
					'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '非会員の登録を許可するか | 0:許可しない | 1:許可する'),
					'is_repeat_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
				),
			),
			'drop_field' => array(
				'registrations' => array('is_limit_number', 'limit_number'),
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
