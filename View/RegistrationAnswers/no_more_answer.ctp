<?php
/**
 * registration page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo __d('registrations', 'you will not be able to answer this registration.'); ?>
<?php if ($displayType == RegistrationsComponent::DISPLAY_TYPE_LIST): ?>
	<div class="text-center">
	    <?php echo $this->BackTo->pageLinkButton(__d('registrations', 'Back to Top'), array('icon' => 'chevron-left')); ?>
	</div>
<?php endif;
