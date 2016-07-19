<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>
<?php echo $this->BlockTabs->block('answer_list', ['displayAllTab' =>
	true, 'displayBlockTitle' => true]); ?>
<?php if (empty($summaries)): ?>
	<p>
		<?php echo __d('net_commons', '%s is not.', __d('registration', 'Registration data')); ?>
	</p>

<?php else: ?>
		<div class="table-responsive">
			<table class="table">
				<thead>
					<th>
						<?php echo __d('registrations', 'RegistrationAnswerSummary ID'); ?>
					</th>
					<?php foreach ($registration['RegistrationPage'][0]['RegistrationQuestion'] as $question)
						: ?>
						<th>
							<?php echo h($question['question_value']);?>
						</th>
					<?php endforeach; ?>
				</thead>
				<tbody>
					<?php foreach ($summaries as $summary): ?>
						<tr>
							<td>
								<?php echo h($summary['RegistrationAnswerSummary']['id']); ?>
							</td>
							<?php foreach ($summary['RegistrationAnswer'] as $answer): ?>
								<?php
								if (Hash::check($answer, 'RegistrationAnswer.answer_values')) {
									$value = implode(',', $answer['RegistrationAnswer']['answer_values']);
								} else {
									$value = $answer['RegistrationAnswer']['answer_value'];
								}
								?>
								<td>
									<?php echo h($value); ?>
								</td>
							<?php endforeach;?>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
<?php endif
