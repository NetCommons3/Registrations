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

<?php echo $this->NetCommonsForm->hidden('id'); ?>
<?php echo $this->NetCommonsForm->hidden('frame_key'); ?>
<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

<!--<div class="col-sm-12 form-group">-->
<!--	--><?php //echo $this->element('Registrations.FrameSettings/edit_display_type'); ?>
<!--</div>-->
<?php echo $this->NetCommonsForm->input('display_type', array(
	'type' => 'hidden',
	'class' => '',
	'options' => array(
		RegistrationsComponent::DISPLAY_TYPE_SINGLE => __d('registrations', 'Show only one registration'),
		RegistrationsComponent::DISPLAY_TYPE_LIST => __d('registrations', 'Show registrations list')),
	'legend' => false,
	'label' => false,
	'before' => '<div class="radio-inline"><label>',
	'separator' => '</label></div><div class="radio-inline"><label>',
	'after' => '</label></div>',
	'hiddenField' => false,
	'ng-model' => 'registrationFrameSettings.displayType',
));
// ε(　　　　 v ﾟωﾟ)　＜ 仮にhiddenにしとく。あとで設定ごと削除する
?>

<!--<div class="col-sm-12 form-group" ng-show="registrationFrameSettings.displayType == --><?php //echo RegistrationsComponent::DISPLAY_TYPE_LIST; ?><!--">-->
<!--	--><?php //echo $this->element('Registrations.FrameSettings/edit_list_display_option'); ?>
<!--</div>-->

<div class="col-sm-12 form-group">
	<?php echo $this->element('Registrations.FrameSettings/edit_display_registration'); ?>
</div>