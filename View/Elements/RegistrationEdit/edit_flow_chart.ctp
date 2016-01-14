<?php
/**
 * registration edit flow template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
	$steps = array(
	1 => __d('registrations', 'Set questions'),
	2 => __d('registrations', 'Set result display'),
	3 => __d('registrations', 'Set registration'));
	$stepCount = count($steps);
	$stepWidth = 'style="width: ' . 100 / $stepCount . '%;"';
	$check = $steps;
	?>

<div class="progress registration-steps">
	<?php foreach ($steps as $index => $step): ?>
		<?php if ($index == $current): ?>
			<div class="progress-bar progress-bar registration-step-item" <?php echo $stepWidth; ?> >
				<span class="registration-step-item-title">
					<span class="btn-primary"><span class="badge"><?php echo $index; ?></span></span>
		<?php else : ?>
			<div class="registration-step-item" <?php echo $stepWidth; ?>>
				<span class="registration-step-item-title">
					<span class="badge"><?php echo $index; ?></span>
		<?php endif; ?>
					<?php echo $step; ?>
				</span>
					<?php if (next($check)) :?>
						<span class="glyphicon glyphicon-chevron-right"></span>
					<?php endif; ?>
			</div>
	<?php endforeach; ?>
</div>
