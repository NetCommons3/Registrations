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
						<?php echo __d('registrations', 'Registration Number'); ?>
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
								<?php echo h($summary['RegistrationAnswerSummary']['serial_number']); ?>
							</td>
							<?php
							// question.idをキーanswer_valueを値とした連想配列をつくる
							$answers = array();
							foreach ($summary['RegistrationAnswer'] as $answer) {
								if (isset($answer['UploadFile'])) {
									$value = $this->NetCommonsHtml->link(
										$answer['RegistrationAnswer']['answer_value'],
										[
											'action' => 'download_file',
											$answer['RegistrationAnswer']['id'],
											'answer_value_file',
										]
									);
								} elseif (Hash::check($answer, 'RegistrationAnswer.answer_values')) {
									$otherAnswer = Hash::get($answer,
										'RegistrationAnswer.other_answer_value');
									if ($otherAnswer) {
										// 「その他」を取り除いて代わりにその他に入力されたテキストを追加
										array_pop($answer['RegistrationAnswer']['answer_values']);
										$answer['RegistrationAnswer']['answer_values'][] =
											$otherAnswer;
									}
									$value = h(implode(',',
										$answer['RegistrationAnswer']['answer_values']));
								} else {
									$value = h($answer['RegistrationAnswer']['answer_value']);
								}
								$answers[$answer['RegistrationQuestion']['id']] = $value;
							}
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
					'url' => NetCommonsUrl::blockUrl([
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

		<div class="text-center">
			<?php echo $this->BackTo->indexLinkButton(
				__d('registrations', 'Back to page'), 'default_setting_action', array('icon' => 'arrow-left')
			); ?>
		</div>

<?php endif;
