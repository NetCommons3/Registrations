<?php
/**
 * RegistrationFrameSetting Model
 *
 * @property Frame $Frame
 * @property RegistrationFrameDisplayRegistration $RegistrationFrameDisplayRegistration
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');

/**
 * Summary for RegistrationFrameSetting Model
 *
 * @property RegistrationFrameDisplayRegistration RegistrationFrameDisplayRegistration
 */
class RegistrationFrameSetting extends RegistrationsAppModel {

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
		'Frame' => array(
			'className' => 'Frames.Frame',
			'foreignKey' => 'frame_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
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
			'frame_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					//'message' => 'Your custom message here',
					'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'display_type' => array(
				'inList' => array(
					'rule' => array('inList', array(RegistrationsComponent::DISPLAY_TYPE_SINGLE, RegistrationsComponent::DISPLAY_TYPE_LIST)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			//'display_num_per_page' => array(
			//	'inList' => array(
			//		'rule' => array('inList', array_keys(RegistrationsComponent::getDisplayNumberOptions())),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//),
			//'sort_type' => array(
			//	'inList' => array(
			//		'rule' => array('inList', array_keys(RegistrationsComponent::getSortOrders())),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//),
		));

		parent::beforeValidate($options);

		return true;
	}

/**
 * getRegistrationFrameSettingConditions 指定されたframe_keyの設定要件をSQL検索用の配列で取り出す
 *
 * @param string $frameKey frame key
 * @return array ... displayNum sortField sortDirection
 */
	public function getRegistrationFrameSettingConditions($frameKey) {
		list(, $limit, $sort, $dir) = $this->getRegistrationFrameSetting($frameKey);
		return array(
			'offset' => 0,
			'limit' => $limit,
			'order' => 'Registration.' . $sort . ' ' . $dir);
	}
/**
 * getFrameSetting 指定されたframe_keyの設定要件を取り出す
 *
 * @param string $frameKey frame key
 * @return array ... displayNum sortField sortDirection
 */
	public function getRegistrationFrameSetting($frameKey) {
		$frameSetting = $this->find('first', array(
			'conditions' => array(
				'frame_key' => $frameKey
			),
			'recursive' => -1
		));
		// 指定されたフレーム設定が存在しない場合
		if (! $frameSetting) {
			// とりあえずデフォルトの表示設定を返す
			// しかし、表示対象登録フォームが設定されていないわけなので、空っぽの一覧表示となる
			$frameSetting = $this->getDefaultFrameSetting();
		}

		$setting = $frameSetting['RegistrationFrameSetting'];
		$displayType = $setting['display_type'];
		$displayNum = $setting['display_num_per_page'];
		if ($setting['sort_type'] == RegistrationsComponent::REGISTRATION_SORT_MODIFIED) {
			$sort = 'modified';
			$dir = 'DESC';
		} elseif ($setting['sort_type'] == RegistrationsComponent::REGISTRATION_SORT_CREATED) {
			$sort = 'created';
			$dir = 'ASC';
		} elseif ($setting['sort_type'] == RegistrationsComponent::REGISTRATION_SORT_TITLE) {
			$sort = 'title';
			$dir = 'ASC';
		} elseif ($setting['sort_type'] == RegistrationsComponent::REGISTRATION_SORT_END) {
			$sort = 'publish_end';
			$dir = 'ASC';
		}
		return array($displayType, $displayNum, $sort, $dir);
	}

/**
 * getDefaultFrameSetting
 * return default frame setting
 *
 * @return array
 */
	public function getDefaultFrameSetting() {
		$frame = array(
			'RegistrationFrameSetting' => array(
				//'id' => '',
				'display_type' => RegistrationsComponent::DISPLAY_TYPE_SINGLE,
				'display_num_per_page' => RegistrationsComponent::REGISTRATION_DEFAULT_DISPLAY_NUM_PER_PAGE,
				'sort_type' => RegistrationsComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
				'frame_key' => Current::read('Frame.key'),
			)
		);
		return $frame;
	}

/**
 * saveFrameSettings
 *
 * @param array $data save data
 * @return bool
 * @throws InternalErrorException
 */
	public function saveFrameSettings($data) {
		$this->loadModels([
			'RegistrationFrameDisplayRegistration' => 'Registrations.RegistrationFrameDisplayRegistration',
		]);

		//トランザクションBegin
		$this->begin();
		try {
			// フレーム設定のバリデート
			$this->create();
			$this->set($data);
			if (! $this->validates()) {
				return false;
			}

			// フレームに表示する登録フォーム一覧設定のバリデート
			// 一覧表示タイプと単独表示タイプ
			if (isset($data['RegistrationFrameDisplayRegistrations'])) {
				$ret = $this->RegistrationFrameDisplayRegistration->validateFrameDisplayRegistration($data);
				if ($ret === false) {
					return false;
				}
			}
			// フレーム設定の登録
			if (! $this->save($data, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			// フレームに表示する登録フォーム一覧設定の登録
			// 一覧表示タイプと単独表示タイプ
			if (isset($data['RegistrationFrameDisplayRegistrations'])) {
				$ret = $this->RegistrationFrameDisplayRegistration->saveFrameDisplayRegistration($data);
				if ($ret === false) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
			//トランザクションCommit
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}
}
