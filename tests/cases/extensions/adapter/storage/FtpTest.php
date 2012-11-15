<?php

/**
 * @copyright Copyright 2012, Djordje Kovacevic (http://djordjekovacevic.com)
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_filemanager\tests\cases\extensions\adapter\storage;

use li3_filemanager\extensions\storage\Locations;
use lithium\core\Libraries;

class FtpTest extends \lithium\test\Unit {
	
	protected $_timestamp;
	protected $_location;
	protected  $_tmp_dir;
	
	protected function _init() {
		parent::_init();
		$this->_timestamp = time();
		$this->_tmp_dir = "{$this->_timestamp}_test";
	}
	
	public function skip() {
		$this->skipIf(
			!in_array('test-ftp', Locations::get()),
			'You don\'t have <code>test-ftp</code> location setup!'
		);
		$this->skipIf(
			!is_object($this->_location = Locations::get('test-ftp')),
			'Adapter not initialized!'
		);
	}
	
	public function testInit() {
		$this->assertNull($this->_location->_init());
	}
	
	public function testdirectoryCreation() {
		$this->assertTrue($this->_location->mkdir($this->_tmp_dir));
		$this->assertTrue($this->_location->mkdir($this->_tmp_dir . '/Test_1/Sub'));
		$this->expectException();
		$this->assertFalse($this->_location->mkdir(
			$this->_tmp_dir . '/Test_2/Sub',
			array('recursive' => false)
		));
	}
	
	public function testRead() {
		$expected = array(
			array(
				'name' => 'Test_1',
				'dir' => true,
				'url' => "{$this->_location->_config['url']}/{$this->_tmp_dir}/Test_1",
				'path' => "/{$this->_tmp_dir}",
				'size' => null,
				'mode' => null
			)
		);
		$this->assertEqual($expected, $this->_location->ls($this->_tmp_dir));
		$this->assertFalse($this->_location->ls("{$this->_tmp_dir}/Test_99"));
	}
	
	public function testCopy() {
		$this->assertTrue($this->_location->copy("{$this->_tmp_dir}/Test_1", "{$this->_tmp_dir}/Test_2"));
	}
	
	public function testMove() {
		$this->assertTrue($this->_location->move("{$this->_tmp_dir}/Test_2", "{$this->_tmp_dir}/Test_3"));
	}
	
	public function testRemove() {
		$this->expectException();
		$this->assertFalse($this->_location->remove($this->_tmp_dir, array('recursive' => false)));
		$this->assertTrue($this->_location->remove($this->_tmp_dir));
	}
	
	public function testDisconnect() {
		$this->assertNull($this->_location->invokeMethod('_disconnect'));
	}
	
}

?>