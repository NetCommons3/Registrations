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
 * Registration list for check
 *
 * @var array
 */
	public $chkRegistrationList = array();

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
			'Registration' => 'Registrations.Registration',
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
		// チェック用の登録フォームリストを確保しておく
		$conditions = $this->Registration->getBaseCondition();
		$registrations = $this->Registration->find('all', array(
			'conditions' => $conditions,
			'recursive' => -1
		));
		$this->chkRegistrationList = Hash::combine(
			$registrations, '{n}.Registration.id', '{n}.Registration.key');

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
				'inList' => array(
					'rule' => array('inList', $this->chkRegistrationList),
					'message' => __d('net_commons', 'Invalid request.'),
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
		$frameSetting = $data['RegistrationFrameSetting'];

		if ($frameSetting['display_type'] == RegistrationsComponent::DISPLAY_TYPE_SINGLE) {
			$saveData = Hash::extract($data, 'Single.RegistrationFrameDisplayRegistration');
			if (! $saveData) {
				return false;
			}
			$this->set($saveData);
			$ret = $this->validates();
		} else {
			$saveData = Hash::extract($data, 'List.RegistrationFrameDisplayRegistration');
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
 * @throws $ex
 */
	public function saveFrameDisplayRegistration($data) {
		if (! $this->validateFrameDisplayRegistration($data)) {
			return false;
		}
		$frameSetting = $data['RegistrationFrameSetting'];

		//トランザクションBegin
		$this->begin();
		try {
			//if ($frameSetting['display_type'] == RegistrationsComponent::DISPLAY_TYPE_SINGLE) {
			//	// このフレームに設定されている全てのレコードを消す
			//	// POSTされた登録フォームのレコードのみ作成する
				$ret = $this->saveDisplayRegistrationForSingle($data);
			//} else {
				// hiddenでPOSTされたレコードについて全て処理する
				// POSTのis_displayが０，１によってdeleteかinsertで処理する
				//$ret = $this->saveDisplayRegistrationForList($data);
			//}
			//トランザクションCommit
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $ret;
	}

/**
 * saveDisplayRegistrationForList
 *
 * @param mix $data PostData
 * @return bool
 * @throws InternalErrorException
 */
	public function saveDisplayRegistrationForList($data) {
		$frameKey = Current::read('Frame.key');

		foreach ($data['List']['RegistrationFrameDisplayRegistration'] as $value) {
			$registrationKey = $value['registration_key'];
			$isDisplay = $value['is_display'];
			$saveQs = array(
				'frame_key' => $frameKey,
				'registration_key' => $registrationKey
			);
			if ($isDisplay != 0) {
				// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
				$this->saveDisplayRegistration($saveQs);
			} else {
				if (! $this->deleteAll($saveQs, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
		}
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->updateFrameDefaultAction("''");

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

		$saveData = Hash::extract($data, 'Single.RegistrationFrameDisplayRegistration');
		$saveData['frame_key'] = $frameKey;
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->saveDisplayRegistration($saveData);
		$action = sprintf('\'registration_answers/view/%s/%s\'',
			Current::read('Block.id'),
			$saveData['registration_key']);
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->updateFrameDefaultAction($action);

		return true;
	}

/**
 * saveDisplayRegistration
 * saveRegistrationFrameDisplayRegistration
 *
 * @param array $data save data
 * @return bool
 * @throws InternalErrorException
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
		if (!$this->save($data, false)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		// フレームのデフォルトにする
		$action = "'" . 'registration_answers/view/' . Current::read('Block.id') . '/' . $data['registration_key'] . "'";
		if (!$this->updateFrameDefaultAction($action)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		return true;
	}
/**
 * updateFrameDefaultAction
 * update Frame default_action
 *
 * @param string $action default_action
 * @return bool
 * @throws InternalErrorException
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
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return true;
	}
}
