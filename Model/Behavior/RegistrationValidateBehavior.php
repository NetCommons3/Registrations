<?php
/**
 * RegistrationValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * RegistrationValidate Behavior
 *
 * @package  Registrations\Registrations\Model\Befavior
 * @author Allcreator <info@allcreator.net>
 */
class RegistrationValidateBehavior extends ModelBehavior {

/**
 * Checks if flag is on, required other fields
 *
 * @param object &$model use model
 * @param array $check check data array
 * @param mix $requireValue when check data value equal this value, then require other field
 * @param array $others require data field names
 * @param string $ope require condition AND or OR or XOR
 * @return bool
 */
	public function requireOtherFields(&$model, $check, $requireValue, $others, $ope) {
		$checkPatterns = array(
			'AND' => array('midstream' => array('chk' => true, 'ret' => false), 'end' => array('ret' => true)),
			'OR' => array('midstream' => array('chk' => false, 'ret' => true), 'end' => array('ret' => false)),
			'XOR' => array('midstream' => array('chk' => false, 'ret' => false), 'end' => array('ret' => true)),
		);
		$ope = strtoupper($ope);
		$checkPattern = $checkPatterns[$ope];
		$value = array_values($check);
		$value = $value[0];
		if ($value != $requireValue) {
			return true;
		}
		foreach ($others as $other) {
			$checkData = Hash::get($model->data, $other);
			$otherFieldsName = explode('.', $other);
			// is_系のフィールドの場合、チェックボックスで実装され、OFFでも０という数値が入ってくる
			// そうすると「Blank」判定してほしいのに「ある」と判定されてしまう
			// なのでis_で始まるフィールドのデータの設定を確認するときだけは == falseで判定する
			if (strncmp('is_', $otherFieldsName[count($otherFieldsName) - 1], 3) === 0) {
				$ret = ($checkData == false);
			} else {
				$ret = Validation::blank($checkData);
			}
			if ($ret == $checkPattern['midstream']['chk']) {
				return $checkPattern['midstream']['ret'];
			}
		}
		return $checkPattern['end']['ret'];
	}
/**
 * Checks datetime null or datetime
 *
 * @param object &$model use model
 * @param array $check check data array
 * @return bool
 */
	public function checkDateTime(&$model, $check) {
		foreach ($check as $val) {
			if (Validation::blank($val)) {
				continue;
			}
			$ret = Validation::datetime($val);
			if (!$ret) {
				return false;
			}
		}
		return true;
	}

/**
 * Used to compare 2 datetime values.
 *
 * @param object &$model use model
 * @param string|array $check datetime string
 * @param string $operator Can be either a word or operand
 *	is greater >, is less <, greater or equal >=
 *	less or equal <=, is less <, equal to ==, not equal !=
 * @param string $compare compare datetime string
 * @return bool Success
 */
	public function checkDateComp(&$model, $check, $operator, $compare) {
		// 比較対象がないので比較する必要なし
		if (Validation::blank($model->data['Registration'][$compare])) {
			return true;
		}

		$check2 = strtotime($model->data['Registration'][$compare]);
		foreach ($check as $val) {
			if (Validation::blank($val)) {
				continue;
			}
			$check1 = strtotime($val);
			$ret = Validation::comparison($check1, $operator, $check2);
			if (!$ret) {
				return false;
			}
		}
		return true;
	}

/**
 * checkMinMax
 * min and max is require both value
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	public function checkMinMax(&$model, $check) {
		// 範囲使わない設定のときはチェックしない
		if ($model->data['RegistrationQuestion']['is_range'] == RegistrationsComponent::USES_NOT_USE) {
			return true;
		}

		// 最大値、最小値はテキストで「数値型」の場合と、日付け型の「日」「日時」の場合のみ設定可能
		if (!$this->__checkMinMaxNumeric($model, $check)) {
			return false;
		}
		if (!$this->__checkMinMaxDate($model, $check)) {
			return false;
		}
		if (!$this->__checkMinMaxDateTime($model, $check)) {
			return false;
		}
		if ($model->data['RegistrationQuestion']['min'] >= $model->data['RegistrationQuestion']['max']) {
			return false;
		}

		return true;
	}

/**
 * getPeriodStatus
 * get period status now and specified time
 *
 * @param object &$model use model
 * @param bool $check flag data
 * @param string $startTime start time
 * @param string $endTime end time
 * @return int
 */
	public function getPeriodStatus(&$model, $check, $startTime, $endTime) {
		$ret = RegistrationsComponent::REGISTRATION_PERIOD_STAT_IN;

		if ($check == WorkflowBehavior::PUBLIC_TYPE_LIMITED) {
			$nowTime = (new NetCommonsTime())->getNowDatetime();
			$nowTime = strtotime($nowTime);
			if ($nowTime < strtotime($startTime)) {
				$ret = RegistrationsComponent::REGISTRATION_PERIOD_STAT_BEFORE;
			}
			if ($nowTime > strtotime($endTime)) {
				$ret = RegistrationsComponent::REGISTRATION_PERIOD_STAT_END;
			}
		}
		return $ret;
	}

/**
 * __checkMinMaxNumeric
 * min and max is require both value
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	private function __checkMinMaxNumeric(&$model, $check) {
		if ($model->data['RegistrationQuestion']['question_type_option'] == RegistrationsComponent::TYPE_OPTION_NUMERIC) {
			if (!Validation::numeric($model->data['RegistrationQuestion']['min'])) {
				return false;
			}
			if (!Validation::numeric($model->data['RegistrationQuestion']['max'])) {
				return false;
			}
		}
		return true;
	}

/**
 * __checkMinMaxDate
 * min and max is require both value
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	private function __checkMinMaxDate(&$model, $check) {
		if ($model->data['RegistrationQuestion']['question_type_option'] == RegistrationsComponent::TYPE_OPTION_DATE) {
			if (!Validation::date($model->data['RegistrationQuestion']['min'])) {
				return false;
			}
			if (!Validation::date($model->data['RegistrationQuestion']['max'])) {
				return false;
			}
		}
		return true;
	}

/**
 * __checkMinMaxDateTime
 * min and max is require both value
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	private function __checkMinMaxDateTime(&$model, $check) {
		if ($model->data['RegistrationQuestion']['question_type_option'] == RegistrationsComponent::TYPE_OPTION_DATE_TIME) {
			if (!Validation::datetime($model->data['RegistrationQuestion']['min'])) {
				return false;
			}
			if (!Validation::datetime($model->data['RegistrationQuestion']['max'])) {
				return false;
			}
		}
		return true;
	}
}