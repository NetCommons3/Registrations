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

	<?php if (!empty($question['question_value'])): ?>
		<h2>
			<?php echo $question['question_value']; ?>
		</h2>
	<?php endif; ?>
	<p>
		<?php echo __d('registrations', 'The total number of answers: ') . h($question['answer_total_cnt']); ?>

		<?php
			//各質問ごと集計表示の共通ヘッダー
			$questionTypeStr = '';
			//質問タイプ(選択型用)
			switch ($question['question_type']) {
			case RegistrationsComponent::TYPE_SELECTION:
				$questionTypeStr = __d('registrations', 'Select one');
				break;
			case RegistrationsComponent::TYPE_MULTIPLE_SELECTION:
				$questionTypeStr = __d('registrations', 'Select more than one');
				break;
			case RegistrationsComponent::TYPE_MATRIX_SELECTION_LIST:
				$questionTypeStr = __d('registrations', 'Matrix (selection list)');
				break;
			case RegistrationsComponent::TYPE_MATRIX_MULTIPLE:
				$questionTypeStr = __d('registrations', 'Matrix (multiple)');
				break;
			case RegistrationsComponent::TYPE_SINGLE_SELECT_BOX:
				$questionTypeStr = __d('registrations', 'List selection');
				break;
			}
		?>
		<small>(<?php echo $questionTypeStr; ?>)</small>

	</p>

</div>
