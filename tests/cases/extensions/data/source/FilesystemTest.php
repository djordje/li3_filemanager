<?php

namespace li3_filemanager\tests\cases\extensions\data\source;

use li3_filemanager\extensions\data\Locations;
use lithium\core\Libraries;

class FilesystemTest extends \lithium\test\Unit {

	protected $_adapter;
	
	protected $_li3_filemanager;

	protected function _init() {
		parent::_init();
		Locations::add('filesystemTest', array(
			'adapter' => 'Filesystem',
			'location' => LITHIUM_APP_PATH.'/libraries/li3_filemanager/resources/fs'
		));
		$this->_adapter = Locations::get('filesystemTest');
		$this->_li3_filemanager = Libraries::path('li3_filemanager\\', array('dirs' => true));
	}

	public function testInit() {
		$this->assertNull($this->_adapter->_init());
	}

	public function testInitialRead() {
		$result = $this->_adapter->ls('*', true);
		$this->assertTrue(is_array($result));
	}

	public function testDirectoryCreation() {
		$result = $this->_adapter->mkdir('test');
		$this->assertTrue($result);
		if ($result) {
			$this->expectException();
			$this->assertFalse($this->_adapter->mkdir('test'));
			$this->assertTrue($this->_adapter->mkdir('test/1'));
			$this->assertTrue($this->_adapter->mkdir('test/1/first'));
			$this->assertTrue($this->_adapter->mkdir('test/two'));
			$this->expectException();
			$this->assertFalse($this->_adapter->mkdir('notExists/1'));
		}
	}

	public function testMove() {
		$move = $this->_adapter->mv('test/1', 'test/one');
		$this->assertTrue($move);
	}

	public function testCopy() {
		$this->assertTrue($this->_adapter->cp('test/one', 'test/tree'));
		$this->assertTrue($this->_adapter->cp('DontRemove.txt', 'test/DontRemove.txt'));
		$this->assertFalse($this->_adapter->cp('test/notExist', 'test/four'));
	}

	public function testRead() {
		$this->assertFalse($this->_adapter->ls('test/four/*'));

		$result = $this->_adapter->ls('test/*', true);
		$expected = array('test/DontRemove.txt', 'test/one', 'test/tree', 'test/two');
		$this->assertEqual($expected, $result);

		$result = $this->_adapter->ls('test/*');
		$this->assertTrue(is_array($result));
		$this->assertTrue(is_array($result['dirs']));
		$this->assertTrue(is_array($result['files']));
	}

	public function testUploading() {
		$tmp_name = $this->_li3_filemanager . '/resources/fs/test/DontRemove.txt';
		$postedFiles = array(
			array(
				'error' => 0,
				'tmp_name' => $tmp_name,
				'name' => 'DontRemove.txt'
			)
		);
		$this->assertTrue($this->_adapter->upload($postedFiles, 'test/tree'));

		$postedFiles = array(
			array(
				'error' => 1,
				'tmp_name' => null,
				'name' => null
			)
		);
		$this->assertFalse($this->_adapter->upload($postedFiles, 'test/tree'));
	}

	public function testRemove() {
		$this->assertFalse($this->_adapter->rm('test/notExist'));
		$this->assertFalse($this->_adapter->rm('test'));
		$this->assertTrue($this->_adapter->rm('test/two'));
		$this->assertTrue($this->_adapter->rmR('test'));
	}

}

?>