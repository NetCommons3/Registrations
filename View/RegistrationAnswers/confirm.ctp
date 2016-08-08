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

<article id="nc-registrations-confirm"
		 ng-controller="RegistrationsAnswer">

	<?php echo $this->element('Registrations.Answers/answer_header'); ?>

	<?php echo $this->element('Registrations.Answers/answer_test_mode_header'); ?>

	<p>
		<?php echo __d('registrations', 'Please confirm your answers.'); ?>
	</p>

	<?php echo $this->NetCommonsForm->create('RegistrationAnswer'); ?>
	<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Registration.id', array('value' => $registration['Registration']['id'])); ?>

	<?php foreach($registration['RegistrationPage'] as $pIndex => $page): ?>
		<?php foreach($page['RegistrationQuestion'] as $qIndex => $question): ?>

			<?php if (isset($answers[$question['key']])): ?>

				<?php if ($question['is_require'] == RegistrationsComponent::REQUIRES_REQUIRE): ?>
					<div class="pull-left">
						<?php echo $this->element('NetCommons.required'); ?>
					</div>
				<?php endif ?>

				<label>
					<?php echo $question['question_value']; ?>
				</label>

				<div class="well form-control-static">
					<div class="form-group">
						 <?php echo $this->RegistrationAnswer->answer($question, true); ?>
					</div>
				</div>
			<?php endif ?>
		<?php endforeach; ?>
	<?php endforeach; ?>


	<div class="text-center">

		<a class="btn btn-default" href="<?php echo $this->NetCommonsHtml->url(array(
																	'controller' => 'registration_answers',
																	'action' => 'view',
																	'block_id' => Current::read('Block.id'),
																	'key' => $registration['Registration']['key'],
																	'frame_id' => Current::read('Frame.id'))); ?>">
			<span class="glyphicon glyphicon-chevron-left"></span>
			<?php echo __d('registrations', 'Start over'); ?>
		</a>

		<?php echo $this->NetCommonsForm->button(
		__d('net_commons', 'Confirm'),
		array(
		'class' => 'btn btn-primary',
		'name' => 'confirm_' . 'registration',
		)) ?>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>

</article>