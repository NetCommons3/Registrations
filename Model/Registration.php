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
			'order' => array('page_sequence' => 'ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

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
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'on' => 'update', // Limit validation to 'create' or 'update' operations 新規の時はブロックIDがなかったりするから
				)
			),
			'title' => array(
					'rule' => 'notBlank',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('registrations', 'Title')),
					'required' => true,
					'allowEmpty' => false,
					'required' => true,
			),
			'public_type' => array(
				'publicTypeCheck' => array(
					'rule' => array('inList', array(WorkflowBehavior::PUBLIC_TYPE_PUBLIC, WorkflowBehavior::PUBLIC_TYPE_LIMITED)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', WorkflowBehavior::PUBLIC_TYPE_LIMITED, array('Registration.publish_start', 'Registration.publish_end'), 'OR'),
					'message' => __d('registrations', 'if you set the period, please set time.')
				)
			),
			'publish_start' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('registrations', 'Invalid datetime format.')
				)
			),
			'publish_end' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('registrations', 'Invalid datetime format.')
				),
				'checkDateComp' => array(
					'rule' => array('checkDateComp', '>=', 'publish_start'),
					'message' => __d('registrations', 'start period must be smaller than end period')
				)
			),
			'total_show_timing' => array(
				'inList' => array(
					'rule' => array('inList', array(RegistrationsComponent::USES_USE, RegistrationsComponent::USES_NOT_USE)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', RegistrationsComponent::USES_USE, array('Registration.total_show_start_period'), 'AND'),
					'message' => __d('registrations', 'if you set the period, please set time.')
				)
			),
			'total_show_start_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('registrations', 'Invalid datetime format.')
				)
			),
			'is_no_member_allow' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_anonymity' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_key_pass_use' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFieldsKey' => array(
					'rule' => array('requireOtherFields', RegistrationsComponent::USES_USE, array('AuthorizationKey.authorization_key'), 'AND'),
					'message' => __d('registrations', 'if you set the use key phrase period, please set key phrase text.')
				),
				'authentication' => array(
					'rule' => array('requireOtherFields', RegistrationsComponent::USES_USE, array('Registration.is_image_authentication'), 'XOR'),
					'message' => __d('registrations', 'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			'is_repeat_allow' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_image_authentication' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'authentication' => array(
					'rule' => array('requireOtherFields', RegistrationsComponent::USES_USE, array('Registration.is_key_pass_use'), 'XOR'),
					'message' => __d('registrations', 'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			'is_answer_mail_send' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));

		parent::beforeValidate($options);
		// 最低でも１ページは存在しないとエラー
		if (! isset($this->data['RegistrationPage'][0])) {
			$this->validationErrors['pickup_error'] = __d('registrations', 'please set at least one page.');
		} else {
			// ページデータが存在する場合
			// 配下のページについてバリデート
			$validationErrors = array();
			$this->RegistrationPage = ClassRegistry::init('Registrations.RegistrationPage', true);
			$maxPageIndex = count($this->data['RegistrationPage']);
			$options['maxPageIndex'] = $maxPageIndex;
			foreach ($this->data['RegistrationPage'] as $pageIndex => $page) {
				// それぞれのページのフィールド確認
				$this->RegistrationPage->create();
				$this->RegistrationPage->set($page);
				// ページシーケンス番号の正当性を確認するため、現在の配列インデックスを渡す
				$options['pageIndex'] = $pageIndex;
				if (! $this->RegistrationPage->validates($options)) {
					$validationErrors['RegistrationPage'][$pageIndex] = $this->RegistrationPage->validationErrors;
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
		$this->RegistrationPage = ClassRegistry::init('Registrations.RegistrationPage', true);
		$this->RegistrationAnswerSummary = ClassRegistry::init('Registrations.RegistrationAnswerSummary', true);

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
				isset($val['Registration']['public_type']) ? $val['Registration']['public_type'] : false,
				$val['Registration']['publish_start'],
				$val['Registration']['publish_end']);

			//
			// ページ配下の質問データも取り出す
			// かつ、ページ数、質問数もカウントする
			$val['Registration']['page_count'] = 0;
			$val['Registration']['question_count'] = 0;
			$this->RegistrationPage->setPageToRegistration($val);

			$val['Registration']['all_answer_count'] = $this->RegistrationAnswerSummary->find('count', array(
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
		// すでに結びついている場合は何もしないでよい
		if (!empty($data['Frame']['block_id'])) {
			return $data;
		}
		$frame = $data['Frame'];
		// ルームに存在するブロックを探す
		$block = $this->Block->find('first', array(
			'conditions' => array(
				'Block.room_id' => $frame['room_id'],
				'Block.plugin_key' => $frame['plugin_key'],
			)
		));
		// まだない場合
		if (empty($block)) {
			// 作成する
			$block = $this->Block->save(array(
				'room_id' => $frame['room_id'],
				'language_id' => $frame['language_id'],
				'plugin_key' => $frame['plugin_key'],
			));
			if (! $block) {
				return false;
			}
			Current::$current['Block'] = $block['Block'];
		}

		$this->loadModels([
			'Frame' => 'Frames.Frame',
			'RegistrationSetting' => 'Registrations.RegistrationSetting',
		]);
		$data['Frame']['block_id'] = $block['Block']['id'];
		if (! $this->Frame->save($data)) {
			return false;
		}
		Current::$current['Frame']['block_id'] = $block['Block']['id'];

		$blockSetting = $this->RegistrationSetting->create();
		$blockSetting['RegistrationSetting']['block_key'] = $block['Block']['key'];
		$this->RegistrationSetting->saveRegistrationSetting($blockSetting);
		return $data;
	}
/**
 * geRegistrationsList
 * get registrations by specified block id and specified user id limited number
 *
 * @param array $conditions find condition
 * @param array $options 検索オプション
 * @return array
 */
	public function getRegistrationsList($conditions, $options = array()) {
		//$limit = RegistrationsComponent::REGISTRATION_DEFAULT_DISPLAY_NUM_PER_PAGE}, $offset = 0, $sort = 'modified DESC') {
		// 絞込条件
		$baseConditions = $this->getBaseCondition();
		$conditions = Hash::merge($baseConditions, $conditions);

		// 取得オプション
		$this->RegistrationFrameSetting = ClassRegistry::init('Registrations.RegistrationFrameSetting', true);
		$defaultOptions = $this->RegistrationFrameSetting->getRegistrationFrameSettingConditions(Current::read('Frame.key'));
		$options = Hash::merge($defaultOptions, $options);
		$list = $this->find('all', array(
			'recursive' => 0,
			'conditions' => $conditions,
			$options
		));
		return $list;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getCondition($addConditions = array()) {
		// ベースとなる権限のほかに現在フレームに表示設定されている登録フォームか見ている
		$conditions = $this->getBaseCondition($addConditions);

		$frameDisplay = ClassRegistry::init('Registrations.RegistrationFrameDisplayRegistrations');
		$keys = $frameDisplay->find(
			'list',
			array(
				'conditions' => array('RegistrationFrameDisplayRegistrations.frame_key' => Current::read('Frame.key')),
				'fields' => array('RegistrationFrameDisplayRegistrations.registration_key'),
				'recursive' => -1
			)
		);
		$conditions['Registration.key'] = $keys;

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
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

		if (! Current::read('User.id')) {
			$conditions['is_no_member_allow'] = RegistrationsComponent::PERMISSION_PERMIT;
		}

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
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
		$this->loadModels([
			'RegistrationPage' => 'Registrations.RegistrationPage',
			'RegistrationFrameDisplayRegistration' => 'Registrations.RegistrationFrameDisplayRegistration',
			'RegistrationAnswerSummary' => 'Registrations.RegistrationAnswerSummary',
		]);

		//トランザクションBegin
		$this->begin();

		try {
			$status = $registration['Registration']['status'];
			$this->create();
			// 登録フォームは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			// （ちなみにこのカット処理をbeforeSaveで共通でやってしまおうとしたが、
			//   beforeSaveでIDをカットしてもUPDATE動作になってしまっていたのでここに置くことにした)
			$registration = Hash::remove($registration, 'Registration.id');

			$this->set($registration);

			$saveRegistration = $this->save($registration);
			if (! $saveRegistration) {
				$this->rollback();
				return false;
			}
			$registrationId = $this->id;

			// ページ以降のデータを登録
			$registration = Hash::insert($registration, 'RegistrationPage.{n}.registration_id', $registrationId);
			if (! $this->RegistrationPage->saveRegistrationPage($registration['RegistrationPage'])) {
				$this->rollback();
				return false;
			}
			// フレーム内表示対象登録フォームに登録する
			if (! $this->RegistrationFrameDisplayRegistration->saveDisplayRegistration(array(
				'registration_key' => $saveRegistration['Registration']['key'],
				'frame_key' => Current::read('Frame.key')
			))) {
				$this->rollback();
				return false;
			}
			// これまでのテスト回答データを消す
			$this->RegistrationAnswerSummary->deleteTestAnswerSummary($saveRegistration['Registration']['key'], $status);

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $registration;
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
		$this->loadModels([
			'RegistrationFrameDisplayRegistration' => 'Registrations.RegistrationFrameDisplayRegistration',
			'RegistrationAnswerSummary' => 'Registrations.RegistrationAnswerSummary',
		]);
		$this->begin();
		try {
			// 登録フォーム質問データ削除
			if (! $this->deleteAll(array(
					'Registration.key' => $data['Registration']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//コメントの削除
			$this->deleteCommentsByContentKey($this->data['Registration']['key']);

			// 登録フォーム表示設定削除
			if (! $this->RegistrationFrameDisplayRegistration->deleteAll(array(
				'registration_key' => $data['Registration']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// 登録フォーム回答削除
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
			$this->saveField('export_key', $exportKey);
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
 * @return void
 */
	public function clearRegistrationId(&$registration) {
		foreach ($registration as $qKey => $q) {
			if (is_array($q)) {
				$this->clearRegistrationId($registration[$qKey]);
			} elseif (preg_match('/^id$/', $qKey) ||
				preg_match('/^key$/', $qKey) ||
				preg_match('/^created(.*?)/', $qKey) ||
				preg_match('/^modified(.*?)/', $qKey)) {
				unset($registration[$qKey]);
			}
		}
	}
}
