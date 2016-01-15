<?php
/**
 * RegistrationFrameDisplayRegistration Model
 *
 * @property RegistrationFrameSetting $RegistrationFrameSetting
 * @property Registration $Registration
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');

/**
 * Summary for RegistrationFrameDisplayRegistration Model
 */
class RegistrationFrameDisplayRegistration extends RegistrationsAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

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
		),
		'Registration' => array(
			'className' => 'Registrations.Registration',
			'foreignKey' => 'registration_key',
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
			'registration_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		));
		parent::beforeValidate($options);

		return true;
	}

/**
 * validateFrameDisplayRegistration
 *
 * @param mix $data PostData
 * @return bool
 */
	public function validateFrameDisplayRegistration($data) {
		if ($data['RegistrationFrameSetting']['display_type'] == RegistrationsComponent::DISPLAY_TYPE_SINGLE) {
			$saveData = Hash::extract($data, 'Single.RegistrationFrameDisplayRegistrations');
			$this->set($saveData);
			$ret = $this->validates();
		} else {
			$saveData = $data['RegistrationFrameDisplayRegistrations'];
			$ret = $this->saveAll($saveData, array('validate' => 'only'));
		}
		return $ret;
	}
/**
 * saveFrameDisplayRegistration
 * this function is called when save registration
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveFrameDisplayRegistration($data) {
		//トランザクションは親元のRegistrationFrameSettingでやっているので不要
		//if ($data['RegistrationFrameSetting']['display_type'] == RegistrationsComponent::DISPLAY_TYPE_SINGLE) {
		//	// このフレームに設定されている全てのレコードを消す
		//	// POSTされた登録フォームのレコードのみ作成する
		//	$ret = $this->saveDisplayRegistrationForSingle($data);
		//} else {
			// hiddenでPOSTされたレコードについて全て処理する
			// POSTのis_displayが０，１によってdeleteかinsertで処理する
			$ret = $this->saveDisplayRegistrationForList($data);
		//}
		return $ret;
	}

/**
 * saveDisplayRegistrationForList
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveDisplayRegistrationForList($data) {
		$frameKey = Current::read('Frame.key');

		foreach ($data['RegistrationFrameDisplayRegistrations'] as $index => $value) {
			$registrationKey = $value['registration_key'];
			$isDisplay = $data['List']['RegistrationFrameDisplayRegistrations'][$index]['is_display'];
			$saveQs = array(
				'frame_key' => $frameKey,
				'registration_key' => $registrationKey
			);
			if ($isDisplay != 0) {
				if (!$this->saveDisplayRegistration($saveQs)) {
					return false;
				}
			} else {
				if (!$this->deleteAll($saveQs, false)) {
					return false;
				}
			}
		}
		if (!$this->updateFrameDefaultAction("''")) {
			return false;
		}
		return true;
	}

/**
 * saveDisplayRegistrationForSingle
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveDisplayRegistrationForSingle($data) {
		$frameKey = Current::read('Frame.key');
		$deleteQs = array(
			'frame_key' => $frameKey,
		);
		$this->deleteAll($deleteQs, false);

		$saveData = Hash::extract($data, 'Single.RegistrationFrameDisplayRegistrations');
		$saveData['frame_key'] = $frameKey;
		if (!$this->saveDisplayRegistration($saveData)) {
			return false;
		}
		$action = "'" . 'registration_answers/view/' . Current::read('Block.id') . '/' . $saveData['registration_key'] . "'";
		if (!$this->updateFrameDefaultAction($action)) {
			return false;
		}
		return true;
	}

/**
 * saveDisplayRegistration
 * saveRegistrationFrameDisplayRegistration
 *
 * @param array $data save data
 * @return bool
 */
	public function saveDisplayRegistration($data) {
		// 該当データを検索して
		$displayRegistration = $this->find('first', array(
			'conditions' => $data
		));
		if (! empty($displayRegistration)) {
			// あるならもう作らない
			return true;
		}
		$this->create();
		if (!$this->save($data)) {
			return false;
		}
		// フレームのデフォルトにする

		$action = "'" . 'registration_answers/view/' . Current::read('Block.id') . '/' . $data['registration_key'] . "'";
		if (!$this->updateFrameDefaultAction($action)) {
			return false;
		}
		return true;
	}
/**
 * updateFrameDefaultAction
 * update Frame default_action
 *
 * @param string $action default_action
 * @return bool
 */
	public function updateFrameDefaultAction($action) {
		// frameのdefault_actionを変更しておく
		$this->loadModels([
			'Frame' => 'Frames.Frame',
		]);
		$conditions = array(
			'Frame.key' => Current::read('Frame.key')
		);
		$frameData = array(
			'default_action' => $action
		);
		if (! $this->Frame->updateAll($frameData, $conditions)) {
			return false;
		}
		return true;
	}
}
