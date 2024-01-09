<?php
/**
 * registration answer authorization key guard view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
echo $this->NetCommonsHtml->script(array(
	'/authorization_keys/js/key_auth_init.js'
));
echo $this->NetCommonsHtml->css('/registrations/css/registration.css');
?>
<article ng-controller="AuthorizationKey" ng-init="initialize('<?php echo h($postUrl); ?>')">
	<?php echo $this->element('Registrations.Answers/answer_header'); ?>

	<?php echo $this->element('Registrations.Answers/answer_test_mode_header'); ?>

	<?php

	echo $this->NetCommonsForm->create('AuthorizationKeys' . CurrentLib::read('Frame.id'), array('type' => 'post', 'url' => $postUrl));
	echo $this->NetCommonsForm->hidden('Frame.id');
	echo $this->NetCommonsForm->hidden('Block.id');

	echo $this->element('AuthorizationKeys.authorization_key');
	?>

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
