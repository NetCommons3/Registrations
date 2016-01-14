<?php
/**
 * registration comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$list = array(
	RegistrationsComponent::REGISTRATION_ANSWER_VIEW_ALL => __d('registrations', 'View All'),
	RegistrationsComponent::REGISTRATION_ANSWER_UNANSWERED => __d('registrations', 'Unanswered'),
	RegistrationsComponent::REGISTRATION_ANSWER_ANSWERED => __d('registrations', 'Answered'),
);
if (Current::permission('content_creatable')) {
	$list[RegistrationsComponent::REGISTRATION_ANSWER_TEST] = __d('registrations', 'Test');
}
$urlParams = Hash::merge(array(
	'controller' => 'registrations',
	'action' => 'index'),
	$this->params['named']);
if (isset($this->params['named']['answer_status']) && in_array($this->params['named']['answer_status'], $list)) {
	$currentStatus = $this->params['named']['answer_status'];
} else {
	$currentStatus = RegistrationsComponent::REGISTRATION_ANSWER_VIEW_ALL;
}

?>

<div class="form-group registration-list-select">

	<label><?php echo __d('registrations', 'Answer status'); ?></label>

	<span class="btn-group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<?php echo $list[$currentStatus]; ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<?php foreach ($list as $key => $status) : ?>
				<li<?php echo ($status === $currentStatus ? ' class="active"' : ''); ?>>
					<?php echo $this->NetCommonsHtml->link($status,
						Hash::merge($urlParams, array('answer_status' => $key))
					); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</span>

</div>