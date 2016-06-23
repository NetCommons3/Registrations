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
<?php echo $this->NetCommonsForm->hidden('display_type',
	array(
		'div' => 'form-inline',
		'hiddenField' => false,
		'ng-model' => 'registrationFrameSettings.displayType',
		'error' => true,
	));

