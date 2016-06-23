<?php
/**
 * Registration frame display setting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<label><?php echo __d('registrations', 'Registration display setting'); ?></label>
<?php echo $this->NetCommonsForm->radio('display_type',
	array(
		RegistrationsComponent::DISPLAY_TYPE_SINGLE => __d('registrations', 'Show only one registration'),
		RegistrationsComponent::DISPLAY_TYPE_LIST => __d('registrations', 'Show registrations list'),
	),
	array(
		'div' => 'form-inline',
		'hiddenField' => false,
		'ng-model' => 'registrationFrameSettings.displayType',
		'error' => true,
	));

