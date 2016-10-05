<?php

// Bootstrap: Web-specific (not CLI) configuration and setup

require_once('bootstrap.php');

$router->exceptionHandler(function($e) use ($response) {
	$response->setCode(500);

	if ($_SERVER['ENV'] == 'DEV') $message = $e->getMessage() . ' ('. $e->getFile() .':'. $e->getLine() .')';
	else $message = 'Server error :(';

	Firelit\View::quickRender('error', false, array(
		'error' => 500,
		'message' => $message
	));

	exit;
});

Firelit\View::$viewFolder = __DIR__.'/views/';

Firelit\Response::$exceptOnHeaders = ($_SERVER['ENV'] == 'DEV');

// Session config

Firelit\DatabaseSessionHandler::$config = array(
	'tableName' => 'sessions', // Table where it is all stored
	'colKey' => 'key', // The key column for the unique session ID
	'colData' => 'data', // The data column for storing session data
	'colExp' => 'expires', // The datetime column for expiration date
	'expSeconds' => 604800 // 7 days
);

Firelit\Session::$config['validatorSalt'] = 'fZ40iPuJiv11';
// Firelit\Session::$config['cookie']['secureOnly'] = ($_SERVER['ENV'] != 'DEV');
Firelit\Session::$config['cookie']['httpOnly'] = true;
Firelit\Session::init(new Firelit\DatabaseSessionHandler);

// Error routes

$router->errorRoute(0, function() use ($response) {
	$response->setCode(500);
	Firelit\View::quickRender('error', false, array(
		'error' => 500,
		'message' => 'Server error!'
	));
	exit;
});

$router->errorRoute(404, function() use ($response) {
	$response->setCode(404);
	Firelit\View::quickRender('error', false, array(
		'error' => 404,
		'message' => 'Page not found!'
	));
	exit;
});

$router->errorRoute(400, function($code, $message) use ($response) {
	$response->setCode(400);
	Firelit\View::quickRender('error', false, array(
		'error' => 400,
		'message' => $message
	));
	exit;
});

// HTTPS only

// if (($_SERVER['ENV'] != 'DEV') && empty($_SERVER['HTTPS'])) {
// 	$response->redirect('https://'. $request->host . $request->path);
// 	exit;
// }