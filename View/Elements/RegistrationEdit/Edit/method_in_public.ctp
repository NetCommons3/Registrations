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
		echo $this->QuestionEdit->registrationAttributeCheckbox('is_no_member_allow',
			__d('registrations', 'accept the non-members answer')
		);
	?>
	<div class="row" ng-show="registration.registration.isNoMemberAllow==<?php echo RegistrationsComponent::USES_USE; ?>">
		<div class="col-xs-11 col-xs-offset-1">
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
		<span class="help-block">
			<?php echo __d('registrations', 'If you allowed to say also to non-members , the registration will be possible to repeatedly answer.'); ?>
		</span>
		</div>
	</div>
	<?php
		echo $this->QuestionEdit->registrationAttributeCheckbox('is_anonymity',
			__d('registrations', 'anonymous answer'
		));
	?>
</div>
