<?php
/**
 * MailSetting Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * Answer Behavior
 *
 * @package  Registrations\Registrations\Model\Befavior
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 */
class MailSettingBehavior extends ModelBehavior {

/**
 * フォーム設定にあるメール文面等をメール設定へ反映する
 *
 * @param Model $model Model
 * @param array $saveRegistration Registraion データ
 * @return void
 * @throws InternalErrorException
 */
	public function updateMailSetting(Model $model, $saveRegistration) {
		// 登録通知メール設定を取得
		$mailSetting = $model->MailSetting->getMailSettingPlugin(
			$saveRegistration[$model->alias]['language_id'],
			MailSettingFixedPhrase::ANSWER_TYPE,
			$model->plugin
		);
		//if (!$mailSetting) {
		//	$mailSetting = $this->MailSetting->createMailSetting('registrations');
		//}
		// 登録通知メール設定を変更
		$pluginLowercase = strtolower(Inflector::singularize($model->plugin));
		$mailSetting['MailSetting']['plugin_key'] = 'registrations';
		$mailSetting['MailSetting']['reply_to']
			= $saveRegistration[$model->alias]['reply_to'];
		$mailSetting['MailSettingFixedPhrase']['mail_fixed_phrase_subject']
			= $saveRegistration[$model->alias][$pluginLowercase . '_mail_subject'];
		$mailSetting['MailSettingFixedPhrase']['mail_fixed_phrase_body']
			= $saveRegistration[$model->alias][$pluginLowercase . '_mail_body'];
		$mailSetting['MailSettingFixedPhrase']['plugin_key'] = $model->plugin;

		// 登録通知メール設定を保存
		if ($model->MailSetting->save($mailSetting)) {
			$mailSetting = Hash::insert(
				$mailSetting,
				'MailSettingFixedPhrase.mail_setting_id',
				$model->MailSetting->id
			);
			$model->MailSettingFixedPhrase = ClassRegistry::init(
				'Mails.MailSettingFixedPhrase'
			);
			if (!$model->MailSettingFixedPhrase->save($mailSetting)) {
				throw new InternalErrorException(
					__d('net_commons', 'Internal Server Error')
				);
			}
		}
	}

}