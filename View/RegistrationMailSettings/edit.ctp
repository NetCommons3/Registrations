<?php
/**
 * メール設定 template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @author Mitsuru Mutaguchi <mutaguchi@opensource-workshop.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>

	<div class="tab-content">
		<?php echo $this->BlockTabs->block(BlockTabsHelper::BLOCK_TAB_MAIL_SETTING, true); ?>
		<?php /** @see MailFormHelper::editFrom() */ ?>
		<?php echo $this->MailForm->editFrom(
			array(
				array(
					'mailBodyPopoverMessage' => __d('registrations', 'MailSetting.mail_fixed_phrase_body.popover'),
				),
				array(
					'mailBodyPopoverMessage' => __d('mails', 'MailSetting.mail_fixed_phrase_body.popover.answer'),
					'panelHeading' => __d('registrations', 'Registration mail'),
					'permissionOnly' => true,
				),
			),
			NetCommonsUrl::backToIndexUrl('default_setting_action'),
			0 // 問合せ先メールアドレス 非表示
		); ?>
	</div>
</div>
