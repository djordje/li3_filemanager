<?php

namespace li3_filemanager\models;

class File extends \lithium\core\StaticObject {

	/**
	 * Stores object instances for internal use.
	 * @var array
	 */
	protected static $_instances = array();

	/**
	 * Class dependencies.
	 * @var array
	 */
	protected static $_classes = array(
		'locations' => 'li3_filemanager\extensions\data\Locations'
	);
	
	/**
	 * Location that will be used for `Locations::get($location)`
	 * @var string
	 */
	public static $location = 'default';

	/**
	 * @var boolean
	 */
	protected static $_initialized = false;
	
	/**
	 * If not initialized create adapter with defined location and return object
	 * Otherwise return instantiated object
	 * @return object
	 */
	protected static function _init() {
		if (!static::$_initialized) {
			$locations = static::$_classes['locations'];
			static::$_instances['adapter'] = $locations::get(static::$location);
			static::$_initialized = true;
		}
		return static::$_instances['adapter'];
	}

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
		$adapter = static::_init();
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
		$adapter = static::_init();
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
		$adapter = static::_init();
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
		$adapter = static::_init();
		return $adapter->mv($from, $to);
	}

	/**
	 * This method call {remove - `$adapter->rm()`} or {recursive remove `$adapter->rmR()`}
	 * @param string $path - path to be removed
	 * @param boolean $recursive - FALSE = remove, TRUE = recursive remove
	 * @return bollean
	 */
	public static function rm($path, $recursive = FALSE) {
		$adapter = static::_init();
		if ($recursive) {
			return $adapter->rmR($path);
		}
		return $adapter->rm($path);
	}

	/**
	 * This method is wrapper for same method in adapter
	 * Call it and pass param
	 * @param array $postedFiles
	 * @param string $dst
	 * @return boolean
	 */
	public static function upload($postedFiles, $dst) {
		$adapter = static::_init();
		return $adapter->upload($postedFiles, $dst);
	}

}

?>