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

$jsQuestionPage = NetCommonsAppController::camelizeKeyRecursive($questionPage);
$jsAnswers = NetCommonsAppController::camelizeKeyRecursive($answers);
?>
<?php echo $this->element('Registrations.scripts'); ?>

<article id="nc-registrations-answer-<?php echo Current::read('Frame.id'); ?>"
		ng-controller="RegistrationsAnswer"
		 ng-init="initialize(
		 <?php echo h(json_encode($jsQuestionPage)); ?>,
		 <?php echo h(json_encode($jsAnswers)); ?>)">

	<?php echo $this->element('Registrations.Answers/answer_header'); ?>

	<?php echo $this->element('Registrations.Answers/answer_test_mode_header'); ?>

	<?php if ($questionPage['page_sequence'] > 0): ?>
		<?php $progress = round((($questionPage['page_sequence']) / $registration['Registration']['page_count']) * 100); ?>
		<div class="row">
			<div class="col-sm-8">
			</div>
			<div class="col-sm-4">
				<div class="progress">
					<uib-progressbar class="progress-striped" value="<?php echo $progress ?>" type="warning"><?php echo $progress ?>%</uib-progressbar>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php
		echo $this->NetCommonsForm->create('RegistrationAnswer', array(
			'url' => NetCommonsUrl::actionUrl(array(
							'controller' => 'registration_answers',
							'action' => 'view',
							Current::read('Block.id'),
							$registration['Registration']['key'],
							'frame_id' => Current::read('Frame.id')
		)),
			'type' => 'file'
			));
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');
		echo $this->NetCommonsForm->hidden('RegistrationPage.page_sequence');
		echo $this->NetCommonsForm->hidden('RegistrationPage.route_number');
	?>

		<?php foreach($questionPage['RegistrationQuestion'] as $index => $question): ?>
			<?php
			$fieldName = 'answer_value';
			if ($question['question_type'] === RegistrationsComponent::TYPE_FILE) {
				// 添付ファイルは answer_value_fileフィールドで処理しているのでエラー確認もanswer_value_fileに対しておこなう
				$fieldName = 'answer_value_file';
			}
			$hasError = $this->Form->isFieldError('RegistrationAnswer.' . $question['key'] . '.0.' . $fieldName);
			?>
			<div class="form-group
							<?php if ($hasError): ?>
							has-error
							<?php endif ?>">


				<label class="control-label">
					<?php echo h($question['question_value']); ?>
					<?php if ($question['is_require'] == RegistrationsComponent::REQUIRES_REQUIRE): ?>
							<?php echo $this->element('NetCommons.required'); ?>
					<?php endif ?>
				</label>

				<p class="help-block">
					<?php echo $question['description']; ?>
				</p>

				<?php echo $this->RegistrationAnswer->answer($question); ?>
			</div>
		<?php endforeach; ?>


	<div class="text-center">
		<?php echo $this->NetCommonsForm->button(
		__d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
		array(
		'class' => 'btn btn-primary',
		'name' => 'next_' . '',
		)) ?>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>

</article>
