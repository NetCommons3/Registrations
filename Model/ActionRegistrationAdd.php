<?php
/**
 * ActionRegistrationAdd Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');
App::uses('TemporaryUploadFile', 'Files.Utility');
App::uses('UnZip', 'Files.Utility');
App::uses('WysiwygZip', 'Wysiwyg.Utility');

/**
 * Summary for ActionRegistrationAdd Model
 */
class ActionRegistrationAdd extends RegistrationsAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'registrations';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$this->validate = ValidateMerge::merge($this->validate, array(
			'create_option' => array(
				'rule' => array(
					'inList', array(
						RegistrationsComponent::REGISTRATION_CREATE_OPT_NEW,
						RegistrationsComponent::REGISTRATION_CREATE_OPT_REUSE,
						RegistrationsComponent::REGISTRATION_CREATE_OPT_TEMPLATE)),
				'message' => __d('registrations', 'Please choose create option.'),
				'required' => true
			),
			'title' => array(
				'rule' => array(
					'requireWhen',
					'create_option',
					RegistrationsComponent::REGISTRATION_CREATE_OPT_NEW),
				'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('registrations', 'Title')),
				'required' => false,
			),
			'past_registration_id' => array(
				'requireWhen' => array(
					'rule' => array(
						'requireWhen',
						'create_option',
						RegistrationsComponent::REGISTRATION_CREATE_OPT_REUSE),
					'message' => __d('registrations', 'Please select past registration.'),
				),
				'checkPastRegistration' => array(
					'rule' => array('checkPastRegistration'),
					'message' => __d('registrations', 'Please select past registration.'),
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * createRegistration
 * 登録フォームデータを作成する
 *
 * @param array $data 作成する登録フォームデータ
 * @return array|bool
 */
	public function createRegistration($data) {
		// 渡されたRegistrationデータを自Modelデータとする
		$this->set($data);
		// データチェック
		if ($this->validates()) {
			// Postデータの内容に問題がない場合は、そのデータをもとに新しい登録フォームデータを作成
			$registration = $this->getNewRegistration();
			return $registration;
		} else {
			return false;
		}
	}

/**
 * requireWhen
 *
 * @param mixed $check チェック対象入力データ
 * @param string $sourceField チェック対象フィールド名
 * @param mix $sourceValue チェック値
 * @return bool
 */
	public function requireWhen($check, $sourceField, $sourceValue) {
		// チェックすべきかどうかの判定データが、指定の状態かチェック
		if ($this->data['ActionRegistrationAdd'][$sourceField] != $sourceValue) {
			// 指定状態でなければ問題なし
			return true;
		}
		// 指定の状態であれば、チェック対象データがちゃんと入っているか確認する
		// Validation::notBlank($check);
		if (! array_shift($check)) {
			// 指定のデータが指定の値になっている場合は、このデータ空っぽの場合はエラー
			return false;
		}
		return true;
	}

/**
 * checkPastRegistration
 *
 * @param mix $check チェック対象入力データ
 * @return bool
 */
	public function checkPastRegistration($check) {
		if ($this->data['ActionRegistrationAdd']['create_option'] !=
			RegistrationsComponent::REGISTRATION_CREATE_OPT_REUSE) {
			return true;
		}
		$this->Registration = ClassRegistry::init('Registrations.Registration', true);
		$baseCondition = $this->Registration->getBaseCondition(array(
			'Registration.id' => $check['past_registration_id']
		));
		unset($baseCondition['block_id']);
		unset($baseCondition['Registration.block_id']);
		$cnt = $this->Registration->find('count', array(
			'conditions' => $baseCondition,
			'recursive' => -1
		));
		if ($cnt == 0) {
			return false;
		}
		return true;
	}

/**
 * getNewRegistration
 *
 * @return array
 */
	public function getNewRegistration() {
		$this->Registration = ClassRegistry::init('Registrations.Registration', true);
		$this->RegistrationPage = ClassRegistry::init('Registrations.RegistrationPage', true);
		$this->RegistrationQuestion = ClassRegistry::init('Registrations.RegistrationQuestion', true);
		$createOption = $this->data['ActionRegistrationAdd']['create_option'];

		// 指定された作成のオプションによって処理分岐
		if ($createOption == RegistrationsComponent::REGISTRATION_CREATE_OPT_NEW) {
			// 空の新規作成
			$registration = $this->_createNew();
		} elseif ($createOption == RegistrationsComponent::REGISTRATION_CREATE_OPT_REUSE) {
			// 過去データからの作成
			$registration = $this->_createFromReuse();
		} elseif ($createOption == RegistrationsComponent::REGISTRATION_CREATE_OPT_TEMPLATE) {
			// テンプレートファイルからの作成
			$registration = $this->_createFromTemplate();
		}
		return $registration;
	}
/**
 * _createNew
 *
 * @return array RegistrationData
 */
	protected function _createNew() {
		// 登録フォームデータを新規に作成する
		// 新規作成の場合、タイトル文字のみ画面で設定されPOSTされる
		// Titleをもとに、登録フォームデータ基本構成を作成し返す

		// デフォルトデータをもとに新規作成
		$registration = $this->_getDefaultRegistration(array(
			'title' => $this->data['ActionRegistrationAdd']['title']));
		// 登録フォームデータを返す
		return $registration;
	}
/**
 * _createFromReuse
 *
 * @return array RegistrationData
 */
	protected function _createFromReuse() {
		// 登録フォームデータを過去の登録フォームデータをもとにして作成する
		// 過去からの作成の場合、参考にする過去の登録フォームのidのみPOSTされてくる
		// (orgin_idではなくidである点に注意！)
		// idをもとに、過去の登録フォームデータを取得し、
		// そのデータから今回作成する登録フォームデータ基本構成を作成し返す

		// 過去の登録フォームのコピー・クローンで作成
		$registrationId = $this->data['ActionRegistrationAdd']['past_registration_id'];
		$registration = $this->_getRegistrationCloneById($registrationId);
		return $registration;
	}
/**
 * _getDefaultRegistration
 * get default data of registrations
 *
 * @param array $addData add data to Default data
 * @return array
 */
	protected function _getDefaultRegistration($addData) {
		$registration = array();
		$registration['Registration'] = Hash::merge(
			array(
				'block_id' => Current::read('Block.id'),
				'title' => '',
				'key' => '',
				'status' => WorkflowComponent::STATUS_IN_DRAFT,
				'is_total_show' => RegistrationsComponent::EXPRESSION_SHOW,
				'answer_timing' => RegistrationsComponent::USES_NOT_USE,
				'is_key_pass_use' => RegistrationsComponent::USES_NOT_USE,
				'total_show_timing' => RegistrationsComponent::USES_NOT_USE,
				'registration_mail_subject' =>
					__d('registrations', 'Registration.mail.default.subject'),
				'registration_mail_body' =>
					__d('registrations', 'Registration.mail.default.body'),
				'thanks_content' =>
					__d('registrations', 'Thank you for registering.'),
			),
			$addData);

		$registration['RegistrationPage'][0] = $this->RegistrationPage->getDefaultPage($registration);
		return $registration;
	}
/**
 * _getRegistrationCloneById 指定されたIDにの登録フォームデータのクローンを取得する
 *
 * @param int $registrationId 登録フォームID(編集なのでoriginではなくRAWなIDのほう
 * @return array
 */
	protected function _getRegistrationCloneById($registrationId) {
		// 前もってValidate処理で存在確認されている場合しか
		// この関数が呼ばれないので$registrationの判断は不要
		$registration = $this->Registration->find('first', array(
			'conditions' => array('Registration.id' => $registrationId),
			'recursive' => 1
		));
		// ID値のみクリア
		$this->Registration->clearRegistrationId($registration);
		// Wysiwygエディタ内のファイルの複製処理
		$registration = $this->_copyWysiwygFiles($registration);

		return $registration;
	}
/**
 * _copyWysiwygFiles
 *
 * 引数で指定された登録フォームの中を分析し、
 * ウィジウィグに設定されているファイルは複製を作ります
 *
 * @param array $registration 登録フォームデータ
 * @return array $registration 複製を作り終えた登録フォームデータ
 */
	protected function _copyWysiwygFiles($registration) {
		$wysiswyg = new WysiwygZip();
		$flatRegistration = Hash::flatten($registration);
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
					$wysiswygZipFile = $wysiswyg->createWysiwygZip($value);
					$value = $wysiswyg->getFromWysiwygZip($wysiswygZipFile);
				}
			}
		}
		$registration = Hash::expand($flatRegistration);
		return $registration;
	}

/**
 * _createFromTemplate
 *
 * @return array RegistrationData
 */
	protected function _createFromTemplate() {
		// 登録フォームデータをUPLOADされた登録フォームテンプレートファイルのデータをもとにして作成する
		// テンプレートからの作成の場合、テンプレートファイルがUPLOADされてくる
		// アップされたファイルをもとに、登録フォームデータを解凍、取得し、
		// そのデータから今回作成する登録フォームデータ基本構成を作成し返す

		if (empty($this->data['ActionRegistrationAdd']['template_file']['name'])) {
			$this->validationErrors['template_file'][] =
				__d('registrations', 'Please input template file.');
			return null;
		}

		try {
			// アップロードファイルを受け取り、
			// エラーチェックはない。ここでのエラー時はInternalErrorExceptionとなる
			$uploadFile = new TemporaryUploadFile($this->data['ActionRegistrationAdd']['template_file']);

			// アップロードファイル解凍
			$unZip = new UnZip($uploadFile->path);
			$temporaryFolder = $unZip->extract();
			// エラーチェック
			if (! $temporaryFolder) {
				$this->validationErrors['template_file'][] = __d('registrations', 'illegal import file.');
				return null;
			}

			// フィンガープリント確認
			$fingerPrint = $this->__checkFingerPrint($temporaryFolder->path);
			if ($fingerPrint === false) {
				$this->validationErrors['template_file'][] = __d('registrations', 'illegal import file.');
				return null;
			}

			// 登録フォームテンプレートファイル本体をテンポラリフォルダに展開する。
			$registrationZip = new UnZip(
				$temporaryFolder->path . DS . RegistrationsComponent::REGISTRATION_TEMPLATE_FILENAME);
			if (! $registrationZip->extract()) {
				$this->validationErrors['template_file'][] =
					__d('registrations', 'illegal import file.');
				return null;
			}

			// jsonファイルを読み取り、PHPオブジェクトに変換
			$jsonFilePath =
				$registrationZip->path . DS . RegistrationsComponent::REGISTRATION_JSON_FILENAME;
			$jsonFile = new File($jsonFilePath);
			$jsonData = $jsonFile->read();
			$jsonRegistration = json_decode($jsonData, true);
		} catch (Exception $ex) {
			$this->validationErrors['template_file'][] = __d('registrations', 'file upload error.');
			return null;
		}

		// 初めにファイルに記載されている登録フォームプラグインのバージョンと
		// 現サイトの登録フォームプラグインのバージョンを突合し、差分がある場合はインポート処理を中断する。
		if ($this->_checkVersion($jsonRegistration) === false) {
			$this->validationErrors['template_file'][] = __d('registrations', 'version is different.');
			return null;
		}

		// バージョンが一致した場合、登録フォームデータをメモリ上に構築
		$registrations = $this->_getRegistrations(
			$registrationZip->path,
			$jsonRegistration['Registrations'],
			$fingerPrint);

		// 現在の言語環境にマッチしたデータを返す
		return $registrations[0];
	}

/**
 * _getRegistrations
 *
 * @param string $folderPath path string to import zip file exist
 * @param array $registrations registration data in import json file
 * @param string $importKey import key (hash string)
 * @return array RegistrationData
 */
	protected function _getRegistrations($folderPath, $registrations, $importKey) {
		$wysiswyg = new WysiwygZip();

		foreach ($registrations as &$q) {
			// WysIsWygのデータを入れなおす
			$flatRegistration = Hash::flatten($q);
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
						// keyと同じ名前のフォルダの下にあるkeyの名前のZIPファイルを渡して
						// その返ってきた値をこのカラムに設定
						$value =
							$wysiswyg->getFromWysiwygZip(
								$folderPath . DS . $value, $model->alias . '.' . $columnName);
					}
				}
			}
			$q = Hash::expand($flatRegistration);
			$q['Registration']['import_key'] = $importKey;
		}
		return $registrations;
	}
/**
 * __checkFingerPrint
 *
 * @param string $folderPath folder path
 * @return string finger print string
 */
	private function __checkFingerPrint($folderPath) {
		// フィンガープリントファイルを取得
		$file = new File(
				$folderPath . DS . RegistrationsComponent::REGISTRATION_FINGER_PRINT_FILENAME,
				false);
		$fingerPrint = $file->read();

		// ファイル内容から算出されるハッシュ値と指定されたフットプリント値を比較し
		// 同一であれば正当性が保証されたと判断する（フォーマットチェックなどは行わない）
		$registrationZipFile =
			$folderPath . DS . RegistrationsComponent::REGISTRATION_TEMPLATE_FILENAME;
		if (sha1_file($registrationZipFile, false) != $fingerPrint) {
			return false;
		}
		$file->close();
		return $fingerPrint;
	}
/**
 * _checkVersion
 *
 * @param array $jsonData バージョンが含まれたJson
 * @return bool
 */
	protected function _checkVersion($jsonData) {
		// バージョン情報を取得するためComposer情報を得る
		$Plugin = ClassRegistry::init('PluginManager.Plugin');
		$composer = $Plugin->getComposer('netcommons/registrations');
		if (!$composer) {
			return false;
		}
		if (!isset($jsonData['version'])) {
			return false;
		}
		if ($composer['version'] != $jsonData['version']) {
			return false;
		}
		return true;
	}
}
