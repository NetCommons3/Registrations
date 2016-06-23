<?php
/**
 * RegistrationOwnAnswerComponentテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * RegistrationsComponent::isOnlyInputType()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registrations\test_app\Plugin\Registrations\Controller
 */
class TestRegistrationsOwnAnswerComponentController extends AppController {

/**
 * 使用コンポーネント
 *
 * @var array
 */
	public $components = array(
		'Session',
		'Registrations.RegistrationsOwnAnswer'
	);

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->autoRender = true;
	}
}
