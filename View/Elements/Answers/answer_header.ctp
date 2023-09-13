<?php
/**
 * answer header view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php if ($this->Workflow->canEdit('Registration', $registration)) : ?>

	<div class="pull-right">
		<?php echo $this->Button->editLink('', array(
		'plugin' => 'registrations',
		'controller' => 'registration_edit',
		'action' => 'edit_question',
		'key' => $registration['Registration']['key'])); ?>
	</div>
<?php endif; ?>

<h1>
	<?php echo $this->Workflow->label($registration['Registration']['status']); ?>

	<?php echo $this->TitleIcon->titleIcon($registration['Registration']['title_icon']); ?>
	<?php echo h($registration['Registration']['title']); ?>
	<small><?php echo h($registration['Registration']['sub_title']);?></small>
	<?php if (isset($remainingCount) && $remainingCount > 0) : ?>
		<div class="registration-answer-header-remaining-count">
			<small><?php echo __d('registrations', '(Remaining: %s)', $remainingCount);?></small>
		</div>
	<?php endif; ?>
</h1>
