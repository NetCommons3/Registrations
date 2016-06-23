<?php
/**
 * 実施後の登録フォーム
 * 登録フォーム項目の種別によって異なる詳細設定のファイル
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
	<?php echo $this->NetCommonsForm->radio('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_type_option',
		array(
			RegistrationsComponent::TYPE_OPTION_DATE => __d('registrations', 'Date'),
			RegistrationsComponent::TYPE_OPTION_TIME => __d('registrations', 'Time'),
			RegistrationsComponent::TYPE_OPTION_DATE_TIME => __d('registrations', 'Date and Time')
		),
		array(
			'div' => 'form-inline',
			'label' => false,
			'ng-model' => 'question.questionTypeOption',
			'disabled' => 'disabled',
		));
	?>
	</div>
</div>


<div class="row">
	<div class="col-xs-12">
		<?php echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_range',
			array(
				'label' => __d('registrations', 'set range to answer date and time'),
				'value' => RegistrationsComponent::USES_USE,
				'ng-model' => 'question.isRange',
				'ng-checked' => 'question.isRange == ' . RegistrationsComponent::USES_USE,
				'disabled' => 'disabled',
			));
		?>
	</div>
</div>

<div class="row">
	<div ng-show="question.isRange == <?php echo RegistrationsComponent::USES_USE; ?>">
		<div class="col-xs-11 col-xs-offset-1">

			<span ng-if="question.questionTypeOption == <?php echo RegistrationsComponent::TYPE_OPTION_DATE; ?>">
				{{question.min | date : 'yyyy-MM-dd'}}
			</span>
			<span ng-if="question.questionTypeOption == <?php echo RegistrationsComponent::TYPE_OPTION_TIME; ?>">
				{{question.min | date : 'HH:mm'}}
			</span>
			<span ng-if="question.questionTypeOption == <?php echo RegistrationsComponent::TYPE_OPTION_DATE_TIME; ?>">
				{{question.min | date : 'yyyy-MM-dd HH:mm'}}
			</span>

			<span class="form-control-static text-center"><?php echo __d('registrations', ' - '); ?></span>

			<span ng-if="question.questionTypeOption == <?php echo RegistrationsComponent::TYPE_OPTION_DATE; ?>">
				{{question.max | date : 'yyyy-MM-dd'}}
			</span>
			<span ng-if="question.questionTypeOption == <?php echo RegistrationsComponent::TYPE_OPTION_TIME; ?>">
				{{question.max | date : 'HH:mm'}}
			</span>
			<span ng-if="question.questionTypeOption == <?php echo RegistrationsComponent::TYPE_OPTION_DATE_TIME; ?>">
				{{question.max | date : 'yyyy-MM-dd HH:mm'}}
			</span>
		</div>

	</div>
</div>

