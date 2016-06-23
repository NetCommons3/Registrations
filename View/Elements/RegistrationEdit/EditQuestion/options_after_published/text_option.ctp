<?php
/**
 * registration question text option template
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
		<div class="form-group">
			<label class="checkbox-inline">
				<?php echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_type_option',
					array(
					'value' => RegistrationsComponent::TYPE_OPTION_NUMERIC,
					'ng-model' => 'question.questionTypeOption',
					'ng-checked' => 'question.questionTypeOption == ' . RegistrationsComponent::TYPE_OPTION_NUMERIC,
					'disabled' => 'disabled',
					));
				?>
				<?php echo __d('registrations', 'Numeric'); ?>
			</label>
		</div>
		<div class="form-group">
			<label class="checkbox-inline">
				<?php echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_range',
				array(
				'value' => RegistrationsComponent::USES_USE,
				'ng-model' => 'question.isRange',
				'ng-checked' => 'question.isRange == ' . RegistrationsComponent::USES_USE,
				'disabled' => 'disabled',
				));
				?>
				<?php echo __d('registrations', 'Please check if you want to set limit(or length) value.'); ?>
			</label>
		</div>
	</div>
	<div class="col-xs-5 col-xs-offset-1 form-inline" ng-if="question.isRange == <?php echo RegistrationsComponent::USES_USE; ?>">
		<label>
			<?php echo __d('registrations', 'Minimum'); ?>
		</label>
		{{question.min}}
	</div>
	<div class="col-xs-6 form-inline" ng-if="question.isRange == <?php echo RegistrationsComponent::USES_USE; ?>">
		<label>
		<?php echo __d('registrations', 'Maximum'); ?>
			</label>
		{{question.max}}
	</div>
</div>
