<?php
/**
 * registration edit result "display_type" option set template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="form-group">
	<label><?php echo __d('registrations', 'display type');?></label>

	<?php
		/*
		 * Formヘルパー使うとAngularのrepeatとバッティングしてradioのcheckedがうまく動作しなくなる
		 * 仕方ないのでべタにHTMLタグを書くことにする
		 */
	?>

	<div class="radio"><label>
		<input type="radio"
			   ng-attr-name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][result_display_type]"
			   value="<?php echo RegistrationsComponent::RESULT_DISPLAY_TYPE_BAR_CHART; ?>"
			   ng-model="question.resultDisplayType"
				/>
		<?php echo __d('registrations', 'Bar Chart'); ?>
	</label></div>
	<div class="radio"><label>
		<input type="radio"
			   ng-attr-name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][result_display_type]"
			   value="<?php echo RegistrationsComponent::RESULT_DISPLAY_TYPE_PIE_CHART; ?>"
			   ng-model="question.resultDisplayType"
				/>
		<?php echo __d('registrations', 'Pie Chart'); ?>
	</label></div>
	<div class="radio"><label>
		<input type="radio"
			   ng-attr-name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][result_display_type]"
			   value="<?php echo RegistrationsComponent::RESULT_DISPLAY_TYPE_TABLE; ?>"
			   ng-model="question.resultDisplayType"
				/>
		<?php echo __d('registrations', 'Table'); ?>
	</label></div>
	<?php echo $this->NetCommonsForm->hidden(
	'RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.result_display_type',
	array('ng-value' => 'question.resultDisplayType'));
	?>
</div>
<?php echo $this->element(
	'Registrations.RegistrationEdit/ng_errors', array(
	'errorArrayName' => 'question.errorMessages.resultDisplayType',
));
