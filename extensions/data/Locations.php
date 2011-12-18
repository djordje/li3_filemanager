<?php

namespace li3_filemanager\extensions\data;

class Locations {
	
	/**
	 * Place holder for storing named configurations
	 * @var array
	 */
	protected static $_configurations = array();
	
	/**
	 * This static method give us abillity to create new configurations from bootstrap or any
	 * other place in our application
	 * @param string $name - name for configuration, we access this configuration by this name
	 * @param array $config - pass configuration (just 'path' for now, because we always
	 * use `'li3_filemanager\extensions\data\FilesystemAdapter'` adapter
	 * @example
	 * {{{
	 *		use \li3_filemanager\extensions\data\Locations;
	 *		Locations::add('img', array(
	 *			'path' => LITHIUM_APP_PATH.'/webroot/images'
	 *		));
	 * }}}
	 */
	public static function add($name, array $config = array()) {
		$defaults = array(
			'path' => LITHIUM_APP_PATH.'/webroot/img',
			'adapter' => 'li3_filemanager\extensions\data\FilesystemAdapter'
		);
		self::$_configurations[$name] = $config + $defaults;
	}
	
	/**
	 * Get configuration by name
	 * If configuration exists this method return preconfigured adapter object
	 * @param string $name
	 * @return mixed (object or boolean [FALSE])
	 */
	public static function get($name) {
		if (isset (self::$_configurations[$name])) {
			return new self::$_configurations[$name]['adapter'](array(
				'path' => self::$_configurations[$name]['path']
			));
		}
		return FALSE;
	}
	
}

?>