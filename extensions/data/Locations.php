<?php

namespace li3_filemanager\extensions\data;

use lithium\core\Libraries;

class Locations extends \lithium\core\Adaptable {
	
	protected static $_configurations = array();
	
	protected static $_adapters = 'data.source';
	
	public static function add($name, array $config = array()) {
		$defaults = array(
			'adapter'  => null,
			'location' => LITHIUM_APP_PATH.'/libraries/li3_filemanager/resources/fs',
		);
		return static::$_configurations[$name] = $config + $defaults;
	}

	
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