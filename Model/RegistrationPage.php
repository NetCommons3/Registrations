<?php
/**
 * RegistrationPage Model
 *
 * @property Registration $Registration
 * @property RegistrationQuestion $RegistrationQuestion
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Summary for RegistrationPage Model
 */
class RegistrationPage extends RegistrationsAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
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
		'Registration' => array(
			'className' => 'Registrations.Registration',
			'foreignKey' => 'registration_id',
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
		'RegistrationQuestion' => array(
			'className' => 'Registrations.RegistrationQuestion',
			'foreignKey' => 'registration_page_id',
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
 * getDefaultPage
 * get default data of registration page
 *
 * @return array
 */
	public function getDefaultPage() {
		$this->RegistrationQuestion = ClassRegistry::init('Registrations.RegistrationQuestion', true);

		$page = array(
			'page_title' => __d('registrations', 'First Page'),
			'page_sequence' => 0,
			'key' => '',
			'route_number' => 0,
		);
		$page['RegistrationQuestion'][0] = $this->RegistrationQuestion->getDefaultQuestion();

		return $page;
	}

/**
 * getNextPage
 * get next answer page number
 *
 * @param array $registration registration
 * @param int $nowPageSeq current page sequence number
 * @param array $nowAnswers now answer
 *
 * @return array
 */
	public function getNextPage($registration, $nowPageSeq, $nowAnswers) {
		// 次ページはデフォルトならば＋１です
		$nextPageSeq = $nowPageSeq + 1;
		// 登録にスキップロジックで指定されたものがないかチェックし、行き先があるならそのページ番号を返す
		foreach ($nowAnswers as $answer) {
			$targetQuestion = Hash::extract($registration['RegistrationPage'], '{n}.RegistrationQuestion.{n}[key=' . $answer[0]['registration_question_key'] . ']');
			if ($targetQuestion) {
				$q = $targetQuestion[0];
				// skipロジック対象の質問ならば次ページのチェックを行う
				if ($q['is_skip'] == RegistrationsComponent::SKIP_FLAGS_SKIP) {
					$choiceIds = explode(RegistrationsComponent::ANSWER_VALUE_DELIMITER,
						trim($answer[0]['answer_value'], RegistrationsComponent::ANSWER_DELIMITER));
					// スキップロジックの選択肢みつけた
					$choice = Hash::extract($q['RegistrationChoice'], '{n}[key=' . $choiceIds[0] . ']');
					if ($choice) {
						$c = $choice[0];
						if (!empty($c['skip_page_sequence'])) {
							// スキップ先ページ
							$nextPageSeq = $c['skip_page_sequence'];
							break;
						}
					}
				}
			}
		}
		// 次ページがもしかして存在しない（つまりエンドかも）
		if ($nextPageSeq == RegistrationsComponent::SKIP_GO_TO_END) {
			return false;
		}
		// ページ情報がない？終わりにする
		if (!isset($registration['RegistrationPage'])) {
			return false;
		}
		// ページ配列はページのシーケンス番号順に取り出されているので
		$pages = $registration['RegistrationPage'];
		$endPage = end($pages);
		if ($endPage['page_sequence'] < $nextPageSeq) {
			return false;
		}
		return $nextPageSeq;
	}
/**
 * setPageToRegistration
 * setup page data to registration array
 *
 * @param array &$registration registration data
 * @return void
 */
	public function setPageToRegistration(&$registration) {
		$this->RegistrationQuestion = ClassRegistry::init('Registrations.RegistrationQuestion', true);
		// ページデータが登録フォームデータの中にない状態でここが呼ばれている場合、
		if (!isset($registration['RegistrationPage'])) {
			$pages = $this->find('all', array(
				'conditions' => array(
					'registration_id' => $registration['Registration']['id'],
				),
				'order' => array('page_sequence ASC'),
				'recursive' => -1));

			$registration['RegistrationPage'] = Hash::combine($pages, '{n}.RegistrationPage.page_sequence', '{n}.RegistrationPage');
		}
		$registration['Registration']['page_count'] = 0;
		if (isset($registration['RegistrationPage'])) {
			foreach ($registration['RegistrationPage'] as &$page) {
				$this->RegistrationQuestion->setQuestionToPage($registration, $page);
				$registration['Registration']['page_count']++;
			}
		}
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
		$pageIndex = $options['pageIndex'];
		// Pageモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'page_sequence' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
				),
				'comparison' => array(
					'rule' => array('comparison', '==', $pageIndex),
					'message' => __d('registrations', 'page sequence is illegal.')
				),
			),
			'route_number' => array(
				'numeric' => array(
				'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
				),
			),
		);
		// ウィザード画面でのセットアップ中の場合はまだ親登録フォームIDの正当性についてのチェックは行わない
		if (! (isset($options['validate']) && $options['validate'] == RegistrationsComponent::REGISTRATION_VALIDATE_TYPE)) {
			$this->validate = Hash::merge($this->validate, array(
				'registration_id' => array(
					'numeric' => array(
						'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						'allowEmpty' => false,
						'required' => true,
					),
				),
			));
		}

		parent::beforeValidate($options);

		// 付属の質問以下のvalidate
		if (! isset($this->data['RegistrationQuestion'][0])) {
			$this->validationErrors['page_sequence'][] = __d('registrations', 'please set at least one question.');
		} else {
			$validationErrors = array();
			$this->RegistrationQuestion = ClassRegistry::init('Registrations.RegistrationQuestion', true);
			foreach ($this->data['RegistrationQuestion'] as $qIndex => $question) {
				// 質問データバリデータ
				$this->RegistrationQuestion->create();
				$this->RegistrationQuestion->set($question);
				$options['questionIndex'] = $qIndex;
				if (! $this->RegistrationQuestion->validates($options)) {
					$validationErrors['RegistrationQuestion'][$qIndex] = $this->RegistrationQuestion->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}
		return true;
	}
/**
 * saveRegistrationPage
 * save RegistrationPage data
 *
 * @param array &$registrationPages registration pages
 * @throws InternalErrorException
 * @return bool
 */
	public function saveRegistrationPage(&$registrationPages) {
		$this->loadModels([
			'RegistrationQuestion' => 'Registrations.RegistrationQuestion',
		]);

		// RegistrationPageが単独でSaveされることはない
		// 必ず上位のRegistrationのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない

		foreach ($registrationPages as &$page) {
			// 登録フォームは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			$page = Hash::remove($page, 'RegistrationPage.id');
			$this->create();
			if (! $this->save($page, false)) {	// validateは上位のregistrationで済んでいるはず
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$pageId = $this->id;

			$page = Hash::insert($page, 'RegistrationQuestion.{n}.registration_page_id', $pageId);
			// もしもQuestionやChoiceのsaveがエラーになった場合は、
			// QuestionやChoiceのほうでInternalExceptionErrorが発行されるのでここでは何も行わない
			$this->RegistrationQuestion->saveRegistrationQuestion($page['RegistrationQuestion']);
		}
		return true;
	}
}
