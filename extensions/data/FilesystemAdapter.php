<?php

namespace li3_filemanager\extensions\data;

/**
 * Filesystem operations adapter
 * On instantiating we pass location path to constructor
 * Then constructor does `chdir()` so all other actions take predifined location
 * as a root. All actions are named by UNIX bash command names
 * This object abstract default PHP functions so we can perform recursive copy and remove
 */

class FilesystemAdapter {
	
	/**
	 * Change current dir to one defined in location passed as config array
	 * @param array $config 
	 */
	public function __construct(array $config = array()) {
		chdir($config['path']);
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
	 * @return mixed (boolean FALSE or array of paths)
	 */
	public function ls($filter) {
		if ($glob = glob($filter)) {
			return $glob;
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
			$parrent($filter);
			if (is_dir($parrent($filter))) {
				return array();
			} else {
				return FALSE;
			}
		}
	}
	
	/**
	 * Make new directory on passed path
	 * Path is relative ro root defined by constructor
	 * @param string $name
	 * @return boolean
	 */
	public function mkdir($name) {
		if (mkdir($name)) {
			return TRUE;
		} else {
			return FALSE;
		}
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
			if (copy($src, $dst)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else if(is_dir($src)) {
			mkdir($dst);
			$content = glob($src.'/*');
			foreach($content as $path) {
				$splitSrc = explode('/', $path);
				$splitDst = explode('/', $dst);
				$t = array(
					'src' => $splitSrc,
					'dst' => $splitDst
				);
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
		if (rename($from, $to)) {
			return TRUE;
		} else {
			return FALSE;
		}
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
				if (rmdir($path)) {
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				if (unlink($path)) {
					return TRUE;
				} else {
					return FALSE;
				}
			}
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
			$content = $this->ls($path.'/*');
			foreach ($content as $file) {
				$this->rmR($file);
			}
			if ($this->rm($path)) {
				return TRUE;
			}
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