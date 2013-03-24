<?php

/**
 * @copyright Copyright 2012, Djordje Kovacevic (http://djordjekovacevic.com)
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_filemanager\tests\cases\extensions\adapter\storage;

use li3_filemanager\extensions\storage\Locations;
use lithium\core\Libraries;

class FileSystemTest extends \lithium\test\Unit {
	
	protected $_timestamp;
	protected $_location;
	protected $_tmp_dir;
	
	protected function _init() {
		parent::_init();
		
		$this->_timestamp = time();
		
		$this->_tmp_dir  = Libraries::path('li3_filemanager\\', array('dirs' => true));
		$this->_tmp_dir .= "/resources/tmp/{$this->_timestamp}_test";
		
		Locations::add('test', array(
			'adapter' => 'FileSystem',
			'url' => 'http://example.com/tmp',
			'location' => $this->_tmp_dir
		));
		
		$this->_location = Locations::get('test');
	}

	public function skip() {
		$this->skipIf(!is_object($this->_location), 'Adapter not initialized');
		$this->skipIf(
			$this->_location->_config['adapter'] !== 'FileSystem',
			'Adapter should be `FileSystem`.'
		);
		$this->skipIf(
			$this->_location->_config['location'] !== $this->_tmp_dir,
			"Location should be `li3_filemanage/resources/tmp/{$this->_timestamp}_test`."
		);
		$this->skipIf(
			!is_writable(Libraries::path('li3_filemanager\\', array('dirs' => true)) . '/resources'),
			'Test location not writable!<br />' .
			'Check does `li3_filemanager/resources/tmp` directorty exists if not create it.<br />' .
			'On *nix OS-es you should <code>$ chmod -R 0777 libraries/li3_filemanager/resources</code>'
		);
		$this->skipIf(!mkdir($this->_tmp_dir), 'Couldn\'t create directory for further testing!');
		$this->skipIf(
			!file_put_contents("{$this->_tmp_dir}/test.txt", "This is test data\n"),
			'Couldn\'t write test file!'
		);
	}
	
	public function testDirectoryCreation() {
		$this->assertTrue($this->_location->mkdir('Test_1/first'));
		
		$this->expectException();
		$this->assertFalse($this->_location->mkdir('Test_2/first', array('recursive' => false)));
		
		$this->assertTrue($this->_location->mkdir('Test_2', array('recursive' => false)));
		
		$this->assertFalse($this->_location->mkdir('Test_1'));
	}
	
	public function testRead() {
		if(substr(strtoupper(PHP_OS), 0, 3) === 'WIN') {
			$expected = 'expected_win';
		} else {
			$expected = 'expected_nix';
		}
		$expected_win = array(
			array(
				'name' => 'test.txt',
				'dir' => false,
				'url' => 'http://example.com/tmp/test.txt',
				'path' => '/',
				'size' => 18,
				'mode' => '0666'
			),
			array(
				'name' => 'Test_1',
				'dir' => true,
				'url' => 'http://example.com/tmp/Test_1',
				'path' => '/',
				'size' => null,
				'mode' => '0777'
			),
			array(
				'name' => 'Test_2',
				'dir' => true,
				'url' => 'http://example.com/tmp/Test_2',
				'path' => '/',
				'size' => null,
				'mode' => '0777'
			)
		);
		$expected_nix = array(
			array(
				'name' => 'test.txt',
				'dir' => false,
				'url' => 'http://example.com/tmp/test.txt',
				'path' => '/',
				'size' => 18,
				'mode' => '0664'
			),
			array(
				'name' => 'Test_1',
				'dir' => true,
				'url' => 'http://example.com/tmp/Test_1',
				'path' => '/',
				'size' => null,
				'mode' => '0775'
			),
			array(
				'name' => 'Test_2',
				'dir' => true,
				'url' => 'http://example.com/tmp/Test_2',
				'path' => '/',
				'size' => null,
				'mode' => '0775'
			)
		);
		$this->assertEqual($$expected, $this->_location->ls());
		$expected_win = array(
			array(
				'name' => 'first',
				'dir' => true,
				'url' => 'http://example.com/tmp/Test_1/first',
				'path' => '/Test_1',
				'size' => null,
				'mode' => '0777'
			)
		);
		$expected_nix = array(
			array(
				'name' => 'first',
				'dir' => true,
				'url' => 'http://example.com/tmp/Test_1/first',
				'path' => '/Test_1',
				'size' => null,
				'mode' => '0775'
			)
		);
		$this->assertEqual($$expected, $this->_location->ls('Test_1'));
		$this->assertFalse($this->_location->ls('Test_99'));
	}
	
	public function testCopy() {
		$this->assertTrue($this->_location->copy('test.txt', 'Test_1/test.txt'));
		$this->assertTrue($this->_location->copy('Test_1', 'Test_3'));
		
		$this->assertFalse($this->_location->copy('Tets_99', 'Tets_98'));
	}
	
	public function testMove() {
		$this->assertTrue($this->_location->move('Test_2', 'Test_4'));
	}
	
	public function testUpload() {
		$file = array(
			'error' => UPLOAD_ERR_OK,
			'tmp_name' => "{$this->_tmp_dir}/test.txt",
			'size' => 18,
			'type' => 'text/plain',
			'name' => 'test.txt'
		);
		$this->assertTrue($this->_location->upload($file, 'Test_1/first'));
		$this->assertFalse($this->_location->upload($file, 'Test_99'));
	}
	
	public function testRemove() {
		$this->assertTrue($this->_location->remove('test.txt'));
		
		$this->expectException();
		$this->assertFalse($this->_location->remove('Test_1', array('recursive' =>false)));
		
		$this->assertTrue($this->_location->remove($this->_tmp_dir, array('prepandLocation' => false)));
	}
	
}

?>