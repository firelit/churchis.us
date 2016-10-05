<?php

require_once('vendor/autoload.php');

use Firelit\Registry;
use Firelit\Response;
use Firelit\Router;
use Firelit\Request;
use Firelit\Controller;
use Firelit\View;

// Setup
$response = Response::init();
$request = Request::init(false, (preg_match('!^/api/!', $_SERVER['REQUEST_URI']) ? 'json' : 'querystring'));
$router = Router::init($request);

require_once('bootstrap-web.php');

$router->defaultRoute(function() {
	throw new Firelit\RouteToError(404);
});

// Normal routes

$router->add('GET', '!^/$!', function($matches) {
	Controller::handoff('Index', 'viewIndex');
});

$router->add('*', '!^/(login)(/callback)?$!', function($matches) {
	Controller::handoff('Login', $matches[0]);
});

$router->add('GET', '!^/(logout)$!', function($matches) {
	Controller::handoff('Login', $matches[0]);
});

$router->add('GET', '!^/signup/leader$!', function() {
	Controller::handoff('LeaderSignup', 'viewForm');
});

	$router->add('POST', '!^/signup/leader$!', function() {
		Controller::handoff('LeaderSignup', 'submitForm');
	});

$router->add('GET', '!^/signup/member$!', function() {
	Controller::handoff('MemberSignup', 'viewForm');
});

	$router->add('POST', '!^/signup/member$!', function() {
		Controller::handoff('MemberSignup', 'submitForm');
	});

// Manage routes

$router->add('GET', '!^/manage/(.*)$!', function() {
	Controller::handoff('Manage', 'viewIndex');
});

// API routes

$router->add('GET', '!^/api/dashboard$!', function() {
	Controller::handoff('Dashboard', 'view');
});

	$router->add('POST', '!^/api/dashboard$!', function() {
		Controller::handoff('Dashboard', 'post');
	});

$router->add('GET', '!^/api/groups$!', function() {
	Controller::handoff('Groups', 'viewAll');
});

	$router->add('GET', '!^/api/groups/(\d+)$!', function($matches) {
		Controller::handoff('Groups', 'view', $matches[0]);
	});

	$router->add('PUT', '!^/api/groups/(\d+)$!', function($matches) {
		Controller::handoff('Groups', 'edit', $matches[0]);
	});

	$router->add('DELETE', '!^/api/groups/(\d+)$!', function($matches) {
		Controller::handoff('Groups', 'delete', $matches[0]);
	});

		$router->add('POST', '!^/api/groups/(\d+)/members/(\d+)$!', function($matches) {
			Controller::handoff('Groups', 'addMember', $matches[0], $matches[1]);
		});

		$router->add('DELETE', '!^/api/groups/(\d+)/members/(\d+)$!', function($matches) {
			Controller::handoff('Groups', 'removeMember', $matches[0], $matches[1]);
		});

		$router->add('GET', '!^/api/groups/(\d+)/meetings$!', function($matches) {
			Controller::handoff('GroupMeetings', 'viewAll', $matches[0]);
		});

			$router->add('POST', '!^/api/groups/(\d+)/meetings$!', function($matches) {
				Controller::handoff('GroupMeetings', 'addMeeting', $matches[0]);
			});

			$router->add('DELETE', '!^/api/groups/(\d+)/meetings/(\d+)$!', function($matches) {
				Controller::handoff('GroupMeetings', 'removeMeeting', $matches[0], $matches[1]);
			});

$router->add('GET', '!^/api/members$!', function() {
	Controller::handoff('Members', 'viewAll');
});

	$router->add('POST', '!^/api/members$!', function() {
		Controller::handoff('Members', 'create');
	});

	$router->add('GET', '!^/api/members/(\d+)$!', function($matches) {
		Controller::handoff('Members', 'view', $matches[0]);
	});

	$router->add('PUT', '!^/api/members/(\d+)$!', function($matches) {
		Controller::handoff('Members', 'edit', $matches[0]);
	});

	$router->add('DELETE', '!^/api/members/(\d+)$!', function($matches) {
		Controller::handoff('Members', 'delete', $matches[0]);
	});

$router->add('GET', '!^/api/users$!', function() {
	Controller::handoff('Users', 'viewAll');
});

	$router->add('GET', '!^/api/users/(\d+)$!', function($matches) {
		Controller::handoff('Users', 'view', $matches[0]);
	});
