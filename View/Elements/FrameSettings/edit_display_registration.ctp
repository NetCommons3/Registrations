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

<div class="registration-list-wrapper">
	<table class="table table-hover registration-table-vcenter">
		<tr>
			<th>
				<?php echo __d('registrations', 'Display'); ?>
				<div class="text-center" ng-if="registrationFrameSettings.displayType == <?php echo RegistrationsComponent::DISPLAY_TYPE_LIST; ?>">
					<?php $this->NetCommonsForm->unlockField('all_check'); ?>
					<?php echo $this->NetCommonsForm->checkbox('all_check', array(
					'ng-model' => 'WinBuf.allCheck',
					'ng-change' => 'allCheckClicked()',
					'label' => false,
					'div' => false,
					'class' => '',
					)); ?>
				</div>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Registration.status', __d('registrations', 'Status')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Registration.title', __d('registrations', 'Title')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Registration.publish_start', __d('registrations', 'Implementation date')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Registration.is_total_show', __d('registrations', 'Aggregates')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Registration.modified', __d('net_commons', 'Updated date')); ?>
			</th>
		</tr>
		<?php foreach ((array)$registrations as $index => $quest): ?>
		<tr class="animate-repeat btn-default">
			<td>

				<div class="text-center" ng-show="registrationFrameSettings.displayType == <?php echo RegistrationsComponent::DISPLAY_TYPE_LIST; ?>">
					<?php echo $this->NetCommonsForm->checkbox('List.RegistrationFrameDisplayRegistrations.' . $index . '.is_display', array(
					'options' => array(true => ''),
					'label' => false,
					'div' => false,
					'class' => '',
					//'value' => $quest['Registration']['key'],
					//'checked' => (isset($quest['RegistrationFrameDisplayRegistration']['registration_key'])) ? true : false,
					'ng-model' => 'isDisplay[' . $index . ']'
					));
					?>
					<?php echo $this->NetCommonsForm->hidden('RegistrationFrameDisplayRegistrations.' . $index . '.registration_key', array('value' => $quest['Registration']['key'])); ?>
				</div>

				<div class="text-center"  ng-show="registrationFrameSettings.displayType == <?php echo RegistrationsComponent::DISPLAY_TYPE_SINGLE; ?>">
					<?php echo $this->NetCommonsForm->radio('Single.RegistrationFrameDisplayRegistrations.registration_key',
					array($quest['Registration']['key'] => ''), array(
					'legend' => false,
					'label' => false,
					'div' => false,
					'class' => false,
					'hiddenField' => false,
					'checked' => (isset($quest['RegistrationFrameDisplayRegistration']['registration_key'])) ? true : false,
					));
					?>
				</div>

			</td>
			<td>
				<?php echo $this->RegistrationStatusLabel->statusLabelManagementWidget($quest);?>
			</td>
			<td>
				<?php echo $quest['Registration']['title']; ?>
			</td>
			<td>
				<?php if ($quest['Registration']['public_type'] == WorkflowBehavior::PUBLIC_TYPE_LIMITED): ?>
					<?php echo $this->Date->dateFormat($quest['Registration']['publish_start']); ?>
					<?php echo __d('registrations', ' - '); ?>
					<?php echo $this->Date->dateFormat($quest['Registration']['publish_end']); ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($quest['Registration']['is_total_show'] == RegistrationsComponent::EXPRESSION_SHOW): ?>
					<?php echo __d('registrations', 'On'); ?>
				<?php endif ?>
			</td>
			<td>
				<?php echo $this->Date->dateFormat($quest['Registration']['modified']); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>

<?php echo $this->element('NetCommons.paginator');

