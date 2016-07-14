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

App::uses('AppController', 'Controller');
App::uses('TemporaryFolder', 'Files.Utility');
App::uses('CsvFileWriter', 'Files.Utility');
App::uses('ZipDownloader', 'Files.Utility');

/**
 * BlocksController
 *
 * @author AllCreator <info@allcreator.net>
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
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'index,download,export' => 'block_editable',
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
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array(
					'url' => array('controller' => 'registration_blocks')
				),
				'role_permissions' => array(
					'url' => array('controller' => 'registration_block_role_permissions')
				),
				'frame_settings' => array(
					'url' => array('controller' => 'registration_frame_settings')
				),
				'mail_settings' => array(
					'url' => array('controller' => 'registration_mail_settings')
				),
			),
		),
		'Blocks.BlockIndex',
		'NetCommons.NetCommonsForm',
		'NetCommons.Date',
		'NetCommons.TitleIcon',
		'AuthorizationKeys.AuthKeyPopupButton',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('index');
		// 設定画面を表示する前にこのルームの登録フォームブロックがあるか確認
		// 万が一、まだ存在しない場合には作成しておく
		// afterFrameSaveが呼ばれないような状況の想定

		// このタイミングで作るのはやめてみる by Ryuji AMANO
		//$frame['Frame'] = Current::read('Frame');
		//$this->Registration->afterFrameSave($frame);
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		$conditions = $this->Registration->getBaseCondition();
		unset($conditions['block_id']);
		$this->Paginator->settings = array(
			'Registration' => array(
				'order' => array('Registration.id' => 'desc'),
				'conditions' => $this->Registration->getBlockConditions($conditions),
				'recursive' => 0,
			)
		);

		$registrations = $this->Paginator->paginate('Registration');
		if (!$registrations) {
			$this->view = 'not_found';
			return;
		}

		$this->set('registrations', $registrations);
		$this->request->data['Frame'] = Current::read('Frame');
	}

/**
 * index
 *
 * @return void
 */
	//public function _index() {
	//	// 条件設定値取得
	//	// 条件設定値取得
	//	$conditions = $this->Registration->getBaseCondition();
	//
	//	// データ取得
	//	$this->paginate = array(
	//		'conditions' => $conditions,
	//		'page' => 1,
	//		'order' => array('Registration.modified' => 'DESC'),
	//		//'limit' => RegistrationsComponent::REGISTRATION_DEFAULT_DISPLAY_NUM_PER_PAGE,
	//		'recursive' => 0,
	//	);
	//	$registration = $this->paginate('Registration');
	//	if (! $registration) {
	//		$this->view = 'not_found';
	//		return;
	//	}
	//
	//	$this->set('registrations', $registration);
	//}

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
		$registration = $this->RegistrationAnswerSummaryCsv->getRegistrationForAnswerCsv(
			$registrationKey);
		if (! $registration) {
			$this->_setFlashMessageAndRedirect(
				__d('registrations', 'Designation of the registration does not exist.'));
			return;
		}
		// 圧縮用パスワードキーを求める
		if (! empty($this->request->data['AuthorizationKey']['authorization_key'])) {
			$zipPassword = $this->request->data['AuthorizationKey']['authorization_key'];
		} else {
			$this->_setFlashMessageAndRedirect(
				__d('registrations', 'Setting of password is required always to download answers.'));
			return;
		}

		try {
			$tmpFolder = new TemporaryFolder();
			$csvFile = new CsvFileWriter(array(
				'folder' => $tmpFolder->path
			));
			// 登録データを一気に全部取得するのは、データ爆発の可能性があるので
			// REGISTRATION_CSV_UNIT_NUMBER分に制限して取得する
			$offset = 0;
			do {
				$datas = $this->RegistrationAnswerSummaryCsv->getAnswerSummaryCsv(
					$registration,
					self::REGISTRATION_CSV_UNIT_NUMBER, $offset);
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
			$this->_setFlashMessageAndRedirect(__d('registrations', 'download error'));
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
 * _setFlashMessageAndRedirect
 *
 * @param string $message flash error message
 *
 * @return void
 */
	protected function _setFlashMessageAndRedirect($message) {
		$this->NetCommons->setFlashNotification(
			$message,
			array(
				'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL
			));
		$this->redirect(NetCommonsUrl::actionUrl(array(
			'controller' => 'registration_blocks',
			'action' => 'index',
			'frame_id' => Current::read('Frame.id')
		)));
	}
}