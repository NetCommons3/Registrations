<?php
/**
 * RegistrationsAdd Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * RegistrationsAddController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationAddController extends RegistrationsAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'PluginManager.Plugin',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Files.FileUpload',					// FileUpload
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'add' => 'content_creatable',
			),
		),
		'Registrations.Registrations',
		'Registrations.RegistrationBlockTabs',

	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Registrations.RegistrationStatusLabel',
		'Registrations.RegistrationUtil'
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		// ここへは設定画面の一覧から来たのか、一般画面の一覧から来たのか
		$this->_decideSettingLayout();
	}

/**
 * add registration display method
 *
 * @return void
 */
	public function add() {
		// NetCommonsお約束：投稿権限のある人物しかこのアクションにアクセスできない
		// それは$componentsの組み込みでallow => add => content_creatableで担保される
		// アクション処理内でチェックする必要はない
		unset($this->helpers['Blocks.BlockTabs']['blockTabs']['role_permissions']);
		unset($this->helpers['Blocks.BlockTabs']['blockTabs']['mail_settings']);
		unset($this->helpers['Blocks.BlockTabs']['blockTabs']['answer_list']);

		// POSTされたデータを読み取り
		if ($this->request->is('post')) {
			// Postデータをもとにした新登録フォームデータの取得をModelに依頼する
			$actionModel = ClassRegistry::init('Registrations.ActionRegistrationAdd', 'true');

			if ($registration = $actionModel->createRegistration($this->request->data)) {
				$tm = $this->_getRegistrationEditSessionIndex();
				// 作成中登録フォームデータをセッションキャッシュに書く
				$this->Session->write('Registrations.registrationEdit.' . $tm, $registration);

				// 次の画面へリダイレクト
				$urlArray = array(
					'controller' => 'registration_edit',
					'action' => 'edit_question',
					Current::read('Block.id'),
					'frame_id' => Current::read('Frame.id'),
					's_id' => $tm,
				);
				if ($this->layout == 'NetCommons.setting') {
					$urlArray['q_mode'] = 'setting';
				}
				$this->redirect(NetCommonsUrl::actionUrl($urlArray));
				return;
			} else {
				// データに不備があった場合
				$this->NetCommons->handleValidationError($actionModel->validationErrors);
			}
		} else {
			// 新規に登録フォームを作成するときは最初にブロックをつくっておく
			$frame['Frame'] = Current::read('Frame');
			$this->Registration->createBlock($frame);
		}

		// 過去データ 取り出し
		$conditions = Hash::remove($this->Registration->getBaseCondition(), 'block_id');
		unset($conditions['Registration.block_id']);
		$conditions['Block.room_id'] = Current::read('Room.id');
		$pastRegistrations = $this->Registration->find('all',
			array(
				'fields' => array(
					'id', 'title', 'status', 'answer_timing', 'answer_start_period', 'answer_end_period',
				),

				'conditions' => $conditions,
				'offset' => 0,
				'limit' => 1000,
				'recursive' => 0,
				'order' => array('Registration.modified DESC'),
			));
		$this->set('pastRegistrations', $pastRegistrations);

		if ($this->layout == 'NetCommons.setting') {
			$this->set('cancelUrl', NetCommonsUrl::backToIndexUrl('default_setting_action'));
		} else {
			$this->set('cancelUrl', NetCommonsUrl::backToPageUrl());
		}
		//
		// NetCommonsお約束：投稿のデータはrequest dataに設定する
		//
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		// create_optionが未設定のときは初期値として「ＮＥＷ」を設定する
		if (! $this->request->data('ActionRegistrationAdd.create_option')) {
			$this->request->data(
				'ActionRegistrationAdd.create_option',
				RegistrationsComponent::REGISTRATION_CREATE_OPT_NEW);
		}
	}
}
