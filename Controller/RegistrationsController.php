<?php
/**
 * Registrations Controller
 *
 * @property PaginatorComponent $Paginator
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * RegistrationsController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationsController extends RegistrationsAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Registrations.RegistrationFrameSetting',
		'Registrations.RegistrationFrameDisplayRegistration',
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
				'edit,delete' => 'content_creatable',
			),
		),
		'Registrations.Registrations',
		'Registrations.RegistrationsOwnAnswer',
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Workflow.Workflow',
		'NetCommons.Date',
		'NetCommons.DisplayNumber',
		'NetCommons.Button',
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
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		// 表示方法設定値取得
		list(, $displayNum, $sort, $dir) =
			$this->RegistrationFrameSetting->getRegistrationFrameSetting(Current::read('Frame.key'));

		// 条件設定値取得
		$conditions = $this->Registration->getCondition();

		// データ取得
		$this->Paginator->settings = array_merge(
			$this->Paginator->settings,
			array(
				'conditions' => $conditions,
				'page' => 1,
				'sort' => $sort,
				'limit' => $displayNum,
				'direction' => $dir,
				'recursive' => 0,
			)
		);
		if (!isset($this->params['named']['answer_status'])) {
			$this->request->params['named']['answer_status'] = RegistrationsComponent::REGISTRATION_ANSWER_VIEW_ALL;
		}
		$registration = $this->paginate('Registration', $this->_getPaginateFilter());
		$this->set('registrations', $registration);

		$this->__setOwnAnsweredKeys();

		if (count($registration) == 0) {
			$this->view = 'Registrations/noRegistration';
		}
	}

/**
 * _getPaginateFilter method
 *
 * @return array
 */
	protected function _getPaginateFilter() {
		$filter = array();

		if ($this->request->params['named']['answer_status'] == RegistrationsComponent::REGISTRATION_ANSWER_TEST) {
			$filter = array(
				'Registration.status !=' => WorkflowComponent::STATUS_PUBLISHED
			);
			return $filter;
		}

		$filterCondition = array('Registration.key' => $this->RegistrationsOwnAnswer->getOwnAnsweredKeys());
		if ($this->request->params['named']['answer_status'] == RegistrationsComponent::REGISTRATION_ANSWER_UNANSWERED) {
			$filter = array(
				'NOT' => $filterCondition
			);
		} elseif ($this->request->params['named']['answer_status'] == RegistrationsComponent::REGISTRATION_ANSWER_ANSWERED) {
			$filter = array(
				$filterCondition
			);
		}

		return $filter;
	}

/**
 * Set view value of answered registration keys
 *
 * @return void
 */
	private function __setOwnAnsweredKeys() {
		if ($this->request->params['named']['answer_status'] == RegistrationsComponent::REGISTRATION_ANSWER_UNANSWERED) {
			$this->set('ownAnsweredKeys', array());

			return;
		}

		$this->set('ownAnsweredKeys', $this->RegistrationsOwnAnswer->getOwnAnsweredKeys());
	}

}