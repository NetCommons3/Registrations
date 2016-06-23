<?php
/**
 * Registration frame display setting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->NetCommonsForm->label(__d('registrations', 'select display registrations.')); ?>
<?php $this->NetCommonsForm->unlockField('List'); ?>
<?php echo $this->NetCommonsForm->hidden('Single.RegistrationFrameDisplayRegistration.registration_key', array('value' => '')); ?>


<div class="registration-list-wrapper">
	<table class="table table-hover registration-table-vcenter">
		<tr>
			<th>
				<?php /* echo __d('registrations', 'Display'); */?>
				<div class="text-center" ng-show="registrationFrameSettings.displayType == <?php echo RegistrationsComponent::DISPLAY_TYPE_LIST; ?>">
					<?php $this->NetCommonsForm->unlockField('all_check'); ?>
					<?php echo $this->NetCommonsForm->checkbox('all_check', array(
					'ng-model' => 'WinBuf.allCheck',
					'ng-change' => 'allCheckClicked()',
					)); ?>
				</div>
			</th>
			<th>
				<a href="" ng-click="status=!status;sort('registration.status', status)"><?php echo __d('registrations', 'Status'); ?></a>
			</th>
			<th>
				<a href="" ng-click="title=!title;sort('registration.title', title)"><?php echo __d('registrations', 'Title'); ?></a>
			</th>
			<th>
				<a href="" ng-click="answerStartPeriod=!answerStartPeriod;sort('registration.answerStartPeriod', answerStartPeriod)"><?php echo __d('registrations', 'Implementation date'); ?></a>
			</th>
			<th>
				<a href="" ng-click="isTotalShow=!isTotalShow;sort('registration.isTotalShow', isTotalShow)"><?php echo __d('registrations', 'Aggregates'); ?></a>
			</th>
			<th>
				<a href="" ng-click="modified=!modified;sort('registration.modified', modified)"><?php echo __d('net_commons', 'Updated date'); ?></a>
			</th>
		</tr>
		<?php $this->NetCommonsForm->unlockField('List.RegistrationFrameDisplayRegistration'); ?>
		<tr class="animate-repeat btn-default" ng-repeat="(index, quest) in registrations">
			<td>
				<div class="text-center" ng-show="registrationFrameSettings.displayType == <?php echo RegistrationsComponent::DISPLAY_TYPE_LIST; ?>">
					<?php echo $this->NetCommonsForm->checkbox('List.RegistrationFrameDisplayRegistration.{{index}}.is_display', array(
					'options' => array(true => ''),
					'label' => false,
					'div' => 'form-inline',
					'ng-model' => 'isDisplay[index]'
					));
					?>
					<?php echo $this->NetCommonsForm->hidden('List.RegistrationFrameDisplayRegistration.{{index}}.registration_key', array('ng-value' => 'quest.registration.key')); ?>
				</div>
				<div class="text-center"  ng-show="registrationFrameSettings.displayType == <?php echo RegistrationsComponent::DISPLAY_TYPE_SINGLE; ?>">
					<?php echo $this->NetCommonsForm->radio('Single.RegistrationFrameDisplayRegistration.registration_key',
					array('{{quest.registration.key}}' => ''), array(
					'label' => false,
					'div' => 'form-inline',
					'hiddenField' => false,
					'ng-model' => 'quest.registrationFrameDisplayRegistration.registrationKey',
					));
					?>
				</div>
			</td>
			<td>
				<?php echo $this->element('Registrations.ng_status_label', array('status' => 'quest.registration.status', 'periodRangeStat' => 'quest.registration.periodRangeStat')); ?>
			</td>
			<td>
				<img ng-if="quest.registration.titleIcon != ''" ng-src="{{quest.registration.titleIcon}}" class="nc-title-icon" />
				{{quest.registration.title}}
			</td>
			<td>
				<span ng-if="quest.registration.answerTiming == <?php echo RegistrationsComponent::USES_USE; ?>">
				(
					{{quest.registration.answerStartPeriod | ncDatetime}}
					<?php echo __d('registrations', ' - '); ?>
					{{quest.registration.answerEndPeriod | ncDatetime}}
					<?php echo __d('registrations', 'Implementation'); ?>
					)
				</span>
			</td>
			<td>
				<span ng-if="quest.registration.isTotalShow == <?php echo RegistrationsComponent::EXPRESSION_SHOW ?>">
					<?php echo __d('registrations', 'On'); ?>
				</span>
			</td>
			<td>
				{{quest.registration.modified | ncDatetime}}
			</td>
		</tr>
	</table>
</div>

