<?php

/**
 * li3_filemanager bootstrap
 * Define development locations
 */

use li3_filemanager\extensions\data\Locations;

Locations::add('default', array(
	'adapter' => 'Filesystem'
));

?>