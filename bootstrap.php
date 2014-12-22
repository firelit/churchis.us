<?php

if (file_exists('private.php'))
	require_once('private.php');

$_SERVER['ENV'] = 'DEV';

Firelit\Registry::set('database', array(
	'type' => 'mysql',
	'host' => $_SERVER['DB_HOST'],
	'user' => $_SERVER['DB_USER'],
	'pass' => $_SERVER['DB_PASS'],
	'name' => $_SERVER['DB_NAME']
));
