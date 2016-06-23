<?php
/**
 * registration comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
	echo $this->NetCommonsForm->hidden('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_choice_random',
		array('value' => RegistrationsComponent::USES_NOT_USE,
	));
	echo $this->NetCommonsForm->hidden('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_skip',
		array('value' => RegistrationsComponent::SKIP_FLAGS_NO_SKIP,
	));

