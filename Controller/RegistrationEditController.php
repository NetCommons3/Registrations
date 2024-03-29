<?php
/**
 * RegistrationEdit Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('MailSetting', 'Mails.Model');

/**
 * RegistrationEditController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationEditController extends RegistrationsAppController {

/**
 * edit registration session key
 *
 * @var int
 */
	const	REGISTRATION_EDIT_SESSION_INDEX = 'Registrations.registrationEdit.';

/**
 * layout
 *
 * @var array
 */
	public $layout = '';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		// 登録通知メール
		'Mails.MailSetting',
		//'Mails.MailSettingFixedPhrase',
		'DataTypes.DataTypeChoice',
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
				'edit,edit_question,edit_result,delete' => 'content_creatable',
			),
		),
		'Registrations.Registrations',
		'NetCommons.NetCommonsTime',
		'Registrations.RegistrationBlockTabs',
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'Workflow.Workflow',
		'NetCommons.TitleIcon',
		'Registrations.RegistrationEdit',
		'NetCommons.Wizard' => array(
			'navibar' => array(
				'edit_question' => array(
					'url' => array(
						'controller' => 'registration_edit',
						'action' => 'edit_question',
					),
					'label' => array('registrations', 'Set questions'),
				),
				'edit' => array(
					'url' => array(
						'controller' => 'registration_edit',
						'action' => 'edit',
					),
					'label' => array('registrations', 'Set registration'),
				),
			),
			'cancelUrl' => null
		),
		'Wysiwyg.Wysiwyg',
		//'Blocks.BlockTabs' => array(
		//	'mainTabs' => array(
		//		'block_index' => array(
		//			'url' => array('controller' => 'registration_blocks')
		//		),
		//	),
		//	'blockTabs' => array(
		//		'block_settings' => array(
		//			'url' => array('controller' => 'registration_edit', 'action' =>
		//				'edit_question', 'q_mode' => 'setting')
		//		),
		//		'role_permissions' => array(
		//			'url' => array('controller' => 'registration_block_role_permissions')
		//		),
		//		'mail_settings' => array(
		//			'url' => array('controller' => 'registration_mail_settings')
		//		),
		//		'answer_list' => array(
		//			'url' => array('controller' => 'registration_blocks', 'action' =>
		//				'answer_list'),
		//			'label' => ['registrations', 'Answer List'],
		//		),
		//	),
		//),

		// 登録通知メール
		//'Blocks.BlockRolePermissionForm',
		//'Mails.MailForm',
	);

/**
 * target registration　
 *
 */
	protected $_registration = null;

/**
 * session index
 *
 */
	protected $_sessionIndex = null;

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		// 登録通知メール
		//$this->MailSettings->permission =
		//	array('mail_answer_receivable');
		//$this->MailSettings->typeKeys =
		//	array(MailSettingFixedPhrase::ANSWER_TYPE); //

		parent::beforeFilter();

		// セッションインデックスパラメータ
		$sessionName =
			self::REGISTRATION_EDIT_SESSION_INDEX . $this->_getRegistrationEditSessionIndex();

		if ($this->request->is('post') || $this->request->is('put')) {
			// ウィザード画面なのでセッションに記録された前画面データが必要
			$this->_registration = $this->Session->read($sessionName);
			if (! $this->_registration) {
				// セッションタイムアウトの場合
				return;
			}
		} else {
			// redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
			// セッションに記録された値がある場合はそちらを優先
			if ($this->Session->check($sessionName)) {
				$this->_registration = $this->Session->read($sessionName);
			} else {
				// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
				// まずは、そのキーを取り出す
				// 登録フォームキー
				$registrationKey = $this->_getRegistrationKeyFromPass();
				if ($registrationKey) {
					$conditions = [$this->Registration->alias . '.key' => $registrationKey];
				} else {
					// 登録フォームキーが指定されてなければブロックIDから。編集時にブロックタブから遷移したとき
					$blockId = Current::read('Block.id');
					$conditions = [$this->Registration->alias . '.block_id' => $blockId];
				}
				// 登録フォームキーの指定がある場合は過去データ編集と判断
				// 指定された登録フォームデータを取得
				// NetCommonsお約束：履歴を持つタイプのコンテンツデータはgetWorkflowContentsで取り出す
				$this->_registration = $this->Registration->getWorkflowContents('first', array(
					'recursive' => 0,
					'conditions' => $conditions
				));
				// NetCommonsお約束：編集の場合には改めて編集権限をチェックする必要がある
				// getWorkflowContentsはとりあえず自分が「見られる」コンテンツデータを取ってきてしまうので
				if (! $this->Registration->canEditWorkflowContent($this->_registration)) {
					$this->_registration = null;
				}
			}
		}
		if ($this->_registration === null ||
			! Hash::check($this->_registration, 'Registration.id')) {
			// 登録フォーム新規作成時はブロック設定以外のタブは表示しない
			unset($this->helpers['Blocks.BlockTabs']['blockTabs']['role_permissions']);
			unset($this->helpers['Blocks.BlockTabs']['blockTabs']['mail_settings']);
			unset($this->helpers['Blocks.BlockTabs']['blockTabs']['answer_list']);
		}
		// ここへは設定画面の一覧から来たのか、一般画面の一覧から来たのか
		$this->_decideSettingLayout();
	}
/**
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();

		//ウィザード
		foreach ($this->helpers['NetCommons.Wizard']['navibar'] as &$actions) {
			$urlParam = $actions['url'];
			$urlParam = Hash::merge($urlParam, $this->request->params['named']);
			foreach ($this->request->params['pass'] as $passParam) {
				$urlParam[$passParam] = null;
			}
			$actions['url'] = $urlParam;
		}
	}

/**
 * edit question method
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit_question() {
		// 処理対象の登録フォームデータが見つかっていない場合、エラー
		if (empty($this->_registration)) {
			$this->throwBadRequest();
			return false;
		}

		// Postの場合
		if ($this->request->is('post') || $this->request->is('put')) {
			$postRegistration = $this->request->data;
			// 登録フォームデータに作成されたPost項目データをかぶせる
			// （項目作成画面では項目データ属性全てをPOSTしているのですり替えでOK）
			$registration = $this->_registration;
			$registration['Registration'] = Hash::merge(
				$this->_registration['Registration'],
				$postRegistration['Registration']);

			// 発行後の登録フォームは項目情報は書き換えない→アンケートと違っていつでも編集OK
			// 未発行の場合はPostデータを上書き設定して
			//if ($this->Registration->hasPublished($registration) == 0) {
			//	$registration['RegistrationPage'] = $postRegistration['RegistrationPage'];
			//} else {
			//	$this->Registration->clearRegistrationId($registration, true);
			//	// booleanの値がPOST時と同じようになるように調整
			//	$registration['RegistrationPage'] =
			//		RegistrationsAppController::changeBooleansToNumbers(
			//			$registration['RegistrationPage']);
			//}
			$registration['RegistrationPage'] = $postRegistration['RegistrationPage'];

			// バリデート
			$this->Registration->set($registration);
			if (! $this->Registration->validates(
				array('validate' => RegistrationsComponent::REGISTRATION_VALIDATE_TYPE))) {
				$this->__setupViewParameters($registration, '');
				return;
			}

			// バリデートがOKであればPOSTで出来上がったデータをセッションキャッシュに書く
			$this->Session->write(
				self::REGISTRATION_EDIT_SESSION_INDEX . $this->_sessionIndex,
				$registration);

			// 次の画面へリダイレクト
			$this->redirect($this->_getActionUrl('edit'));
		} else {
			// 登録フォームデータが取り出せている場合、それをキャッシュに書く
			$this->Session->write(
				self::REGISTRATION_EDIT_SESSION_INDEX . $this->_getRegistrationEditSessionIndex(),
				$this->_sorted($this->_registration));
			$this->__setupViewParameters($this->_registration, '');
		}
	}

/**
 * edit method
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit() {
		// 処理対象の登録フォームデータが見つかっていない場合、エラー
		if (empty($this->_registration)) {
			$this->throwBadRequest();
			return;
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$postRegistration = $this->request->data;

			$beforeStatus = $this->_registration['Registration']['status'];

			// 設定画面では登録フォーム本体に纏わる情報のみがPOSTされる
			$registration = Hash::merge($this->_registration, $postRegistration);

			// 指示された編集状態ステータス
			$registration['Registration']['status'] = $this->Workflow->parseStatus();

			// それをDBに書く
			$saveRegistration = $this->Registration->saveRegistration($registration);
			// エラー
			if ($saveRegistration == false) {
				$registration['Registration']['status'] = $beforeStatus;
				$this->__setupViewParameters($registration, $this->_getActionUrl('edit_question'));
				return;
			}

			// 成功時 セッションに書き溜めた編集情報を削除
			$this->Session->delete(
				self::REGISTRATION_EDIT_SESSION_INDEX . $this->_getRegistrationEditSessionIndex());

			if ($this->layout == 'NetCommons.setting') {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			} else {
				// 登録画面（詳細）へリダイレクト
				$action = 'view';
				//if ($saveRegistration['Registration']['status'] == WorkflowComponent::STATUS_PUBLISHED) {
				//	$action = 'view';
				//} else {
				//	$action = 'test_mode';
				//}
				$urlArray = array(
					'controller' => 'registration_answers',
					'action' => $action,
					Current::read('Block.id'),
					$this->_getRegistrationKey($saveRegistration),
					'frame_id' => Current::read('Frame.id'),
				);
				$this->redirect(NetCommonsUrl::actionUrl($urlArray));
			}
			return;
		} else {
			// 登録通知メール

			// 指定されて取り出した登録フォームデータをセッションキャッシュに書く
			$this->Session->write(
				$this->_getRegistrationEditSessionIndex(),
				$this->_registration);
			$this->__setupViewParameters($this->_registration, $this->_getActionUrl('edit_question'));
		}
		$registrationKey = $this->_registration['Registration']['key'];
		$comments = $this->Registration->getCommentsByContentKey($registrationKey);
		$this->set('comments', $comments);
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete() {
		if (! $this->request->is('delete')) {
			$this->throwBadRequest();
			return;
		}

		//削除権限チェック
		if (! $this->Registration->canDeleteWorkflowContent($this->_registration)) {
			$this->throwBadRequest();
			return;
		}

		// 削除処理
		if (! $this->Registration->deleteRegistration($this->request->data)) {
			$this->throwBadRequest();
			return;
		}

		$this->Session->delete(self::REGISTRATION_EDIT_SESSION_INDEX . $this->_sessionIndex);

		if ($this->layout == 'NetCommons.setting') {
			$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
		} else {
			$this->redirect(NetCommonsUrl::backToPageUrl());
		}
	}

/**
 * cancel method
 *
 * @return void
 */
	public function cancel() {
		$this->Session->delete(self::REGISTRATION_EDIT_SESSION_INDEX . $this->_sessionIndex);
		$this->redirect(NetCommonsUrl::backToPageUrl());
	}
/**
 * _getActionUrl method
 *
 * @param string $method 遷移先アクション名
 * @return void
 */
	protected function _getActionUrl($method) {
		$urlArray = array(
			'controller' => Inflector::underscore($this->name),
			'action' => $method,
			Current::read('Block.id'),
			$this->_getRegistrationKey($this->_registration),
			'frame_id' => Current::read('Frame.id'),
			's_id' => $this->_getRegistrationEditSessionIndex()
		);
		if ($this->layout == 'NetCommons.setting') {
			$urlArray['q_mode'] = 'setting';
		}
		return NetCommonsUrl::actionUrl($urlArray);
	}
/**
 * __setupViewParameters method
 *
 * @param array $registration 登録フォームデータ
 * @param string $backUrl BACKボタン押下時の戻るパス
 * @return void
 */
	private function __setupViewParameters($registration, $backUrl) {
		//$isPublished = $this->Registration->hasPublished($registration);

		// エラーメッセージはページ、項目、選択肢要素のそれぞれの場所に割り当てる
		$this->NetCommons->handleValidationError($this->Registration->validationErrors);
		$flatError = Hash::flatten($this->Registration->validationErrors);
		$newFlatError = array();
		foreach ($flatError as $key => $val) {
			if (preg_match('/^(.*)\.(.*)\.(.*)$/', $key, $matches)) {
				$newFlatError[$matches[1] . '.error_messages.' . $matches[2] . '.' . $matches[3]] = $val;
			}
		}
		$registration = Hash::merge($registration, Hash::expand($newFlatError));
		$registration = $this->NetCommonsTime->toUserDatetimeArray(
			$registration,
			array(
				'Registration.answer_start_period',
				'Registration.answer_end_period',
				'Registration.total_show_start_period',
		));

		$this->set('postUrl', array('url' => $this->_getActionUrl($this->action)));
		if ($this->layout == 'NetCommons.setting') {
			$this->set('cancelUrl', array('url' => NetCommonsUrl::backToIndexUrl('default_setting_action')));
		} else {
			$this->set('cancelUrl', array('url' => NetCommonsUrl::backToPageUrl()));
		}
		$this->set('deleteUrl', array('url' => $this->_getActionUrl('delete')));

		$this->set('questionTypeOptions', $this->Registrations->getQuestionTypeOptionsWithLabel());

		// 都道府県データ
		$prefectures = $this->_getPrefectures();
		$this->set('prefectures', $prefectures);
		//$this->set('isPublished', $isPublished);
		$this->set('isPublished', false);

		$this->request->data = $registration;
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');

		// メール通知設定
		//$conditions = [
		//	'plugin_key' => 'registrations',
		//	'block_key' => Current::read('Block.key')
		//];
		//$mailSetting = $this->MailSetting->find('first', ['conditions' => $conditions]);
		//$this->set('mailSetting', $mailSetting);
	}

/**
 * DataTypeから都道府県データを取得してJSON形式で返す
 *
 * @return string json形式の都道府県リスト
 */
	protected function _getPrefectures() {
		// メタデータから取得
		$options = [
			'conditions' => [
				'data_type_key' => 'prefecture',
				'language_id' => Current::read('Language.id'),
			],
			'order' => 'DataTypeChoice.weight ASC'
		];
		$prefectures = $this->DataTypeChoice->find('all', $options);
		$prefectures = Hash::extract($prefectures, '{n}.DataTypeChoice.name');
		// json形式にする
		$prefectures = json_encode($prefectures);
		return $prefectures;
	}
}
