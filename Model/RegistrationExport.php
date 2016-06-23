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
App::uses('WysiwygZip', 'Wysiwyg.Utility');

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
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Registration' => 'Registrations.Registration',
			'RegistrationPage' => 'Registrations.RegistrationPage',
			'RegistrationQuestion' => 'Registrations.RegistrationQuestion',
		]);
	}

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
		$Plugin = ClassRegistry::init('PluginManager.Plugin');
		$composer = $Plugin->getComposer('netcommons/registrations');
		// 最初のデータは登録フォームプラグインのバージョン
		$zipData['version'] = $composer['version'];

		// 言語数分
		$Language = ClassRegistry::init('M17n.Language');
		$languages = $Language->find('all', array(
			'recursive' => -1
		));
		$registrations = array();
		foreach ($languages as $lang) {
			// 指定の登録フォームデータを取得
			$registration = $this->Registration->find('first', array(
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
			$this->Registration->clearRegistrationId($registration);
			$registrations[] = $registration;
		}
		// Exportするデータが一つも見つからないって
		if (empty($registration)) {
			return false;
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
		$wysiswyg = new WysiwygZip();

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
					$wysiswygZipFile = $wysiswyg->createWysiwygZip($value, $model->alias . '.' . $columnName);
					$wysiswygFileName = $key . '.zip';
					$zipFile->addFile($wysiswygZipFile, $wysiswygFileName);
					$value = $wysiswygFileName;
				}
			}
		}
		$registration = Hash::expand($flatRegistration);
		// jsonデータにして書き込み
		$zipFile->addFromString(
			RegistrationsComponent::REGISTRATION_JSON_FILENAME,
			json_encode($registration));
	}
}
