<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>
<?php echo $this->BlockTabs->block('answer_list', ['displayAllTab' =>
	true, 'displayBlockTitle' => true]); ?>
<?php if (empty($summaries)): ?>
	<p>
		<?php echo __d('net_commons', '%s is not.', __d('registrations', 'Registration data')); ?>
	</p>

<?php else: ?>
	<?php $questions = $registration['RegistrationPage'][0]['RegistrationQuestion']; ?>
		<div class="table-responsive">
			<table class="table">
				<thead>
					<th>
						<?php echo __d('registrations', 'RegistrationAnswerSummary ID'); ?>
					</th>
					<?php foreach ($questions as $question)
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
							<?php
							// question.idをキーanswer_valueを値とした連想配列をつくる
							$answers = array();
							foreach ($summary['RegistrationAnswer'] as $answer) {
								if (isset($answer['UploadFile'])) {
									$value = $this->NetCommonsHtml->link(
										$answer['RegistrationAnswer']['answer_value'],
										$this->NetCommonsHtml->url(
											[
												'action' => 'download_file',
												$answer['RegistrationAnswer']['id'],
												'answer_value_file',
											]
										)
									);
								} elseif (Hash::check($answer, 'RegistrationAnswer.answer_values')) {
									$value = h(implode(',',
										$answer['RegistrationAnswer']['answer_values']));
								} else {
									$value = h($answer['RegistrationAnswer']['answer_value']);
								}
							}
							$answers[$answer['RegistrationQuestion']['id']] = $value;
							?>
							<?php foreach ($questions as $question) : ?>
								<td>
									<?php echo Hash::get($answers, $question['id'], '') ?>
								</td>
							<?php endforeach;?>
						</tr>
					<?php endforeach;?>
				
				</tbody>
			</table>
		</div>
		<?php echo $this->element('NetCommons.paginator'); ?>
		<div class="text-right">
			<?php
			echo $this->NetCommonsForm->create('',
				array(
					'type' => 'delete',
					'url' => $this->NetCommonsHtml->url([
						'action' => 'delete_answer',
						'key' => $registration['Registration']['key']
					])
				));
			?>
			<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
			<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

			<?php echo $this->NetCommonsForm->hidden('key'); ?>

			<?php echo $this->Button->delete(
				__d('registrations', 'Delete all answer'),
				sprintf(__d('net_commons', 'Deleting the %s. Are you sure to proceed?'), __d('registrations', 'Registration data'))
			); ?>
			<?php echo $this->NetCommonsForm->end(); ?>

		</div>

<?php endif;
