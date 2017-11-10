<?php
/**
 * AddAnswerSummarySerialNumber
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class AddAnswerSummarySerialNumber
 */
class AddAnswerSummarySerialNumber extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_answer_summary_serial_number';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'registration_answer_summaries' => array(
					'serial_number' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'after' => 'id'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'registration_answer_summaries' => array('serial_number'),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$this->RegistrationAnswerSummary = $this->generateModel('RegistrationAnswerSummary');

		if ($direction == 'up') {
			// 受付番号をIDで埋める。
			$summaries = $this->RegistrationAnswerSummary->find('all', [
				'conditions' => [
					'answer_status' => 2
				]
			]);
			foreach ($summaries as $summary) {
				$summary['RegistrationAnswerSummary']['serial_number'] = $summary['RegistrationAnswerSummary']['id'];
				$this->RegistrationAnswerSummary->create();
				$this->RegistrationAnswerSummary->save($summary, [
					'validate' => false,
					'callbacks' => false
				]);
			}
		}
		return true;
	}
}
