<?php

/**
 * @copyright Copyright 2012, Djordje Kovacevic (http://djordjekovacevic.com)
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_filemanager\extensions\adapter\storage;

class Ftp extends \lithium\core\Object {
	
	/**
	 * Tell us does we successfully connected and loged in on FTP server
	 * @var boolean
	 */
	protected $_connected = false;

	/**
	 * Connection stream place holder
	 */
	protected $_connection;

	/**
	 * Connect to FTP server on initialization
	 */
	protected function _init() {
		parent::_init();
		if ($this->_config['url'] && substr($this->_config['url'], -1) === '/') {
			$this->_config['url'] = rtrim($this->_config['url'], '/');
		}
		$this->_connect();
	}

	/**
	 * This method setup connection (FTP, SFTP) and prepare
	 * connection stream, then try to login.
	 */
	protected function _connect() {
		if ($this->_config['ssl']) {
			$this->_connection = ftp_ssl_connect(
					$this->_config['host'], $this->_config['port'], $this->_config['timeout']
			);
		} else {
			$this->_connection = ftp_connect(
					$this->_config['host'], $this->_config['port'], $this->_config['timeout']
			);
		}
		if ($this->_connection) {
			$login = ftp_login(
					$this->_connection, $this->_config['username'], $this->_config['password']
			);
			if ($this->_connection) {
				$this->_connected = true;
			}
			if ($this->_config['passive']) {
				ftp_pasv($this->_connection, $this->_config['passive']);
			}
		}
	}
	
	/**
	 * This method close connection stream
	 */
	protected function _disconnect() {
		if ($this->_connected) {
			if (ftp_close($this->_connection)) {
				$this->_connected = false;
			}
		}
	}
	
	/**
	 * This method emulate PHP's is_dir() in FTP enviorment
	 * @param string $location
	 * @return boolean
	 */
	protected function _is_dir($location = null) {
		$glob = ftp_nlist($this->_connection, $location);
		if ($glob && in_array('.', $glob) && in_array('..', $glob)) {
			return true;
		}
		return false;
	}
	
	/**
	 * This method emulate PHP's file_exists() in FTP enviorment
	 * @param string $path
	 * @return boolean
	 */
	protected function _file_exists($path = null) {
		if (!$path) {
			return false;
		} elseif ($this->_is_dir($path)) {
			return true;
		} elseif (ftp_size($this->_connection, $path) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Download and upload file to new location
	 * We use this method only inside `copy()` method
	 * @param dtring $source
	 * @param string $dest
	 * @return boolean
	 */
	protected function _copy_file($source, $dest) {
		$tmp = tempnam(sys_get_temp_dir(), 'FTP');
		$get = ftp_get($this->_connection, $tmp, $source, FTP_BINARY);
		$put = false;
		if ($get) {
			$put = ftp_put($this->_connection, $dest, $tmp, FTP_BINARY);
		}
		unlink($tmp);
		return $put;
	}
	
	/**
	 * Disconnect connection stream on destruction
	 */
	public function __destruct() {
		$this->_disconnect();
	}
	
	/**
	 * List directory conntent
	 * @param string $location
	 * @return boolean
	 */
	public function ls($location = null) {
		if ($this->_config['location']) {
			if ($location === '/') {
				$location = $this->_config['location'] . $location;
			} else {
				$location = "{$this->_config['location']}/{$location}";
			}
		}
		if (!$location) {
			$location = '/';
		}
		
		if (!$this->_is_dir($location)) {
			return false;
		}
		
		$scan = array();
		
		foreach (ftp_nlist($this->_connection, $location) as $d) {
			if ($d === '.' || $d === '..') {
				continue;
			}

			
			if ($location === '/') {
				$path = $location;
				$url = "/{$d}";
			} else {
				$path = "/{$location}";
				$url = "{$path}/{$d}";
			}
			
			if (($size = ftp_size($this->_connection, $url)) < 0) {
				$size = null;
			}
			
			$scan[] = array(
				'name' => $d,
				'dir' => $this->_is_dir($url),
				'url' => "{$this->_config['url']}{$url}",
				'path' => $path,
				'size' => $size,
				'mode' => null
			);
		}
		
		return $scan;
	}

	/**
	 * Create directory
	 * @param string $name
	 * @param array $options
	 * @return boolean
	 */
	public function mkdir($name, array $options = array()) {
		$defauts = array('mode' => 0777, 'recursive' => true);
		$options += $defauts;
		$dir_created = false;
		$current_path = null;
		
		$mkdir = function($connection, $path) use($options) {
			if (@ftp_mkdir($connection, $path)) {
				if (ftp_chmod($connection, $options['mode'], $path)) {
					return true;
				}
				return false;
			}
			return false;
		};
		
		if ($options['recursive']) {
			$parts = explode('/', $name);
			foreach ($parts as $part) {
				if (!$current_path) {
					$current_path = $part;
				} else {
					$current_path .= "/{$part}";
				}
				
				if ($this->_is_dir($current_path)) {
					continue;
				}
				
				$dir_created = $mkdir($this->_connection, $current_path);
			}
			
			return $dir_created;
		}
		return $mkdir($this->_connection, $name);
	}
	
	/**
	 * Upload file to destination
	 * @param array $file
	 * @param string $dest
	 * @param array $options
	 * @return boolean
	 */
	public function upload(array $file, $dest, array $options = array()) {
		$defaults = array('overwrite' => false);
		$options += $defaults;
		
		if ($this->_config['location']) {
			$dest = "{$this->_config['location']}/{$dest}";
		}
		
		if ($file['error'] === UPLOAD_ERR_OK && $this->_is_dir($dest)) {
			$name = "{$dest}/{$file['name']}";
			if (!$options['overwrite'] && $this->_file_exists($name)) {
				return false;
			}
			$uploaded = ftp_put($this->_connection, $name, $file['tmp_name'], FTP_BINARY);
			unlink($file['tmp_name']);
			return $uploaded;
		}
		return false;
	}

	/**
	 * Copy file or directory
	 * FTP doesn't support copy natively, so we must emulate it
	 * @param string $source
	 * @param string $dest
	 * @param array $options
	 * @return boolean
	 */
	public function copy($source, $dest, array $options = array()) {	
		if ($this->_file_exists($source) && !$this->_file_exists($dest)) {
			if ($this->_is_dir($source)) {
				if ($this->mkdir($dest)) {
					$ls = $this->ls($source);
					foreach ($ls as $f) {
						$src = "{$source}/{$f['name']}";
						$dst = "{$dest}/{$f['name']}";
						if (!$this->copy($src, $dst)) {
							return false;
						}
					}
					return true;
				}
				return false;
			}
			return $this->_copy_file($source, $dest);
		}
		
		return false;
	}
	
	/**
	 * Move file or directory to another place
	 * @param string $oldname
	 * @param string $newname
	 * @return boolean
	 */
	public function move($oldname, $newname) {
		if ($this->_config['location']) {
			$oldname = "{$this->_config['location']}/{$oldname}";
			$newname = "{$this->_config['location']}/{$newname}";
		}
		
		if ($this->_file_exists($oldname) && !$this->_file_exists($newname)) {
			return ftp_rename($this->_connection, $oldname, $newname);
		}
		
		return false;
	}
	
	/**
	 * Remove file or directory
	 * @param string $name
	 * @param array $options
	 * @return boolean
	 */
	public function remove($name, array $options = array()) {
		$defaults = array('recursive' => true);
		$options += $defaults;
		
		if ($this->_is_dir($name)) {
			if ($options['recursive']) {
				$ls = $this->ls($name);
				foreach ($ls as $f) {
					if (!$this->remove("{$name}/{$f['name']}")) {
						return false;
					}
				}
			}
			return ftp_rmdir($this->_connection, $name);
		}
		return ftp_delete($this->_connection, $name);
	}
	
}

?>