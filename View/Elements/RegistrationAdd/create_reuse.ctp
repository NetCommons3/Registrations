<?php
/**
 * registration add create reuse element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->radio('create_option',
	array(RegistrationsComponent::REGISTRATION_CREATE_OPT_REUSE => __d('registrations', 'Re-use past registration')),
	array('ng-model' => 'createOption',
	'hiddenField' => false,
	'ng-disabled' => 'registrations.length == 0',
	));
?>
<div class="form-horizontal" uib-collapse="createOption != '<?php echo RegistrationsComponent::REGISTRATION_CREATE_OPT_REUSE; ?>'">
	<div class="col-xs-11 col-xs-offset-1">
		<div class="form-group">
			<div class="col-xs-3">
				<?php echo $this->NetCommonsForm->label('past_search',
					__d('registrations', 'Past registration') . $this->element('NetCommons.required')); ?>
			</div>
			<?php echo $this->NetCommonsForm->input('past_search', array(
				'type' => 'search',
				'label' => false,
				'div' => 'col-xs-9',
				'required' => true,
				'id' => 'registrations_past_search_filter',
				'ng-model' => 'q.registration.title',
				'placeholder' => __d('registrations', 'Refine by entering the part of the registration name')
			));?>

			<ul class="col-xs-12 registration-select-box form-control ">
				<li class="animate-repeat btn-default"
					ng-repeat="item in registrations | filter:q" ng-model="$parent.pastRegistrationSelect"
					uib-btn-radio="item.registration.id" uncheckable>

					<img ng-if="item.registration.titleIcon != ''" ng-src="{{item.registration.titleIcon}}" class="nc-title-icon" />
					{{item.registration.title}}

					<?php echo $this->element('Registrations.ng_status_label',
					array('status' => 'item.registration.status', 'periodRangeStat' => 'item.registration.periodRangeStat')); ?>

					<span ng-if="item.registration.answerTiming == <?php echo RegistrationsComponent::USES_USE; ?>">
					(
						{{item.registration.answerStartPeriod | ncDatetime}}
						<?php echo __d('registrations', ' - '); ?>
						{{item.registration.answerEndPeriod | ncDatetime}}
						<?php echo __d('registrations', 'Implementation'); ?>
					)
					</span>
				</li>
			</ul>
			<?php $this->NetCommonsForm->unlockField('past_registration_id'); ?>
			<?php echo $this->NetCommonsForm->hidden('past_registration_id', array('ng-value' => 'pastRegistrationSelect')); ?>
			<?php echo $this->NetCommonsForm->error('past_registration_id', null, array('class' => 'help-block')); ?>
		</div>
	</div>
</div>
