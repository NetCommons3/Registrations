<?php
/**
 * RegistrationAnswers Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * RegistrationAnswersController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 * @property RegistrationAnswer $RegistrationAnswer
 * @property RegistrationsOwnAnswerComponent $RegistrationOwnAnswer
 * @property RegistrationAnswerSummary $RegistrationAnswerSummary
 */
class RegistrationAnswersController extends RegistrationsAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Registrations.RegistrationPage',
		'Registrations.RegistrationAnswerSummary',
		'Registrations.RegistrationAnswer',
		//'Registrations.RegistrationFrameSetting',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission',
		'Registrations.Registrations',
		'Registrations.RegistrationsOwnAnswer',
		'AuthorizationKeys.AuthorizationKey' => array(
			'operationType' => 'embed',
			'targetAction' => 'view',
			'model' => 'Registration',
			'contentId' => 0),
		'VisualCaptcha.VisualCaptcha' => array(
			'operationType' => 'embed',
			'targetAction' => 'view'),
	);

/**
 * use helpers
 *
 */
	public $helpers = [
		'NetCommons.Date',
		'NetCommons.TitleIcon',
		'Workflow.Workflow',
		'Registrations.RegistrationAnswer'
	];

/**
 * target registration data
 *
 */
	private $__registration = null;

/**
 * target isAbleToAnswer Action
 *
 */
	private $__ableToAnswerAction = ['view', 'confirm'];

/**
 * frame setting display type
 */
	private $__displayType = null;

/**
 * beforeFilter
 * NetCommonsお約束：できることならControllerのbeforeFilterで実行可/不可の判定して流れを変える
 *
 * @return void
 */
	public function beforeFilter() {
		// ゲストアクセスOKのアクションを設定
		$this->Auth->allow('view', 'confirm', 'thanks', 'no_more_answer', 'key_auth', 'img_auth');

		// 親クラスのbeforeFilterを済ませる
		parent::beforeFilter();

		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// 登録フォームキー
		//$registrationKey = $this->_getRegistrationKeyFromPass();

		// キーで指定された登録フォームデータを取り出しておく
		$conditions = $this->Registration->getWorkflowConditions(
			//array('Registration.key' => $registrationKey)
			array('Registration.block_id' => Current::read('Block.id'))
		);

		$this->__registration = $this->Registration->find('first', array(
			'conditions' => $conditions,
			'recursive' => 1
		));
		if (! $this->__registration) {
			$this->setAction('throwBadRequest');	// returnをつけるとテストコードが通らない
		}

		// 現在の表示形態を調べておく
		//list($this->__displayType) = $this->RegistrationFrameSetting->getRegistrationFrameSetting(
		//	Current::read('Frame.key')
		//);

		// 以下のisAbleto..の内部関数にてNetCommonsお約束である編集権限、参照権限チェックを済ませています
		// 閲覧可能か
		if (!$this->isAbleTo($this->__registration)) {
			// 不可能な時は「登録できません」画面を出すだけ
			$this->setAction('no_more_answer');
			return;
		}
		if (in_array($this->action, $this->__ableToAnswerAction)) {
			// 登録可能か
			if (!$this->isAbleToAnswer($this->__registration)) {
				// 登録が不可能な時は「登録できません」画面を出すだけ
				$this->setAction('no_more_answer');
				return;
			}
		}
	}

/**
 * test_mode
 *
 * テストモード登録のとき、一番最初に表示するページ
 * 一覧表示画面で「テスト」ボタンがここへ誘導するようになっている。
 * どのような登録フォームであるのかの各種属性設定をわかりやすくまとめて表示する表紙的な役割を果たす。
 *
 * あくまで作成者の便宜のために表示しているものであるので、最初のページだったら必ずここを表示といったような
 * 強制的redirectなどは設定しない。なので強制URL-Hackしたらこの画面をスキップすることだって可能。
 * 作成者への「便宜」のための親切心ページなのでスキップしたい人にはそうさせてあげるのでよいと考える。
 *
 * @return void
 */
	public function test_mode() {
		$status = $this->__registration['Registration']['status'];
		// テストモード確認画面からのPOSTや、現在の登録フォームデータのステータスが公開状態の時
		// 次へリダイレクト
		if ($this->request->is('post') || $status == WorkflowComponent::STATUS_PUBLISHED) {
			$this->_redirectAnswerPage();
			return;
		}
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('registration', $this->__registration);
	}

/**
 * _viewGuard
 *
 * 登録フォームが認証キーや画像認証でガードされているかどうかを調べ、
 * ガードがある場合は適宜、相当のアクションへ転送する
 *
 * @return void
 */
	protected function _viewGuard() {
		$registrationKey = $this->_getRegistrationKey($this->__registration);
		$quest = $this->__registration['Registration'];

		if (!$this->Session->check('Registration.auth_ok.' . $registrationKey)) {
			if ($this->request->is('get') ||
				!isset($this->request->data['RegistrationPage']['page_sequence'])) {
				// 認証キーコンポーネントお約束：
				if ($quest['is_key_pass_use'] == RegistrationsComponent::USES_USE) {
					$this->AuthorizationKey->contentId = $quest['id'];
					$this->AuthorizationKey->guard(
						AuthorizationKeyComponent::OPERATION_EMBEDDING,
						'Registration',
						$this->__registration);
					$this->setAction('key_auth');
					return;
				}
				if ($quest['is_image_authentication'] == RegistrationsComponent::USES_USE) {
					// 画像認証コンポーネントお約束：
					$this->setAction('img_auth');
					return;
				}
			}
		} else {
			$this->Session->delete('Registration.auth_ok.' . $registrationKey);
		}
	}

/**
 * key_auth
 *
 * 認証キーガード
 *
 * @return void
 */
	public function key_auth() {
		$isKeyPassUse = $this->__registration['Registration']['is_key_pass_use'];
		if ($isKeyPassUse != RegistrationsComponent::USES_USE) {
			$this->_redirectAnswerPage();
			return;
		}
		$qKey = $this->_getRegistrationKey($this->__registration);
		if ($this->request->is('post')) {
			if ($this->AuthorizationKey->check()) {
				$this->Session->write('Registration.auth_ok.' . $qKey, 'OK');
				// 画面へ行く
				$url = NetCommonsUrl::actionUrl(array(
					'controller' => 'registration_answers',
					'action' => 'view',
					Current::read('Block.id'),
					$qKey,
					'frame_id' => Current::read('Frame.id'),
				));
				$this->redirect($url);
				return;
			}
		}
		$url = NetCommonsUrl::actionUrl(array(
			'controller' => 'registration_answers',
			'action' => 'key_auth',
			Current::read('Block.id'),
			$qKey,
			'frame_id' => Current::read('Frame.id'),
		));
		$this->set('registration', $this->__registration);
		$this->set('displayType', $this->__displayType);
		$this->set('postUrl', $url);
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}

/**
 * img_auth
 *
 * 画像認証ガード
 *
 * @return void
 */
	public function img_auth() {
		$isImgUse = $this->__registration['Registration']['is_image_authentication'];
		if ($isImgUse != RegistrationsComponent::USES_USE) {
			$this->_redirectAnswerPage();
			return;
		}
		$qKey = $this->_getRegistrationKey($this->__registration);
		if ($this->request->is('post')) {
			if ($this->VisualCaptcha->check()) {
				$this->Session->write('Registration.auth_ok.' . $qKey, 'OK');
				// 画面へ行く
				$this->_redirectAnswerPage();
				return;
			}
		}
		$url = NetCommonsUrl::actionUrl(array(
			'controller' => 'registration_answers',
			'action' => 'img_auth',
			Current::read('Block.id'),
			$qKey,
			'frame_id' => Current::read('Frame.id'),
		));
		$this->set('registration', $this->__registration);
		$this->set('displayType', $this->__displayType);
		$this->set('postUrl', $url);
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}

/**
 * view method
 * Display the question of the registration , to accept the answer input
 *
 * @return void
 */
	public function view() {
		$registration = $this->__registration;
		$registrationKey = $this->_getRegistrationKey($this->__registration);

		//登録数制限
		if ($this->_isLimitNumber($registration)) {
			$this->set('registration', $registration);
			$this->render('limit');
			return;
		}
		//
		$this->_viewGuard();

		// 選択肢ランダム表示対応
		$this->__shuffleChoice($registration);

		// ページの指定のない場合はFIRST_PAGE_SEQUENCEをデフォルトとする
		$nextPageSeq = RegistrationsComponent::FIRST_PAGE_SEQUENCE;	// default

		$postPageSeq = null;
		if (isset($this->data['RegistrationPage']['page_sequence'])) {
			$postPageSeq = $this->data['RegistrationPage']['page_sequence'];
		}

		// POSTチェック
		if ($this->request->is('post')) {
			// サマリ情報準備
			$summary = $this->RegistrationsOwnAnswer->forceGetProgressiveAnswerSummary(
				$this->__registration
			);
			$nextPageSeq = $postPageSeq;

			// 登録データがある場合は登録をDBに書きこむ
			if (isset($this->data['RegistrationAnswer'])) {
				if (! $this->RegistrationAnswer->saveAnswer($this->data, $registration, $summary)) {
					// 保存エラーの場合は今のページを再表示
					$nextPageSeq = $postPageSeq;
				} else {
					// 登録データがあり、無事保存できたら次ページを取得する
					$nextPageSeq = $this->RegistrationPage->getNextPage(
						$registration,
						$postPageSeq,
						$this->data['RegistrationAnswer']);
				}
			}
			// 次ページはもう存在しない
			if ($nextPageSeq === false) {
				// 確認画面へいってもよい状態ですと書きこむ
				$this->RegistrationAnswerSummary->saveAnswerStatus(
					$summary,
					RegistrationsComponent::ACTION_BEFORE_ACT
				);
				// 確認画面へ
				$url = NetCommonsUrl::actionUrl(array(
					'controller' => 'registration_answers',
					'action' => 'confirm',
					Current::read('Block.id'),
					$registrationKey,
					'frame_id' => Current::read('Frame.id'),
				));
				$this->redirect($url);
				return;
			}
		}
		if (! ($this->request->is('post') && $nextPageSeq == $postPageSeq)) {
			$summary = $this->RegistrationsOwnAnswer->getProgressiveSummaryOfThisUser(
				$registrationKey);
			$setAnswers = $this->RegistrationAnswer->getProgressiveAnswerOfThisSummary(
				$registration,
				$summary);
			$this->set('answers', $setAnswers);
			$this->request->data['RegistrationAnswer'] = $setAnswers;

			// 入力される登録データですがsetで設定するデータとして扱います
			// 誠にCake流儀でなくて申し訳ないのですが、様々な種別のAnswerデータを
			// 特殊な文字列加工して統一化した形状でDBに入れている都合上、このような仕儀になっています
		} else {
			$this->set('answers', $this->request->data['RegistrationAnswer']);
		}

		// 項目情報をView変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->request->data['RegistrationPage'] = $registration['RegistrationPage'][$nextPageSeq];
		$this->set('registration', $registration);
		$this->set('questionPage', $registration['RegistrationPage'][$nextPageSeq]);
		$this->set('displayType', $this->__displayType);
		$this->NetCommons->handleValidationError($this->RegistrationAnswer->validationErrors);

		//新着データを既読にする
		$this->Registration->saveTopicUserStatus($registration);
	}

/**
 * confirm method
 *
 * @return void
 */
	public function confirm() {
		// 確認してもいいサマリレコード取得
		$summary = $this->RegistrationsOwnAnswer->getConfirmSummaryOfThisUser(
			$this->_getRegistrationKey($this->__registration));
		if (!$summary) {
			$this->setAction('throwBadRequest');
			return;
		}

		// 解答入力画面で表示していたときのシャッフルを取り出す
		$this->__shuffleChoice($this->__registration);

		// POSTチェック
		if ($this->request->is('post')) {
			// サマリの状態を完了にして確定する
			$this->RegistrationAnswerSummary->saveAnswerStatus(
				$summary,
				RegistrationsComponent::ACTION_ACT);
			$this->RegistrationsOwnAnswer->saveOwnAnsweredKeys(
				$this->_getRegistrationKey($this->__registration));

			// ありがとう画面へ行く
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'registration_answers',
				'action' => 'thanks',
				Current::read('Block.id'),
				$this->_getRegistrationKey($this->__registration),
				'frame_id' => Current::read('Frame.id'),
				'summary_id' => $summary['RegistrationAnswerSummary']['id']
			));
			$this->redirect($url);
		}

		// 登録情報取得
		// 登録情報並べ替え
		$setAnswers = $this->RegistrationAnswer->getProgressiveAnswerOfThisSummary(
			$this->__registration,
			$summary);

		// 項目情報をView変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('registration', $this->__registration);
		$this->request->data['RegistrationAnswer'] = $setAnswers;
		$this->set('answers', $setAnswers);
		$this->set('displayType', $this->__displayType);
	}

/**
 * thanks method
 *
 * @return void
 */
	public function thanks() {
		$qKey = $this->__registration['Registration']['key'];
		// 登録済みか確認
		if (! $this->RegistrationsOwnAnswer->checkOwnAnsweredKeys($qKey)) {
			$this->setAction('throwBadRequest');
			return;
		}
		// 後始末
		// 登録中にたまっていたセッションキャッシュをクリア
		$this->Session->delete('Registrations.' . $qKey);

		// 登録データを取得
		$summary = $this->RegistrationAnswerSummary->findById($this->request->named['summary_id']);
		// 項目のIDを取得
		$questionIds = Hash::extract(
			$this->__registration['RegistrationPage'],
			'{n}.RegistrationQuestion.{n}.id');
		$answers = $this->RegistrationAnswer->getAnswersBySummary($summary, $questionIds);
		$this->set('summary', $summary);
		$this->set('answers', $answers);

		// View変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('registration', $this->__registration);
		$this->set('ownAnsweredKeys', $this->RegistrationsOwnAnswer->getOwnAnsweredKeys());
		$this->set('displayType', $this->__displayType);

		//新着データを登録済みにする
		$this->Registration->saveTopicUserStatus($this->__registration, true);
	}

/**
 * no_more_answer method
 * 条件によって登録できない登録フォームにアクセスしたときに表示
 *
 * @return void
 */
	public function no_more_answer() {
		$this->set('displayType', $this->__displayType);
	}

/**
 * _shuffleChoice
 * shuffled choices and write into session
 *
 * @param array &$registration 登録フォーム
 * @return void
 */
	private function __shuffleChoice(&$registration) {
		foreach ($registration['RegistrationPage'] as &$page) {
			foreach ($page['RegistrationQuestion'] as &$q) {
				$choices = $q['RegistrationChoice'];
				if ($q['is_choice_random'] == RegistrationsComponent::USES_USE) {
					$sessionPath = sprintf(
						'Registrations.%s.RegistrationQuestion.%s.RegistrationChoice',
						$registration['Registration']['key'],
						$q['key']
					);
					if ($this->Session->check($sessionPath)) {
						$choices = $this->Session->read($sessionPath);
					} else {
						shuffle($choices);
						$this->Session->write($sessionPath, $choices);
					}
				}
				$q['RegistrationChoice'] = $choices;
			}
		}
	}
/**
 * _redirectAnswerPage
 *
 * @return void
 */
	protected function _redirectAnswerPage() {
		$this->redirect(NetCommonsUrl::actionUrl(array(
			'controller' => 'registration_answers',
			'action' => 'view',
			Current::read('Block.id'),
			$this->_getRegistrationKey($this->__registration),
			'frame_id' => Current::read('Frame.id')
		)));
	}

/**
 * 登録数制限に達してるかチェック
 *
 * @param array $registration Registrationデータ
 * @return bool
 */
	protected function _isLimitNumber($registration) {
		if ($registration['Registration']['is_limit_number']) {
			$limit = $registration['Registration']['limit_number'];
			// 登録数カウント
			$options = [
				'conditions' => $this->RegistrationAnswerSummary->getResultCondition($registration)
			];
			$answerCount = $this->RegistrationAnswerSummary->find('count', $options);
			if ($limit <= $answerCount) {
				return true;
			}
		}
		return false;
	}
}
