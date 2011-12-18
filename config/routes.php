<?php

/**
 * Create route for li3_filemanager
 */

use lithium\net\http\Router;

Router::connect('/file/{:action}/{:args}', array(
	'controller' => 'File', 'library' => 'li3_filemanager'
));

?>