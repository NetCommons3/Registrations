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
<?php echo $this->element('Registrations.scripts'); ?>

<article id="nc-registrations-answer-confirm-<?php Current::read('Frame.id'); ?>">

	<?php echo $this->element('Registrations.Answers/answer_test_mode_header'); ?>

	<?php echo $this->element('Registrations.Answers/answer_header'); ?>

	<?php echo $this->NetCommonsForm->create('RegistrationAnswer', array(
	)); ?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Registration.id', array('value' => $registration['Registration']['id'])); ?>

		<p>
			<?php echo $registration['Registration']['thanks_content']; ?>
		</p>
		<hr>

		<!--<div class="text-center">-->
		<!--	--><?php //echo $this->BackTo->pageLinkButton(__d('registrations', 'Back to page'), array(
		//		'icon' => 'remove',
		//		'iconSize' => 'lg')); ?>
		<!--	--><?php
		//		echo $this->RegistrationUtil->getAggregateButtons($registration,
		//			array('title' => __d('registrations', 'Aggregate'),
		//					'class' => 'primary',
		//					'size' => 'lg'));
		//	?>
		<!--</div>-->
	<?php echo $this->NetCommonsForm->end(); ?>
</article>