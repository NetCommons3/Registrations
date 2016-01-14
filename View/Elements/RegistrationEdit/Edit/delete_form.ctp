<?php
/**
 * Element of Registration delete form
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->create('Registration', array(
			'type' => 'delete',
			'url' => NetCommonsUrl::actionUrl(array(
				'controller' => 'registration_edit',
				'action' => 'delete',
				'block_id' => Current::read('Block.id'),
				'frame_id' => Current::read('Frame.id'),
				'key' => h($this->data['Registration']['key'])
			))
		)); ?>

	<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

	<?php echo $this->NetCommonsForm->hidden('id'); ?>
	<?php echo $this->NetCommonsForm->hidden('key'); ?>

	<?php echo $this->Button->delete('',
			sprintf(__d('net_commons', 'Deleting the %s. Are you sure to proceed?'), __d('registrations', 'Registration'))
		); ?>
<?php echo $this->NetCommonsForm->end();
