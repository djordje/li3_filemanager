<?php

namespace li3_filemanager\tests\cases\extensions\data;
use li3_filemanager\extensions\data\Locations;

class LocationsTest extends \lithium\test\Unit {

	public function  testAdd() {
		$this->assertTrue(is_array(
			Locations::add('test', array(
				'adapter' => 'Filesystem',
				'location' => Libraries::path('li3_filemanager\\', array('dirs' => true)) . '/resources/fs'
			))
		));
	}

	public function testGet() {
		$this->assertTrue(Locations::get(false));
		$this->assertTrue(is_array(Locations::get()));
		$this->assertNull(Locations::get('test2'));
		$this->assertTrue(is_array(Locations::get('test', array('config' => true))));
		$this->assertNull(Locations::get('test', array('autoCreate' => false)));
		$this->assertTrue(Locations::get('test'));
	}

}

?>