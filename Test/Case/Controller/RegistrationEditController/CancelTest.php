<?php
/**
 * RegistrationEditController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * RegistrationEditController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\Case\Controller
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class RegistrationEditControllerCancelTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.registrations.registration',
		'plugin.registrations.registration_setting',
		'plugin.registrations.registration_frame_setting',
		'plugin.registrations.registration_frame_display_registration',
		'plugin.registrations.registration_page',
		'plugin.registrations.registration_question',
		'plugin.registrations.registration_choice',
		'plugin.registrations.registration_answer_summary',
		'plugin.registrations.registration_answer',
		'plugin.authorization_keys.authorization_keys',
		'plugin.workflow.workflow_comment',
	);

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'registrations';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'registration_edit';

/**
 * testCancel
 *
 * @return void
 */
	public function testCancel() {
		//テスト実施
		$urlOptions = array(
			'action' => 'cancel', 'block_id' => 2, 'frame_id' => 6
		);
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
		), $urlOptions);
		$this->_testNcAction($url, array('method' => 'get'), null, 'view');
		$result = $this->headers['Location'];
		$this->assertNotEmpty($result);
	}
}