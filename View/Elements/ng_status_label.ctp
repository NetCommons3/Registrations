<?php
/**
 * registration comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<span ng-switch="<?php echo $status ?>">
    <span ng-switch-when="<?php echo WorkflowComponent::STATUS_IN_DRAFT ?>" class="label label-info"><?php echo __d('net_commons', 'Temporary'); ?></span>
    <span ng-switch-when="<?php echo WorkflowComponent::STATUS_APPROVAL_WAITING ?>" class="label label-warning"><?php echo __d('net_commons', 'Approving'); ?></span>
    <span ng-switch-when="<?php echo WorkflowComponent::STATUS_DISAPPROVED ?>" class="label label-danger"><?php echo __d('net_commons', 'Disapproving'); ?></span>
    <span ng-switch-default>
        <span ng-switch="<?php echo $periodRangeStat; ?>">
            <span ng-switch-when="<?php echo RegistrationsComponent::REGISTRATION_PERIOD_STAT_BEFORE ?>" class="label label-default"><?php echo __d('registrations', 'Before public'); ?></span>
            <span ng-switch-when="<?php echo RegistrationsComponent::REGISTRATION_PERIOD_STAT_END ?>" class="label label-default"><?php echo __d('registrations', 'End'); ?></span>
    </span>
</span>
