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
	'/registrations/js/registrations_edit_question.js',
));
$jsRegistration = NetCommonsAppController::camelizeKeyRecursive(RegistrationsAppController::changeBooleansToNumbers($this->data));
?>

<article id="nc-registrations-question-edit-result"
	 ng-controller="Registrations.edit.question"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsRegistration)); ?>)">

	<?php echo $this->element('Registrations.RegistrationEdit/registration_title'); ?>

	<?php echo $this->Wizard->navibar('edit_result'); ?>

	<div class="panel panel-default">

	<?php echo $this->NetCommonsForm->create('RegistrationQuestion', $postUrl); ?>

		<?php $this->NetCommonsForm->unlockField('RegistrationPage'); ?>

		<?php echo $this->NetCommonsForm->hidden('Registration.key'); ?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

		<div class="panel-body">

			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('Registration.is_total_show',
						array('type' => 'radio',
						'options' => array(RegistrationsComponent::EXPRESSION_NOT_SHOW => __d('registrations', 'not disclose the total result'), RegistrationsComponent::EXPRESSION_SHOW => __d('registrations', 'publish aggregate result')),
						'label' => __d('registrations', 'Published aggregate results'),
						'ng-model' => 'registration.registration.isTotalShow'
				)); ?>
			</div>

			<div ng-show="registration.registration.isTotalShow == <?php echo RegistrationsComponent::EXPRESSION_SHOW; ?>">

				<div class="form-group">
					<?php echo $this->NetCommonsForm->wysiwyg('Registration.total_comment',
					array(
					'label' => __d('registrations', 'Text to be displayed in the aggregate results page'),
					'ng-model' => 'registration.registration.totalComment',
					)) ?>
				</div>

				<div class="">
					<label><?php echo __d('registrations', 'Question you want to display the aggregate results'); ?></label>
					<uib-accordion ng-repeat="(pageIndex, page) in registration.registrationPage">
						<uib-accordion-group ng-repeat="(qIndex, question) in page.registrationQuestion" panel-class="{{getResultAccordionClass(question)}}">

							<uib-accordion-heading>
								<?php echo $this->element('Registrations.RegistrationEdit/EditResult/accordion_heading'); ?>
							</uib-accordion-heading>

							<?php echo $this->element('Registrations.RegistrationEdit/EditResult/is_display_set'); ?>

							<div ng-show="question.isResultDisplay == <?php echo RegistrationsComponent::EXPRESSION_SHOW; ?>">

								<?php echo $this->element('Registrations.RegistrationEdit/EditResult/display_type_set'); ?>

								<div class="form-group" ng-show="question.resultDisplayType != <?php echo RegistrationsComponent::RESULT_DISPLAY_TYPE_TABLE; ?>">

									<?php echo $this->element('Registrations.RegistrationEdit/EditResult/graph_color_set'); ?>

								</div>
							</div>
						</uib-accordion-group>
					</uib-accordion>
				</div>
			</div>
		</div>
		<div class="panel-footer text-center">
			<?php echo $this->Wizard->buttons('edit_result', $cancelUrl); ?>
		</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</article>