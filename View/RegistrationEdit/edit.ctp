<?php
/**
 * registration setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
echo $this->element('Registrations.scripts');
echo $this->NetCommonsHtml->script(array(
	'/components/moment/min/moment.min.js',
	'/components/moment/min/moment-with-locales.min.js',
	//'/components/tinymce-dist/tinymce.min.js',
	//'/components/angular-ui-tinymce/src/tinymce.js',
	//'/wysiwyg/js/wysiwyg.js',
	'/registrations/js/registrations_edit.js',
));

$jsRegistration = NetCommonsAppController::camelizeKeyRecursive(RegistrationsAppController::changeBooleansToNumbers($this->data));
?>

<?php //echo $this->element('Registrations.RegistrationEdit/edit_flow_chart', array('current' => '2')); ?>

<div
	id="nc-registrations-setting-edit"
	 ng-controller="Registrations.setting"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
									<?php echo h(json_encode($jsRegistration)); ?>)">

	<?php echo $this->NetCommonsForm->create('Registration', $postUrl);

		/* NetCommonsお約束:プラグインがデータを登録するところではFrame.id,Block.id,Block.keyの３要素が必ず必要 */
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');
		echo $this->NetCommonsForm->hidden('Block.key');

		echo $this->NetCommonsForm->hidden('Registration.key');
		echo $this->NetCommonsForm->hidden('Registration.import_key');
		echo $this->NetCommonsForm->hidden('Registration.export_key');
	?>
		<div class="modal-body">
			<div class="form-group registration-group">
				<?php /* 登録フォームタイトル設定 */
					echo $this->NetCommonsForm->input('title',
					array('label' => __d('registrations', 'Title'),
						'ng-model' => 'registrations.registration.title'
					));
				?>
				<?php echo $this->NetCommonsForm->input('sub_title',
					array('label' => __d('registrations', 'Sub Title'),
						'ng-model' => 'registrations.registration.subTitle',
						'placeholder' => __d('registrations', 'Please enter if there is a sub title')
					));
				?>
			</div>

			<label class="h3"><?php echo __d('registrations', 'Registration answer period'); ?></label>
			<div class="form-group registration-group">

				<?php /* 登録フォーム期間設定 */
					echo $this->QuestionEdit->registrationAttributeCheckbox('public_type',
						__d('registrations', 'set the answer period'),
						array(
						'value' => WorkflowBehavior::PUBLIC_TYPE_LIMITED,
						//'ng-checked' => 'registrations.registration.publicType==' . "'" . WorkflowBehavior::PUBLIC_TYPE_LIMITED . "'",
						'ng-true-value' => '"' . WorkflowBehavior::PUBLIC_TYPE_LIMITED . '"',
						'ng-false-value' => '"' . WorkflowBehavior::PUBLIC_TYPE_PUBLIC .'"' ,
						'hiddenField' => WorkflowBehavior::PUBLIC_TYPE_PUBLIC
						),
						__d('registrations', 'After approval will be immediately published . Stop of the registration to select the stop from the registration data list .'));
				?>
				<div class="row" ng-show="registrations.registration.publicType == '<?php echo WorkflowBehavior::PUBLIC_TYPE_LIMITED; ?>'">
					<div class="col-sm-5">
						<?php
							echo $this->QuestionEdit->registrationAttributeDatetime('publish_start', false,
								array('min' => '', 'max' => 'publish_end'));
						?>
					</div>
					<div class="col-sm-1">
						<?php echo __d('registrations', ' - '); ?>
					</div>
					<div class="col-sm-5">
						<?php
							echo $this->QuestionEdit->registrationAttributeDatetime('publish_end', false,
								array('min' => 'publish_start', 'max' => ''));
						?>
					</div>
				</div>
			</div>

			<!--<label class="h3">--><?php //echo __d('registrations', 'Counting result display start date'); ?><!--</label>-->
			<!--<div class="row form-group registration-group">-->
			<!---->
			<!--	--><?php ///* 集計結果表示期間設定 */
			//		echo $this->QuestionEdit->registrationAttributeCheckbox('total_show_timing',
			//			__d('registrations', 'set the aggregate display period'),
			//			array(),
			//			__d('registrations', 'If not set , it will be displayed after the respondent answers.'));
			//	?>
			<!--	<div class="row" ng-show="registrations.registration.totalShowTiming != 0">-->
			<!--		<div class="col-sm-5">-->
			<!--			--><?php
			//				echo $this->QuestionEdit->registrationAttributeDatetime('publish_start', false);
			//			?>
			<!--		</div>-->
			<!--		<div class="col-sm-6">-->
			<!--			--><?php //echo __d('registrations', 'Result will display at this time.'); ?>
			<!--		</div>-->
			<!--	</div>-->
			<!--</div>-->

			<label class="h3"><?php echo __d('registrations', '設定'); ?></label>
			<div class="form-group registration-group">
				<?php
					//echo $this->QuestionEdit->registrationAttributeCheckbox('is_no_member_allow',
					//	__d('registrations', 'accept the non-members answer'));
				echo $this->NetCommonsForm->hidden('is_no_member_allow');
				?>

<!--				<div ng-hide="registrations.registration.isNoMemberAllow != 1">-->
				<?php
					echo $this->QuestionEdit->registrationAttributeCheckbox('is_key_pass_use',
						__d('registrations', 'use key phrase'),
						array(
							'ng-disabled' => 'registrations.registration.isImageAuthentication == ' . RegistrationsComponent::USES_USE));
					echo $this->element('AuthorizationKeys.edit_form', [
						'options' => array(
							'div' => 'checkbox',
						'ng-show' => 'registrations.registration.isKeyPassUse != 0',
					)]);



					//echo $this->QuestionEdit->registrationAttributeCheckbox('is_anonymity',
					//	__d('registrations', 'anonymous answer'));

					//echo $this->QuestionEdit->registrationAttributeCheckbox('is_repeat_allow',
					//	__d('registrations', 'forgive the repetition of the answer'));

					echo $this->QuestionEdit->registrationAttributeCheckbox('is_image_authentication',
						__d('registrations', 'do image authentication'),
							array(
								'ng-disabled' => 'registrations.registration.isKeyPassUse == ' . RegistrationsComponent::USES_USE));
				?>
				<!--</div>-->

				<?php
				echo $this->QuestionEdit->registrationAttributeCheckbox('is_limit_number',
					__d('registrations', '登録数を制限する'));

				echo $this->Html->div(null,
					$this->NetCommonsForm->input('limit_number',array('label' => '登録数')),
					['ng-show' => 'registrations.registration.isLimitNumber != 0']
				);
				?>
			</div>

			<label class="h3"><?php echo __d('registrations', 'メール設定'); ?></label>
			<div class="form-group registration-group">

			<?php
			echo $this->QuestionEdit->registrationAttributeCheckbox('is_answer_mail_send',
				__d('registrations', 'Deliver e-mail when submitted'));
			?>
				<img src="/registrations/mail.png" ng-show="registrations.registration.isAnswerMailSend==1"/>

			</div>
			<!--<label class="h3">--><?php //echo __d('registrations', 'Registration open mail'); ?><!--</label>-->
			<!--<div class="form-group registration-group">-->
			<!--	--><?php
			//		echo $this->QuestionEdit->registrationAttributeCheckbox('is_open_mail_send',
			//			__d('registrations', 'Deliver e-mail when registration has opened'));
			//	?>
			<!--	<div ng-show="registrations.registration.isOpenMailSend == --><?php //echo RegistrationsComponent::USES_USE; ?><!--">-->
			<!--		--><?php
			//			echo $this->NetCommonsForm->input('open_mail_subject', array(
			//				'label' => __d('registrations', 'open mail subject'),
			//				'ng-model' => 'registrations.registration.openMailSubject'));
			//			echo $this->NetCommonsForm->wysiwyg('open_mail_body', array(
			//				'label' => __d('registrations', 'open mail text'),
			//				'ng-model' => 'registrations.registration.openMailBody'));
			//		?>
			<!--	</div>-->
			<!--</div>-->

			<label class="h3"><?php echo __d('registrations', 'Thanks page message settings'); ?></label>
			<div class="form-group registration-group">
				<div class="nc-wysiwyg-alert">
					<?php
						echo $this->NetCommonsForm->wysiwyg('thanks_content', array(
							'label' => false,
							'ng-model' => 'registrations.registration.thanksContent'));
					?>
				</div>
			</div>
			<?php echo $this->Workflow->inputComment('Registration.status'); ?>
			<!--<a href="#" ng-click="ddd()">LOG</a>-->


		</div>
		<?php echo $this->Workflow->buttons('Registration.status', $cancelUrl, true, $backUrl); ?>

	<?php echo $this->NetCommonsForm->end(); ?>

	<?php if ($this->request->params['action'] === 'edit' && !empty($this->data['Registration']['key']) && $this->Workflow->canDelete('Registration', $this->data)) : ?>
		<div class="panel-footer text-right">
			<?php echo $this->element('Registrations.RegistrationEdit/Edit/delete_form'); ?>
		</div>
	<?php endif; ?>

	<?php echo $this->Workflow->comments(); ?>

</div>
