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
		//'Registrations.RegistrationFrameSetting',
		'Registrations.RegistrationAnswerSummary',
		'Registrations.RegistrationAnswerSummaryCsv',
		'Registrations.RegistrationAnswer',
		'Blocks.Block',
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
				'*' => 'block_editable',
			),
		),
		'Paginator',
		'Files.Download',
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
				//'role_permissions' => array(
				//	'url' => array('controller' => 'registration_block_role_permissions')
				//),
				////'frame_settings' => array(
				////	'url' => array('controller' => 'registration_frame_settings')
				////),
				//'mail_settings' => array(
				//	'url' => array('controller' => 'registration_mail_settings')
				//),
			),
			'blockTabs' => array(
				'block_settings' => array(
					'url' => array('controller' => 'registration_edit', 'action' =>
						'edit_question', 'q_mode' => 'setting')
				),
				'role_permissions' => array(
					'url' => array('controller' => 'registration_block_role_permissions')
				),
				//'frame_settings' => array(
				//	'url' => array('controller' => 'registration_frame_settings')
				//),
				'mail_settings' => array(
					'url' => array('controller' => 'registration_mail_settings')
				),
				'answer_list' => array(
					'url' => array('controller' => 'registration_blocks', 'action' =>
						'answer_list'),
					'label' => ['registrations', 'Answer List'],
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
		// 未使用のまま残ってるregistraionsのブロックを削除する
		$this->_deleteNotUsedBlocks();

		$conditions = $this->Registration->getBaseCondition();
		unset($conditions['block_id']);
		$this->Paginator->settings = array(
			'Registration' => $this->Registration->getBlockIndexSettings([
				'conditions' => $conditions,
				'recursive' => 0
			])
		);
		//$this->Paginator->settings = array(
		//	'Registration' => array(
		//		'order' => array('Registration.id' => 'desc'),
		//		'conditions' => $this->Registration->getBlockConditions($conditions),
		//		'recursive' => 0,
		//	)
		//);

		$registrations = $this->Paginator->paginate('Registration');
		if (!$registrations) {
			$this->view = 'not_found';
			return;
		}

		$this->set('registrations', $registrations);
		$this->request->data['Frame'] = Current::read('Frame');
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
			// ヘッダ出力
			$header = $this->RegistrationAnswerSummaryCsv->getHeader($registration);
			$csvFile->add($header);

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
			} while ($dataCount === self::REGISTRATION_CSV_UNIT_NUMBER);
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
 * 登録一覧
 *
 * @return void
 * @throws InternalErrorException
 */
	public function answer_list() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// 登録フォームキー
		$registrationKey = $this->_getRegistrationKeyFromPass();
		// キー情報をもとにデータを取り出す
		if ($registrationKey) {
			$conditions = [$this->Registration->alias . '.key' => $registrationKey];
		} else {
			// 登録フォームキーが指定されてなければブロックIDから
			$blockId = Current::read('Block.id');
			$conditions = [$this->Registration->alias . '.block_id' => $blockId];
		}
		// 登録一覧は公開されてる登録フォームが対象。
		$conditions = Hash::merge([
				'Registration.is_active' => true,
			],
			$conditions);
		$registration = $this->Registration->find('first', array(
			'conditions' => $conditions,
			'recursive' => -1
		));
		//$registration = $this->RegistrationAnswerSummaryCsv->getRegistrationForAnswerCsv(
		//	$registrationKey
		//);

		if (!$registration) {
			// 公開されてなければメッセージをだして一覧へ
			$this->_setFlashMessageAndRedirect(
				__d('registrations', 'The registration form has not been published.'));
			return;
		}
		$registrationKey = $registration['Registration']['key'];
		$this->set('registration', $registration);
		// 一覧用に登録データを取得する
		$this->Paginator->settings = array_merge(
			$this->Paginator->settings,
			array(
				'RegistrationAnswerSummary' => [
					'conditions' => array(
						'answer_status' => RegistrationsComponent::ACTION_ACT,
						'test_status' => RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM,
						'registration_key' => $registrationKey,
					),
					//'limit' => $this->_frameSetting['BlogFrameSetting']['articles_per_page'],
					'order' => 'RegistrationAnswerSummary.modified DESC',
				]
			)
		);
		$summaries = $this->Paginator->paginate('RegistrationAnswerSummary');
		if (empty($summaries)) {
			$this->set('summaries', $summaries);
			return;
		}

		// 項目のIDを取得
		$questionIds = Hash::extract(
			$registration['RegistrationPage'],
			'{n}.RegistrationQuestion.{n}.id');

		// summary loop
		foreach ($summaries as & $summary) {
			$answers = $this->RegistrationAnswer->getAnswersBySummary($summary, $questionIds);
			$summary['RegistrationAnswer'] = $answers;
		}
		$this->set('summaries', $summaries);
	}

/**
 * delete answer
 *
 * @return void
 */
	public function delete_answer() {
		if (! $this->request->is('delete')) {
			$this->throwBadRequest();
			return;
		}

		$registrationKey = $this->_getRegistrationKeyFromPass();
		// 削除処理
		if (! $this->RegistrationAnswerSummary->deleteAnswerSummary($registrationKey)) {
			$this->throwBadRequest();
			return;
		}

			$this->redirect(NetCommonsUrl::actionUrl([
				'action' => 'index',
				'frame_id' => Current::read('Frame.id'),
			]));
	}

/**
 * 添付ファイルダウンロード
 *
 * @return mixed
 */
	public function download_file() {
		// ここから元コンテンツを取得する処理
		$answerId = $this->params['key'];
		return $this->Download->doDownload(
			$answerId,
			[
				'field' => 'answer_value_file',
				'download' => true,
			]
		);
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

/**
 * 24時間以上Registrationと関連づいてないゴミブロックを削除する
 *
 * @return void
 */
	protected function _deleteNotUsedBlocks() {
		// 24時間以上Registrationsと関連づいてないブロックを探す。
		$date = new DateTime(NetCommonsTime::getNowDatetime());
		$date->sub(new DateInterval('P1D'));

		$options = [
			'conditions' => [
				'Block.plugin_key' => 'registrations',
				'Registration.id' => null,
				'Block.modified <' => $date->format('Y-m-d H:i:s'),
			],
			'joins' => [
				[
					'table' => $this->Registration->table,
					'alias' => $this->Registration->alias,
					'type' => 'LEFT',
					'conditions' => array(
						'Block.id = Registration.block_id',
					)
				],
			],
			// ブロックのIDだけ取得できればOK
			'fields' => ['Block.id']
		];

		$blocks = $this->Block->find('all', $options);
		$blockIds = Hash::extract($blocks, '{n}.Block.id');
		if (empty($blockIds)) {
			return;
		}
		// 削除実行
		$this->Block->begin();
		$this->Block->deleteAll(['Block.id' => $blockIds]);
		$this->Block->commit();
	}
}