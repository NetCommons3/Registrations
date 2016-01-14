<?php
/**
 * registration aggregate total table view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="col-xs-12">
	<div class="table-responsive">
		<table class="table table-striped table-bordered registration-table-vcenter table-responsive">
			<thead>
				<tr>
					<th><?php echo __d('registrations', 'Item name'); ?></th>
					<th><?php echo __d('registrations', 'Aggregate value'); ?></th>
					<th><?php echo __d('registrations', 'The percentage'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($question['RegistrationChoice'] as $choice): ?>
				<tr>
					<td>
						<?php echo h($choice['choice_label']); ?>
					</td>
					<td>
						<?php
							$cnt = (isset($choice['aggregate_total']['aggregate_not_matrix'])) ? $choice['aggregate_total']['aggregate_not_matrix'] : '0';
							echo h($cnt);
						?>
					</td>
					<td>
						<?php
							$thePercentage = RegistrationsComponent::NOT_OPERATION_MARK;
							if (isset($question['answer_total_cnt'])) {
								$percent = round( (intval($cnt) / intval($question['answer_total_cnt'])) * 100, 1, PHP_ROUND_HALF_UP );
								$thePercentage = $percent . ' ' . RegistrationsComponent::PERCENTAGE_UNIT;
							}
							echo $thePercentage;
						?>
					</td>
				</tr>

			<?php endforeach; ?>
			</tbody>

		</table>
	</div>
</div>