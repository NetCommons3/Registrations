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
?>
<input type="text"
	   name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][choice_label]"
	   class="form-control input-sm"
	   ng-model="choice.choiceLabel"
		/>
<span ng-if="choice.otherChoiceType != <?php echo RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
	<?php echo __d('registrations', '(This is [other] choice. Area to enter the text is automatically granted at the time of implementation.)'); ?>
</span>

<?php echo $this->element(
	'Registrations.RegistrationEdit/ng_errors', array(
	'errorArrayName' => 'choice.errorMessages.choiceLabel',
)); ?>

<?php // Version1ではchoice_valueの値はchoice_labelと同じにしておく ?>
<input type="hidden"
	   name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][choice_value]"
	   ng-value="choice.choiceLabel"
		/>
<input type="hidden"
	   name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][choice_sequence]"
	   ng-value="choice.choiceSequence"
		/>
<input type="hidden"
	   name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][matrix_type]"
	   ng-value="choice.matrixType"
		/>
<input type="hidden"
	   name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][other_choice_type]"
	   ng-value="choice.otherChoiceType"
		/>
<input type="hidden"
	   name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][key]"
	   ng-value="choice.key"
		/>
<input type="hidden"
	   name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{choice.choiceSequence}}][graph_color]"
	   ng-value="choice.graphColor"
		/>
