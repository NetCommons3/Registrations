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
	<div class="col-xs-12">
	<?php
		echo $this->NetCommonsForm->hidden('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_choice_random',
			array('value' => RegistrationsComponent::USES_NOT_USE,
		));
		echo $this->NetCommonsForm->hidden('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_skip',
			array('value' => RegistrationsComponent::SKIP_FLAGS_NO_SKIP,
		));
	?>

	<?php
		echo $this->NetCommonsForm->radio('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_type_option',
				array(
					RegistrationsComponent::TYPE_OPTION_DATE => __d('registrations', 'Date'),
					RegistrationsComponent::TYPE_OPTION_TIME => __d('registrations', 'Time'),
					RegistrationsComponent::TYPE_OPTION_DATE_TIME => __d('registrations', 'Date and Time')),
				array(
				'div' => 'form-inline',
				'label' => false,
				'ng-model' => 'question.questionTypeOption',
				'ng-click' => 'changeDatetimepickerType(pageIndex, qIndex)'
		));
	?>
	</div>
</div>


<div class="row">
	<div class="col-xs-12">
		<?php
			echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_range',
				array(
					'label' => __d('registrations', 'set range to answer date and time'),
					'value' => RegistrationsComponent::USES_USE,
					'ng-model' => 'question.isRange',
					'ng-checked' => 'question.isRange == ' . RegistrationsComponent::USES_USE,
					'error' => 'question.errorMessages.isRange',
			));
		?>
		<?php
			echo $this->element('Registrations.RegistrationEdit/ng_errors', array(
				'errorArrayName' => 'question.errorMessages.isRange',
			));
		?>
	</div>
</div>


<div class="row">
	<div class="col-xs-12" ng-show="question.isRange == <?php echo RegistrationsComponent::USES_USE; ?>">
		<div class="form-inline">
			<div class="input-group">
				<?php
				echo $this->element(
				'Registrations.RegistrationEdit/EditQuestion/options_before_published/date_range_input', array(
				'field' => 'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.min',
				'calOpenId' => 0,
				'model' => 'question.min',
				'min' => '',
				'max' => 'question.max',
				'limitTarget' => 'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.max',
				//'error' => 'question.errorMessages.min',
				'error' => false,
				));
				?>
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-minus"></span>
				</span>
				<?php
				echo $this->element(
				'Registrations.RegistrationEdit/EditQuestion/options_before_published/date_range_input', array(
				'field' => 'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.max',
				'calOpenId' => 1,
				'model' => 'question.max',
				'min' => 'question.min',
				'max' => '',
				'limitTarget' => 'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.min',
				//'error' => 'question.errorMessages.max',
				'error' => false,
				));
				?>
			</div>
			<?php
			echo $this->element('Registrations.RegistrationEdit/ng_errors', array(
			'errorArrayName' => 'question.errorMessages.min',
			));
			echo $this->element('Registrations.RegistrationEdit/ng_errors', array(
			'errorArrayName' => 'question.errorMessages.max',
			));
			?>
		</div>
	</div>
</div>