<?php

namespace li3_filemanager\extensions\data\source;

/**
 * Filesystem data source/adapter
 * _init() `chdir()` to defined location so all other actions take predifined location
 * as a root. All actions are named by UNIX bash command names
 * This object abstract default PHP functions so we can perform recursive copy and remove
 */
class Filesystem extends \lithium\core\Object {
	
	/**
	 * Change current dir to one defined in location passed as config array
	 */
	protected function _init() {
		parent::_init();
		chdir($this->_config['location']);
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
		$output = array(
			'dirs' => array(),
			'files' => array()
		);
		foreach ($input as $path) {
			$meta = array('path' => null, 'name' => null, 'mode' => null, 'size' => null);
			$meta['path'] = $path;
			$meta['name'] = basename($path);
			$meta['mode'] = substr(sprintf('%o', fileperms($path)), -4);
			if (is_dir($path)) {
				$output['dirs'][] = $meta;
			} else {
				$meta['size'] = filesize($path);
				$output['files'][] = $meta;
			}
		}
		return $output;
	}

	/**
	 * Pass filter to ls and you'll get file and dir list that mach pattern or FALSE
	 * This method is relative to currrent dir (defined in location passed to constructor)
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
		if ($glob = glob($ls)) {
			if ($raw) {
				return $glob;
			}
			return $this->_prettifyOutput($glob);
		} else {
			$parrent = function($path) {
				$split = explode('/', $path);
				$end = count($split) - 1;
				unset ($split[$end]);
				if (empty ($split)) {
					return '.';
				} else {
					return join('/', $split);
				}
			};
			$parrent($ls);
			if (is_dir($parrent($ls))) {
				return array();
			} else {
				return FALSE;
			}
		}
	}
	
	/**
	 * Make new directory on passed path
	 * Path is relative to root defined by constructor
	 * @param string $name
	 * @return boolean
	 */
	public function mkdir($name) {
		return mkdir($name);
	}
	
	/**
	 * This method expand default PHP copy to enable recursive copy
	 * Passed source and destination is relative to root defined by constructor
	 * @param string $src
	 * @param string $dst
	 * @return boolean
	 */
	public function cp($src, $dst) {
		if (is_file($src)) {
			return copy($src, $dst);
		} else if(is_dir($src)) {
			mkdir($dst);
			$content = glob($src.'/*');
			foreach($content as $path) {
				$splitSrc = explode('/', $path);
				$splitDst = explode('/', $dst);
				$subDst = join('/', $splitDst + $splitSrc);
				$this->cp($path, $subDst);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * This is wrapper for default PHP rename function
	 * We use this method for rename and move of a file or dir
	 * Passed paths are relative to root defined by constructor
	 * @param string $from
	 * @param string $to
	 * @return boolean
	 */
	public function mv($from, $to) {
		return rename($from, $to);
	}
	
	/**
	 * Pass path of file or empty dir as a param and remove it
	 * This method implements combination of PHP functions `unlink()` and `rmdir()`
	 * Passed paths are relative to root defined by constructor
	 * @param string $path
	 * @return boolean
	 */
	public function rm($path) {
		if (file_exists($path)) {
			if (is_dir($path)) {
				$glob = $this->ls($path.'/*');
				if (empty ($glob)) {
					return rmdir($path);
				} else {
					return FALSE;
				}
			} else {
				return unlink($path);
			}
		} else {
			return false;
		}
	}

	/**
	 * This method enables recursive remove (removing dir with content)
	 * This is wrapper for `$this->rm()` but it loop trough content and
	 * remove things in right order
	 * Passed paths are relative to root defined by constructor
	 * @param string $path
	 * @return boolean
	 */
	public function rmR($path) {
		if (is_dir($path)) {
			$content = $this->ls("{$path}/*");
			foreach ($content as $file) {
				$this->rmR($file);
			}
			return $this->rm($path);
		} else {
			$this->rm($path);
		}
	}
	
	/**
	 * This method move uploaded files to specified destination
	 * We pass array of posted files and desired destination and this method
	 * loop trough array and move files by PHP `move_uploaded_files()` function
	 * Passed destination path is relative to root defined by constructor
	 * @param array $postedFiles
	 * @param string $dst
	 * @return boolean
	 */
	public function upload($postedFiles, $dst) {
		foreach ($postedFiles as $file) {
			if ($file['error'] == 0 && is_dir($dst)) {
				move_uploaded_file($file['tmp_name'], $dst.'/'.$file['name']);
			} else {
				return FALSE;
			}
		}
		return TRUE;
	}
	
}

?>