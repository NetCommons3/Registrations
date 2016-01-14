<?php
/**
 * 登録フォーム質問の種別によって異なる詳細設定のファイル
 * このファイルでは日付け・時間入力タイプをフォローしている
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<?php
		echo $this->NetCommonsForm->hidden('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_choice_random',
			array('value' => RegistrationsComponent::USES_NOT_USE,
		));
		echo $this->NetCommonsForm->hidden('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_skip',
			array('value' => RegistrationsComponent::SKIP_FLAGS_NO_SKIP,
		));
	?>

	<?php
		echo $this->NetCommonsForm->input('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_type_option',
			array(
				'type' => 'radio',
				'before' => '<div class="col-sm-3"><div class="radio"><label>',
				'separator' => '</label></div></div><div class="col-sm-3"><div class="radio"><label>',
				'after' => '</label></div></div>',
				'options' => array(RegistrationsComponent::TYPE_OPTION_DATE => __d('registrations', 'Date'),
							RegistrationsComponent::TYPE_OPTION_TIME => __d('registrations', 'Time'),
							RegistrationsComponent::TYPE_OPTION_DATE_TIME => __d('registrations', 'Date and Time')),
				'legend' => false,
				'div' => false,
				'label' => false,
				'class' => '',
				'ng-model' => 'question.questionTypeOption',
				'ng-click' => 'changeDatetimepickerType(pageIndex, qIndex)'
		));
	?>
	<div class="col-sm-3">
	</div>
</div>


<div class="row">
	<div class="col-sm-12">
		<div class="checkbox">
			<label>
				<?php
					echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_range',
						array(
							'value' => RegistrationsComponent::USES_USE,
							'ng-model' => 'question.isRange',
							'ng-checked' => 'question.isRange == ' . RegistrationsComponent::USES_USE,
							'error' => 'question.errorMessages.isRange',
					));
				?>
				<?php echo __d('registrations', 'set range to answer date and time'); ?>
			</label>
			<?php
				echo $this->element('Registrations.RegistrationEdit/ng_errors', array(
					'errorArrayName' => 'question.errorMessages.isRange',
				));
			?>
		</div>
	</div>
</div>


<div class="row">
	<div ng-show="question.isRange == <?php echo RegistrationsComponent::USES_USE; ?>">
		<div class="col-sm-5">
			<?php
			echo $this->element(
			'Registrations.RegistrationEdit/EditQuestion/options_before_published/date_range_input', array(
			'field' => 'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.min',
			'calOpenId' => 0,
			'model' => 'question.min',
			'min' => '',
			'max' => 'question.max',
			'limitTarget' => 'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.max',
			'error' => 'question.errorMessages.min',
			));
			?>
		</div>

		<div class="col-sm-2"><p class="form-control-static text-center"><?php echo __d('registrations', ' - '); ?></p></div>

		<div class="col-sm-5">
			<?php
			echo $this->element(
			'Registrations.RegistrationEdit/EditQuestion/options_before_published/date_range_input', array(
			'field' => 'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.max',
			'calOpenId' => 1,
			'model' => 'question.max',
			'min' => 'question.min',
			'max' => '',
			'limitTarget' => 'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.min',
			'error' => 'question.errorMessages.max',
			));
			?>
		</div>
	</div>
</div>