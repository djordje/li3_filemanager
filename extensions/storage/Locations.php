<?php

/**
 * @copyright Copyright 2012, Djordje Kovacevic (http://djordjekovacevic.com)
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_filemanager\extensions\storage;

use lithium\core\Libraries;

/**
 * The `Locations` class is place to add named configurations, and get adapter instance
 * in `Location` class.
 */
class Locations extends \lithium\core\Adaptable {
	
	/**
	 * @var array
	 */
	protected static $_configurations = array();

	/**
	 * Libraries::locate() compatible path to adapters for this class.
	 * @var string Dot-delimited path.
	 */
	protected static $_adapters = 'adapter.storage';
	
	/**
	 * Add new named location
	 * 
	 * {{{
	 * 		Locations::add('default', array(
	 * 			'adapter' => 'Filesystem',
	 * 			'location' => LITHIUM_APP_PATH.'/webroot/img'
	 * 		));
	 * }}}
	 * 
	 * @param string $name - Location name
	 * @param array $config - pass configuration array
	 */
	public static function add($name, array $config = array()) {
		$defaults = array(
			'adapter'  => null,
			'location' => null,
			'host'     => null,
			'username' => null,
			'password' => null,
			'ssl'      => false,
			'port'     => 21,
			'timeout'  => 90,
			'passive'  => true,
			'url'      => false
		);
		return static::$_configurations[$name] = $config + $defaults;
	}
	
	/**
	 * Read the configuration or access the connections you have set up.
	 *
	 * Usage:
	 * {{{
	 * // Gets the names of all available configurations
	 * $configurations = Connections::get();
	 *
	 * // Gets the configuration array for the connection named 'db'
	 * $config = Connections::get('db', array('config' => true));
	 *
	 * // Gets the instance of the connection object, configured with the settings defined for
	 * // this object in Connections::add()
	 * $dbConnection = Connections::get('db');
	 *
	 * // Gets the connection object, but only if it has already been built.
	 * // Otherwise returns null.
	 * $dbConnection = Connections::get('db', array('autoCreate' => false));
	 * }}}
	 *
	 * @param string $name The name of the connection to get, as defined in the first parameter of
	 *        `add()`, when the connection was initially created.
	 * @param array $options Options to use when returning the connection:
	 *        - `'autoCreate'`: If `false`, the connection object is only returned if it has
	 *          already been instantiated by a previous call.
	 *        - `'config'`: If `true`, returns an array representing the connection's internal
	 *          configuration, instead of the connection itself.
	 * @return mixed A configured instance of the connection, or an array of the configuration used.
	 */
	public static function get($name = null, array $options = array()) {
		static $mockAdapter;

		$defaults = array('config' => false, 'autoCreate' => true);
		$options += $defaults;

		if ($name === false) {
			if (!$mockAdapter) {
				$class = Libraries::locate('data.source', 'Mock');
				$mockAdapter = new $class();
			}
			return $mockAdapter;
		}

		if (!$name) {
			return array_keys(static::$_configurations);
		}

		if (!isset(static::$_configurations[$name])) {
			return null;
		}
		if ($options['config']) {
			return static::_config($name);
		}
		$settings = static::$_configurations[$name];

		if (!isset($settings[0]['object'])) {
			if (!$options['autoCreate']) {
				return null;
			}
		}
		return static::adapter($name);
	}
	
}

?>