<?php
/**
* Registration BlockTabs Component
*
* @author   Ryuji AMANO <ryuji@ryus.co.jp>
* @link http://www.netcommons.org NetCommons Project
* @license http://www.netcommons.org/license.txt NetCommons License
*/

class RegistrationBlockTabsComponent extends Component {
	public function initialize(Controller $controller) {
		$controller->helpers['Blocks.BlockTabs'] = array(
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
		);
	}
}