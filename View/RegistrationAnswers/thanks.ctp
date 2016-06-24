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

	<?php echo $this->element('Registrations.Answers/answer_header'); ?>

	<?php echo $this->element('Registrations.Answers/answer_test_mode_header'); ?>

	<p>
		<?php echo $registration['Registration']['thanks_content']; ?>
	</p>
		<!--<hr>-->
		<!---->
		<!--<div class="text-center">-->
		<!--	--><?php //if ($displayType == RegistrationsComponent::DISPLAY_TYPE_LIST): ?>
		<!--		--><?php //echo $this->BackTo->indexLinkButton(__d('registrations', 'Back to page')); ?>
		<!--	--><?php //endif; ?>
		<!--	--><?php
		//		echo $this->RegistrationUtil->getAggregateButtons($registration,
		//			array('title' => __d('registrations', 'Aggregate'),
		//					'class' => 'primary',
		//	));
		//	?>
		<!--</div>-->
</article>