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
	'/components/d3/d3.min.js',
	'/components/nvd3/nv.d3.min.js',
	'/components/angular-nvd3/dist/angular-nvd3.min.js',
	'/registrations/js/registrations_graph.js'
	));
echo $this->NetCommonsHtml->css('/components/nvd3/nv.d3.css');

$jsQuestions = NetCommonsAppController::camelizeKeyRecursive(RegistrationsAppController::changeBooleansToNumbers($questions));
?>

<?php /* FUJI: 下のdivのidがnc-registrations-total-xx でよいか要確認. */ ?>
<div id="nc-registrations-total-<?php echo Current::read('Frame.id'); ?>"
	ng-controller="RegistrationsAnswerSummary"
	ng-init="initialize(<?php echo h(json_encode($jsQuestions)); ?>)">

<article>
	<?php echo $this->element('Registrations.Answers/answer_test_mode_header'); ?>

	<?php echo $this->element('Registrations.Answers/answer_header'); ?>

	<?php if (!empty($registration['Registration']['total_comment'])): ?>
		<div class="row">
			<div class="col-xs-12">
					<p>
						<?php echo $registration['Registration']['total_comment']; ?>
					</p>
			</div>
		</div>
	<?php endif; ?>

	<?php foreach ($questions as $registrationQuestionId => $question): ?>
		<?php
			if ($question['is_result_display'] != RegistrationsComponent::EXPRESSION_SHOW) {
				continue;	//集計表示をしない、なので飛ばす
			}

			//集計表示用のelement名決定
			$elementName = '';
			$matrix = '';
			if (RegistrationsComponent::isMatrixInputType($question['question_type'])) {
				$matrix = '_matrix';
			}
			if ($question['result_display_type'] == RegistrationsComponent::RESULT_DISPLAY_TYPE_BAR_CHART) {
				$elementName = 'Registrations.AnswerSummaries/aggregate' . $matrix . '_bar_chart';
			} elseif ($question['result_display_type'] == RegistrationsComponent::RESULT_DISPLAY_TYPE_PIE_CHART) {
				$elementName = 'Registrations.AnswerSummaries/aggregate' . $matrix . '_pie_chart';
			} elseif ($question['result_display_type'] == RegistrationsComponent::RESULT_DISPLAY_TYPE_TABLE) {
				$elementName = 'Registrations.AnswerSummaries/aggregate' . $matrix . '_table';
			} else {
				continue; // 不明な表示タイプ
			}
		?>
		<div class="row">
			<?php
			//各質問ごと集計表示の共通ヘッダー
			echo $this->element('Registrations.AnswerSummaries/aggregate_common_header',
				array('question' => $question));

			//グラフ・表の本体部分
			echo $this->element($elementName,
					array(
						'question' => $question,
						'questionId' => $registrationQuestionId));

			//各質問ごと集計表示の共通フッター
			echo $this->element('Registrations.AnswerSummaries/aggregate_common_footer',
				array('question' => $question));
			?>
		</div>
	<?php endforeach; ?>

	<div class="text-center">
		<?php echo $this->BackTo->pageLinkButton(__d('registrations', 'Back to Top'), array('icon' => 'menu-up', 'iconSize' => 'lg')); ?>
	</div>

</article>
</div>
