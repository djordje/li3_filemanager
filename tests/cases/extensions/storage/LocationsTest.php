<?php

/**
 * @copyright Copyright 2012, Djordje Kovacevic (http://djordjekovacevic.com)
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_filemanager\tests\cases\extensions\storage;

use li3_filemanager\extensions\storage\Locations;
use lithium\core\Libraries;

class LocationsTest extends \lithium\test\Unit {
	
	public function testAdding() {
		$location = Locations::add('test', array(
			'adapter' => 'FileSystem',
			'location' => Libraries::path('li3_filemanager\\', array('dirs' => true)) . '/resources/tmp'
		));
		$this->assertTrue(is_array($location));
	}

	public function testGetting() {
		$this->assertTrue(Locations::get(false));
		$this->assertTrue(is_array(Locations::get()));
		$this->assertNull(Locations::get('test2'));
		$this->assertTrue(is_array(Locations::get('test', array('config' => true))));
		$this->assertNull(Locations::get('test', array('autoCreate' => false)));
		$this->assertTrue(Locations::get('test'));
	}
	
}

?>