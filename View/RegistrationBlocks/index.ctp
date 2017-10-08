<?php
/**
 * registration content list view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
echo $this->NetCommonsHtml->script(array(
	'/authorization_keys/js/authorization_keys.js',
));
?>
<article class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>

	<?php echo $this->BlockIndex->description(); ?>

	<div class="tab-content">
		<?php echo $this->BlockIndex->create(); ?>

		<?php echo $this->BlockIndex->addLink('',
		array(
			'controller' => 'registration_add',
			'action' => 'add',
			'frame_id' => Current::read('Frame.id'),
			'block_id' => Current::read('Block.id'),
			'q_mode' => 'setting'
		)); ?>

		<div id="nc-registration-setting-<?php echo Current::read('Frame.id'); ?>">
			<?php echo $this->BlockIndex->startTable(); ?>
				<thead>
				<tr>
					<?php echo $this->BlockIndex->tableHeader(
						'Frame.block_id'
					); ?>

					<?php echo $this->BlockIndex->tableHeader(
						'Registration.status', __d('registrations', 'Status'),
						array('sort' => true, 'type' => false)
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'Registration.title', __d('registrations', 'Title'),
						array('sort' => true, 'editUrl' => true)
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'Registration.modified', __d('net_commons', 'Updated date'),
						array('sort' => true, 'type' => 'datetime')
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'', __d('registrations', 'Answer Number'),
						array('type' => 'center')
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'', __d('registrations', 'Answer CSV'),
						array('type' => 'center')
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'', __d('registrations', 'Answer List'),
						array('type' => 'center')
					); ?>
				</tr>
				</thead>
				<tbody>
					<?php foreach ((array)$registrations as $registration) : ?>
					<?php echo $this->BlockIndex->startTableRow($registration['Block']['id']); ?>
						<?php echo $this->BlockIndex->tableData(
							'Frame.block_id', $registration['Block']['id']
						); ?>
						<?php echo $this->BlockIndex->tableData(
						'',
						$this->RegistrationStatusLabel->statusLabelManagementWidget($registration),
						array('escape' => false)
						); ?>
						<?php echo $this->BlockIndex->tableData(
						'',
						$this->TitleIcon->titleIcon($registration['Registration']['title_icon']) . h($registration['Registration']['title']),
						array(
							'escape' => false,
							'editUrl' => array(
								'plugin' => 'registrations',
								'controller' => 'registration_edit',
								'action' => 'edit_question',
								'block_id' => $registration['Registration']['block_id'],
								//Current::read('Block.id'),
								$registration['Registration']['key'],
								'frame_id' => Current::read('Frame.id'),
								'q_mode' => 'setting'
							)
						)); ?>
						<?php echo $this->BlockIndex->tableData(
						'',
						$registration['Registration']['modified'],
						array('type' => 'datetime')
						); ?>
						<?php echo $this->BlockIndex->tableData(
							'',
							$registration['Registration']['all_answer_count'],
							array('type' => 'number')
						); ?>
						<?php if ($registration['Registration']['all_answer_count'] > 0): ?>
							<?php echo $this->BlockIndex->tableData(
							'',
							$this->AuthKeyPopupButton->popupButton(
								array(
									'url' => NetCommonsUrl::actionUrl(array(
									'plugin' => 'registrations',
									'controller' => 'registration_blocks',
									'action' => 'download',
									'block_id' => $registration['Registration']['block_id'],
									$registration['Registration']['key'],
									'frame_id' => Current::read('Frame.id'))),
									'popup-title' => __d('authorization_keys', 'Compression password'),
									'popup-label' => __d('authorization_keys', 'Compression password'),
									'popup-placeholder' => __d('authorization_keys', 'please input compression password'),
								)
							),
							array('escape' => false, 'type' => 'center')
							); ?>
							<?php echo $this->BlockIndex->tableData(
								'',
								$this->NetCommonsHtml->link(__d('registrations', 'Answer List'),
									[
									'action' => 'answer_list',
									$registration['Registration']['key']
									],
									[
										'class' => 'btn btn-default'
									]),
								array('escape' => false, 'type' => 'center')
							); ?>
						<?php else: ?>
							<td></td>
							<td></td>
						<?php endif; ?>
					<?php echo $this->BlockIndex->endTableRow(); ?>
					<?php endforeach; ?>
				</tbody>
			<?php echo $this->BlockIndex->endTable(); ?>
			<?php echo $this->BlockIndex->end(); ?>

			<?php echo $this->element('NetCommons.paginator'); ?>
		</div>
	</div>
</article>