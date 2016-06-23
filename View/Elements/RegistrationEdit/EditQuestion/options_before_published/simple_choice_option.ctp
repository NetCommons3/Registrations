<?php
/**
 * 登録フォーム質問の種別によって異なる詳細設定のファイル
 * このファイルでは択一選択、複数選択、リスト選択タイプをフォローしている
 * 択一選択が設定か所はもっとも多い
 * 複数選択は択一選択の設定から「スキップロジック」を抜いたもの
 * リスト選択は択一選択から「その他」設定を抜いたものである
 * 2015.03.12現在では「テンプレートデータから読み込み」の部分が未対応です
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<div class="col-xs-12">
		<button type="button" class="btn btn-default pull-right" ng-click="addChoice($event, pageIndex, qIndex, question.registrationChoice.length, '<?php echo RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
			<span class="glyphicon glyphicon-plus"></span><?php echo __d('registrations', 'add choices'); ?>
		</button>
		<?php /* 選択肢を横並びにする */ ?>
		<label class="checkbox-inline" ng-show="question.questionType != <?php echo RegistrationsComponent::TYPE_SINGLE_SELECT_BOX; ?>">
			<?php echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_choice_horizon',
			array(
			'value' => RegistrationsComponent::USES_USE,
			'ng-model' => 'question.isChoiceHorizon',
			'ng-checked' => 'question.isChoiceHorizon == ' . RegistrationsComponent::USES_USE
			));
			?>
			<?php echo __d('registrations', 'horizontal choices'); ?>
			<?php echo $this->element(
			'Registrations.RegistrationEdit/ng_errors', array(
			'errorArrayName' => 'question.errorMessages.isChoiceHorizon',
			)); ?>
		</label>
		<?php ///* 選択肢をランダムにする */ ?>
		<!--<label class="checkbox-inline" ng-show="question.questionType != --><?php //echo RegistrationsComponent::TYPE_SINGLE_SELECT_BOX; ?><!--">-->
		<!--	--><?php //echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_choice_random',
		//	array(
		//	'value' => RegistrationsComponent::USES_USE,
		//	'ng-model' => 'question.isChoiceRandom',
		//	'ng-checked' => 'question.isChoiceRandom == ' . RegistrationsComponent::USES_USE
		//	));
		//	?>
		<!--	--><?php //echo __d('registrations', 'randomaize choices'); ?>
		<!--	--><?php //echo $this->element(
		//	'Registrations.RegistrationEdit/ng_errors', array(
		//	'errorArrayName' => 'question.errorMessages.isChoiceRandom',
		//	)); ?>
		<!--</label>-->
		<?php ///* スキップにする */ ?>
		<!--<label class="checkbox-inline" ng-show="question.questionType != --><?php //echo RegistrationsComponent::TYPE_MULTIPLE_SELECTION; ?><!--">-->
		<!--	--><?php //echo $this->NetCommonsForm->checkbox('RegistrationPage.{{pageIndex}}.RegistrationQuestion.{{qIndex}}.is_skip',
		//	array(
		//	'value' => RegistrationsComponent::SKIP_FLAGS_SKIP,
		//	'ng-model' => 'question.isSkip',
		//	'ng-checked' => 'question.isSkip == ' . RegistrationsComponent::SKIP_FLAGS_SKIP,
		//	'ng-disabled' => 'isDisabledSetSkip(page, question)'
		//	));
		//	?>
		<!--	--><?php //echo $this->element(
		//	'Registrations.RegistrationEdit/ng_errors', array(
		//	'errorArrayName' => 'question.errorMessages.isSkip',
		//	)); ?>
		<!--	--><?php //echo __d('registrations', 'set page skip'); ?>
		<!--</label>-->
	</div>
</div>
<div class="row">
	<div class="col-xs-12">

		<ul class="list-group registration-edit-choice-list-group">
			<li class="list-group-item" ng-repeat="(cIndex, choice) in question.registrationChoice" >
				<div class="form-inline pull-right">
					<!--<select name="data[RegistrationPage][{{pageIndex}}][RegistrationQuestion][{{qIndex}}][RegistrationChoice][{{cIndex}}][skip_page_sequence]"-->
					<!--		class="form-control input-sm"-->
					<!--		ng-change="changeSkipPageChoice(choice.skipPageSequence)"-->
					<!--		ng-model="choice.skipPageSequence"-->
					<!--		ng-if="question.isSkip == --><?php //echo RegistrationsComponent::SKIP_FLAGS_SKIP; ?>
					<!--		&& question.questionType != --><?php //echo RegistrationsComponent::TYPE_MULTIPLE_SELECTION; ?><!--">-->
					<!--	<option ng-repeat="skipPage in registration.registrationPage | filter: greaterThan('pageSequence', page)"-->
					<!--			value="{{skipPage.pageSequence}}"-->
					<!--			ng-selected="choice.skipPageSequence == skipPage.pageSequence || (choice.skipPageSequence == null && skipPage.pageSequence == pageIndex+1)">-->
					<!--		{{1 * skipPage.pageSequence + 1}}-->
					<!--	</option>-->
					<!--	<option value="--><?php //echo RegistrationsComponent::SKIP_GO_TO_END ?><!--"-->
					<!--			ng-selected="choice.skipPageSequence == --><?php //echo RegistrationsComponent::SKIP_GO_TO_END ?><!-- || (choice.skipPageSequence == null && registration.registrationPage.length-1==pageIndex)">-->
					<!--		--><?php //echo __d('registrations', 'goto end'); ?>
					<!--	</option>-->
					<!--	<option-->
					<!--			value="{{registration.registrationPage.length}}"-->
					<!--			title="--><?php //echo __d('registrations', '(new page will be created)'); ?><!--">-->
					<!--		--><?php //echo __d('registrations', 'create new page for this skip'); ?>
					<!--	</option>-->
					<!--</select>-->
					<button class="btn btn-default" type="button"
							ng-disabled="question.registrationChoice.length < 2"
							ng-click="deleteChoice($event, pageIndex, qIndex, choice.choiceSequence)"
							ng-if="choice.otherChoiceType == <?php echo RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
						<span class="glyphicon glyphicon-remove"> </span>
					</button>
				</div>
				<div class="form-inline">
					<?php echo $this->element('Registrations.RegistrationEdit/EditQuestion/options_before_published/choice'); ?>
				</div>
				<?php echo $this->element(
					'Registrations.RegistrationEdit/ng_errors', array(
					'errorArrayName' => 'choice.errorMessages.skipPageSequence',
				)); ?>
			</li>
		</ul>

		<div class="" ng-if="question.questionType != <?php echo RegistrationsComponent::TYPE_SINGLE_SELECT_BOX; ?>">
			<div class="pull-right text-right">
				<button type="button" class="btn btn-default"
						ng-show="question.registrationChoice.length > 2"
						ng-click="addChoice($event, pageIndex, qIndex, question.registrationChoice.length, '<?php echo RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
					<span class="glyphicon glyphicon-plus"></span><?php echo __d('registrations', 'add choices'); ?>
				</button>
				<p class="help-block small">
					<?php echo __d('registrations', 'You can not use the character of |, : for choice text '); ?>
				</p>
			</div>
			<label class="checkbox-inline" ng-if="question.questionType != <?php echo RegistrationsComponent::TYPE_SINGLE_SELECT_BOX; ?>">
				<input type="checkbox" ng-model="question.hasAnotherChoice" ng-change="changeAnotherChoice(pageIndex, qIndex, '<?php echo RegistrationsComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT ?>', '<?php echo RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>')">
				<?php echo __d('registrations', 'add another choice'); ?>
			</label>
		</div>
	</div>
</div>
<?php
/* まだデータテンプレートからの読み込み方式が提唱されていないのでコメントアウトしておく FUJI: 2015.03.11
<div class="row text-center">
	<select class="form-control">
		<option>データテンプレートから選択肢を読み込む</option>
	</select>
</div>
*/