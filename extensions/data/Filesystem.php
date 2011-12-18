<?php

namespace li3_filemanager\extensions\data;

/**
 * Filesystem model
 * This static object enable interaction with filesystem
 */
class Filesystem {
	
	/**
	 * Location that will be used when instantiating adapter
	 * You can change it from controller
	 * @var string
	 */
	public static $location = 'default';
	
	/**
	 * This method call same method in adapter and pass filter to it
	 * If receive answare format data in array
	 * @param string $ls
	 * @return mixed(
	 *		array - if directory have content
	 *		empty array - if directory is empty
	 *		boolean FALSE - if directorty does not exists
	 * )
	 */
	public static function ls($ls = '*') {
		$adapter = Locations::get(self::$location);
		if ($content = $adapter->ls($ls)) {
			$output = array(
				'dirs' => array(),
				'files' => array()
			);
			foreach ($content as $path) {
				if (is_dir($path)) {
					$output['dirs'][$path] = array(
						'path' => $path,
						'name' => basename($path),
						'realpath' => realpath($path),
						'mode' => substr(sprintf('%o', fileperms($path)), -4)
					);
				} else {
					$output['files'][$path] = array(
						'path' => $path,
						'name' => basename($path),
						'realpath' => realpath($path),
						'mode' => substr(sprintf('%o', fileperms($path)), -4),
						'size' => filesize($path)
					);
				}
			}
		} else {
			$output = $content;
		}
		return $output;
	}
	
	/**
	 * This method is wrapper for same mthod in adapter
	 * Call it and pass param
	 * @param string $name
	 * @return boolean
	 */
	public static function mkdir($name) {
		$adapter = Locations::get(self::$location);
		return $adapter->mkdir($name);
	}
	
	/**
	 * This method is wrapper for same mthod in adapter
	 * Call it and pass param
	 * @param string $src
	 * @param string $dst
	 * @return boolean
	 */
	public static function cp($src, $dst) {
		$adapter = Locations::get(self::$location);
		return $adapter->cp($src, $dst);
	}
	
	/**
	 * This method is wrapper for same mthod in adapter
	 * Call it and pass param
	 * @param string $from
	 * @param string $to
	 * @return boolean
	 */
	public static function mv($from, $to) {
		$adapter = Locations::get(self::$location);
		return $adapter->mv($from, $to);
	}
	
	/**
	 * This method call {remove - `$adapter->rm()`} or {recursive remove `$adapter->rmR()`}
	 * @param string $path - path to be removed
	 * @param boolean $recursive - FALSE = remove, TRUE = recursive remove
	 * @return bollean
	 */
	public static function rm($path, $recursive = FALSE) {
		$adapter = Locations::get(self::$location);
		switch ($recursive) {
			case TRUE:
				return $adapter->rmR($path);
				break;
			case FALSE:
				return $adapter->rm($path);
				break;
		}
	}
	
	/**
	 * This method is wrapper for same mthod in adapter
	 * Call it and pass param
	 * @param array $postedFiles
	 * @param string $dst
	 * @return boolean
	 */
	public static function upload($postedFiles, $dst) {
		$adapter = Locations::get(self::$location);
		return $adapter->upload($postedFiles, $dst);
	}
	
}

?>