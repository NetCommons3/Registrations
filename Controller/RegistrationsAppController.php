<?php
/**
 * Registrations AppController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * RegistrationsAppController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationsAppController extends AppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Registrations.Registration',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Security',
		'Pages.PageLayout',
		'Registrations.Registrations',
		'Registrations.RegistrationsOwnAnswer',
	);

/**
 * _sorted method
 * to sort a given array by key
 *
 * @param array $obj data array
 * @return array ソート後配列
 */
	protected function _sorted($obj) {
		// シーケンス順に並び替え、かつ、インデックス値は０オリジン連番に変更
		// ページ配列もないのでそのまま戻す
		if (!Hash::check($obj, 'RegistrationPage.{n}')) {
			return $obj;
		}
		$obj['RegistrationPage'] =
			Hash::sort($obj['RegistrationPage'], '{n}.page_sequence', 'asc', 'numeric');

		foreach ($obj['RegistrationPage'] as &$page) {
			if (Hash::check($page, 'RegistrationQuestion.{n}')) {
				$page['RegistrationQuestion'] =
					Hash::sort($page['RegistrationQuestion'], '{n}.question_sequence', 'asc', 'numeric');

				foreach ($page['RegistrationQuestion'] as &$question) {
					if (Hash::check($question, 'RegistrationChoice.{n}')) {
						$question['RegistrationChoice'] =
							Hash::sort($question['RegistrationChoice'], '{n}.choice_sequence', 'asc', 'numeric');
					}
				}
			}
		}
		return $obj;
	}

/**
 * changeBooleansToNumbers method
 * to change the Boolean value of a given array to 0,1
 *
 * @param array $data data array
 * @return array
 */
	public static function changeBooleansToNumbers(Array $data) {
		// Note the order of arguments and the & in front of $value
		array_walk_recursive($data, 'self::converter');
		return $data;
	}

/**
 * __converter method
 * to change the Boolean value to 0,1
 *
 * @param array &$value value
 * @param string $key key
 * @return void
 * @SuppressWarnings("unused")
 */
	public static function converter(&$value, $key) {
		if (is_bool($value)) {
			$value = ($value ? '1' : '0');
		}
	}

/**
 * _decideSettingLayout
 *
 * セッティング系の画面からの流れなのかどうかを判断し、レイアウトを決める
 * 
 * @return void
 */
	protected function _decideSettingLayout() {
		$isSetting = Hash::get($this->request->params, 'named.q_mode');
		if ($isSetting == 'setting') {
			if (Current::permission('block_editable')) {
				$this->layout = 'NetCommons.setting';
			}
			return;
		}
	}
/**
 * isAbleTo
 * Whether access to survey of the specified ID
 * Forced URL hack guard
 * And against the authority of the state of the specified registration respondents put a guard
 * It is not in the public state
 * Out of period
 * Stopped
 * Repeatedly answer
 * You are not logged in to not forgive other than member
 *
 * @param array $registration 対象となる登録フォームデータ
 * @return bool
 */
	public function isAbleTo($registration) {
		// 指定の登録フォームの状態と登録者の権限を照らし合わせてガードをかける
		// 編集権限を持っていない場合
		//   公開状態にない
		//   期間外
		//   停止中
		//   繰り返し登録
		//   会員以外には許してないのに未ログインである

		// 編集権限を持っている場合
		//   公開状態にない場合はALL_OK
		//
		// 　公開状態になっている場合は
		//   期間外
		//   停止中
		//   繰り返し登録
		//   会員以外には許してないのに未ログインである

		// 公開状態が「公開」になっている場合は編集権限の有無にかかわらず共通だ
		// なのでまずは公開状態だけを確認する

		// 編集権限があればオールマイティＯＫなのでこの後の各種チェックは不要！
		if ($this->Registration->canEditWorkflowContent($registration)) {
			return true;
		}
		// 編集権限がない場合は、もろもろのチェックを行うこと

		// 読み取り権限がない？
		if (! $this->Registration->canReadWorkflowContent()) {
			// それはだめだ
			return false;
		}

		$quest = $registration['Registration'];

		// 基本、権限上、見ることができるコンテンツだ
		// しかし、登録フォーム独自の条件部分のチェックを行う必要がある
		// 期間外
		if ($quest['answer_timing'] == RegistrationsComponent::USES_USE
			&& $quest['period_range_stat'] != RegistrationsComponent::REGISTRATION_PERIOD_STAT_IN) {
			return false;
		}

		// 会員以外には許していないのに未ログイン
		if ($quest['is_no_member_allow'] == RegistrationsComponent::PERMISSION_NOT_PERMIT) {
			if (! Current::read('User.id')) {
				return false;
			}
		}

		return true;
	}
/**
 * isAbleToAnswer 指定されたIDに登録できるかどうか
 * 強制URLハックのガード
 * 指定の登録フォームの状態と登録者の権限を照らし合わせてガードをかける
 * 公開状態にない
 * 期間外
 * 停止中
 * 繰り返し登録
 * 会員以外には許してないのに未ログインである
 *
 * @param array $registration 対象となる登録フォームデータ
 * @return bool
 */
	public function isAbleToAnswer($registration) {
		$quest = $registration['Registration'];
		if ($quest['status'] != WorkflowComponent::STATUS_PUBLISHED) {
			return true;
		}
		// 繰り返し登録を許していないのにすでに登録済みか
		if ($quest['is_repeat_allow'] == RegistrationsComponent::PERMISSION_NOT_PERMIT) {
			if ($this->RegistrationsOwnAnswer->checkOwnAnsweredKeys($quest['key'])) {
				return false;
			}
		}

		return true;
	}
/**
 * isAbleToDisplayAggregatedData 指定されたIDを集計表示していいいかどうか？
 *
 * @param int $registration Registration
 * @return bool
 */
	public function isAbleToDisplayAggregatedData($registration) {
		$quest = $registration['Registration'];
		// 集計表示許さているか
		if ($quest['is_total_show'] != RegistrationsComponent::EXPRESSION_SHOW) {
			return false;
		}

		// 編集権限がある場合は無条件に許可
		if ($this->Registration->canEditWorkflowContent($registration)) {
			return true;
		}

		// 集計表示に期間設定しているか
		// 期間設定がある
		if ($quest['total_show_timing'] == RegistrationsComponent::USES_USE) {
			$nowDatetime = (new NetCommonsTime())->getNowDatetime();
			// まだ公開期間ではない
			if (strtotime($nowDatetime) < strtotime($quest['total_show_start_period'])) {
				return false;
			}
		}

		// 会員以外には許していないのに未ログイン
		if ($quest['is_no_member_allow'] == RegistrationsComponent::PERMISSION_NOT_PERMIT) {
			if (! Current::read('User.id')) {
				return false;
			}
		}

		// していない または 公開期間内
		// 本人登録があるかどうがか表示有無の判断基準
		if (! $this->RegistrationsOwnAnswer->checkOwnAnsweredKeys($quest['key'])) {
			//本人による「登録」データなし
			return false;	// 見てはいけない
		}
		//本人による「登録」データあり
		return true;	// みてよし
	}

/**
 * _getRegistrationKeyFromPass
 *
 * @return string
 */
	protected function _getRegistrationKeyFromPass() {
		if (isset($this->params['pass'][RegistrationsComponent::REGISTRATION_KEY_PASS_INDEX])) {
			$key = $this->params['pass'][RegistrationsComponent::REGISTRATION_KEY_PASS_INDEX];
			if (strpos($key, 's_id:') === 0) {
				return '';
			}
			return $key;
		}
		return '';
	}

/**
 * _getRegistrationEditSessionIndex
 *
 * @return string
 */
	protected function _getRegistrationEditSessionIndex() {
		if (isset($this->_sessionIndex) && $this->_sessionIndex !== null) {
			return $this->_sessionIndex;
		}
		if (isset($this->params['named']['s_id'])) {
			$tm = $this->params['named']['s_id'];
		} else {
			$tm = Security::hash(microtime(true), 'md5');
		}
		$this->_sessionIndex = $tm;
		return $tm;
	}

/**
 * _getRegistrationKey
 *
 * @param array $registration Registration data
 * @return string
 */
	protected function _getRegistrationKey($registration) {
		if (isset($registration['Registration']['key'])) {
			return $registration['Registration']['key'];
		} else {
			return '';
		}
	}

}
