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
<?php echo $this->element('Registrations.scripts'); ?>

<article id="nc-registrations-answer-confirm-<?php Current::read('Frame.id'); ?>">

	<?php echo $this->element('Registrations.Answers/answer_header'); ?>

	<?php echo $this->element('Registrations.Answers/answer_test_mode_header'); ?>

	<p>
		<?php echo $registration['Registration']['thanks_content']; ?>
	</p>
	<table class="table">
		<tbody>
			<tr>
				<th>
					<?php echo __d('registrations', 'RegistrationAnswerSummary ID') ?>
				</th>
				<td>
					<?php echo $summary['RegistrationAnswerSummary']['id']; ?>
				</td>
			</tr>
			<?php foreach ($answers as $answer) : ?>
				<tr>
					<th>
						<?php echo h($answer['RegistrationQuestion']['question_value']); ?>
					</th>
					<td>
						<?php
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
						echo nl2br($value);
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<footer class="text-center">
		<?php echo $this->BackTo->pageLinkButton(__d('net_commons', 'Close')); ?>
	</footer>
</article>
