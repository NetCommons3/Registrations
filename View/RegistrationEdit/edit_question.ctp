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

echo $this->element('Registrations.scripts');
echo $this->NetCommonsHtml->script(array(
	'/components/moment/min/moment.min.js',
	'/components/moment/min/moment-with-locales.min.js',
	'/components/tinymce-dist/tinymce.min.js',
	'/components/angular-ui-tinymce/src/tinymce.js',
	'/net_commons/js/wysiwyg.js',
	'/registrations/js/registrations_edit_question.js',
));
$jsRegistration = NetCommonsAppController::camelizeKeyRecursive(RegistrationsAppController::changeBooleansToNumbers($this->data));
?>

<?php
	if ($isPublished) {
		$elementFolder = 'Registrations.RegistrationEdit/EditQuestion/options_after_published/';
	} else {
		$elementFolder = 'Registrations.RegistrationEdit/EditQuestion/options_before_published/';
	}
?>

<div id="nc-registrations-question-edit"
	 ng-controller="Registrations.edit.question"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsRegistration)); ?>,
							'<?php echo h($newPageLabel); ?>',
							'<?php echo h($newQuestionLabel); ?>',
							'<?php echo h($newChoiceLabel); ?>',
							'<?php echo h($newChoiceColumnLabel); ?>',
							'<?php echo h($newChoiceOtherLabel); ?>')">

	<?php echo $this->NetCommonsForm->create('RegistrationQuestion', $postUrl); ?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Registration.key');?>
		<?php	/* Wizard中は一時保存ステータスで回さないとWorkflowに叱られる */
		echo $this->NetCommonsForm->hidden('Registration.status', array('value' => WorkflowComponent::STATUS_IN_DRAFT));
		?>

		<?php $this->NetCommonsForm->unlockField('RegistrationPage'); ?>

		<div class="modal-body">

			<?php echo $this->element('Registrations.RegistrationEdit/edit_flow_chart', array('current' => '1')); ?>

			<?php echo $this->element('Registrations.RegistrationEdit/registration_title'); ?>

			<!--<tabset>-->
				<div ng-repeat="(pageIndex, page) in registration.registrationPage" active="page.tabActive">
					<!--<tab-heading>-->
					<!--	{{pageIndex+1}}<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="page.hasError"></span>-->
					<!--</tab-heading>-->

					<div class="tab-body">
						<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/add_question_button'); ?>
						<div class="clearfix"></div>

						<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/hidden_page_info_set'); ?>

					<accordion close-others="true">
						<accordion-group
								class="form-horizontal"
								ng-repeat="(qIndex, question) in page.registrationQuestion"
								is-open="question.isOpen">

							<accordion-heading>
								<?php /* 質問ヘッダーセット（移動ボタン、削除ボタンなどの集合体 */
									echo $this->element('Registrations.RegistrationEdit/EditQuestion/accordion_heading'); ?>
								<div class="clearfix"></div>
							</accordion-heading>

							<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/hidden_question_info_set'); ?>

							<?php /* ここから質問本体設定 */
								/* 必須? */
								echo $this->QuestionEdit->questionInput('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_require',
									__d('registrations', 'Required'),
									array(	'type' => 'checkbox',
											'class' => '',
											'ng-checked' => 'question.isRequire == ' . RegistrationsComponent::USES_USE,
											'ng-model' => 'question.isRequire',
											'ng-disabled' => 'isPublished != 0',
									),
									__d('registrations', 'set answer to this question is required'));
								/* 質問タイトル */
								echo $this->QuestionEdit->questionInput('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_value',
									__d('registrations', 'question title'),
									array('type' => 'text',
										'ng-model' => 'question.questionValue',
										'required' => 'required',
										'ng-disabled' => 'isPublished != 0',
									));
								/* 質問文 */
								echo $this->QuestionEdit->questionInput('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.description',
									__d('registrations', 'question sentence'),
									array('type' => 'wysiswyg',
										'id' => false,
										'ng-model' => 'question.description',
										'ui-tinymce' => 'tinymce.options',
										'rows' => 5,
										'ng-disabled' => 'isPublished != 0',
									));
								/* 質問種別 */
								echo $this->QuestionEdit->questionInput('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_type',
									__d('registrations', 'Question type'),
									array('type' => 'select',
										'required' => true,
										'options' => $questionTypeOptions,
										'ng-model' => 'question.questionType',
										'ng-change' => 'changeQuestionType($event, {{pageIndex}}, {{qIndex}})',
										'ng-disabled' => 'isPublished != 0',
										'empty' => null
									));
							?>
							<div class="row form-group">
								<div class="col-sm-12">
									<div class="well">
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_SELECTION; ?>">
											<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_MULTIPLE_SELECTION; ?>">
											<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_TEXT; ?>">
											<?php echo $this->element($elementFolder . 'text_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_TEXT_AREA; ?>">
											<?php /* 複数行テキストの場合は詳細設定がないです */ ?>
										</div>
										<!--<div ng-if="question.questionType == --><?php //echo RegistrationsComponent::TYPE_MATRIX_SELECTION_LIST; ?><!--">-->
										<!--	--><?php //echo $this->element($elementFolder . 'matrix_choice_option'); ?>
										<!--</div>-->
										<!--<div ng-if="question.questionType == --><?php //echo RegistrationsComponent::TYPE_MATRIX_MULTIPLE; ?><!--">-->
										<!--	--><?php //echo $this->element($elementFolder . 'matrix_choice_option'); ?>
										<!--</div>-->
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_DATE_AND_TIME; ?>">
											<?php echo $this->element($elementFolder . 'date_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_SINGLE_SELECT_BOX; ?>">
											<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
										</div>
									</div>
								</div>
							</div >

						</accordion-group>
					</accordion>

					<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/add_question_button'); ?>

					<!--<div class="text-center" ng-if="isPublished == 0">-->
					<!--	<button class="btn btn-danger" type="button"-->
					<!--			ng-disabled="registration.registrationPage.length < 2"-->
					<!--			ng-click="deletePage($index, '--><?php //echo __d('registrations', 'Do you want to delete this page?'); ?><!--')">-->
					<!--		<span class="glyphicon glyphicon-remove"></span>--><?php //echo __d('registrations', 'Delete this page'); ?>
					<!--	</button>-->
					<!--</div>-->
				</div>
			</div>
			<!--<tab class="registration-add-page-tab" ng-click="addPage($event)" ng-if="isPublished == 0">-->
			<!--	<tab-heading>-->
			<!--		<span class="glyphicon glyphicon-plus"></span>-->
			<!--		<span class="">--><?php //echo __d('registrations', 'Add Page'); ?><!--</span>-->
			<!--	</tab-heading>-->
			<!--</tab>-->
		<!--</tabset>-->

	</div>
	<div class="modal-footer">
		<div class="text-center">
			<?php echo $this->Button->cancel(__d('net_commons', 'Cancel'), $cancelUrl, array('icon' => 'remove')); ?>
			<?php echo $this->Button->save(__d('net_commons', 'NEXT'), array('icon' => 'chevron-right')) ?>
		</div>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</div>