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
		echo $this->NetCommonsForm->hidden('is_no_member_allow', array(
			'value' => RegistrationsComponent::USES_NOT_USE,
			'ng-model' => 'registration.registration.isNoMemberAllow'
		));
		echo $this->NetCommonsForm->hidden('is_key_pass_use', array(
			'value' => RegistrationsComponent::USES_NOT_USE,
			'ng-model' => 'registration.registration.isKeyPassUse'
		));
		echo $this->NetCommonsForm->hidden('is_image_authentication', array(
			'value' => RegistrationsComponent::USES_NOT_USE,
			'ng-model' => 'registration.registration.isImageAuthentication'
		));
		echo $this->QuestionEdit->registrationAttributeCheckbox('is_anonymity',
			__d('registrations', 'anonymous answer'
		));
	?>
</div>
