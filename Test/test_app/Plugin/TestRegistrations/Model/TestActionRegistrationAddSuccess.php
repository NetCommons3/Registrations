<?php
/**
 * ActionRegistrationAddModel
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ActionRegistrationAdd', 'Registrations.Model');

/**
 * ActionRegistrationAddModel
 *
 * Travisの環境など、ローカル開発環境以外でテストコードを動作させると
 * テスト用エクスポートテンプレートファイルとバージョンが違うよというエラーが
 * 成功パターンの試験のためにはバージョンチェックは無条件に成功を返してほしい
 * そのために作成されたモック
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Model
 */
class TestActionRegistrationAddSuccess extends ActionRegistrationAdd {

/**
 * Use table config
 *
 * @var string
 */
	public $useTable = 'registrations';

/**
 * Use alias config
 *
 * @var string
 */
	public $alias = 'ActionRegistrationAdd';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * getNewRegistration
 *
 * @return void
 * @throws InternalErrorException
 */
	public function getNewRegistration() {
		App::uses('TemporaryUploadFile', 'TestFiles.Utility');
		$this->returnValue = parent::getNewRegistration();
		return $this->returnValue;
	}
/**
 * _checkVersion
 *
 * @param array $jsonData バージョンが含まれたJson
 * @return bool
 */
	protected function _checkVersion($jsonData) {
		return true;
	}
}