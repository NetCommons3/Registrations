<?php
/**
 * Registrations App Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');
App::uses('Current', 'NetCommons.Utility');
App::uses('RegistrationsComponent', 'Registrations.Controller/Component');

/**
 * Summary for RegistrationQuestion Model
 */
class RegistrationsAppModel extends AppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
	);
}
