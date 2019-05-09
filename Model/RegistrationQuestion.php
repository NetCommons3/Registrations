<?php
/**
 * RegistrationQuestion Model
 *
 * @property RegistrationPage $RegistrationPage
 * @property RegistrationChoice $RegistrationChoice
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');

/**
 * Summary for RegistrationQuestion Model
 */
class RegistrationQuestion extends RegistrationsAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Wysiwyg.Wysiwyg' => array(
			'fields' => array('question_value')
		),
		//多言語
		'M17n.M17n' => array(
			'commonFields' => array(
				'question_sequence',
				'question_type',
				'is_require',
				'question_type_option',
				'is_choice_random',
				'is_choice_horizon',
				'is_skip',
				'is_jump',
				'is_range',
				'min',
				'max',
				'is_result_display',
				'result_display_type',
			),
			'associations' => array(
				'RegistrationChoice' => array(
					'class' => 'Registrations.RegistrationChoice',
					'foreignKey' => 'registration_question_id',
					'isM17n' => true,
				),
			),
			'afterCallback' => false,
		),
	);

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
		'RegistrationPage' => array(
			'className' => 'Registrations.RegistrationPage',
			'foreignKey' => 'registration_page_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'RegistrationChoice' => array(
			'className' => 'Registrations.RegistrationChoice',
			'foreignKey' => 'registration_question_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
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
			'RegistrationChoice' => 'Registrations.RegistrationChoice',
		]);
	}

/**
 * Called before each find operation. Return false if you want to halt the find
 * call, otherwise return the (modified) query data.
 *
 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified
 *  $query to continue with new $query
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforefind
 */
	public function beforeFind($query) {
		//hasManyで実行されたとき、多言語の条件追加
		if (! $this->id && isset($query['conditions']['registration_page_id'])) {
			$registrationPageId = $query['conditions']['registration_page_id'];
			$query['conditions']['registration_page_id'] =
					$this->getRegistrationPageIdsForM17n($registrationPageId);
			$query['conditions']['OR'] = array(
				'RegistrationQuestion.language_id' => Current::read('Language.id'),
				'RegistrationQuestion.is_translation' => false,
			);

			return $query;
		}

		return parent::beforeFind($query);
	}

/**
 * 多言語データ取得のため、当言語のregistration_page_idから全言語のregistration_page_idを取得する
 *
 * @param id $registrationPageId 当言語のregistration_page_id
 * @return array
 */
	public function getRegistrationPageIdsForM17n($registrationPageId) {
		$registrationPage = $this->RegistrationPage->find('first', array(
			'recursive' => -1,
			'callbacks' => false,
			'fields' => array('id', 'key', 'registration_id'),
			'conditions' => array('id' => $registrationPageId),
		));

		$registrationId = $registrationPage['RegistrationPage']['registration_id'];
		$registrationPageIds = $this->RegistrationPage->find('list', array(
			'recursive' => -1,
			'callbacks' => false,
			'fields' => array('id', 'id'),
			'conditions' => array(
				'registration_id' => $this->RegistrationPage->getRegistrationIdsForM17n($registrationId),
				'key' => $registrationPage['RegistrationPage']['key']
			),
		));

		return array_values($registrationPageIds);
	}

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$qIndex = $options['questionIndex'];
		// Questionモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'question_sequence' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'comparison' => array(
					'rule' => array('comparison', '==', $qIndex),
					'message' => __d('registrations', 'question sequence is illegal.')
				),
			),
			'question_type' => array(
				'inList' => array(
					'rule' => array('inList', RegistrationsComponent::$typesList),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'question_value' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('registrations', 'Please input question text.'),
				),
			),
			'is_require' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_choice_random' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_skip' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_result_display' => array(
				'inList' => array(
					'rule' => array(
						'inList',
						$this->_getResultDisplayList($this->data['RegistrationQuestion']['question_type'])
					),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'result_display_type' => array(
				'inList' => array(
					'rule' => array('inList', RegistrationsComponent::$resultDispTypesList),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_range' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		);
		// 範囲制限設定された項目の場合
		if ($this->data['RegistrationQuestion']['is_range'] == true) {
			$this->validate = ValidateMerge::merge($this->validate, array(
				'min' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => __d('registrations', 'Please enter both the maximum and minimum values.'),
					),
					'comparison' => array(
						'rule' => array('comparison', '<', $this->data['RegistrationQuestion']['max']),
						'message' => __d('registrations', 'Please enter smaller value than max.')
					),
				),
				'max' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => __d('registrations', 'Please enter both the maximum and minimum values.'),
					),
					'comparison' => array(
						'rule' => array('comparison', '>', $this->data['RegistrationQuestion']['min']),
						'message' => __d('registrations', 'Please enter bigger value than min.')
					),
				),
			));

		}
		// validates時にはまだregistration_page_idの設定ができないのでチェックしないことにする
		// registration_page_idの設定は上位のRegistrationPageクラスで責任を持って行われるものとする

		parent::beforeValidate($options);

		$isSkip = $this->data['RegistrationQuestion']['is_skip'];
		// 付属の選択肢以下のvalidate
		if ($this->_checkChoiceExists() && isset($this->data['RegistrationChoice'])) {
			// この項目種別に必要な選択肢データがちゃんとあるなら選択肢をバリデート
			$validationErrors = array();
			foreach ($this->data['RegistrationChoice'] as $cIndex => $choice) {
				// 項目データバリデータ
				$this->RegistrationChoice->create();
				$this->RegistrationChoice->set($choice);
				$options['choiceIndex'] = $cIndex;
				$options['isSkip'] = $isSkip;
				if (!$this->RegistrationChoice->validates($options)) {
					$validationErrors['RegistrationChoice'][$cIndex] =
						$this->RegistrationChoice->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}

		return true;
	}

/**
 * getDefaultQuestion
 * get default data of registration question
 *
 * @return array
 */
	public function getDefaultQuestion() {
		$question = array(
			'question_sequence' => 0,
			'question_value' => __d('registrations', 'New Question') . '1',
			'question_type' => RegistrationsComponent::TYPE_SELECTION,
			'is_require' => RegistrationsComponent::USES_NOT_USE,
			'is_skip' => RegistrationsComponent::SKIP_FLAGS_NO_SKIP,
			'is_choice_random' => RegistrationsComponent::USES_NOT_USE,
			'is_range' => RegistrationsComponent::USES_NOT_USE,
			'is_result_display' => RegistrationsComponent::EXPRESSION_SHOW,
			'result_display_type' => RegistrationsComponent::RESULT_DISPLAY_TYPE_BAR_CHART
		);
		$question['RegistrationChoice'][0] = $this->RegistrationChoice->getDefaultChoice();
		return $question;
	}

/**
 * setQuestionToPage
 * setup page data to registration array
 *
 * @param array &$registration registration data
 * @param array &$page registration page data
 * @return void
 */
	public function setQuestionToPage(&$registration, &$page) {
		$questions = $this->find('all', array(
			'conditions' => array(
				'registration_page_id' => $page['id'],
			),
			'order' => array(
				'question_sequence' => 'asc',
			)
		));

		if (!empty($questions)) {
			foreach ($questions as $question) {
				if (isset($question['RegistrationChoice'])) {
					$choices = $question['RegistrationChoice'];
					$question['RegistrationQuestion']['RegistrationChoice'] = $choices;
					$page['RegistrationQuestion'][] = $question['RegistrationQuestion'];
				}
				$registration['Registration']['question_count']++;
			}
		}
	}

/**
 * saveRegistrationQuestion
 * save RegistrationQuestion data
 *
 * @param array &$questions registration questions
 * @throws InternalErrorException
 * @return bool
 */
	public function saveRegistrationQuestion(&$questions) {
		$this->loadModels([
			'RegistrationChoice' => 'Registrations.RegistrationChoice',
		]);
		// RegistrationQuestionが単独でSaveされることはない
		// 必ず上位のRegistrationのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない

		foreach ($questions as &$question) {
			// 登録フォームは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			$question = Hash::remove($question, 'RegistrationQuestion.id');

			$this->create();
			if (! $this->save($question, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$questionId = $this->id;

			if (isset($question['RegistrationChoice'])) {
				$question = Hash::insert(
					$question,
					'RegistrationChoice.{n}.registration_question_id', $questionId);
				// もしもChoiceのsaveがエラーになった場合は、
				// ChoiceのほうでInternalExceptionErrorが発行されるのでここでは何も行わない
				$this->RegistrationChoice->saveRegistrationChoice($question['RegistrationChoice']);
			}
		}
		return true;
	}

/**
 * _checkChoiceExists
 *
 * 適正な選択肢を持っているか
 *
 * @return bool
 */
	protected function _checkChoiceExists() {
		$questionType = $this->data['RegistrationQuestion']['question_type'];
		// テキストタイプ、テキストエリアタイプの時は選択肢不要
		if (RegistrationsComponent::isOnlyInputType($questionType)) {
			return true;
		}

		// 上記以外の場合は最低１つは必要
		if (! Hash::check($this->data, 'RegistrationChoice.{n}')) {
			$this->validationErrors['question_type'][] =
				__d('registrations', 'please set at least one choice.');
			return false;
		}

		// マトリクスタイプの時は行に１つ列に一つ必要
		// マトリクスタイプのときは、行、カラムの両方ともに最低一つは必要
		if (RegistrationsComponent::isMatrixInputType($questionType)) {
			$rows = Hash::extract(
				$this->data['RegistrationChoice'],
				'{n}[matrix_type=' . RegistrationsComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX . ']');
			$cols = Hash::extract(
				$this->data['RegistrationChoice'],
				'{n}[matrix_type=' . RegistrationsComponent::MATRIX_TYPE_COLUMN . ']');

			if (empty($rows) || empty($cols)) {
				$this->validationErrors['question_type'][] =
					__d('registrations', 'please set at least one choice at row and column.');
				return false;
			}
		}
		return true;
	}

/**
 * _getResultDisplayList
 * 項目種別に応じて許されるisResultDisplayの設定値
 *
 * @param int $questionType 項目種別
 * @return array
 */
	protected function _getResultDisplayList($questionType) {
		if (RegistrationsComponent::isOnlyInputType($questionType)) {
			return array(RegistrationsComponent::USES_NOT_USE);
		}
		return array(RegistrationsComponent::USES_USE, RegistrationsComponent::USES_NOT_USE);
	}
}