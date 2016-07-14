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
	'/registrations/js/registrations_edit.js',
));
$jsRegistration = NetCommonsAppController::camelizeKeyRecursive(RegistrationsAppController::changeBooleansToNumbers($this->data));
?>
<?php if (Current::permission('block_editable')) : ?>
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>
	<?php echo $this->BlockTabs->block(BlockTabsHelper::BLOCK_TAB_SETTING, ['displayAllTab' =>
		true, 'displayBlockTitle' => true]); ?>
<?php endif ?>

<article
	id="nc-registrations-setting-edit"
	 ng-controller="Registrations.setting"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
									<?php echo h(json_encode($jsRegistration)); ?>)">

	<?php echo $this->element('Registrations.RegistrationEdit/registration_title'); ?>

	<?php echo $this->Wizard->navibar('edit'); ?>

	<div class="panel panel-default">

	<?php echo $this->NetCommonsForm->create('Registration', $postUrl);

		/* NetCommonsお約束:プラグインがデータを登録するところではFrame.id,Block.id,Block.keyの３要素が必ず必要 */
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');
		echo $this->NetCommonsForm->hidden('Block.key');

		echo $this->NetCommonsForm->hidden('Registration.key');
		echo $this->NetCommonsForm->hidden('Registration.import_key');
		echo $this->NetCommonsForm->hidden('Registration.export_key');
	?>
		<div class="panel-body">

			<div class="form-group">
				<?php /* 登録フォームタイトル設定 */
					echo $this->TitleIcon->inputWithTitleIcon('title', 'Registration.title_icon',
					array('label' => __d('registrations', 'Title'),
						'ng-model' => 'registration.registration.title'
					));
				?>
				<?php echo $this->NetCommonsForm->input('sub_title',
					array('label' => __d('registrations', 'Sub Title'),
						'ng-model' => 'registration.registration.subTitle',
					));
				?>
			</div>

			<div class="form-group">
				<?php echo $this->element('Registrations.RegistrationEdit/Edit/mail_setting'); ?>
				
			</div>
			
			<div class="form-group">
				<?php echo $this->NetCommonsForm->label('', __d('registrations', 'Registration answer period')); ?>

				<?php /* 登録フォーム期間設定 */
					echo $this->RegistrationEdit->registrationAttributeCheckbox('answer_timing',
						__d('registrations', 'set the answer period'),
						array(),
						__d('registrations', 'After approval will be immediately published . Stop of the registration to select the stop from the registration data list .'));
				?>
				<div class="row" ng-show="registration.registration.answerTiming == '<?php echo RegistrationsComponent::USES_USE; ?>'">
					<div class="col-xs-11 col-xs-offset-1">
						<div class="form-inline">
							<div class="input-group">
								<?php
									echo $this->RegistrationEdit->registrationAttributeDatetime('answer_start_period',
									array(
										'label' => false,
										'min' => '',
										'max' => 'answer_end_period',
										'div' => false,
										'error' => false));
								?>
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-minus"></span>
								</span>
								<?php
									echo $this->RegistrationEdit->registrationAttributeDatetime('answer_end_period',
									array(
										'label' => false,
										'min' => 'answer_start_period',
										'max' => '',
										'div' => false,
										'error' => false
									));
								?>
							</div>
							<?php echo $this->NetCommonsForm->error('answer_start_period'); ?>
							<?php echo $this->NetCommonsForm->error('answer_end_period'); ?>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $this->NetCommonsForm->label('', __d('registrations', 'Registration number limit')); ?>
				<?php
				echo $this->RegistrationEdit->registrationAttributeCheckbox('is_limit_number',
					__d('registrations', 'To limit the number of registrations'));
				?>
				<div class="row">
					<div class="col-xs-11 col-xs-offset-1">
						<?php
						echo $this->Html->div(null,
							$this->NetCommonsForm->input('limit_number', array('label' => __d('registrations', 'Limit number'))),
							['ng-show' => 'registration.registration.isLimitNumber != 0']
						);
						?>

					</div>

				</div>
			</div>

			<div class="form-group">
				<?php echo $this->NetCommonsForm->label('', __d('registrations', 'Auth method')); ?>
				<?php if (Current::read('Room.space_id') == Space::PUBLIC_SPACE_ID): ?>
				<?php echo $this->element('Registrations.RegistrationEdit/Edit/method_in_public'); ?>
				<?php else: ?>
				<?php echo $this->element('Registrations.RegistrationEdit/Edit/method_in_group'); ?>
				<?php endif; ?>
			</div>

			<div class="form-group">
				<?php echo $this->NetCommonsForm->label('', __d('registrations', 'Thanks page message settings')); ?>
				<?php
					echo $this->NetCommonsForm->wysiwyg('thanks_content', array(
						'label' => false,
						'ng-model' => 'registration.registration.thanksContent'));
				?>
			</div>
			<hr />
			<?php echo $this->Workflow->inputComment('Registration.status'); ?>
		</div>

		<?php echo $this->Wizard->workflowButtons('Registration.status', $cancelUrl['url'], null, true); ?>

		<?php echo $this->NetCommonsForm->end(); ?>

		<?php if ($this->request->params['action'] === 'edit' && !empty($this->data['Registration']['key']) && $this->Workflow->canDelete('Registration', $this->data)) : ?>
			<div class="panel-footer text-right">
				<?php echo $this->element('Registrations.RegistrationEdit/Edit/delete_form'); ?>
			</div>
		<?php endif; ?>

	<?php echo $this->Workflow->comments(); ?>

	</div>
</article>
