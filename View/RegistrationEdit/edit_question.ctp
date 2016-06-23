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

echo $this->Wysiwyg->wysiwygScript();
echo $this->element('Registrations.scripts');
echo $this->NetCommonsHtml->script(array(
	'/components/moment/min/moment.min.js',
	'/components/moment/min/moment-with-locales.min.js',
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

<article id="nc-registrations-question-edit"
	 ng-controller="Registrations.edit.question"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsRegistration)); ?>,
							'<?php echo h($newPageLabel); ?>',
							'<?php echo h($newQuestionLabel); ?>',
							'<?php echo h($newChoiceLabel); ?>',
							'<?php echo h($newChoiceColumnLabel); ?>',
							'<?php echo h($newChoiceOtherLabel); ?>')">

	<?php echo $this->element('Registrations.RegistrationEdit/registration_title'); ?>

	<?php echo $this->Wizard->navibar('edit_question'); ?>

	<div class="panel panel-default">

		<?php /* echo $this->element('Registrations.RegistrationEdit/registration_description'); */ ?>

		<?php echo $this->NetCommonsForm->create('RegistrationQuestion', $postUrl); ?>

		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Registration.key');?>

		<?php $this->NetCommonsForm->unlockField('RegistrationPage'); ?>

		<div class="panel-body">

			<uib-tabset active="activeTabIndex">
				<uib-tab ng-repeat="(pageIndex, page) in registration.registrationPage" index="$index">
					<uib-tab-heading>
						<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="page.hasError"></span>
						{{pageIndex+1}}
					</uib-tab-heading>

					<div class="tab-body">
						<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/add_question_button', array('isPublished' => $isPublished)); ?>
						<div class="clearfix"></div>

						<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/hidden_page_info_set'); ?>

					<uib-accordion close-others="true">
						<uib-accordion-group
								class="form-horizontal"
								ng-repeat="(qIndex, question) in page.registrationQuestion"
								is-open="question.isOpen">

							<uib-accordion-heading>
								<?php /* 質問ヘッダーセット（移動ボタン、削除ボタンなどの集合体 */
									echo $this->element('Registrations.RegistrationEdit/EditQuestion/accordion_heading', array('isPublished' => $isPublished)); ?>
								<div class="clearfix"></div>
							</uib-accordion-heading>

							<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/hidden_question_info_set'); ?>

							<?php /* ここから質問本体設定 */
								/* 質問タイトル */
								echo $this->QuestionEdit->questionInput('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_value',
									__d('registrations', 'question title'),
									array('type' => 'text',
										'ng-model' => 'question.questionValue',
										'required' => 'required',
									));
								/* 必須 */
								echo $this->QuestionEdit->questionInput('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_require',
								__d('registrations', 'Required'),
								array(
								'type' => 'checkbox',
								'ng-checked' => 'question.isRequire == ' . RegistrationsComponent::USES_USE,
								'ng-model' => 'question.isRequire',
								),
								__d('registrations', 'set answer to this question is required'));
								/* 質問文 */
								echo $this->QuestionEdit->questionInput('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.description',
									__d('registrations', 'question sentence'),
									array('type' => 'wysiwyg',
										'id' => false,
										'ng-model' => 'question.description',
									));
								/* 質問種別 */
								echo $this->QuestionEdit->questionInput('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.question_type',
									__d('registrations', 'Question type'),
									array('type' => 'select',
										'required' => true,
										'options' => $questionTypeOptions,
										'ng-model' => 'question.questionType',
										'ng-change' => 'changeQuestionType($event, {{pageIndex}}, {{qIndex}})',
										'empty' => null
									));
							?>
							<div class="row form-group">
								<div class="col-xs-12">
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
											<?php echo $this->element($elementFolder . 'text_area_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_MATRIX_SELECTION_LIST; ?>">
											<?php echo $this->element($elementFolder . 'matrix_choice_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_MATRIX_MULTIPLE; ?>">
											<?php echo $this->element($elementFolder . 'matrix_choice_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_DATE_AND_TIME; ?>">
											<?php echo $this->element($elementFolder . 'date_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo RegistrationsComponent::TYPE_SINGLE_SELECT_BOX; ?>">
											<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
										</div>
									</div>
								</div>
							</div >

						</uib-accordion-group>
					</uib-accordion>

					<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/add_question_button'); ?>

					<?php if (! $isPublished): ?>
						<div class="text-center">
							<button class="btn btn-danger" type="button"
									ng-disabled="registration.registrationPage.length < 2"
									ng-click="deletePage($index, '<?php echo __d('registrations', 'Do you want to delete this page?'); ?>')">
								<span class="glyphicon glyphicon-remove"></span><?php echo __d('registrations', 'Delete this page'); ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
			</uib-tab>
			<?php if (! $isPublished): ?>
				<a class="registration-add-page-tab" ng-click="addPage($event)">
					<span class="glyphicon glyphicon-plus"></span>
					<span class=""><?php echo __d('registrations', 'Add Page'); ?></span>
				</a>
			<?php endif; ?>
		</uib-tabset>

	</div>

		<div class="panel-footer text-center">
			<?php echo $this->Wizard->buttons('edit_question', $cancelUrl); ?>
		</div>


	<?php echo $this->NetCommonsForm->end(); ?>
</article>