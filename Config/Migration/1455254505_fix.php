<?php
class Fix extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'fix';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'registration_answer_summaries' => array(
					'answer_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '回答状態 1ページずつ表示するような登録フォームの場合、途中状態か否か | 0:回答未完了 | 1:回答完了'),
					'test_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'テスト時の回答かどうか 0:本番回答 | 1:テスト時回答'),
					'answer_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '回答回数　ログインして回答している人物の場合に限定して回答回数をカウントする'),
					'answer_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '回答完了の時刻　ページわけされている場合、insert_timeは回答開始時刻となるため、完了時刻を設ける'),
					'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録フォーム回答した時のセッション値を保存します。', 'charset' => 'utf8'),
					'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ログイン後、登録フォームに回答した人のusersテーブルのid。未ログインの場合NULL'),
				),
				'registration_answers' => array(
					'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '回答した文字列を設定する
選択肢、リストなどの選ぶだけの場合は、選択肢のid値:ラベルを入れる

選択肢タイプで「その他」を選んだ場合は、入力されたテキストは、ここではなく、other_answer_valueに入れる。

複数選択肢
これらの場合は、(id値):(ラベル)を|つなぎで並べる。
', 'charset' => 'utf8'),
				),
				'registration_frame_settings' => array(
					'sort_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '表示並び順 0:新着順 1:回答期間順（降順） 2:登録フォームステータス順（昇順） 3:タイトル順（昇順）'),
				),
				'registration_questions' => array(
					'is_require' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '回答必須フラグ | 0:不要 | 1:必須'),
					'is_skip' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '登録フォーム回答のスキップ有無  0:スキップ 無し  1:スキップ有り'),
					'is_jump' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '登録フォーム回答の分岐'),
				),
				'registrations' => array(
					'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '非会員の回答を許可するか | 0:許可しない | 1:許可する'),
					'is_anonymity' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '会員回答であっても匿名扱いとするか否か | 0:非匿名 | 1:匿名'),
					'is_key_pass_use' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'キーフレーズによる回答ガードを設けるか | 0:キーフレーズガードは用いない | 1:キーフレーズガードを用いる'),
					'is_repeat_allow' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
					'total_show_timing' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => '集計結果を表示するタイミング | 0:登録フォーム回答後、すぐ | 1:期間設定'),
					'is_answer_mail_send' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '登録フォーム回答時に編集者、編集長にメールで知らせるか否か | 0:知らせない| 1:知らせる
'),
				),
			),
			'create_field' => array(
				'registrations' => array(
					'is_limit_number' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'modified'),
					'limit_number' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'is_limit_number'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'registration_answer_summaries' => array(
					'answer_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '登録状態 1ページずつ表示するような登録フォームの場合、途中状態か否か | 0:登録未完了 | 1:登録完了'),
					'test_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'テスト時の登録かどうか 0:本番登録 | 1:テスト時登録'),
					'answer_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録回数　ログインして登録している人物の場合に限定して登録回数をカウントする'),
					'answer_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録完了の時刻　ページわけされている場合、insert_timeは登録開始時刻となるため、完了時刻を設ける'),
					'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録フォーム登録した時のセッション値を保存します。', 'charset' => 'utf8'),
					'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ログイン後、登録フォームに登録した人のusersテーブルのid。未ログインの場合NULL'),
				),
				'registration_answers' => array(
					'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録した文字列を設定する
選択肢、リストなどの選ぶだけの場合は、選択肢のid値:ラベルを入れる

選択肢タイプで「その他」を選んだ場合は、入力されたテキストは、ここではなく、other_answer_valueに入れる。

複数選択肢
これらの場合は、(id値):(ラベル)を|つなぎで並べる。
', 'charset' => 'utf8'),
				),
				'registration_frame_settings' => array(
					'sort_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '表示並び順 0:新着順 1:登録期間順（降順） 2:登録フォームステータス順（昇順） 3:タイトル順（昇順）'),
				),
				'registration_questions' => array(
					'is_require' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '登録必須フラグ | 0:不要 | 1:必須'),
					'is_skip' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '登録フォーム登録のスキップ有無  0:スキップ 無し  1:スキップ有り'),
					'is_jump' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '登録フォーム登録の分岐'),
				),
				'registrations' => array(
					'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '非会員の登録を許可するか | 0:許可しない | 1:許可する'),
					'is_anonymity' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '会員登録であっても匿名扱いとするか否か | 0:非匿名 | 1:匿名'),
					'is_key_pass_use' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'キーフレーズによる登録ガードを設けるか | 0:キーフレーズガードは用いない | 1:キーフレーズガードを用いる'),
					'is_repeat_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
					'total_show_timing' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => '集計結果を表示するタイミング | 0:登録フォーム登録後、すぐ | 1:期間設定'),
					'is_answer_mail_send' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '登録フォーム登録時に編集者、編集長にメールで知らせるか否か | 0:知らせない| 1:知らせる
'),
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
