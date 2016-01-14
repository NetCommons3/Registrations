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
	<?php echo $this->BlockTabs->main(BlockTabsComponent::MAIN_TAB_BLOCK_INDEX); ?>

	<div class="tab-content">
		<div class="pull-right">
			<?php echo $this->element('Registrations.Registrations/add_button'); ?>
		</div>

		<div id="nc-registration-setting-<?php echo Current::read('Frame.id'); ?>">
		<?php echo $this->NetCommonsForm->create('', array(
				'url' => NetCommonsUrl::actionUrl(array('plugin' => 'frames', 'controller' => 'frames', 'action' => 'edit'))
			)); ?>

			<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>

			<table class="table table-hover">
				<thead>
				<tr>
					<th>
						<?php echo $this->Paginator->sort('Registration.status', __d('registrations', 'Status')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('Registration.title', __d('registrations', 'Title')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('Registration.modified', __d('net_commons', 'Updated date')); ?>
					</th>
					<th>
						<?php echo __d('registrations', 'Answer CSV'); ?>
					</th>
					<!--<th>-->
					<!--	--><?php //echo __d('registrations', 'Templates'); ?>
					<!--</th>-->
				</tr>
				</thead>
				<tbody>
					<?php foreach ((array)$registrations as $registration) : ?>
					<tr>
						<td>
							<?php echo $this->RegistrationStatusLabel->statusLabelManagementWidget($registration);?>
						</td>
						<td>
							<?php echo $this->NetCommonsHtml->link(h($registration['Registration']['title']),
									NetCommonsUrl::actionUrl(array(
										'plugin' => 'registrations',
										'controller' => 'registration_edit',
										'action' => 'edit_question',
										Current::read('Block.id'),
										$registration['Registration']['key'],
										'frame_id' => Current::read('Frame.id')))); ?>
						</td>
						<td>
							<?php echo $this->Date->dateFormat($registration['Registration']['modified']); ?>
						</td>
						<td>
							<?php if ($registration['Registration']['all_answer_count'] > 0): ?>
							<a  authorization-keys-popup-link frame-id="<?php echo Current::read('Frame.id'); ?>"
								class="btn btn-success"
								url="<?php echo NetCommonsUrl::actionUrl(array(
										'plugin' => 'registrations',
										'controller' => 'registration_blocks',
										'action' => 'download',
										Current::read('Block.id'),
										$registration['Registration']['key'],
										'frame_id' => Current::read('Frame.id'))); ?>">
								<span class="glyphicon glyphicon-download" ></span>
							</a>
							<?php endif; ?>
						</td>
						<!--<td>-->
						<!--	--><?php //if ($registration['Registration']['status'] == WorkflowComponent::STATUS_PUBLISHED): ?>
						<!--	<a class="btn btn-warning"-->
						<!--	   href="--><?php //echo NetCommonsUrl::actionUrl(array(
						//				'plugin' => 'registrations',
						//				'controller' => 'registration_blocks',
						//				'action' => 'export',
						//				Current::read('Block.id'),
						//				$registration['Registration']['key'],
						//				'frame_id' => Current::read('Frame.id'))); ?><!--">-->
						<!--		<span class="glyphicon glyphicon-export" ></span>-->
						<!--	</a>-->
						<!--	--><?php //endif; ?>
						<!--</td>-->
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php echo $this->NetCommonsForm->end(); ?>
			<?php echo $this->element('NetCommons.paginator'); ?>
		</div>
	</div>
</article>