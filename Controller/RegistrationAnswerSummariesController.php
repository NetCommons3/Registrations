<?php
/**
 * RegistrationAnswerSummaries Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * RegistrationAnswerSummariesController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationAnswerSummariesController extends RegistrationsAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Registrations.Registration',
		'Registrations.RegistrationPage',
		'Registrations.RegistrationQuestion',
		'Registrations.RegistrationChoice',
		'Registrations.RegistrationAnswerSummary',
		'Registrations.RegistrationAnswer',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission',
		'Registrations.Registrations',
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'Workflow.Workflow',
	);

/**
 * target registration data
 *
 */
	private $__registration = null;

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		// ゲストアクセスOKのアクションを設定
		$this->Auth->allow('view');

		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// 登録フォームキー
		$registrationKey = $this->_getRegistrationKeyFromPass();

		// キーで指定された登録フォームデータを取り出しておく
		$conditions = $this->Registration->getBaseCondition(
			array('Registration.key' => $registrationKey)
		);
		// 集計結果の表示は登録フォーム公開時期とは異なるので
		$conditions = Hash::remove($conditions, 'public_type');
		$conditions = Hash::remove($conditions, 'publish_start');
		$conditions = Hash::remove($conditions, 'publish_end');

		$this->__registration = $this->Registration->find('first', array(
			'conditions' => $conditions,
		));
		if (! $this->__registration) {
			$this->setAction('throwBadRequest');
			return;
		}

		//集計表示していいかどうかの判断

		if (! $this->isAbleToDisplayAggregatedData($this->__registration)) {
			$this->setAction('throwBadRequest');
			return;
		}
	}

/**
 * result method
 *
 * @return void
 */
	public function view() {
		$registration = $this->__registration;

		//集計処理を行います。
		$questions = $this->RegistrationAnswerSummary->getAggregate($registration);

		//画面用データをセットする。
		$this->set('registration', $registration);
		$this->set('questions', $questions);
	}

}