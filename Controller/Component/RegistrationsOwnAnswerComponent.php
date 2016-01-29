<?php
/**
 * RegistrationsOwnAnswer Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * RegistrationsOwnAnswerComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationsOwnAnswerComponent extends Component {

/**
 * Answered registration keys
 *
 * 登録済み登録フォームキー配列
 *
 * @var array
 */
	private $__ownAnsweredKeys = null;

/**
 * 指定された登録フォームに該当する登録中登録フォームのサマリを取得する
 *
 * @param string $registrationKey 登録済に追加する登録フォームキー
 * @return progressive Answer Summary id list
 */
	public function getProgressiveSummaryOfThisUser($registrationKey) {
		// 戻り値初期化
		$summary = false;
		$answerSummary = ClassRegistry::init('Registrations.RegistrationAnswerSummary');
		// 未ログインの人の場合はセッションにある登録中データを参照する
		//if (! Current::read('User.id')) {
		// 登録フォームとしてはセッションだけで充分
			$session = $this->_Collection->load('Session');
			$summaryId = $session->read('Registrations.progressiveSummary.' . $registrationKey);
			if ($summaryId) {
				$summary = $answerSummary->findById($summaryId);
			}
			return $summary;
		//}
		//// ログインユーザーはDBから探す
		//$conditions = array(
		//	'answer_status' => RegistrationsComponent::ACTION_NOT_ACT,
		//	'registration_key' => $registrationKey,
		//	'user_id' => Current::read('User.id'),
		//);
		//$summary = $answerSummary->find('first', array(
		//	'conditions' => $conditions,
		//	'order' => 'RegistrationAnswerSummary.created DESC'	// 最も新しいものを一つ選ぶ
		//));
		//return $summary;
	}
/**
 * 指定された登録フォームに対応する登録中サマリを作成
 *
 * @param array $registration 登録フォーム
 * @return progressive Answer Summary data
 */
	public function forceGetProgressiveAnswerSummary($registration) {
		$summary = $this->getProgressiveSummaryOfThisUser($registration['Registration']['key']);
		if (! $summary) {
			$answerSummary = ClassRegistry::init('Registrations.RegistrationAnswerSummary');
			$session = $this->_Collection->load('Session');
			$summary = $answerSummary->forceGetProgressiveAnswerSummary($registration, Current::read('User.id'), $session->id());
			if ($summary) {
				$this->saveProgressiveSummaryOfThisUser($registration['Registration']['key'], $summary['RegistrationAnswerSummary']['id']);
			}
		}

		return $summary;
	}

/**
 * 指定された登録フォームのサマリIDを登録中サマリIDとしてセッションに記録
 *
 * @param string $registrationKey 登録中の登録フォームキー
 * @param int $summaryId 登録中のサマリのID
 * @return void
 */
	public function saveProgressiveSummaryOfThisUser($registrationKey, $summaryId) {
		$session = $this->_Collection->load('Session');
		$session->write('Registrations.progressiveSummary.' . $registrationKey, $summaryId);
	}
/**
 * セッションから指定された登録フォームの登録中サマリIDを削除
 *
 * @param string $registrationKey 登録フォームキー
 * @return void
 */
	public function deleteProgressiveSummaryOfThisUser($registrationKey) {
		$session = $this->_Collection->load('Session');
		$session->delete('Registrations.progressiveSummary.' . $registrationKey);
	}

/**
 * 登録済み登録フォームリストを取得する
 *
 * @return Answered Registration keys list
 */
	public function getOwnAnsweredKeys() {
		if (isset($this->__ownAnsweredKeys)) {
			return $this->__ownAnsweredKeys;
		}

		$this->__ownAnsweredKeys = array();

		if (! Current::read('User.id')) {
			$session = $this->_Collection->load('Session');
			$blockId = Current::read('Block.id');
			$ownAnsweredKeys = $session->read('Registrations.ownAnsweredKeys.' . $blockId);
			if (isset($ownAnsweredKeys)) {
				$this->__ownAnsweredKeys = explode(',', $ownAnsweredKeys);
			}

			return $this->__ownAnsweredKeys;
		}

		$answerSummary = ClassRegistry::init('Registrations.RegistrationAnswerSummary');
		$conditions = array(
			'user_id' => Current::read('User.id'),
			'answer_status' => RegistrationsComponent::ACTION_ACT,
			'test_status' => RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM,
			'answer_number' => 1
		);
		$ownAnsweredKeys = $answerSummary->find(
			'list',
			array(
				'conditions' => $conditions,
				'fields' => array('RegistrationAnswerSummary.registration_key'),
				'recursive' => -1
			)
		);
		$this->__ownAnsweredKeys = array_values($ownAnsweredKeys);	// idの使用を防ぐ（いらない？）

		return $this->__ownAnsweredKeys;
	}
/**
 * 登録フォーム登録済みかどうかを返す
 *
 * @param string $registrationKey 登録済に追加する登録フォームキー
 * @return bool
 */
	public function checkOwnAnsweredKeys($registrationKey) {
		// まだ登録済データが初期状態のときはまずは確保
		if ($this->__ownAnsweredKeys === null) {
			$this->getOwnAnsweredKeys();
		}
		if (in_array($registrationKey, $this->__ownAnsweredKeys)) {
			return true;
		}
		return false;
	}
/**
 * セッションの登録済み登録フォームリストに新しい登録フォームを追加する
 *
 * @param string $registrationKey 登録済に追加する登録フォームキー
 * @return void
 */
	public function saveOwnAnsweredKeys($registrationKey) {
		// まだ登録済データが初期状態のときはまずは確保
		if ($this->__ownAnsweredKeys === null) {
			$this->getOwnAnsweredKeys();
		}
		// 登録済み登録フォーム配列に追加
		$this->__ownAnsweredKeys[] = $registrationKey;
		// ログイン状態の人の場合はこれ以上の処理は不要
		// 登録フォームではどちらもセッション保存にしたのでログインユーザでもセッション削除を行う
		//if (Current::read('User.id')) {
		//	return;
		//}
		// 未ログインの人の場合はセッションに書いておく
		$session = $this->_Collection->load('Session');
		$blockId = Current::read('Block.id');
		$session->write('Registrations.ownAnsweredKeys.' . $blockId, implode(',', $this->__ownAnsweredKeys));

		// 登録中登録フォームからは削除しておく
		$this->deleteProgressiveSummaryOfThisUser($registrationKey);
	}

}
