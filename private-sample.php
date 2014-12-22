<?php

$_SERVER['GOOGLE_KEY'] = 'GOOGLE API KEY';
$_SERVER['GOOGLE_SEC'] = 'GOOGLE SECRET';

$_SERVER['MAILGUN_KEY'] = 'MAILGUN API KEY';
$_SERVER['MAILGUN_DOM'] = 'MAILGUN DOMAIN';

// Set as environmental variables in Apache or set here
if (!isset($_SERVER['DB_HOST'])) $_SERVER['DB_HOST'] = 'localhost';
if (!isset($_SERVER['DB_NAME'])) $_SERVER['DB_NAME'] = 'DB_NAME';
if (!isset($_SERVER['DB_USER'])) $_SERVER['DB_USER'] = 'DB_USER';
if (!isset($_SERVER['DB_PASS'])) $_SERVER['DB_PASS'] = 'DB_PASS';