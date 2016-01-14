<?php
/**
 * RegistrationBlockRolePermissions Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationBlocksController', 'Registrations.Controller');

/**
 * RegistrationBlockRolePermissions Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationBlockRolePermissionsController extends RegistrationBlocksController {

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Registrations.RegistrationSetting',
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
				'edit' => 'block_permission_editable',
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockRolePermissionForm',
		'NetCommons.Date',
	);

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		$registrationSetting = $this->RegistrationSetting->getSetting();
		if (! $registrationSetting) {
			$this->setAction('throwBadRequest');
			return false;
		}
		$permissions = $this->Workflow->getBlockRolePermissions(
			array('content_creatable', 'content_publishable', 'content_comment_creatable', 'content_comment_publishable')
		);
		$this->set('roles', $permissions['Roles']);
		if ($this->request->isPost()) {
			if ($this->RegistrationSetting->saveRegistrationSetting($this->request->data)) {
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
				return;
			}
			$this->NetCommons->handleValidationError($this->RegistrationSetting->validationErrors);
			$this->request->data['BlockRolePermission'] = Hash::merge(
				$permissions['BlockRolePermissions'],
				$this->request->data['BlockRolePermission']
			);
			return;
		}
		$this->request->data['RegistrationSetting'] = $registrationSetting['RegistrationSetting'];
		$this->request->data['Block'] = $registrationSetting['Block'];
		$this->request->data['BlockRolePermission'] = $permissions['BlockRolePermissions'];
		$this->request->data['Frame'] = Current::read('Frame');
	}
}
