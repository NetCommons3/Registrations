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
 * use model
 *
 * @var array
 */
	public $uses = array(
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
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'Workflow.Workflow',
		'Registrations.QuestionEdit'
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
		parent::beforeFilter();
		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// 登録フォームキー
		$registrationKey = $this->_getRegistrationKeyFromPass();

		// セッションインデックスパラメータ
		$sessionName = self::REGISTRATION_EDIT_SESSION_INDEX . $this->_getRegistrationEditSessionIndex();

		if ($this->request->isPost() || $this->request->isPut()) {
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
			} elseif (! empty($registrationKey)) {
				// 登録フォームキーの指定がある場合は過去データ編集と判断
				// 指定された登録フォームデータを取得
				// NetCommonsお約束：履歴を持つタイプのコンテンツデータはgetWorkflowContentsで取り出す
				$this->_registration = $this->Registration->getWorkflowContents('first', array(
					'recursive' => 0,
					'conditions' => array(
						$this->Registration->alias . '.key' => $registrationKey
					)
				));
				// NetCommonsお約束：編集の場合には改めて編集権限をチェックする必要がある
				// getWorkflowContentsはとりあえず自分が「見られる」コンテンツデータを取ってきてしまうので
				if (! $this->Registration->canEditWorkflowContent($this->_registration)) {
					$this->_registration = null;
				}
			}
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
		if ($this->request->isPost()) {

			$postRegistration = $this->request->data;

			// 登録フォームデータに作成されたPost質問データをかぶせる
			// （質問作成画面では質問データ属性全てをPOSTしているのですり替えでOK）
			$registration = $this->_registration;
			$registration['Registration'] = Hash::merge($this->_registration['Registration'], $postRegistration['Registration']);

			// 発行後の登録フォームは質問情報は書き換えない
			// 未発行の場合はPostデータを上書き設定して
			//if ($this->Registration->hasPublished($registration) == 0) {
			//	$registration['RegistrationPage'] = $postRegistration['RegistrationPage'];
			//} else {
			//	// booleanの値がPOST時と同じようになるように調整
			//	$registration['RegistrationPage'] = RegistrationsAppController::changeBooleansToNumbers($registration['RegistrationPage']);
			//}
			// いつも編集OK
			$registration['RegistrationPage'] = $postRegistration['RegistrationPage'];

			// バリデート
			$this->Registration->set($registration);
			if (! $this->Registration->validates(array('validate' => 'duringSetup'))) {
				$this->__setupViewParameters($registration, '');
				return;
			}

			// バリデートがOKであればPOSTで出来上がったデータをセッションキャッシュに書く
			$this->Session->write(self::REGISTRATION_EDIT_SESSION_INDEX . $this->_sessionIndex, $registration);

			// 次の画面へリダイレクト
			//$this->redirect($this->_getActionUrl('edit_result'));
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
 * edit_result
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit_result() {
		// 処理対象の登録フォームデータが見つかっていない場合、エラー
		if (empty($this->_registration)) {
			$this->throwBadRequest();
			return;
		}

		if ($this->request->isPost()) {

			$postRegistration = $this->request->data;

			// 集計設定画面では集計に纏わる情報のみがPOSTされるので安心してマージ
			$registration = Hash::merge($this->_registration, $postRegistration);
			// バリデート
			$this->Registration->set($registration);
			if (! $this->Registration->validates(array('validate' => 'duringSetup'))) {
				$this->__setupViewParameters($registration, $this->_getActionUrl('edit_question'));
				return;
			}
			// それをキャッシュに書く
			$this->Session->write(self::REGISTRATION_EDIT_SESSION_INDEX . $this->_getRegistrationEditSessionIndex(), $registration);

			// 次の画面へリダイレクト
			$this->redirect($this->_getActionUrl('edit'));

		} else {
			$this->Session->write(self::REGISTRATION_EDIT_SESSION_INDEX . $this->_getRegistrationEditSessionIndex(), $this->_registration);
			$this->__setupViewParameters($this->_registration, $this->_getActionUrl('edit_question'));
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

		if ($this->request->isPost() || $this->request->isPut()) {
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
				//$this->__setupViewParameters($registration, $this->_getActionUrl('edit_result'));
				$this->__setupViewParameters($registration, $this->_getActionUrl('edit_question'));
				return;
			}
			// 成功時 セッションに書き溜めた編集情報を削除
			$this->Session->delete(self::REGISTRATION_EDIT_SESSION_INDEX . $this->_getRegistrationEditSessionIndex());
			// ページトップへリダイレクト
			$this->redirect(NetCommonsUrl::backToPageUrl());

		} else {
			// 指定されて取り出した登録フォームデータをセッションキャッシュに書く
			$this->Session->write($this->_getRegistrationEditSessionIndex(), $this->_registration);
			$this->__setupViewParameters($this->_registration, $this->_getActionUrl('edit_question'));
		}
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete() {
		if (! $this->request->isDelete()) {
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

		$this->redirect(NetCommonsUrl::backToPageUrl());
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
		return NetCommonsUrl::actionUrl(array(
			'controller' => Inflector::underscore($this->name),
			'action' => $method,
			Current::read('Block.id'),
			$this->_getRegistrationKey($this->_registration),
			'frame_id' => Current::read('Frame.id'),
			's_id' => $this->_getRegistrationEditSessionIndex()
		));
	}
/**
 * __setupViewParameters method
 *
 * @param array $registration 登録フォームデータ
 * @param string $backUrl BACKボタン押下時の戻るパス
 * @return void
 */
	private function __setupViewParameters($registration, $backUrl) {
		$isPublished = $this->Registration->hasPublished($registration);

		// エラーメッセージはページ、質問、選択肢要素のそれぞれの場所に割り当てる
		$this->NetCommons->handleValidationError($this->Registration->validationErrors);
		$flatError = Hash::flatten($this->Registration->validationErrors);
		$newFlatError = array();
		foreach ($flatError as $key => $val) {
			if (preg_match('/^(.*)\.(.*)\.(.*)$/', $key, $matches)) {
				$newFlatError[$matches[1] . '.error_messages.' . $matches[2] . '.' . $matches[3]] = $val;
			}
		}
		$registration = Hash::merge($registration, Hash::expand($newFlatError));

		$this->set('backUrl', $backUrl);
		$this->set('postUrl', array('url' => $this->_getActionUrl($this->action)));
		$this->set('cancelUrl', $this->_getActionUrl('cancel'));
		$this->set('questionTypeOptions', $this->Registrations->getQuestionTypeOptionsWithLabel());
		$this->set('newPageLabel', __d('registrations', 'page'));
		$this->set('newQuestionLabel', __d('registrations', 'New Question'));
		$this->set('newChoiceLabel', __d('registrations', 'new choice'));
		$this->set('newChoiceColumnLabel', __d('registrations', 'new column choice'));
		$this->set('newChoiceOtherLabel', __d('registrations', 'other choice'));
		//$this->set('isPublished', $isPublished);
		$this->set('isPublished', false);
		$this->request->data = $registration;
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');

		// ? FUJI いる？
		//$this->set('contentStatus', $registration['Registration']['status']);
		//$this->set('comments', $this->Registration->getCommentsByContentKey($registration['Registration']['key']));
	}
}
