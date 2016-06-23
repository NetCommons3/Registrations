<?php
/**
 * RegistrationComponent::isOnlyInputType()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationComponent::isOnlyInputType()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registration\Test\Case\Controller\Component\RegistrationComponent
 */
class RegistrationComponentIsOnlyInputTypeTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'registrations';

/**
 * isOnlyInputType()のテスト
 *
 * @param int $type チェック種別
 * @param mix $expect 期待値
 * @dataProvider dataProviderIsOnlyInputTypeType
 * @return void
 */
	public function testIsOnlyInputType($type, $expect) {
		$ret = RegistrationsComponent::isOnlyInputType($type);
		$this->assertEqual($ret, $expect);
	}
/**
 * isOnlyInputType()のテストデータプロバイダ
 *
 * @return void
 */
	public function dataProviderIsOnlyInputTypeType() {
		$data = array(
			array(RegistrationsComponent::TYPE_SELECTION, false),
			array(RegistrationsComponent::TYPE_MULTIPLE_SELECTION, false),
			array(RegistrationsComponent::TYPE_TEXT, true),
			array(RegistrationsComponent::TYPE_TEXT_AREA, true),
			array(RegistrationsComponent::TYPE_MATRIX_SELECTION_LIST, false),
			array(RegistrationsComponent::TYPE_MATRIX_MULTIPLE, false),
			array(RegistrationsComponent::TYPE_DATE_AND_TIME, true),
			array(RegistrationsComponent::TYPE_SINGLE_SELECT_BOX, false)
		);
		return $data;
	}
}
