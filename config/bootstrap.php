<?php

/**
 * li3_filemanager bootstrap
 * Define development locations
 */

use li3_filemanager\extensions\data\Locations;
use lithium\core\Libraries;

Locations::add('default', array(
	'adapter' => 'Filesystem',
	'location' => Libraries::path('li3_filemanager\\', array('dirs' => true)) . '/resources/fs',
));

?>