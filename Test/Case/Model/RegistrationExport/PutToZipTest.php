<?php
/**
 * RegistrationExport::putToZip()のテスト
 *
 * @property RegistrationExport $RegistrationExport
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');
App::uses('ZipDownloader', 'TestFiles.Utility');
App::uses('RegistrationFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationPageFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationQuestionFixture', 'Registrations.Test/Fixture');
App::uses('RegistrationChoiceFixture', 'Registrations.Test/Fixture');

/**
 * RegistrationExport::putToZip()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\RegistrationExport
 */
class RegistrationExportPutToZipTest extends NetCommonsGetTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'registrations';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.m17n.language',
		'plugin.registrations.registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_setting',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'RegistrationExport';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'putToZip';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Registrations', 'TestFiles');
	}

/**
 * putToZip
 *
 * @param string $registrationKey 収集対象の登録フォームキー
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testPutToZip($registrationKey, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		App::uses('ZipDownloader', 'TestFiles.Utility');

		$langCount = 2;	// 2 = 言語数
		$registrationId = intval($expected['registrationId']);

		$data = $this->$model->getExportData($registrationKey);
		$zipFile = new ZipDownloader();
		//テスト実行
		$this->$model->$method($zipFile, $data);

		//チェック
		// WysiswygエディタのZIPファイルが言語数分あるか
		$checkFiles = array(
			'total_comment',
			'thanks_content',
			'open_mail_body',
		);
		$addFiles = Hash::expand(array_flip($zipFile->addFiles));

		// 登録フォーム本体のWysiswyg文章部分が言語数分あるかチェック
		foreach ($checkFiles as $file) {
			$records = Hash::extract($addFiles, 'Registrations.{n}.Registration.' . $file . '.zip');
			$this->assertEqual(count($records), $langCount);
		}
		// 質問文が言語数×質問数文あるかチェック
		$records = Hash::extract($addFiles, 'Registrations.{n}.RegistrationPage.{n}.RegistrationQuestion.{n}.description.zip');
		$this->assertEqual(count($records), $langCount * $expected['questionCount']);

		// ZIPファイルに追加されたJsonコードは登録フォームの構造と同じか
		$jsonRegistration = json_decode($zipFile->addStrings[RegistrationsComponent::REGISTRATION_JSON_FILENAME], true);
		$orgRegistration = $this->_getRegistration($registrationId);
		$this->assertTrue($this->_hasSameArray($orgRegistration, $jsonRegistration['Registrations'][1]));
	}
/**
 * _hasSameArray
 *
 * @param array $part 期待値
 * @param array $hole 実際のデータ
 * @return bool
 */
	protected function _hasSameArray($part, $hole) {
		$flatPart = Hash::flatten($part);
		$flatHole = Hash::flatten($hole);
		foreach ($flatPart as $key => $val) {
			if (preg_match('/\.(id|key|total_comment|thanks_content|open_mail_body|description|created_user|created|modified_user|modified)$/', $key) == 1) {
				continue;
			}
			if (array_key_exists($key, $flatHole)) {
				$find = $flatHole[$key];
				if ($find != $val) {
					return false;
				}
			} else {
				return false;
			}
		}
		return true;
	}

/**
 * getExportDataのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		return array(
			// 登録フォームキー,ページ数,質問数
			array('registration_6', array(
				'pageCount' => 1,
				'questionCount' => 1,
				'registrationId' => 6)),
		);
	}

/**
 * _getRegistration
 *
 * @param int $id 質問ID
 * @return array
 */
	protected function _getRegistration($id) {
		$fixtureRegistration = new RegistrationFixture();
		$fixturePage = new RegistrationPageFixture();
		$fixtureQuestion = new RegistrationQuestionFixture();
		$fixtureChoice = new RegistrationChoiceFixture();

		$data = array();
		$rec = Hash::extract($fixtureRegistration->records, '{n}[id=' . $id . ']');
		$data['Registration'] = $rec[0];

		$rec = Hash::extract($fixturePage->records, '{n}[registration_id=' . $data['Registration']['id'] . ']');
		$rec = Hash::extract($rec, '{n}[language_id=2]');
		$data['RegistrationPage'] = $rec;

		foreach ($data['RegistrationPage'] as &$page) {
			$pageId = $page['id'];

			$rec = Hash::extract($fixtureQuestion->records, '{n}[registration_page_id=' . $pageId . ']');
			$rec = Hash::extract($rec, '{n}[language_id=2]');
			$page['RegistrationQuestion'] = $rec;
			$questionId = $rec[0]['id'];

			$rec = Hash::extract($fixtureChoice->records, '{n}[registration_question_id=' . $questionId . ']');
			if ($rec) {
				$page['RegistrationQuestion'][0]['RegistrationChoice'] = $rec;
			}
		}
		return $data;
	}
}
