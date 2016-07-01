<?php
/**
 * RegistrationComponent::isSelectionInputType()のテスト
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
 * RegistrationComponent::isSelectionInputType()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registration\Test\Case\Controller\Component\RegistrationComponent
 */
class RegistrationComponentIsSelectionInputTypeTest extends NetCommonsControllerTestCase {

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
 * isSelectionInputType()のテスト
 *
 * @param int $type チェック種別
 * @param mix $expect 期待値
 * @dataProvider dataProviderIsSelectionInputType
 * @return void
 */
	public function testIsSelectionInputType($type, $expect) {
		$ret = RegistrationsComponent::isSelectionInputType($type);
		$this->assertEqual($ret, $expect);
	}
/**
 * isSelectionInputType()のテストデータプロバイダ
 *
 * @return void
 */
	public function dataProviderIsSelectionInputType() {
		$data = array(
			array(RegistrationsComponent::TYPE_SELECTION, true),
			array(RegistrationsComponent::TYPE_MULTIPLE_SELECTION, true),
			array(RegistrationsComponent::TYPE_TEXT, false),
			array(RegistrationsComponent::TYPE_TEXT_AREA, false),
			array(RegistrationsComponent::TYPE_MATRIX_SELECTION_LIST, false),
			array(RegistrationsComponent::TYPE_MATRIX_MULTIPLE, false),
			array(RegistrationsComponent::TYPE_DATE_AND_TIME, false),
			array(RegistrationsComponent::TYPE_SINGLE_SELECT_BOX, true)
		);
		return $data;
	}
}
