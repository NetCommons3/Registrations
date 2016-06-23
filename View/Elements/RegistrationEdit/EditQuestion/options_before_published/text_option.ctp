<?php
/**
 * registration comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<div class="col-xs-12">
		<?php
			echo $this->NetCommonsForm->hidden('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_choice_random',
				array('value' => RegistrationsComponent::USES_NOT_USE,
			));
			echo $this->NetCommonsForm->hidden('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_skip',
				array('value' => RegistrationsComponent::SKIP_FLAGS_NO_SKIP,
			));
		?>
		<label class="checkbox-inline">
			<?php echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_type_option',
				array(
				'value' => RegistrationsComponent::TYPE_OPTION_NUMERIC,
				'ng-model' => 'question.questionTypeOption',
				'ng-checked' => 'question.questionTypeOption == ' . RegistrationsComponent::TYPE_OPTION_NUMERIC
				));
			?>
			<?php echo __d('registrations', 'Numeric'); ?>
		</label>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<label class="checkbox-inline">
			<?php echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_range',
				array(
				'value' => RegistrationsComponent::USES_USE,
				'ng-model' => 'question.isRange',
				'ng-checked' => 'question.isRange == ' . RegistrationsComponent::USES_USE
				));
			?>
			<?php echo __d('registrations', 'Please check if you want to set limit(or length) value.'); ?>
		</label>
		<?php echo $this->element(
			'Registrations.RegistrationEdit/ng_errors', array(
			'errorArrayName' => 'question.errorMessages.isRange',
		)); ?>
	</div>
</div>
<div class="row">
	<div class="col-xs-5 col-xs-offset-1">
		<div class="form-inline" ng-if="question.isRange == <?php echo RegistrationsComponent::USES_USE; ?>">
			<?php echo $this->NetCommonsForm->input('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.min',
			array(
			'div' => '',
			'label' => __d('registrations', 'Minimum'),
			'ng-model' => 'question.min'
			));
			?>
		</div>
		<?php echo $this->element(
			'Registrations.RegistrationEdit/ng_errors', array(
			'errorArrayName' => 'question.errorMessages.min',
		)); ?>
	</div>

	<div class="col-xs-6">
		<div class="form-inline" ng-if="question.isRange == <?php echo RegistrationsComponent::USES_USE; ?>">
			<?php echo $this->NetCommonsForm->input('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.max',
			array(
			'div' => '',
			'label' => __d('registrations', 'Maximum'),
			'ng-model' => 'question.max'
			));
			?>
		</div>
		<?php echo $this->element(
			'Registrations.RegistrationEdit/ng_errors', array(
			'errorArrayName' => 'question.errorMessages.max',
		)); ?>
	</div>
</div>

