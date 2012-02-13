<?php

namespace li3_filemanager\tests\cases\models;

use lithium\core\Libraries;
use li3_filemanager\extensions\data\Locations;
use li3_filemanager\models\File;

class FileTest extends \lithium\test\Unit {

	protected $_adapters = array();
	
	protected $_li3_filemanager;

	protected function _init() {
		Locations::add('filesystemTest', array(
			'adapter' => 'Filesystem',
			'location' => LITHIUM_APP_PATH.'/libraries/li3_filemanager/resources/fs'
		));

		$dataSources = Libraries::locate('data.source', null, array(
			'library' => 'li3_filemanager'
		));

		$testableLocations = array('filesystemTest');

		foreach ($dataSources as $dataSource) {
			$adapterName = end(explode('\\', $dataSource));

			if ($adapterName == 'Filesystem') {
				continue;
			}

			$locations = Locations::get();

			foreach ($locations as $location) {
				$config = Locations::get($location, array('config' => true));
				if (
					$config['adapter'] === $adapterName &&
					($location == 'default' || $location == 'test')
				) {
					$testableLocations[] = $location;
				}
			}
		}
		foreach ($testableLocations as $adapter) {
			$this->_adapters[] = $adapter;
		}
		$this->_li3_filemanager = Libraries::path('li3_filemanager\\', array('dirs' => true));
	}

	public function testLs() {
		foreach ($this->_adapters as $adapter) {
			File::$location = $adapter;
			$this->assertTrue(is_array(File::ls()), "Location name: {$adapter}\n" . '{:message}');
			$this->assertFalse(File::ls('notExists/*'), "Location name: {$adapter}\n" . '{:message}');
			File::reset();
		}
	}

	public function testMkdir() {
		foreach ($this->_adapters as $adapter) {
			File::$location = $adapter;
			$this->assertTrue(File::mkdir('test'), "Location name: {$adapter}\n" . '{:message}');
			$this->assertTrue(File::mkdir('test/1'), "Location name: {$adapter}\n" . '{:message}');
			$this->expectException();
			$this->assertFalse(File::mkdir('test'), "Location name: {$adapter}\n" . '{:message}');
			$this->expectException();
			$this->assertFalse(File::mkdir('test/two/child'), "Location name: {$adapter}\n" . '{:message}');
			$this->assertTrue(File::mkdir('test/two'), "Location name: {$adapter}\n" . '{:message}');
			$this->assertTrue(File::mkdir('test/two/child', "Location name: {$adapter}\n" . '{:message}'));
			File::reset();
		}
	}

	public function testMv() {
		foreach ($this->_adapters as $adapter) {
			File::$location = $adapter;
			$this->assertTrue(File::mv('test/1', 'test/one'), "Location name: {$adapter}\n" . '{:message}');
			File::reset();
		}
	}

	public function testCp() {
		foreach ($this->_adapters as $adapter) {
			File::$location = $adapter;
			$this->assertTrue(File::cp('test/two', 'test/tree'), "Location name: {$adapter}\n" . '{:message}');
			File::reset();
		}
	}

	public function testUpload() {
		foreach ($this->_adapters as $adapter) {
			File::$location = $adapter;
			$from = LITHIUM_APP_PATH . '/libraries/li3_filemanager/resources/fs/';
			$postedFiles = array(
				array(
					'error' => 1,
					'tmp_name' => $from . 'DontRemove.txt',
					'name' => 'DontRemove.txt'
				)
			);
			$this->assertFalse(File::upload($postedFiles, 'test/two'), "Location name: {$adapter}\n" . '{:message}');
			File::reset();
		}
	}

	public function testRm() {
		foreach ($this->_adapters as $adapter) {
			File::$location = $adapter;
			$this->assertFalse(File::rm('test', false), "Location name: {$adapter}\n" . '{:message}');
			$this->assertTrue(File::rm('test', true), "Location name: {$adapter}\n" . '{:message}');
			File::reset();
		}
	}

}

?>