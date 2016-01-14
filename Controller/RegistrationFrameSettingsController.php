<?php
/**
 * Registrations FrameSettingsController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationBlocksController', 'Registrations.Controller');

/**
 * RegistrationFrameSettingsController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationFrameSettingsController extends RegistrationBlocksController {

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Blocks.Block',
		'Frames.Frame',
		'Registrations.Registration',
		'Registrations.RegistrationFrameSetting',
		'Registrations.RegistrationFrameDisplayRegistration',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'registration_blocks')),
				'role_permissions' => array('url' => array('controller' => 'registration_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'registration_frame_settings')),
			),
		),
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'edit' => 'page_editable',
			),
		),
		'Registrations.Registrations',
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Date',
		'Registrations.RegistrationUtil'
	);

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		// Postデータ登録
		if ($this->request->isPut() || $this->request->isPost()) {
			if ($this->RegistrationFrameSetting->saveFrameSettings($this->request->data)) {
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				$this->redirect(NetCommonsUrl::backToPageUrl());
				return;
			}
			$this->NetCommons->handleValidationError($this->RegistrationFrameSetting->validationErrors);
		}

		$conditions = array(
			'block_id' => Current::read('Block.id'),
			'is_latest' => true,
		);
		$this->paginate = array(
			'fields' => array('Registration.*', 'RegistrationFrameDisplayRegistration.*'),
			'conditions' => $conditions,
			'page' => 1,
			'sort' => RegistrationsComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
			'limit' => 1000,
			'direction' => 'desc',
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'registration_frame_display_registrations',
					'alias' => 'RegistrationFrameDisplayRegistration',
					'type' => 'LEFT',
					'conditions' => array(
						'RegistrationFrameDisplayRegistration.registration_key = Registration.key',
						'RegistrationFrameDisplayRegistration.frame_key' => Current::read('Frame.key'),
					),
				)
			)
		);
		$registrations = $this->paginate('Registration');

		$frame = $this->RegistrationFrameSetting->find('first', array(
			'conditions' => array(
				'frame_key' => Current::read('Frame.key'),
			),
			'order' => 'RegistrationFrameSetting.id DESC'
		));
		if (!$frame) {
			$frame = $this->RegistrationFrameSetting->getDefaultFrameSetting();
		}

		$this->set('registrations', $registrations);
		$this->set('registrationFrameSettings', $frame['RegistrationFrameSetting']);
		$this->request->data['RegistrationFrameSetting'] = $frame['RegistrationFrameSetting'];
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}
}