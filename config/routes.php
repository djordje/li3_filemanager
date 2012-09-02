<?php

use lithium\net\http\Router;
use lithium\action\Response;

$fm = '/fm';

Router::connect("{$fm}/js/{:file}.{:type}", array(), function($request) {
	$req = $request->params;
	$file = dirname(__DIR__) . "/webroot/js/{$req['file']}.{$req['type']}";
	
	if (!file_exists($file)) {
		return;
	}

	return new Response(array(
		'body' => file_get_contents($file),
		'headers' => array('Content-type' => 'text/javascript'),
	));
});

Router::connect("{$fm}/mkdir/{:args}", array(
	'http:method' => 'POST',
	'library' => 'li3_filemanager',
	'controller' => 'Files',
	'action' => 'mkdir'
));

Router::connect("{$fm}/remove", array(
	'http:method' => 'POST',
	'library' => 'li3_filemanager',
	'controller' => 'Files',
	'action' => 'remove'
));

Router::connect("{$fm}/copy/{:args}", array(
	'http:method' => 'POST',
	'library' => 'li3_filemanager',
	'controller' => 'Files',
	'action' => 'copy'
));

Router::connect("{$fm}/move/{:args}", array(
	'http:method' => 'POST',
	'library' => 'li3_filemanager',
	'controller' => 'Files',
	'action' => 'move'
));

Router::connect("{$fm}/{:args}", array(
	'http:method' => 'POST',
	'library' => 'li3_filemanager',
	'controller' => 'Files',
	'action' => 'upload'
));

Router::connect("{$fm}/{:args}", array(
	'library' => 'li3_filemanager',
	'controller' => 'Files',
	'action' => 'index'
));

?>