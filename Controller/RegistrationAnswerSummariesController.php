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
		'Registrations.RegistrationFrameSetting',
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
		'NetCommons.TitleIcon',
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
		$this->Auth->allow('view', 'no_summaries');

		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// 登録フォームキー
		$registrationKey = $this->_getRegistrationKeyFromPass();

		// キーで指定された登録フォームデータを取り出しておく
		$conditions = $this->Registration->getWorkflowConditions(
			array('Registration.key' => $registrationKey)
		);

		$this->__registration = $this->Registration->find('first', array(
			'conditions' => $conditions,
			'recursive' => 1
		));
		if (! $this->__registration) {
			$this->setAction('throwBadRequest');
		}

		// 現在の表示形態を調べておく
		list($this->__displayType) =
			$this->RegistrationFrameSetting->getRegistrationFrameSetting(Current::read('Frame.key'));

		//集計表示していいかどうかの判断

		if (! $this->isAbleToDisplayAggregatedData($this->__registration)) {
			$this->setAction('no_summaries');
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

/**
 * no_summaries method
 * 条件によって集計結果が見れない登録フォームにアクセスしたときに表示
 *
 * @return void
 */
	public function no_summaries() {
		$this->set('displayType', $this->__displayType);
	}

}
