<?php
/**
 * Schema file
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
/**
 * Schema file
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registration\Config\Schema
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class RegistrationsSchema extends CakeSchema {

/**
 * Database connection
 *
 * @var string
 */
	public $connection = 'master';

/**
 * before
 *
 * @param array $event event
 * @return bool
 */
	public function before($event = array()) {
		return true;
	}

/**
 * after
 *
 * @param array $event event
 * @return void
 */
	public function after($event = array()) {
	}

/**
 * registration_answer_summaries table
 *
 * @var array
 */
	public $registration_answer_summaries = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'serial_number' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true),
		'answer_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '登録状態 1ページずつ表示するような登録フォームの場合、途中状態か否か | 0:登録未完了 | 1:登録完了'),
		'test_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'テスト時の登録かどうか 0:本番登録 | 1:テスト時登録'),
		'answer_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録回数　ログインして登録している人物の場合に限定して登録回数をカウントする'),
		'answer_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録完了の時刻　ページわけされている場合、insert_timeは登録開始時刻となるため、完了時刻を設ける'),
		'registration_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録フォーム登録した時のセッション値を保存します。', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ログイン後、登録フォームに登録した人のusersテーブルのid。未ログインの場合NULL'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * registration_answers table
 *
 * @var array
 */
	public $registration_answers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'matrix_choice_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録した文字列を設定する
選択肢、リストなどの選ぶだけの場合は、選択肢のid値:ラベルを入れる

選択肢タイプで「その他」を選んだ場合は、入力されたテキストは、ここではなく、other_answer_valueに入れる。

複数選択肢
これらの場合は、(id値):(ラベル)を|つなぎで並べる。
', 'charset' => 'utf8'),
		'other_answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択しタイプで「その他」を選んだ場合、入力されたテキストはここに入る。', 'charset' => 'utf8'),
		'registration_answer_summary_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'registration_question_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_registration_answer_registration_answer_summary1_idx' => array('column' => 'registration_answer_summary_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * registration_choices table
 *
 * @var array
 */
	public $registration_choices = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか'),
		'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか'),
		'is_original_copy' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'オリジナルのコピー。言語を新たに追加したときに使用する'),
		'matrix_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'マトリックスタイプの場合の行列区分 | 0:行 | 1:列'),
		'other_choice_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => 'その他欄か否か、また、その他欄の入力エリアタイプ | 0:その他欄でない | 1:テキストタイプを伴ったその他欄 | 2:テキストエリアタイプを伴ったその他欄

'),
		'choice_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '選択肢並び順'),
		'choice_label' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '\'選択肢ラベル\'', 'charset' => 'utf8'),
		'choice_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択肢の値　デフォルトでidと同じ値が入る（将来、選択肢の値を任意に設定して重みアンケができるよう）', 'charset' => 'utf8'),
		'skip_page_sequence' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'registrationsのskip_flagがスキップ有りの時、スキップ先のページ'),
		'jump_route_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'registration_questionsのis_jumpが有りのとき、分岐先のルート'),
		'graph_color' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 16, 'collate' => 'utf8_general_ci', 'comment' => 'グラフ描画時の色', 'charset' => 'utf8'),
		'registration_question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_registration_choice_registration_question1_idx' => array('column' => 'registration_question_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * registration_pages table
 *
 * @var array
 */
	public $registration_pages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか'),
		'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか'),
		'is_original_copy' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'オリジナルのコピー。言語を新たに追加したときに使用する'),
		'registration_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'page_title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ページ名', 'charset' => 'utf8'),
		'route_number' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'page_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'ページ表示順'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_registration_pages_registrations1_idx' => array('column' => 'registration_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * registration_questions table
 *
 * @var array
 */
	public $registration_questions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか'),
		'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか'),
		'is_original_copy' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'オリジナルのコピー。言語を新たに追加したときに使用する'),
		'question_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '質問表示順'),
		'question_value' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'question_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '質問タイプ | 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_require' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '登録必須フラグ | 0:不要 | 1:必須'),
		'question_type_option' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '1: 数値 | 2:日付(未実装) | 3:時刻(未実装) | 4:メール(未実装) | 5:URL(未実装) | 6:電話番号(未実装) | HTML５チェックで将来的に実装されそうなものに順次対応'),
		'is_choice_random' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 質問タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない'),
		'is_choice_horizon' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_skip' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '登録フォーム登録のスキップ有無  0:スキップ 無し  1:スキップ有り'),
		'is_jump' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '登録フォーム登録の分岐'),
		'is_range' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '範囲設定しているか否か'),
		'min' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => '最小値　question_typeがテキストで数値タイプのときのみ有効 ', 'charset' => 'utf8'),
		'max' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => '最大値　question_typeがテキストで数値タイプのときのみ有効 ', 'charset' => 'utf8'),
		'is_result_display' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '集計結果表示をするか否か | 0:しない | 1:する'),
		'result_display_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '表示形式デファイン値が｜区切りで保存される | 0:棒グラフ（マトリクスのときは自動的に積み上げ棒グラフ） | 1:円グラフ | 2:表
'),
		'registration_page_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_registration_question_registration_page1_idx' => array('column' => 'registration_page_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * registrations table
 *
 * @var array
 */
	public $registrations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか'),
		'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか'),
		'is_original_copy' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'オリジナルのコピー。言語を新たに追加したときに使用する'),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開中データか否か'),
		'is_latest' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '最新編集データであるか否か'),
		'block_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '公開状況  1:公開中、2:公開申請中、3:下書き中、4:差し戻し'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録フォームタイトル', 'charset' => 'utf8'),
		'title_icon' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sub_title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録フォームサブタイトル', 'charset' => 'utf8'),
		'answer_timing' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false),
		'answer_start_period' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'answer_end_period' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '非会員の登録を許可するか | 0:許可しない | 1:許可する'),
		'is_anonymity' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '会員登録であっても匿名扱いとするか否か | 0:非匿名 | 1:匿名'),
		'is_key_pass_use' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'キーフレーズによる登録ガードを設けるか | 0:キーフレーズガードは用いない | 1:キーフレーズガードを用いる'),
		'is_repeat_allow' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'is_total_show' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '集計結果を表示するか否か | 0:表示しない | 1:表示する'),
		'total_show_timing' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => '集計結果を表示するタイミング | 0:登録フォーム登録後、すぐ | 1:期間設定'),
		'total_show_start_period' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'total_comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '集計表示ページの先頭に書くメッセージコメント', 'charset' => 'utf8'),
		'is_image_authentication' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'SPAMガード項目を表示するか否か | 0:表示しない | 1:表示する'),
		'thanks_content' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録フォーム最後に表示するお礼メッセージ', 'charset' => 'utf8'),
		'is_open_mail_send' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '登録フォーム開始メールを送信するか(現在未使用) | 0:しない | 1:する'),
		'open_mail_subject' => array('type' => 'string', 'null' => true, 'default' => 'Registration to you has arrived', 'collate' => 'utf8_general_ci', 'comment' => '登録フォーム開始メールタイトル(現在未使用)', 'charset' => 'utf8'),
		'open_mail_body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '登録フォーム開始通知メール本文(現在未使用)', 'charset' => 'utf8'),
		'is_answer_mail_send' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '登録フォーム登録時に編集者、編集長にメールで知らせるか否か | 0:知らせない| 1:知らせる
'),
		'reply_to' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_regist_user_send' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'registration_mail_subject' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registration_mail_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_page_random' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'ページ表示順序ランダム化（※将来機能）
選択肢分岐機能との兼ね合いを考えなくてはならないため、現時点での機能盛り込みは見送る'),
		'is_limit_number' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'limit_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'import_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'export_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_registrations_blocks1_idx' => array('column' => 'block_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
