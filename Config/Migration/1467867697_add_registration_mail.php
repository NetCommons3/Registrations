<?php
class AddRegistrationMail extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_registration_mail';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'registration_questions' => array(
					'question_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '質問表示順'),
					'question_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '質問タイプ | 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
'),
					'is_choice_random' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 質問タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない'),
				),
			),
			'create_field' => array(
				'registrations' => array(
					'reply_to' => array('type' => 'string', 'null' => true, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'is_answer_mail_send'),
					'is_regist_user_send' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'reply_to'),
					'registration_mail_subject' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'is_regist_user_send'),
					'registration_mail_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'registration_mail_subject'),
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
			),
			'drop_field' => array(
				'registrations' => array('reply_to', 'is_regist_user_send', 'registration_mail_subject', 'registration_mail_body'),
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
