<?php

/**
 * @copyright Copyright 2012, Djordje Kovacevic (http://djordjekovacevic.com)
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_filemanager\extensions\storage;

use lithium\util\Inflector;

/**
 * The `Location` class is base class for interacting with `li3_filemanager` adapters.
 * This class enable method filtering, because adapters doesn't implement this feature.
 * 
 * You can, and should in some cases, extend this class. For examle if you want to use
 * different named location you shoud extend this class and override `$_location` propery.
 */
class Location extends \lithium\core\StaticObject {
	
	/**
	 * Location to initialize
	 * @var string
	 */
	public static $_location = 'default';
	
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
		'locations' => 'li3_filemanager\extensions\storage\Locations'
	);
	
	/**
	 * @var boolean
	 */
	protected static $_initialized = false;
	
	/**
	 * Initialize location
	 * Apply method filters
	 */
	public static function __init() {
		static::config();
		static::applyFilter('mkdir', function($self, $params, $chain) {
			$name = explode('/', $params['name']);
			foreach ($name as $i => $s) {
				$name[$i] = Inflector::slug($s);
			}
			$params['name'] = join('/', $name);
			return $chain->next($self, $params, $chain);
		});
		static::applyFilter('upload', function($self, $params, $chain) {
			$file = explode('.', $params['file']['name']);
			$end = count($file) - 1;
			$extension = $file[$end];
			unset($file[$end]);
			$name = join($file);

			$params['file']['name'] = Inflector::slug($name) . ".{$extension}";
			return $chain->next($self, $params, $chain);
		});
		static::applyFilter('copy', function($self, $params, $chain) {
			$source = trim($params['source'], '/');
			$dest = trim($params['dest'], '/');
			if (substr($dest, 0, strlen($source)) === $source) {
				return false;
			}
			return $chain->next($self, $params, $chain);
		});
		static::applyFilter('move', function($self, $params, $chain) {
			$source = trim($params['oldname'], '/');
			$dest = trim($params['newname'], '/');
			if (substr($dest, 0, strlen($source)) === $source) {
				return false;
			}
			return $chain->next($self, $params, $chain);
		});
	}

	/**
	 * Initialize dependecies
	 */
	public static function config() {
		if (!static::$_initialized) {
			$locations = static::$_classes['locations'];
			static::$_instances['adapter'] = $locations::get(static::$_location);
			static::$_initialized = true;
		}
	}
	
	/**
	 * Call same method on adapter object
	 * 
	 * @param string $location
	 * @filter This method can be filtered.
	 */
	public static function ls($location = null) {
		$params = compact('location');
		$adapter = static::$_instances['adapter'];
		$callback = function($self, $params) use($adapter) {
			return $adapter->ls($params['location']);
		};
		return static::_filter(__FUNCTION__, $params, $callback);
	}
	
	/**
	 * Call same method on adapter object
	 * 
	 * @param string $name
	 * @param array $options
	 * @filter This method can be filtered.
	 */
	public static function mkdir($name, array $options = array()) {
		$params = compact('name', 'options');
		$adapter = static::$_instances['adapter'];
		$callback = function($self, $params) use($adapter) {
			return $adapter->mkdir($params['name'], $params['options']);
		};
		return static::_filter(__FUNCTION__, $params, $callback);
	}
	
	/**
	 * Call same method on adapter object
	 * 
	 * @param array $file
	 * @param string $dest
	 * @param array $options
	 * @filter This method can be filtered.
	 */
	public static function upload(array $file, $dest, array $options = array()) {
		$params = compact('file', 'dest', 'options');
		$adapter = static::$_instances['adapter'];
		$callback = function($self, $params) use($adapter) {
			return $adapter->upload($params['file'], $params['dest'], $params['options']);
		};
		return static::_filter(__FUNCTION__, $params, $callback);
	}
	
	/**
	 * Call same method on adapter object
	 * 
	 * @param string $source
	 * @param string $dest
	 * @param array $options
	 * @filter This method can be filtered.
	 */
	public static function copy($source, $dest, array $options = array()) {
		$params = compact('source', 'dest', 'options');
		$adapter = static::$_instances['adapter'];
		$callback = function($self, $params) use($adapter) {
			return $adapter->copy($params['source'], $params['dest'], $params['options']);
		};
		return static::_filter(__FUNCTION__, $params, $callback);
	}
	
	/**
	 * Call same method on adapter object
	 * 
	 * @param string $oldname
	 * @param string $newname
	 * @param array $options
	 * @filter This method can be filtered.
	 */
	public static function move($oldname, $newname) {
		$params = compact('oldname', 'newname');
		$adapter = static::$_instances['adapter'];
		$callback = function($self, $params) use($adapter) {
			return $adapter->move($params['oldname'], $params['newname']);
		};
		return static::_filter(__FUNCTION__, $params, $callback);
	}
	
	/**
	 * Call same method on adapter object
	 * 
	 * @param string $name
	 * @param array $options
	 * @filter This method can be filtered.
	 */
	public static function remove($name, array $options = array()) {
		$params = compact('name', 'options');
		$adapter = static::$_instances['adapter'];
		$callback = function($self, $params) use($adapter) {
			return $adapter->remove($params['name'], $params['options']);
		};
		return static::_filter(__FUNCTION__, $params, $callback);
	}
	
}

?>