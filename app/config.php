<?php

define('MYSQL_HOST', '127.0.0.1');  
define('MYSQL_USER', 'root'); 
define('MYSQL_PASS', '');  
define('MYSQL_DB', 'blank_db_local'); 
	
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');

$settings = [ 
	'settings' => [
		// View settings 
		'view' => [
			'template_path' => __DIR__ . '/templates',
			'twig' => [
				'cache' => __DIR__ . '/../cache/twig',
				'debug' => true,
				'auto_reload' => true
			]
		],
		// monolog settings
		'logger' => [
			'name' => 'app',
			'path' => __DIR__ . '/../log/app.log'
		]
	]
];