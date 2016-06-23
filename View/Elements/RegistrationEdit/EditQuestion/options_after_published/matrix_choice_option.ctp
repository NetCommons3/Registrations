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

<div class="row">

	<div class="col-xs-6">
		<label><?php echo __d('registrations', 'Line choices'); ?></label>
		<ul class="list-group ">
			<li class="list-group-item" ng-repeat="(cIndex, choice) in matrixRows = (question.registrationChoice | filter: {matrixType:<?php echo RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>})" >
				<div class="form-inline">
					<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/options_after_published/choice'); ?>
				</div>
			</li>
		</ul>
	</div>

	<div class="col-xs-6">
		<label><?php echo __d('registrations', 'Column choices'); ?></label>
		<ul class="list-group">
			<li class="list-group-item" ng-repeat="(cIndex, choice) in matrixColumns = (question.registrationChoice | filter: {matrixType:<?php echo RegistrationsComponent::MATRIX_TYPE_COLUMN; ?>})" >
				<div class="form-inline">
					<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/options_after_published/choice'); ?>
				</div>
			</li>
		</ul>
	</div>

</div>
