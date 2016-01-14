<?php
/**
 * Blocks edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->element('Blocks.form_hidden'); ?>

<?php echo $this->Form->hidden('Registration.id', array(
		'value' => isset($registration['id']) ? (int)$registration['id'] : null,
	)); ?>

<?php echo $this->Form->hidden('Registration.key', array(
		'value' => isset($registration['key']) ? $registration['key'] : null,
	)); ?>

<?php echo $this->Form->hidden('RegistrationFrameSetting.id', array(
		'value' => isset($registrationFrameSetting['id']) ? (int)$registrationFrameSetting['id'] : null,
	)); ?>

<?php echo $this->element('Blocks.public_type');