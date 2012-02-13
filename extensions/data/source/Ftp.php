<?php

namespace li3_filemanager\extensions\data\source;

/**
 * Ftp data source/adapter
 * _init() connect to defined FTP, so all other actions take same location
 * as a root (FTP connection root). All actions are named by UNIX bash command names
 * This object abstract default PHP functions so we can perform recursive copy and remove
 */
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
		$this->_connect();
	}

	/**
	 * This method setup connection (FTP, SFTP) and prepare
	 * connection stream, then try to login.
	 */
	protected function _connect() {
		if ($this->_config['ssl']) {
			$this->_connection = ftp_ssl_connect(
				$this->_config['host'],
				$this->_config['port'],
				$this->_config['timeout']
			);
		} else {
			$this->_connection = ftp_connect(
				$this->_config['host'],
				$this->_config['port'],
				$this->_config['timeout']
			);
		}
		if ($this->_connection) {
			$login = ftp_login(
				$this->_connection,
				$this->_config['username'],
				$this->_config['password']
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
		$disconnect = ftp_close($this->_connection);
		if ($disconnect) {
			$this->_connected = false;
		}
	}

	/**
	 * This method emulate PHP's is_dir() in FTP enviorment
	 * @param string $path
	 * @return mixed (boolean false or directory content array)
	 */
	protected function _isDir($path) {
		if ($glob = ftp_nlist($this->_connection, $path)) {
			if (in_array('.', $glob) && in_array('..', $glob)) {
				return $glob;
			}
		}
		return false;
	}

	/**
	 * Disconnect connection stream on destruction
	 */
	public function __destruct() {
		$this->_disconnect();
	}

	/**
	 * This method prettify data array passed to `$input` param
	 * We pass array with files and directories names
	 * This method sort it in arrays (files, dirs)
	 * Each file or directory is array of meta data (path, name, mode, size)
	 * @param array $input
	 * @return array
	 */
	protected function _prettifyOutput($input) {
		$output = array('dirs' => array(), 'files' => array());
		$conn = $this->_connection;
		$config = $this->_config;
		$createMeta = function($path, $dir = true) use ($conn, $config) {
			$meta = array('path' => null, 'name' => null, 'mode' => null, 'size' => null, 'url' => null);
			$meta['path'] = $path;
			$meta['name'] = basename($path);
			if (!$dir) {
				$meta['size'] = ftp_size($conn, $path);
				if ($url = $config['url']) {
					if (substr($url, -1) != '/') {
						$url .= '/';
					}
					$meta['url'] = $url . $path;
				}
			}
			return $meta;
		};
		foreach ($input as $path) {
			if ($this->_isDir($path)) {
				$output['dirs'][] = $createMeta($path);
			} else {
				$output['files'][] = $createMeta($path, false);
			}
		}
		unset ($conn);
		return $output;
	}

	/**
	 * Pass filter to ls and you'll get file and dir list that mach pattern or FALSE
	 * This method is relative to FTP connection root
	 * @example
	 *
	 *		ls('*')         ---> everything in root dir
	 *		ls('li/*')      ---> everything in root/li3 dir
	 *		ls('img/*.jpg') ---> every file with `.jpg` extension in root/img dir
	 * @param string $filter
	 * @param boolean $raw
	 *			- if true method return folder content as array of names
	 *			- if false method return array from `_prettifyOutput()` method
	 * @return mixed (boolean FALSE or array of paths)
	 */
	public function ls($ls, $raw = false) {
		$input = explode('/', $ls);
		$end = count($input) - 1;
		$filter = $input[$end];
		unset ($input[$end]);
		$path = join('/', $input);
		if (!$path) {
			$path = '.';
		}
		$parrent = ($path === '.')? null : "{$path}/";
		$glob = $this->_isDir($path);
		if (!$glob) {
			return false;
		}
		$output = array();
		foreach ($glob as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			} else {
				if ($filter === '*') {
					$output[] = $parrent.$file;
				} else {
					$start = strpos($filter, '.');
					$pattern = '/' . substr($filter, $start) . '/';
					if (preg_match($pattern, $file)) {
						$output[] = $parrent.$file;
					}
				}
			}
		}
		if ($raw) {
			return $output;
		} else {
			return $this->_prettifyOutput($output);
		}
	}

	/**
	 * Make new directory on passed path
	 * Path is relative to root of FTP connection
	 * @param string $name
	 * @return boolean
	 */
	public function mkdir($name) {
		return ftp_mkdir($this->_connection, $name);
	}

	/**
	 * This method emulate `copy()` on FTP
	 * FTP doesn't support copy, but we can emulate this (of course it is lot slower then native)
	 * This method download files to TEMP and upload them in new location.
	 * Directories creation use `$this->mkdir()` method.
	 * Passed source and destination is relative to FTP connection root
	 * @param string $src
	 * @param string $dst
	 * @return boolean
	 */
	public function cp($src, $dst) {
		$temp = sys_get_temp_dir();
		if ($this->_isDir($src)) {
			if ($this->mkdir($dst)) {
				$content = $this->ls("{$src}/*", true);
				foreach($content as $path) {
					$splitSrc = explode('/', $path);
					$splitDst = explode('/', $dst);
					$subDst = join('/', $splitDst + $splitSrc);
					$this->cp($path, $subDst);
				}
				return TRUE;
			}
		} else {
			$name = basename($src);
			$get = ftp_get($this->_connection, "{$temp}/{$name}", $src, FTP_BINARY);
			if ($get) {
				$put = ftp_put($this->_connection, $dst, "{$temp}/{$name}", FTP_BINARY);
			}
			if ($put) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * This is wrapper for default PHP ftp_rename function
	 * We use this method for rename and move of a file or dir
	 * Passed paths are relative to FTP connection root
	 * @param string $from
	 * @param string $to
	 * @return boolean
	 */
	public function mv($from, $to) {
		return ftp_rename($this->_connection, $from, $to);
	}

	/**
	 * Remove file or empty directory
	 * @param string $path
	 * @return boolean
	 */
	public function rm($path) {
		if ($this->_isDir($path)) {
			return ftp_rmdir($this->_connection, $path);
		} else {
			return ftp_delete($this->_connection, $path);
		}
	}

	/**
	 * Recursive remove (enable removing directory with content)
	 * @param string $path
	 * @return boolean
	 */
	public function rmR($path) {
		if ($this->_isDir($path)) {
			$content = $this->ls("{$path}/*", true);
			foreach ($content as $file) {
				$this->rmR($file);
			}
			return $this->rm($path);
		} else {
			return $this->rm($path);
		}
	}

	/**
	 * This method move uploaded files to specified destination on FTP
	 * We pass array of posted files and desired destination and this method
	 * loop trough array and upload files by PHP `ftp_put()` function, then remove temp files
	 * This is emulation of `move_uploaded_files()` in Filesystem adapter
	 * Passed destination path is relative to FTP connection root
	 * @param array $postedFiles
	 * @param string $dst
	 * @param boolean $removeAfterUpload
	 *			- Because we emulate PHP's `move_after_upload()` function
	 *			we have to allow override of temp file unlink for testing purposes
	 * @return boolean
	 */
	public function upload($postedFiles, $dst, $removeAfterUpload = true) {
		foreach ($postedFiles as $file) {
			if ($file['error'] == 0 && $this->_isDir($dst)) {
				ftp_put($this->_connection, "{$dst}/{$file['name']}", $file['tmp_name'], FTP_BINARY);
				if ($removeAfterUpload) {
					unlink($file['name']);
				}
			} else {
				return FALSE;
			}
		}
		return TRUE;
	}

}

?>