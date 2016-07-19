<?php
/**
 * registration registration edit title template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
	<h1 class="">
		<?php if (! Current::permission('block_editable')) : ?>

			<?php if (isset($this->data['Registration']['title_icon'])) {
				echo $this->TitleIcon->titleIcon($this->data['Registration']['title_icon']);
									}?>
			{{registration.registration.title}}
		<?php endif ?>
		<?php if ($this->action != 'edit'): ?>
			<small>
				<div class="help-block small">
					<?php echo __d('registrations', 'If you want to change the registration title, please edit in "Set registration" step.'); ?>
				</div>
			</small>
		<?php endif; ?>
	</h1>
