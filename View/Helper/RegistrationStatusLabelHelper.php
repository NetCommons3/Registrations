<?php
/**
 * Questionnares App Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * Registrations Status Label Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Registrations\View\Helper
 */
class RegistrationStatusLabelHelper extends AppHelper {

/**
 * Status label
 *
 * @param array $registration registration
 * @return string
 */
	public function statusLabel($registration) {
		$status = $registration['Registration']['status'];
		//初期値セット
		$lblColor = 'danger';
		$lblMsg = __d('registrations', 'Undefined');

		if ($status == WorkflowComponent::STATUS_IN_DRAFT) {
			//一時保存中
			$lblColor = 'info';
			$lblMsg = __d('net_commons', 'Temporary');
		} elseif ($status == WorkflowComponent::STATUS_APPROVAL_WAITING) {
			//承認待ち
			$lblColor = 'warning';
			$lblMsg = __d('net_commons', 'Approving');
		} elseif ($status == WorkflowComponent::STATUS_DISAPPROVED) {
			//差し戻し
			$lblColor = 'danger';
			$lblMsg = __d('net_commons', 'Disapproving');
		} else {
			$rangeStat = $registration['Registration']['period_range_stat'];
			if ($rangeStat == RegistrationsComponent::REGISTRATION_PERIOD_STAT_BEFORE) {
				//未実施
				$lblColor = 'default';
				$lblMsg = __d('registrations', 'Before public');
			} elseif ($rangeStat == RegistrationsComponent::REGISTRATION_PERIOD_STAT_END) {
				//終了
				$lblColor = 'default';
				$lblMsg = __d('registrations', 'End');
			} else {
				$lblMsg = '';
			}
		}
		if ($lblMsg) {
			return '<span  class="label label-' . $lblColor . '">' . $lblMsg . '</span>';
		}
		return '';
	}

/**
 * Status label for management widget
 *
 * @param array $registration registration
 * @return string
 */
	public function statusLabelManagementWidget($registration) {
		$label = $this->statusLabel($registration);
		if ($label == '') {
			$label = __d('net_commons', 'Published');
		}
		return $label;
	}
}