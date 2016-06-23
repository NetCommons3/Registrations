<?php
/**
 * registration setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="form-group">
	<?php
		//echo $this->QuestionEdit->registrationAttributeCheckbox('is_no_member_allow',
		//	__d('registrations', 'accept the non-members answer')
		//);
	echo $this->NetCommonsForm->hidden('is_no_member_allow', array(
		'value' => RegistrationsComponent::USES_USE,
		//'ng-model' => 'registration.registration.isNoMemberAllow'
	));
	?>
	<div>
		<?php
			echo $this->QuestionEdit->registrationAttributeCheckbox('is_key_pass_use',
				__d('registrations', 'use key phrase'), array(
				'ng-disabled' => 'registration.registration.isImageAuthentication == ' . RegistrationsComponent::USES_USE . ' || registration.registration.isNoMemberAllow != ' . RegistrationsComponent::USES_USE
			));
			echo $this->element('AuthorizationKeys.edit_form', [
				'options' => array(
					'div' => false,
					'ng-show' => 'registration.registration.isKeyPassUse != 0',
			)]);
			echo $this->QuestionEdit->registrationAttributeCheckbox('is_image_authentication',
				__d('registrations', 'do image authentication'), array(
				'ng-disabled' => 'registration.registration.isKeyPassUse == ' . RegistrationsComponent::USES_USE . ' || registration.registration.isNoMemberAllow != ' . RegistrationsComponent::USES_USE
			));
		?>
	</div>
	<?php
		//echo $this->QuestionEdit->registrationAttributeCheckbox('is_anonymity',
		//	__d('registrations', 'anonymous answer'
		//));
	echo $this->NetCommonsForm->hidden('is_anonymity', array(
		'value' => RegistrationsComponent::USES_NOT_USE,
		//'ng-model' => 'registration.registration.isNoMemberAllow'
	));

	?>
</div>
