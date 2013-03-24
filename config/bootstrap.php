<?php

use li3_filemanager\extensions\storage\Locations;

Locations::add('default', array(
	'adapter' => 'FileSystem',
	'location' => LITHIUM_APP_PATH . '/webroot/img',
	'url' => 'http://localhost/dev/img'
));

?>