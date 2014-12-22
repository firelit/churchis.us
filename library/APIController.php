<?php

class APIController extends Firelit\Controller {
	
	public function __construct() {

		// Switch over to API response
		$resp = Firelit\ApiResponse::init('JSON');		
		$this->response = $resp;

		$router = Firelit\Router::init();

		$router->errorRoute(404, function() use ($resp) {
			$resp->setCode(404);
			$resp->respond(array(
				'errors' => array(array('message' => 'Resource not found'))
			));
			exit;
		});

		$router->errorRoute(400, function($code, $message) use ($resp) {
			$resp->setCode(400);
			$resp->respond(array(
				'errors' => array(array('message' => 'Invalid request'))
			));
			exit;
		});

		$router->exceptionHandler(function($e) use ($resp) {
			if ($_SERVER['ENV'] == 'DEV') $message = $e->getMessage();
			else $message = 'Server error';

			$resp->setCode(500);
			$resp->respond(array(
				'errors' => array(array('message' => $e->getMessage()))
			));
			exit;
		});

		$session = Firelit\Session::init();
		if (!$session->loggedIn)
			throw new Firelit\RouteToError(400, 'User not authenticated');
	
	}

}