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

echo $this->element('Registrations.scripts');
?>

<article id="nc-registrations-<?php echo Current::read('Frame.id'); ?>" ng-controller="Registrations">

	<?php echo $this->element('Registrations.Registrations/add_button'); ?>

	<div class="pull-left">
		<?php echo $this->element('Registrations.Registrations/answer_status'); ?>
	</div>

	<div class="clearfix"></div>

	<table class="table nc-content-list">
		<?php foreach($registrations as $registration): ?>
			<tr><td>
				<div class="row">
					<div class="col-md-8 col-xs-12">

						<?php echo $this->RegistrationStatusLabel->statusLabel($registration);?>

						<?php if ($registration['Registration']['answer_timing'] == RegistrationsComponent::USES_USE): ?>
							<strong>
								<?php echo $this->Date->dateFormat($registration['Registration']['answer_start_period']); ?>
								<?php echo __d('registrations', ' - '); ?>
								<?php echo $this->Date->dateFormat($registration['Registration']['answer_end_period']); ?>
							</strong>
						<?php endif ?>

						<h2>
							<?php echo $this->TitleIcon->titleIcon($registration['Registration']['title_icon']); ?>
							<?php echo h($registration['Registration']['title']); ?>
							<br>
							<small><?php echo h($registration['Registration']['sub_title']); ?></small>
						</h2>

					</div>


					<div class="col-md-4 col-xs-12" >
						<div class="pull-right h3">
							<?php echo $this->RegistrationUtil->getAnswerButtons($registration); ?>
							<?php echo $this->RegistrationUtil->getAggregateButtons($registration, array('icon' => 'stats')); ?>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>

				<?php if ($this->Workflow->canEdit('Registration', $registration)) : ?>
					<?php echo $this->element('Registrations.Registrations/detail_for_editor', array('registration' => $registration)); ?>
				<?php endif ?>
			</td></tr>
		<?php endforeach; ?>
	</table>

	<?php echo $this->element('NetCommons.paginator'); ?>

</article>