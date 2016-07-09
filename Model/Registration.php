<?php
/**
 * Registration Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');
App::uses('NetCommonsUrl', 'NetCommons.Utility');

/**
 * Summary for Registration Model
 */
class Registration extends RegistrationsAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Workflow.Workflow',
		'Workflow.WorkflowComment',
		'AuthorizationKeys.AuthorizationKey',
		'Registrations.RegistrationValidate',
		// 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'Registration.title',
			),
			'publishStartField' => 'answer_start_period',
		),
		'Mails.MailQueueDelete',
		//新着情報
		'Topics.Topics' => array(
			'fields' => array(
				//※登録フォームの場合、'title'は$this->dataの値をセットしないので、
				//　ここではセットせずに、save直前で新着タイトルをセットする
				'publish_start' => 'answer_start_period',
				'answer_period_start' => 'answer_start_period',
				'answer_period_end' => 'answer_end_period',
				'path' => '/:plugin_key/registration_answers/view/:block_id/:content_key',
			),
			'search_contents' => array(
				'title', 'sub_title'
			),
		),
		'Wysiwyg.Wysiwyg' => array(
			'fields' => array('total_comment', 'thanks_content')
		),
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'RegistrationPage' => array(
			'className' => 'Registrations.RegistrationPage',
			'foreignKey' => 'registration_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('page_sequence ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Frame' => 'Frames.Frame',
			'RegistrationPage' => 'Registrations.RegistrationPage',
			'RegistrationSetting' =>
				'Registrations.RegistrationSetting',
			'RegistrationFrameDisplayRegistration' =>
				'Registrations.RegistrationFrameDisplayRegistration',
			'RegistrationAnswerSummary' =>
				'Registrations.RegistrationAnswerSummary',
		]);
	}

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		// ウィザード画面中はstatusチェックをしないでほしいので
		// ここに来る前にWorkflowBehaviorでつけられたstatus-validateを削除しておく
		if (Hash::check($options, 'validate') == RegistrationsComponent::REGISTRATION_VALIDATE_TYPE) {
			$this->validate = Hash::remove($this->validate, 'status');
		}
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					// Limit validation to 'create' or 'update' operations 新規の時はブロックIDがなかったりするから
					'on' => 'update',
				)
			),
			'title' => array(
					'rule' => 'notBlank',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('registrations', 'Title')),
					'required' => true,
					'allowEmpty' => false,
					'required' => true,
			),
			'answer_timing' => array(
				'publicTypeCheck' => array(
					'rule' => array(
						'inList', array(
							RegistrationsComponent::USES_USE, RegistrationsComponent::USES_NOT_USE
					)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array(
						'requireOtherFields',
						RegistrationsComponent::USES_USE,
						array('Registration.answer_start_period', 'Registration.answer_end_period'),
						'OR'
					),
					'message' => __d('registrations', 'if you set the period, please set time.')
				)
			),
			'answer_start_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('registrations', 'Invalid datetime format.')
				)
			),
			'answer_end_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('registrations', 'Invalid datetime format.')
				),
				'checkDateComp' => array(
					'rule' => array('checkDateComp', '>=', 'answer_start_period'),
					'message' => __d('registrations', 'start period must be smaller than end period')
				)
			),
			//'is_total_show' => array(
			//	'boolean' => array(
			//		'rule' => array('boolean'),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//),
			//'total_show_timing' => array(
			//	'inList' => array(
			//		'rule' => array(
			//			'inList',
			//			array(RegistrationsComponent::USES_USE, RegistrationsComponent::USES_NOT_USE)
			//		),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//	'requireOtherFields' => array(
			//		'rule' => array(
			//			'requireOtherFields',
			//			RegistrationsComponent::USES_USE,
			//			array('Registration.total_show_start_period'),
			//			'AND'
			//		),
			//		'message' => __d('registrations', 'if you set the period, please set time.')
			//	)
			//),
			//'total_show_start_period' => array(
			//	'checkDateTime' => array(
			//		'rule' => 'checkDateTime',
			//		'message' => __d('registrations', 'Invalid datetime format.')
			//	)
			//),
			//'is_no_member_allow' => array(
			//	'boolean' => array(
			//		'rule' => array('boolean'),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//),
			//'is_anonymity' => array(
			//	'boolean' => array(
			//		'rule' => array('boolean'),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//),
			'is_key_pass_use' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFieldsKey' => array(
					'rule' => array(
						'requireOtherFields',
						RegistrationsComponent::USES_USE,
						array('AuthorizationKey.authorization_key'),
						'AND'
					),
					'message' =>
						__d('registrations',
							'if you set the use key phrase period, please set key phrase text.')
				),
				'authentication' => array(
					'rule' => array(
						'requireOtherFields',
						RegistrationsComponent::USES_USE,
						array('Registration.is_image_authentication'),
						'XOR'
					),
					'message' =>
						__d('registrations',
							'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			//'is_repeat_allow' => array(
			//	'boolean' => array(
			//		'rule' => array('boolean'),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//),
			'is_image_authentication' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'authentication' => array(
					'rule' => array(
						'requireOtherFields',
						RegistrationsComponent::USES_USE,
						array('Registration.is_key_pass_use'),
						'XOR'
					),
					'message' =>
						__d('registrations',
							'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			'is_answer_mail_send' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_regist_user_send' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'reply_to' => array(
				'email' => array(
					'rule' => array('email', false, null),
					'message' => sprintf(__d('mails', '%s, please enter by e-mail format'),
						__d('mails', 'E-mail address to receive a reply')),
					'allowEmpty' => true,
				),
			),
		));

		parent::beforeValidate($options);
		// 最低でも１ページは存在しないとエラー
		if (! isset($this->data['RegistrationPage'][0])) {
			$this->validationErrors['pickup_error'] =
				__d('registrations', 'please set at least one page.');
		} else {
			// ページデータが存在する場合
			// 配下のページについてバリデート
			$validationErrors = array();
			$maxPageIndex = count($this->data['RegistrationPage']);
			$options['maxPageIndex'] = $maxPageIndex;
			foreach ($this->data['RegistrationPage'] as $pageIndex => $page) {
				// それぞれのページのフィールド確認
				$this->RegistrationPage->create();
				$this->RegistrationPage->set($page);
				// ページシーケンス番号の正当性を確認するため、現在の配列インデックスを渡す
				$options['pageIndex'] = $pageIndex;
				if (! $this->RegistrationPage->validates($options)) {
					$validationErrors['RegistrationPage'][$pageIndex] =
						$this->RegistrationPage->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}

		// 引き続き登録フォーム本体のバリデートを実施してもらうためtrueを返す
		return true;
	}

/**
 * AfterFind Callback function
 *
 * @param array $results found data records
 * @param bool $primary indicates whether or not the current model was the model that the query originated on or whether or not this model was queried as an association
 * @return mixed
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function afterFind($results, $primary = false) {
		if ($this->recursive == -1) {
			return $results;
		}

		foreach ($results as &$val) {
			// この場合はcount
			if (! isset($val['Registration']['id'])) {
				continue;
			}
			// この場合はdelete
			if (! isset($val['Registration']['key'])) {
				continue;
			}

			$val['Registration']['period_range_stat'] = $this->getPeriodStatus(
				isset($val['Registration']['answer_timing']) ? $val['Registration']['answer_timing'] : false,
				$val['Registration']['answer_start_period'],
				$val['Registration']['answer_end_period']);

			//
			// ページ配下の項目データも取り出す
			// かつ、ページ数、項目数もカウントする
			$val['Registration']['page_count'] = 0;
			$val['Registration']['question_count'] = 0;
			$this->RegistrationPage->setPageToRegistration($val);

			$val['Registration']['all_answer_count'] =
				$this->RegistrationAnswerSummary->find('count', array(
					'conditions' => array(
						'registration_key' => $val['Registration']['key'],
						'answer_status' => RegistrationsComponent::ACTION_ACT,
						'test_status' => RegistrationsComponent::TEST_ANSWER_STATUS_PEFORM
					),
					'recursive' => -1
			));
		}
		return $results;
	}

/**
 * After frame save hook
 *
 * このルームにすでに登録フォームブロックが存在した場合で、かつ、現在フレームにまだブロックが結びついてない場合、
 * すでに存在するブロックと現在フレームを結びつける
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function afterFrameSave($data) {
		$frame['Frame'] = $data['Frame'];

		$this->begin();

		try {
			$this->RegistrationSetting->saveBlock($frame);
			// 設定情報も
			$this->RegistrationSetting->saveSetting();
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		return $data;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getBaseCondition($addConditions = array()) {
		$conditions = $this->getWorkflowConditions(array(
			'block_id' => Current::read('Block.id'),
		));
		$conditions = array_merge($conditions, $addConditions);
		return $conditions;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getCondition($addConditions = array()) {
		// 基本条件（ワークフロー条件）
		$conditions = $this->getBaseCondition($addConditions);
		// 現在フレームに表示設定されている登録フォームか
		$keys = $this->RegistrationFrameDisplayRegistration->find(
			'list',
			array(
				'conditions' => array(
					'RegistrationFrameDisplayRegistration.frame_key' => Current::read('Frame.key')),
				'fields' => array('RegistrationFrameDisplayRegistration.registration_key'),
				'recursive' => -1
			)
		);
		$conditions['Registration.key'] = $keys;

		$periodCondition = $this->_getPeriodConditions();
		$conditions[] = $periodCondition;

		if (! Current::read('User.id')) {
			$conditions['is_no_member_allow'] = RegistrationsComponent::PERMISSION_PERMIT;
		}
		$conditions = Hash::merge($conditions, $addConditions);
		return $conditions;
	}

/**
 * 時限公開のconditionsを返す
 *
 * @return array
 */
	protected function _getPeriodConditions() {
		if (Current::permission('content_editable')) {
			return array();
		}
		$netCommonsTime = new NetCommonsTime();
		$nowTime = $netCommonsTime->getNowDatetime();

		$limitedConditions[] = array('OR' => array(
					'Registration.answer_start_period <=' => $nowTime,
					'Registration.answer_start_period' => null,
		));
		$limitedConditions[] = array('OR' => array(
				'Registration.answer_end_period >=' => $nowTime,
				'Registration.answer_end_period' => null,
		));

		$timingConditions = array(
			'OR' => array(
				'Registration.answer_timing' => RegistrationsComponent::USES_NOT_USE,
				'AND' => array(
					'Registration.answer_timing' => RegistrationsComponent::USES_USE,
					$limitedConditions,
				)
		));

		// 集計結果の表示は登録フォーム登録が始まっていることが前提
		$totalLimitPreCond = array(
			'OR' => array(
				'Registration.answer_timing' => RegistrationsComponent::USES_NOT_USE,
				'AND' => array(
					'Registration.answer_timing' => RegistrationsComponent::USES_USE,
					'OR' => array(
						'Registration.answer_start_period <=' => $nowTime,
						'Registration.answer_start_period' => null,
					)
				)
			)
		);
		$totalLimitCond[] = array('OR' => array(
			'Registration.total_show_start_period <=' => $nowTime,
			'Registration.total_show_start_period' => null,
		));

		$totalTimingCond = array(
			'Registration.is_total_show' => RegistrationsComponent::USES_USE,
			$totalLimitPreCond,
			'OR' => array(
				'Registration.total_show_timing' => RegistrationsComponent::USES_NOT_USE,
				$totalLimitCond,
		));
		$timingConditions['OR'][] = $totalTimingCond;

		if (Current::permission('content_creatable')) {
			$timingConditions['OR']['Registration.created_user'] = Current::read('User.id');
		}

		return $timingConditions;
	}

/**
 * saveRegistration
 * save Registration data
 *
 * @param array &$registration registration
 * @throws InternalErrorException
 * @return bool
 */
	public function saveRegistration(&$registration) {
		// 設定画面を表示する前にこのルームの登録フォームブロックがあるか確認
		// 万が一、まだ存在しない場合には作成しておく
		// afterFrameSaveが呼ばれず、また最初に設定画面が開かれもしなかったような状況の想定
		$frame['Frame'] = Current::read('Frame');
		$this->afterFrameSave($frame);

		//トランザクションBegin
		$this->begin();

		try {
			$registration['Registration']['block_id'] = Current::read('Frame.block_id');
			// is_no_member_allowの値によってis_repeat_allowを決定する
			$registration['Registration']['is_repeat_allow'] = RegistrationsComponent::USES_NOT_USE;
			if (Hash::get(
					$registration,
					'Registration.is_no_member_allow') == RegistrationsComponent::USES_USE) {
				$registration['Registration']['is_repeat_allow'] = RegistrationsComponent::USES_USE;
			}
			$status = $registration['Registration']['status'];
			$this->create();
			// 登録フォームは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			// （ちなみにこのカット処理をbeforeSaveで共通でやってしまおうとしたが、
			//   beforeSaveでIDをカットしてもUPDATE動作になってしまっていたのでここに置くことにした)
			$registration = Hash::remove($registration, 'Registration.id');

			$this->set($registration);
			if (!$this->validates()) {
				return false;
			}

			//新着データセット
			$this->setTopicValue(
				'title', sprintf(__d('registrations', '%s started'), $registration['Registration']['title'])
			);
			if (! $registration['Registration']['answer_timing']) {
				$this->setTopicValue('publish_start', null);
				$this->setTopicValue('answer_period_start', null);
				$this->setTopicValue('answer_period_end', null);
			}

			$saveRegistration = $this->save($registration, false);
			if (! $saveRegistration) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$registrationId = $this->id;

			// ページ以降のデータを登録
			$registration = Hash::insert(
				$registration,
				'RegistrationPage.{n}.registration_id',
				$registrationId);

			if (! $this->RegistrationPage->saveRegistrationPage($registration['RegistrationPage'])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// フレーム内表示対象登録フォームに登録する
			if (! $this->RegistrationFrameDisplayRegistration->saveDisplayRegistration(array(
				'registration_key' => $saveRegistration['Registration']['key'],
				'frame_key' => Current::read('Frame.key')
			))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// これまでのテスト登録データを消す
			$this->RegistrationAnswerSummary->deleteTestAnswerSummary(
				$saveRegistration['Registration']['key'],
				$status);

			// TODO Registrationのステータスが公開なら登録通知メール設定を上書きする
			if ($status == WorkflowComponent::STATUS_PUBLISHED) {
				// 登録通知メール設定を取得
				$mailSetting = $this->MailSetting->getMailSettingPlugin(
					$saveRegistration['Registration']['language_id'],
					MailSettingFixedPhrase::ANSWER_TYPE,
					'registrations'
				);
				//if (!$mailSetting) {
				//	$mailSetting = $this->MailSetting->createMailSetting('registrations');
				//}
				// 登録通知メール設定を変更
				$mailSetting['MailSetting']['reply_to'] = $saveRegistration['Registration']['reply_to'];
				$mailSetting['MailSettingFixedPhrase']['mail_fixed_phrase_subject'] = $saveRegistration['Registration']['registration_mail_subject'];
				$mailSetting['MailSettingFixedPhrase']['mail_fixed_phrase_body'] = $saveRegistration['Registration']['registration_mail_body'];
				// 登録通知メール設定を保存
				if ($this->MailSetting->save($mailSetting)) {
					Hash::insert(
						$mailSetting,
						'MailSettingFixedPhrase.mail_setting_id',
						$this->MailSetting->id
					);
					$this->MailSettingFixedPhrase = ClassRegistry::init(
						'Mails.MailSettingFixedPhrase'
					);
					if (!$this->MailSettingFixedPhrase->save($mailSetting)) {
						throw new InternalErrorException(
							__d('net_commons', 'Internal Server Error')
						);
					}
				}
			}

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $saveRegistration;
	}

/**
 * deleteRegistration
 * Delete the registration data set of specified ID
 *
 * @param array $data post data
 * @throws InternalErrorException
 * @return bool
 */
	public function deleteRegistration($data) {
		$this->begin();
		try {
			// 登録フォーム項目データ削除
			$this->contentKey = $data['Registration']['key'];
			if (! $this->deleteAll(array(
					'Registration.key' => $data['Registration']['key']), true, true)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			// 登録フォーム表示設定削除
			if (! $this->RegistrationFrameDisplayRegistration->deleteAll(array(
				'registration_key' => $data['Registration']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// 登録フォーム登録削除
			if (! $this->RegistrationAnswerSummary->deleteAll(array(
				'registration_key' => $data['Registration']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}
/**
 * saveExportKey
 * update export key
 *
 * @param int $registrationId id of registration
 * @param string $exportKey exported key ( finger print)
 * @throws InternalErrorException
 * @return bool
 */
	public function saveExportKey($registrationId, $exportKey) {
		$this->begin();
		try {
			$this->id = $registrationId;
			$this->Behaviors->unload('Mails.MailQueue');
			$this->Behaviors->unload('Wysiwyg.Wysiwyg');
			if (! $this->saveField('export_key', $exportKey)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}
/**
 * hasPublished method
 *
 * @param array $registration registration data
 * @return int
 */
	public function hasPublished($registration) {
		if (isset($registration['Registration']['key'])) {
			$isPublished = $this->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'is_active' => true,
					'key' => $registration['Registration']['key']
				)
			));
		} else {
			$isPublished = 0;
		}
		return $isPublished;
	}

/**
 * clearRegistrationId 登録フォームデータからＩＤのみをクリアする
 *
 * @param array &$registration 登録フォームデータ
 * @param bool $isIdOnly 純粋にIDフィールドのみをクリアするのか
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function clearRegistrationId(&$registration, $isIdOnly = false) {
		foreach ($registration as $qKey => $q) {
			if (is_array($q)) {
				$this->clearRegistrationId($registration[$qKey], $isIdOnly);
			} else {
				$judge = false;
				if ($isIdOnly) {
					$judge = preg_match('/^id$/', $qKey);
				} else {
					$judge = preg_match('/^id$/', $qKey) ||
						preg_match('/^key$/', $qKey) ||
						preg_match('/^created(.*?)/', $qKey) ||
						preg_match('/^modified(.*?)/', $qKey);
				}
				if ($judge) {
					unset($registration[$qKey]);
				}
			}
		}
	}
}
