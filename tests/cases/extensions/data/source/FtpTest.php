<?php

namespace li3_filemanager\tests\cases\extensions\data\source;

use li3_filemanager\extensions\data\Locations;
use lithium\core\Libraries;

class FtpTest extends \lithium\test\Unit {

	protected $_adapter;
	
	protected $_li3_filemanager;

	protected function _init() {
		parent::_init();
		$this->_li3_filemanager = Libraries::path('li3_filemanager\\', array('dirs' => true));
	}


	public function skip() {
		$locations = Locations::get();

		$filesystemLocationName = null;
		$filesystemLocation = array();

		$this->skipIf(!$locations, 'You dont have any locations to test');

		foreach ($locations as $location) {
			$config = Locations::get($location, array('config' => true));
			if ($config['adapter'] === 'Ftp' && !$config['url']) {
				switch ($location) {
					case 'test':
						$filesystemLocationName = $location;
						$filesystemLocation = $config;
						break;
					case 'default':
						if (
							$filesystemLocationName == null ||
							$filesystemLocationName !== 'test'
						) {
							$filesystemLocationName = $location;
							$filesystemLocation = $config;
						}
						break;
					default:
						if (empty ($filesystemLocation)) {
							$filesystemLocationName = $location;
							$filesystemLocation = $config;
						}
						break;
				}
			}
		}

		$this->skipIf(
			empty ($filesystemLocation),
			'You dont have any location that use `Ftp` adapter, or you have `url` key configured'
		);
		$this->_adapterName = $filesystemLocationName;
		$this->_adapter = Locations::get($filesystemLocationName);
	}

	public function testConnecting() {
		$this->assertNull($this->_adapter->_init());
	}

	public function testInitialRead() {
		$this->assertTrue(is_array($this->_adapter->ls('*')));
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

	public function testUpload() {
		//$from = LITHIUM_APP_PATH . '/libraries/li3_filemanager/resources/fs/';
		$tmp_name = $this->_li3_filemanager . '/resources/fs/DontRemove.txt';
		$postedFiles = array(
			array(
				'error' => 0,
				'tmp_name' => $tmp_name,
				'name' => 'DontRemove.txt'
			)
		);
		$this->assertTrue($this->_adapter->upload($postedFiles, 'test', false));

		$postedFiles = array(
			array(
				'error' => 1,
				'tmp_name' => null,
				'name' => null
			)
		);
		$this->assertFalse($this->_adapter->upload($postedFiles, 'test/two', false));
	}

	public function testCopy() {
		$this->expectException();
		$this->assertTrue($this->_adapter->cp('test/one', 'test/tree'));
		$this->expectException();
		$this->assertFalse($this->_adapter->cp('test/notExist', 'test/four'));
		$this->assertTrue(
			$this->_adapter->cp('test/DontRemove.txt', 'test/Readme.txt')
		);
	}

	public function testRead() {
		$this->assertFalse($this->_adapter->ls('test/four/*'));

		$result = $this->_adapter->ls('test/*', true);
		$expected = array(
			'test/DontRemove.txt',
			'test/Readme.txt',
			'test/one',
			'test/tree',
			'test/two'
		);
		$this->assertEqual($expected, $result);

		$result = $this->_adapter->ls('test/*');
		$expected = array(
			'dirs' => array(
				array(
					'path' => 'test/one',
					'name' => 'one',
					'mode' => null,
					'size' => null,
					'url' => null
				),
				array(
					'path' => 'test/tree',
					'name' => 'tree',
					'mode' => null,
					'size' => null,
					'url' => null
				),
				array(
					'path' => 'test/two',
					'name' => 'two',
					'mode' => null,
					'size' => null,
					'url' => null
				),
			),
			'files' => array(
				array(
					'path' => 'test/DontRemove.txt',
					'name' => 'DontRemove.txt',
					'mode' => null,
					'size' => 75,
					'url' => null
				),
				array(
					'path' => 'test/Readme.txt',
					'name' => 'Readme.txt',
					'mode' => null,
					'size' => 75,
					'url' => null
				)
			)
		);
		$this->assertEqual($expected, $result);

		$result = $this->_adapter->ls('test/*.txt');
		$expected = array(
			'dirs' => array(),
			'files' => array(
				array(
					'path' => 'test/DontRemove.txt',
					'name' => 'DontRemove.txt',
					'mode' => null,
					'size' => 75,
					'url' => null
				),
				array(
					'path' => 'test/Readme.txt',
					'name' => 'Readme.txt',
					'mode' => null,
					'size' => 75,
					'url' => null
				)
			)
		);
		$this->assertEqual($expected, $result);
	}

	public function testRemove() {
		$this->expectException();
		$this->assertFalse($this->_adapter->rm('test'));
		$this->assertTrue($this->_adapter->rm('test/Readme.txt'));
		$this->assertTrue($this->_adapter->rmR('test/DontRemove.txt'));
		$this->assertTrue($this->_adapter->rmR('test'));
	}

	public function testDisconnecting() {
		$this->assertNull($this->_adapter->__destruct());
	}

}

?>