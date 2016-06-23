<?php
/**
 * RegistrationAnswerSummaryFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for RegistrationAnswerSummaryFixture
 */
class RegistrationAnswerSummaryFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
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
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_12',
			'user_id' => 3,
			'created_user' => 3
		),
		array(
			'id' => 2,
			'answer_status' => '1',	// 確認前
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_12',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 3,
			'answer_status' => '0',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_2',
			'user_id' => 3,
			'created_user' => 3
		),
		array(
			'id' => 4,
			'answer_status' => '0',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_4',
			'user_id' => 3,
			'created_user' => 3
		),
		array(
			'id' => 5,
			'answer_status' => '0',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_6',
			'user_id' => 3,
			'created_user' => 3
		),
		array(
			'id' => 6,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_6',
			'user_id' => 4,
			'created_user' => 4
		),
		array(
			'id' => 7,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_8',
			'user_id' => 4,
			'created_user' => 4
		),
		array(
			'id' => 8,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_20',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 9,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_22',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 10,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_24',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 11,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_4',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 12,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'registration_key' => 'registration_12',
			'user_id' => '',
			'created_user' => ''
		),
	);
}
