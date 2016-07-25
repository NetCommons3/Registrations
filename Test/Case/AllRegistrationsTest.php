<?php
/**
 * Registrations All Test Suite
 *
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsTestSuite', 'NetCommons.TestSuite');

/**
 * Registrations All Test Suite
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Announcements\Test\Case
 * @codeCoverageIgnore
 */
class AllRegistrationsTest extends NetCommonsTestSuite {

/**
 * All test suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$plugin = preg_replace('/^All([\w]+)Test$/', '$1', __CLASS__);
		$suite = new NetCommonsTestSuite(sprintf('All %s Plugin tests', $plugin));
		//$suite->addTestDirectoryRecursive(CakePlugin::path($plugin) . 'Test' . DS . 'Case');
		// モデルだけ
		$suite->addTestDirectoryRecursive(
			CakePlugin::path($plugin) . 'Test' . DS . 'Case' . DS . 'Model'
		);
		// View
		$suite->addTestDirectoryRecursive(
			CakePlugin::path($plugin) . 'Test' . DS . 'Case' . DS . 'View'
		);
		$suite->addTestDirectoryRecursive(
			CakePlugin::path($plugin) . 'Test' . DS . 'Case' . DS . 'Controller' . DS . 'Component'
		);
		return $suite;
	}
}
