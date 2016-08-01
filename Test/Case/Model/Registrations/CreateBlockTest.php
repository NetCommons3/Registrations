<?php
/**
 * Registrations::afterFrameSave()のテスト
 *
 * @property Registrations $Registrations
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Registrations::afterFrameSave()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Model\Registrations
 */
class RegistrationCreateBlockTest extends NetCommonsModelTestCase {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'registrations';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.registrations.registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.registrations.block_setting_for_registration',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.block_setting_for_registration',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'Registration';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'createBlock';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->Frame = ClassRegistry::init('Frames' . '.' . 'Frame');
		$this->Block = ClassRegistry::init('Blocks' . '.' . 'Block');
		$this->RegistrationSetting = ClassRegistry::init('Registrations' . '.' . 'RegistrationSetting');
		$this->RegistrationFrameSetting = ClassRegistry::init('Registrations' . '.' . 'RegistrationFrameSetting');
	}

/**
 * テストDataの取得
 *
 * @param string $frameId frame id
 * @param string $blockId block id
 * @param string $roomId room id
 * @return array
 */
	private function __getData($frameId, $blockId, $roomId) {
		$data = array();
		$data['Frame']['id'] = $frameId;
		$data['Frame']['block_id'] = $blockId;
		$data['Frame']['language_id'] = 2;
		$data['Frame']['room_id'] = $roomId;
		$data['Frame']['plugin_key'] = 'registrations';

		return $data;
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertNotEmpty($result);

		//登録データ取得
		//$actual = $this->Frame->find('first', array(
		//	'recursive' => -1,
		//	'conditions' => array('id' => $data['Frame']['id']),
		//));
		//$actualBlockId = $actual['Frame']['block_id'];
		//// block_idが設定されていて
		//$this->assertNotEmpty($actualBlockId);
		$actualBlockId = $this->$model->Block->id;

		$block = $this->Block->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $actualBlockId),
		));
		$this->assertNotEmpty($block);

		//そのブロックは登録フォームのもので
		$this->assertTextEquals($block['Block']['plugin_key'], 'registrations');

		//$actualBlockKey = $block['Block']['key'];
		//// 登録フォームのフレーム設定情報もできていること
		//$setting = $this->RegistrationSetting->find('first', array(
		//	'recursive' => -1,
		//	'conditions' => array('block_key' => $actualBlockKey),
		//));
		//$this->assertNotEmpty($setting);
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		return array(
			array($this->__getData(6, 2, 1)), //
			array($this->__getData(14, null, 1)), //
			array($this->__getData(16, null, 4)), //
		);
	}

}
