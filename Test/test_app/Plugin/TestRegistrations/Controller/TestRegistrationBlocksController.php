<?php
/**
 * RegistrationBlocksController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationBlocksController', 'Registrations.Controller');

/**
 * BlocksController
 *
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @package NetCommons\Registrations\Controller
 */
class TestRegistrationBlocksController extends RegistrationBlocksController {

/**
 * csv download item count handling unit
 *
 * @var int
 */
	const	REGISTRATION_CSV_UNIT_NUMBER = 1;

/**
 * download method return values
 *
 * @var array
 */
	public $retunValue;

/**
 * download
 *
 * @return void
 * @throws InternalErrorException
 */
	public function download() {
		App::uses('CsvFileWriter', 'TestFiles.Utility');
		$this->returnValue = parent::download();
		return $this->returnValue;
	}

/**
 * export
 *
 * @return void
 * @throws InternalErrorException
 */
	public function export() {
		App::uses('ZipDownloader', 'TestFiles.Utility');
		$this->returnValue = parent::export();
		return $this->returnValue;
	}
}