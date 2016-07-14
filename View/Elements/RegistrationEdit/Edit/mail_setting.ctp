<?php echo $this->NetCommonsForm->label('', __d('registrations', 'Deliver e-mail when submitted?')); ?>
<?php /* 登録通知メール設定 */
echo $this->RegistrationEdit->registrationAttributeCheckbox('is_answer_mail_send',
	__d('registrations', 'Answer mail send'),
	array());
?>
<div class="row" ng-show="registration.registration.isAnswerMailSend == '<?php echo
RegistrationsComponent::USES_USE; ?>'">
	<div class="col-xs-11 col-xs-offset-1">
		<?php /* 本人にも送る（メールアドレス項目があるときのみ） */
		echo $this->RegistrationEdit->registrationAttributeCheckbox('is_regist_user_send',
			__d('registrations', 'Notify the applicant by e-mail,if there is metadata of e-mail'),
			array());
		?>
		<div class="row" ng-show="registration.registration.isRegistUserSend == '<?php echo
		RegistrationsComponent::USES_USE; ?>'">
			<div class="col-xs-11 col-xs-offset-1">
				<div class="form-group">
					<?php echo $this->NetCommonsForm->input('Registration.reply_to', array(
						'type' => 'text',
						'label' => __d('mails', 'E-mail address to receive a reply'),
						'div' => '',
						'help' => __d('mails', 'You can specify if you want to change the e-mail address to receive a reply'),
					)); ?>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo __d('registrations', 'Registration mail') ?>
			</div>
			<div class="panel-body">

				<?php echo $this->NetCommonsForm->input('Registration.registration_mail_subject', array(
					'type' => 'text',
					'label' => __d('mails', 'Subject'),
					'required' => true,
					//'value' => $mailSettingFixedPhrase['mail_fixed_phrase_subject'],
				)); ?>

				<div class="form-group">
					<?php echo $this->NetCommonsForm->input('Registration.registration_mail_body', array(
						'type' => 'textarea',
						'label' => __d('mails', 'Body'),
						'required' => true,
						//'value' => $mailSettingFixedPhrase['mail_fixed_phrase_body'],
						'div' => '',
					)); ?>
					<?php
					// popover説明
					$mailHelp = $this->NetCommonsHtml->mailHelp(__d('registrations', 'Registration.mail.popover'));
					echo $this->NetCommonsForm->help($mailHelp);
					?>
				</div>
			</div>
		</div>

	</div>
</div>
