<?php
/**
 * RegistrationBlocksController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('TemporaryFolder', 'Files.Utility');
App::uses('CsvFileWriter', 'Files.Utility');
App::uses('ZipDownloader', 'Files.Utility');

/**
 * BlocksController
 *
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationBlocksController extends RegistrationsAppController {

/**
 * csv download item count handling unit
 *
 * @var int
 */
	const	REGISTRATION_CSV_UNIT_NUMBER = 1000;

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Registrations.Registration',
		'Registrations.RegistrationFrameSetting',
		'Registrations.RegistrationAnswerSummary',
		'Registrations.RegistrationAnswerSummaryCsv',
		'Blocks.Block',
		'Registrations.RegistrationExport',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'registration_blocks')),
				'role_permissions' => array('url' => array('controller' => 'registration_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'registration_frame_settings')),
			),
		),
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'index,add,edit,delete' => 'block_editable',
			),
		),
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Session',
		'Blocks.BlockForm',
		'NetCommons.NetCommonsForm',
		'NetCommons.Date',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('index');
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		// 条件設定値取得
		// 条件設定値取得
		$conditions = $this->Registration->getBaseCondition();

		// データ取得
		$this->paginate = array(
			'conditions' => $conditions,
			'page' => 1,
			'sort' => RegistrationsComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
			'limit' => RegistrationsComponent::REGISTRATION_DEFAULT_DISPLAY_NUM_PER_PAGE,
			'direction' => 'desc',
			'recursive' => 0,
		);
		$registration = $this->paginate('Registration');
		if (! $registration) {
			$this->view = 'not_found';
			return;
		}

		$this->set('registrations', $registration);
	}

/**
 * download
 *
 * @return void
 * @throws InternalErrorException
 */
	public function download() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// 登録フォームキー
		$registrationKey = $this->_getRegistrationKeyFromPass();
		// キー情報をもとにデータを取り出す
		$registration = $this->RegistrationAnswerSummaryCsv->getRegistrationForAnswerCsv($registrationKey);
		if (! $registration) {
			$this->setAction('throwBadRequest');
			return;
		}
		// 圧縮用パスワードキーを求める
		if (! empty($this->request->data['AuthorizationKey']['authorization_key'])) {
			$zipPassword = $this->request->data['AuthorizationKey']['authorization_key'];
		} else {
			$this->NetCommons->setFlashNotification(__d('registrations', 'Setting of password is required always to download answers.'),
				array('interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL));
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'registration_blocks',
				'action' => 'index',
				'frame_id' => Current::read('Frame.id'))));
			return;
		}

		try {
			$tmpFolder = new TemporaryFolder();
			$csvFile = new CsvFileWriter(array(
				'folder' => $tmpFolder->path
			));
			// 回答データを一気に全部取得するのは、データ爆発の可能性があるので
			// REGISTRATION_CSV_UNIT_NUMBER分に制限して取得する
			$offset = 0;
			do {
				$datas = $this->RegistrationAnswerSummaryCsv->getAnswerSummaryCsv($registration, self::REGISTRATION_CSV_UNIT_NUMBER, $offset);
				// CSV形式で書きこみ
				foreach ($datas as $data) {
					$csvFile->add($data);
				}
				$dataCount = count($datas);	// データ数カウント
				$offset += $dataCount;		// 次の取得開始位置をずらす
			} while ($dataCount == self::REGISTRATION_CSV_UNIT_NUMBER);
			// データ取得数が制限値分だけとれている間は繰り返す

		} catch (Exception $e) {
			// NetCommonsお約束:エラーメッセージのFlash表示
			$this->NetCommons->setFlashNotification(__d('registrations', 'download error'),
				array('interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL));
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'registration_blocks',
				'action' => 'index',
				'frame_id' => Current::read('Frame.id'))));
			return;
		}
		// Downloadの時はviewを使用しない
		$this->autoRender = false;
		// ダウンロードファイル名決定 登録フォーム名称をつける
		$zipFileName = $registration['Registration']['title'] . '.zip';
		$downloadFileName = $registration['Registration']['title'] . '.csv';
		// 出力
		return $csvFile->zipDownload(rawurlencode($zipFileName), $downloadFileName, $zipPassword);
	}

/**
 * export
 *
 * template file about registration export action
 *
 * @return void
 */
	public function export() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// 登録フォームキー
		$registrationKey = $this->_getRegistrationKeyFromPass();
		// キー情報をもとにデータを取り出す
		$registration = $this->RegistrationAnswerSummaryCsv->getRegistrationForAnswerCsv($registrationKey);
		if (! $registration) {
			$this->setAction('throwBadRequest');
			return;
		}

		try {
			// zipファイル準備
			$zipFile = new ZipDownloader();

			// Export用のデータ配列を取得する
			$zipData = $this->RegistrationExport->getExportData($registrationKey);

			// Export用ファイルデータをZIPファイルに出力する
			// ※この中でWYSISWYGエディタデータは適宜処理されている
			$this->RegistrationExport->putToZip($zipFile, $zipData);

			// アーカイブ閉じる
			$zipFile->close();
		} catch(Exception $e) {
			$this->Session->setFlash(__d('registrations', 'export error') . $e->getMessage(),
				array('interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL));
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'registration_blocks',
				'action' => 'index',
				'frame_id' => Current::read('Frame.id'))));
			return;
		}
		// 大外枠zipファイル準備
		$zipWrapperFile = new ZipDownloader();
		// 登録フォームデータファイルのフィンガープリントを得る
		$fingerPrint = sha1_file($zipFile->path, false);
		// フィンガープリントをアーカイブに加える
		$zipWrapperFile->addFromString(RegistrationsComponent::REGISTRATION_FINGER_PRINT_FILENAME, $fingerPrint);
		// 本体ファイルを
		$zipWrapperFile->addFile($zipFile->path, RegistrationsComponent::REGISTRATION_TEMPLATE_FILENAME);
		// export-key 設定
		$this->Registration->saveExportKey($registration['Registration']['id'], $fingerPrint);

		// viewを使用しない
		$this->autoRender = false;

		// ダウンロード出力ファイル名確定
		$exportFileName = $registration['Registration']['title'] . '.zip';
		// 出力
		return $zipWrapperFile->download(rawurlencode($exportFileName));
	}
}