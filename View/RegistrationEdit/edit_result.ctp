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
echo $this->NetCommonsHtml->script(array(
	'/components/moment/min/moment.min.js',
	'/components/moment/min/moment-with-locales.min.js',
	//'/components/tinymce-dist/tinymce.min.js',
	//'/components/angular-ui-tinymce/src/tinymce.js',
	'/wysiwyg/js/wysiwyg.js',
	'/registrations/js/registrations_edit_question.js',
));
$jsRegistration = NetCommonsAppController::camelizeKeyRecursive(RegistrationsAppController::changeBooleansToNumbers($this->data));
?>

<div id="nc-registrations-question-edit-result"
	 ng-controller="Registrations.edit.question"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsRegistration)); ?>)">

	<?php echo $this->NetCommonsForm->create('RegistrationQuestion', $postUrl); ?>

		<?php $this->NetCommonsForm->unlockField('RegistrationPage'); ?>

		<?php echo $this->NetCommonsForm->hidden('Registration.key'); ?>
		<?php
		echo $this->NetCommonsForm->hidden('Registration.status', array('value' => WorkflowComponent::STATUS_IN_DRAFT));
		?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

		<div class="modal-body">

			<?php //echo $this->element('Registrations.RegistrationEdit/edit_flow_chart', array('current' => '2')); ?>

			<?php echo $this->element('Registrations.RegistrationEdit/registration_title'); ?>

			<div class="row form-group registration-group">
				<label ><?php echo __d('registrations', 'Published aggregate results'); ?></label>
					<?php echo $this->NetCommonsForm->input('Registration.is_total_show',
							array('type' => 'radio',
							'options' => array(RegistrationsComponent::EXPRESSION_NOT_SHOW => __d('registrations', 'not disclose the total result'), RegistrationsComponent::EXPRESSION_SHOW => __d('registrations', 'publish aggregate result')),
							'legend' => false,
							'class' => '',
							'label' => false,
							'before' => '<div class="radio"><label>',
							'separator' => '</label></div><div class="radio"><label>',
							'after' => '</label></div>',
							'ng-model' => 'registration.registration.isTotalShow'
					)); ?>
			</div>

			<div ng-show="registration.registration.isTotalShow == <?php echo RegistrationsComponent::EXPRESSION_SHOW; ?>">

				<div class="form-group registration-group">
					<label><?php echo __d('registrations', 'Text to be displayed in the aggregate results page'); ?></label>
					<div class="nc-wysiwyg-alert">
						<?php echo $this->NetCommonsForm->textarea('Registration.total_comment',
						array(
						'class' => 'form-control',
						'ng-model' => 'registration.registration.totalComment',
						'ui-tinymce' => 'tinymce.options',
						'rows' => 5,
						)) ?>
					</div>
				</div>

				<div class="registration-group">
					<label><?php echo __d('registrations', 'Question you want to display the aggregate results'); ?></label>
					<accordion ng-repeat="(pageIndex, page) in registration.registrationPage">
						<accordion-group ng-repeat="(qIndex, question) in page.registrationQuestion" ng-class="{'panel-success': question.isResultDisplay == <?php echo RegistrationsComponent::EXPRESSION_SHOW; ?>}">

							<accordion-heading>
								<?php echo $this->element('Registrations.RegistrationEdit/EditResult/accordion_heading'); ?>
							</accordion-heading>

							<?php echo $this->element('Registrations.RegistrationEdit/EditResult/is_display_set'); ?>

							<div ng-show="question.isResultDisplay == <?php echo RegistrationsComponent::EXPRESSION_SHOW; ?>">

								<?php echo $this->element('Registrations.RegistrationEdit/EditResult/display_type_set'); ?>

								<div class="form-group" ng-show="question.resultDisplayType != <?php echo RegistrationsComponent::RESULT_DISPLAY_TYPE_TABLE; ?>">

									<?php echo $this->element('Registrations.RegistrationEdit/EditResult/graph_color_set'); ?>

								</div>
							</div>
						</accordion-group>
					</accordion>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="text-center">
				<?php echo $this->Button->cancel(__d('net_commons', 'Cancel'), $cancelUrl, array('icon' => 'remove')); ?>
				<?php echo $this->Backto->linkButton(__d('net_commons', 'BACK'), $backUrl, array('icon' => 'chevron-left')); ?>
				<?php echo $this->Button->save(__d('net_commons', 'NEXT'), array('icon' => 'chevron-right')) ?>
			</div>
		</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</div>