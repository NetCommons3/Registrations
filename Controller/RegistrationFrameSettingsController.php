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
App::uses('RegistrationFrameSetting', 'Registrations.Model');

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
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array(
					'url' => array('controller' => 'registration_blocks')
				),
				'role_permissions' => array(
					'url' => array('controller' => 'registration_block_role_permissions')
				),
				'frame_settings' => array(
					'url' => array('controller' => 'registration_frame_settings')
				),
				'mail_settings' => array(
					'url' => array('controller' => 'registration_mail_settings')
				),
			),
		),
		'NetCommons.DisplayNumber',
		'NetCommons.Date',
		'NetCommons.TitleIcon',
		'Registrations.RegistrationUtil'
	);

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		// Postデータ登録
		if ($this->request->is('put') || $this->request->is('post')) {
			if ($this->RegistrationFrameSetting->saveFrameSettings($this->request->data)) {
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				$this->redirect(NetCommonsUrl::backToPageUrl(true));
				return;
			}
			$this->NetCommons->handleValidationError($this->RegistrationFrameSetting->validationErrors);
		} else {
			$frame = $this->RegistrationFrameSetting->find('first', array(
				'conditions' => array(
					'frame_key' => Current::read('Frame.key'),
				),
				'order' => 'RegistrationFrameSetting.id DESC'
			));
			if (!$frame) {
				$frame = $this->RegistrationFrameSetting->getDefaultFrameSetting();
			}
			$this->request->data['RegistrationFrameSetting'] = $frame['RegistrationFrameSetting'];
			$this->request->data['Frame'] = Current::read('Frame');
			$this->request->data['Block'] = Current::read('Block');
		}

		$registrations = $this->Registration->find('all', array(
			'fields' => array('Registration.*', 'RegistrationFrameDisplayRegistration.*'),
			'conditions' => $this->Registration->getBaseCondition(),
			'order' => array('Registration.modified' => 'DESC'),
			//'page' => 1,
			//'limit' => 1000,
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
		));
		//$registrations = $this->paginate('Registration');
		$this->set('registrations', $registrations);
	}
}