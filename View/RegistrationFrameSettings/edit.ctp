<?php
/**
 * registration setting list view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->element('Registrations.scripts');
$jsRegistrationFrameSettings = NetCommonsAppController::camelizeKeyRecursive(RegistrationsAppController::changeBooleansToNumbers($registrationFrameSettings));
$jsRegistrations = NetCommonsAppController::camelizeKeyRecursive(RegistrationsAppController::changeBooleansToNumbers($registrations));
?>

<article class="nc-registration-frame-settings-content-list-"
	 ng-controller="RegistrationsFrame"
	 ng-init="initialize(<?php echo h(json_encode($jsRegistrations)); ?>,
	 	<?php echo h(json_encode($jsRegistrationFrameSettings)); ?>)">

	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_FRAME_SETTING); ?>

	<div class="tab-content">

		<?php echo $this->element('Blocks.edit_form', array(
				'model' => 'RegistrationFrameSetting',
				'callback' => 'Registrations.FrameSettings/edit_form',
				'cancelUrl' => NetCommonsUrl::backToPageUrl(),
			)); ?>

	</div>

</article>