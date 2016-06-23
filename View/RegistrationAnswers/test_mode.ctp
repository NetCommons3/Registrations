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
<article id="nc-registrations-answer-<?php Current::read('Frame.id'); ?>">

	<?php echo $this->element('Registrations.Answers/answer_header'); ?>

	<?php echo $this->element('Registrations.Answers/answer_test_mode_header'); ?>

	<?php echo $this->NetCommonsForm->create('RegistrationAnswer'); ?>

		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

		<div class="row">
			<div class="col-xs-12">
				<h3><?php echo __d('registrations', 'Registration answer period'); ?></h3>
				<?php if ($registration['Registration']['answer_timing'] == RegistrationsComponent::USES_USE): ?>
					<?php echo date('Y/m/d H:i', strtotime($registration['Registration']['answer_start_period'])); ?>
					<?php echo __d('registrations', ' - '); ?>
					<?php echo date('Y/m/d H:i', strtotime($registration['Registration']['answer_end_period'])); ?>
				<?php else: ?>
					<?php echo __d('registrations', 'do not set the answer period'); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3><?php echo __d('registrations', 'Counting result display start date'); ?></h3>
				<?php if ($registration['Registration']['total_show_timing'] == RegistrationsComponent::USES_USE): ?>
					<?php echo date('Y/m/d H:i', strtotime($registration['Registration']['total_show_start_period'])); ?>
					<?php echo __d('registrations', ' - '); ?>
				<?php else: ?>
					<?php echo __d('registrations', 'do not set the aggregate display period'); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3><?php echo __d('registrations', 'Registration method'); ?></h3>
				<ul>
					<li>
						<?php if ($registration['Registration']['is_no_member_allow'] == RegistrationsComponent::USES_USE): ?>
							<?php echo __d('registrations', 'accept the non-members answer'); ?>
						<?php else: ?>
							<?php echo __d('registrations', 'do not accept the non-members answer'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($registration['Registration']['is_key_pass_use'] == RegistrationsComponent::USES_USE): ?>
							<?php echo __d('registrations', 'use key phrase'); ?>
								<dl class="dl-horizontal">
									<dt><?php echo __d('registrations', 'key phrase'); ?>:</dt>
									<dd><?php echo h($registration['AuthorizationKey']['authorization_key']); ?></dd>
								</dl>
						<?php else: ?>
							<?php echo __d('registrations', 'do not use key phrase'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($registration['Registration']['is_anonymity'] == RegistrationsComponent::USES_USE): ?>
							<?php echo __d('registrations', 'anonymous answer'); ?>
						<?php else: ?>
							<?php echo __d('registrations', 'register answer'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($registration['Registration']['is_repeat_allow'] == RegistrationsComponent::USES_USE): ?>
							<?php echo __d('registrations', 'forgive the repetition of the answer'); ?>
						<?php else: ?>
							<?php echo __d('registrations', 'do not forgive the repetition of the answer'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($registration['Registration']['is_image_authentication'] == RegistrationsComponent::USES_USE): ?>
							<?php echo __d('registrations', 'do image authentication'); ?>
						<?php else: ?>
							<?php echo __d('registrations', 'do not image authentication'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($registration['Registration']['is_answer_mail_send'] == RegistrationsComponent::USES_USE): ?>
						<?php echo __d('registrations', 'Deliver e-mail when submitted'); ?>
						<?php else: ?>
						<?php echo __d('registrations', 'do not deliver e-mail when submitted'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($registration['Registration']['is_open_mail_send'] == RegistrationsComponent::USES_USE): ?>
						<?php echo __d('registrations', 'Deliver e-mail when started'); ?>
						<?php else: ?>
						<?php echo __d('registrations', 'do not deliver e-mail when started'); ?>
						<?php endif; ?>
					</li>
				</ul>
			</div>
		</div>

		<div class="text-center">
			<?php echo $this->BackTo->pageLinkButton(__d('net_commons', 'Cancel'), array('icon' => 'remove')); ?>
			<?php echo $this->NetCommonsForm->button(__d('registrations', 'Start the test answers of this registration') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
			array(
			'class' => 'btn btn-primary',
			'name' => 'next_' . '',
			)) ?>
		</div>
	<?php echo $this->NetCommonsForm->end(); ?>

</article>
