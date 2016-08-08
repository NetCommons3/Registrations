<?php
/**
 * Registration Mail Setting Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('MailSettingsController', 'Mails.Controller');
App::uses('MailSettingFixedPhrase', 'Mails.Model');

/**
 * Registration Mail Setting Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registrations\Controller
 */
class RegistrationMailSettingsController extends MailSettingsController {

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockRolePermissionForm',
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array(
					'url' => array('controller' => 'registration_blocks')
				),
			),
			'blockTabs' => array(
				'block_settings' => array(
					'url' => array('controller' => 'registration_edit', 'action' =>
						'edit_question', 'q_mode' => 'setting')
				),
				'role_permissions' => array(
					'url' => array('controller' => 'registration_block_role_permissions')
				),
				'mail_settings' => array(
					'url' => array('controller' => 'registration_mail_settings')
				),
				'answer_list' => array(
					'url' => array('controller' => 'registration_blocks', 'action' =>
						'answer_list'),
					'label' => ['registrations', 'Answer List'],
				),
			),
		),
		'Mails.MailForm',
	);

/**
 * beforeFilter
 *
 * @return void
 * @see NetCommonsAppController::beforeFilter()
 */
	public function beforeFilter() {
		parent::beforeFilter();
		// 親クラスが独自コントローラのためcomponentsがマージされないので自分で読みこむ。
		$this->RegistrationBlockTabs =
			$this->Components->load('Registrations.RegistrationBlockTabs');

		// メール設定 多段の場合にセット
		$this->MailSettings->permission =
			array('mail_content_receivable', 'mail_answer_receivable');
		$this->MailSettings->typeKeys =
			array(MailSettingFixedPhrase::DEFAULT_TYPE, MailSettingFixedPhrase::ANSWER_TYPE);

		$this->backUrl = NetCommonsUrl::backToIndexUrl('default_setting_action');
	}
}
