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
			array(MailSettingFixedPhrase::DEFAULT_TYPE, MailSettingFixedPhrase::ANSWER_TYPE),
			$model->plugin
		);
		if (! Hash::check($mailSetting, 'MailSetting.id')) {
			// まだメール設定がないときは、登録フォームで登録通知メールONならメール設定も送信する設定にする。
			//$mailSetting = $model->MailSetting->createMailSetting($model->alias);
			$mailSetting['MailSetting']['is_mail_send'] =
				$saveRegistration['Registration']['is_answer_mail_send'];
		}
		// 登録通知メール設定を変更
		$pluginLowercase = strtolower(Inflector::singularize($model->plugin));
		$mailSetting['MailSetting']['plugin_key'] = strtolower($model->plugin);
		$mailSetting['MailSetting']['block_key'] = Current::read('Block.key');
		$mailSetting['MailSetting']['reply_to']
			= $saveRegistration[$model->alias]['reply_to'];
		$mailSetting['MailSettingFixedPhrase']['answer']['mail_fixed_phrase_subject']
			= $saveRegistration[$model->alias][$pluginLowercase . '_mail_subject'];
		$mailSetting['MailSettingFixedPhrase']['answer']['mail_fixed_phrase_body']
			= $saveRegistration[$model->alias][$pluginLowercase . '_mail_body'];
		$mailSetting['MailSettingFixedPhrase']['answer']['plugin_key'] = strtolower($model->plugin);
		$mailSetting['MailSettingFixedPhrase']['answer']['block_key'] = Current::read('Block.key');
		$mailSetting['MailSettingFixedPhrase']['contents']['plugin_key'] = strtolower($model->plugin);
		$mailSetting['MailSettingFixedPhrase']['contents']['block_key'] = Current::read('Block.key');

		// 登録通知メール設定を保存
		if ($model->MailSetting->save($mailSetting)) {
			$mailSetting = Hash::insert(
				$mailSetting,
				'MailSettingFixedPhrase.answer.mail_setting_id',
				$model->MailSetting->id
			);
			$mailSetting = Hash::insert(
				$mailSetting,
				'MailSettingFixedPhrase.contents.mail_setting_id',
				$model->MailSetting->id
			);
			$model->MailSettingFixedPhrase = ClassRegistry::init(
				'Mails.MailSettingFixedPhrase'
			);
			$answerPhrase = $mailSetting['MailSettingFixedPhrase']['answer'];
			//$answerPhrase = array(
			//	'MailSettingFixedPhrase' => $answerPhrase
			//);
			//if (!$model->MailSettingFixedPhrase->save($answerPhrase, ['callbacks' => false])) {
			if (!$model->MailSettingFixedPhrase->save($answerPhrase)) {
				throw new InternalErrorException(
					__d('net_commons', 'Internal Server Error')
				);
			}
			$model->MailSettingFixedPhrase->create();
			$contentsPhrase = $mailSetting['MailSettingFixedPhrase']['contents'];
			//$contentsPhrase = array(
			//	'MailSettingFixedPhrase' => $contentsPhrase
			//);
			//if (!$model->MailSettingFixedPhrase->save($contentsPhrase, ['callbacks' => false])) {
			if (!$model->MailSettingFixedPhrase->save($contentsPhrase)) {
				throw new InternalErrorException(
					__d('net_commons', 'Internal Server Error')
				);
			}
		}
	}

}