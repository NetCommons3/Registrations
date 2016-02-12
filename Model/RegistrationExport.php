<?php
/**
 * RegistrationExport Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');
App::uses('WysIsWygDownloader', 'Registrations.Utility');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Summary for Registration Model
 */
class RegistrationExport extends RegistrationsAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'registrations';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'AuthorizationKeys.AuthorizationKey',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * getExportData
 *
 * @param string $registrationKey 登録フォームキー
 * @return array RegistrationData for Export
 */
	public function getExportData($registrationKey) {
		// 登録フォームデータをjsonにして記述した内容を含むZIPファイルを作成する
		$zipData = array();

		// バージョン情報を取得するためComposer情報を得る
		$Plugin = ClassRegistry::init('Plugins.Plugin');
		$composer = $Plugin->getComposer('netcommons/registrations');
		// 最初のデータは登録フォームプラグインのバージョン
		$zipData['version'] = $composer['version'];

		// 言語数分
		$Language = ClassRegistry::init('Languages.Language');
		$languages = $Language->find('all', array(
			'recursive' => -1
		));
		$Registration = ClassRegistry::init('Registrations.Registration');
		$registrations = array();
		foreach ($languages as $lang) {
			// 指定の登録フォームデータを取得
			$registration = $Registration->find('first', array(
				'conditions' => array(
					'Registration.key' => $registrationKey,
					'Registration.is_active' => true,
					'Registration.is_latest' => true,
					'Registration.language_id' => $lang['Language']['id']
				),
				'recursive' => 0
			));
			// 指定の言語データがない場合もあることを想定
			if (empty($registration)) {
				continue;
			}
			$registration = Hash::remove($registration, 'Block');
			$registration = Hash::remove($registration, 'TrackableCreator');
			$registration = Hash::remove($registration, 'TrackableUpdater');
			$Registration->clearRegistrationId($registration);
			$registrations[] = $registration;
		}
		$zipData['Registrations'] = $registrations;
		return $zipData;
	}
/**
 * putToZip
 *
 * @param ZipDownloader $zipFile ZIPファイルオブジェクト
 * @param array $zipData zip data
 * @return void
 */
	public function putToZip($zipFile, $zipData) {
		$this->Registration = ClassRegistry::init('Registrations.Registration');
		$this->RegistrationPage = ClassRegistry::init('Registrations.RegistrationPage');
		$this->RegistrationQuestion = ClassRegistry::init('Registrations.RegistrationQuestion');

		$wysiswyg = new WysIsWygDownloader();

		// 登録フォームデータの中でもWYSISWYGデータのものについては
		// フォルダ別に確保(フォルダの中にZIPがある）
		$flatRegistration = Hash::flatten($zipData);
		foreach ($flatRegistration as $key => &$value) {
			$model = null;
			if (strpos($key, 'RegistrationQuestion.') !== false) {
				$model = $this->RegistrationQuestion;
			} elseif (strpos($key, 'RegistrationPage.') !== false) {
				$model = $this->RegistrationPage;
			} elseif (strpos($key, 'Registration.') !== false) {
				$model = $this->Registration;
			}
			if (!$model) {
				continue;
			}
			$columnName = substr($key, strrpos($key, '.') + 1);
			if ($model->hasField($columnName)) {
				if ($model->getColumnType($columnName) == 'text') {
					$wysiswygZipFile = $wysiswyg->createWysIsWygZIP($model->alias . '.' . $columnName, $value);
					$wysiswygFileName = $key . '.zip';
					$zipFile->addFile($wysiswygZipFile, $wysiswygFileName);
					$value = $wysiswygFileName;
				}
			}
		}
		$registration = Hash::expand($flatRegistration);
		// jsonデータにして書き込み
		$zipFile->addFromString(RegistrationsComponent::REGISTRATION_JSON_FILENAME, json_encode($registration));
	}
}
