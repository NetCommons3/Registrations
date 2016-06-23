<?php
/**
 * TestRegistrations Model
 *
 * @property Registration $Registration
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationsAppModel', 'Registrations.Model');
App::uses('Registration', 'Registrations.Model');

/**
 * TestRegistrations Model
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Registrations\Test\test_app\Plugin\TestRegistrations\Model
 */
class TestRegistrationModel extends CakeTestModel {

/**
 * table name
 *
 * @var string
 */
	public $useTable = 'registrations';

/**
 * name
 *
 * @var string
 */
	public $name = 'TestRegistrationModel';

/**
 * alias
 *
 * @var string
 */
	public $alias = 'Registration';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Registrations.RegistrationValidate'
	);
}
