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
	<div class="col-md-12 col-xs-12">
		<div class=" well well-sm">
			<div class="pull-right">
				<?php echo $this->Button->editLink('', array(
				'plugin' => 'registrations',
				'controller' => 'registration_edit',
				'action' => 'edit_question',
				'key' => $registration['Registration']['key'])); ?>
			</div>
			<small>
				<dl class="registration-editor-dl">
					<dt><?php echo __d('registrations', 'Author'); ?></dt>
					<dd><?php echo $registration['TrackableCreator']['username']; ?></dd>
					<dt><?php echo __d('registrations', 'Modified by'); ?></dt>
					<dd><?php echo $registration['TrackableUpdater']['username']; ?>
						(<?php echo $this->Date->dateFormat($registration['Registration']['modified']); ?>)
					</dd>
				</dl>
				<dl class="registration-editor-dl">
					<dt><?php echo __d('registrations', 'Pages'); ?></dt>
					<dd><?php echo $registration['Registration']['page_count']; ?></dd>
					<dt><?php echo __d('registrations', 'Questions'); ?></dt>
					<dd><?php echo $registration['Registration']['question_count']; ?></dd>
					<dt><?php echo __d('registrations', 'Answers' ); ?></dt>
					<dd><?php echo $registration['Registration']['all_answer_count']; ?></dd>
				</dl>
				<div class="clearfix"></div>
			</small>
		</div>
	</div>
</div>
