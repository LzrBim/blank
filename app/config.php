<?php

define('MYSQL_HOST', '127.0.0.1');  
define('MYSQL_USER', 'root'); 
define('MYSQL_PASS', '');  
define('MYSQL_DB', 'blank_db_local'); 

define('HTTP_PATH', 'http://local.blank.com/');
define('BASE_PATH', '/Users/crossmj/Sites/Blank/Home/');

define('ASSET_BASE_PATH', BASE_PATH.'public/assets/');
define('ASSET_HTTP_PATH', HTTP_PATH.'assets/');

	
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');

$settings = [ 
	'settings' => [
		
		//App settings
		'displayErrorDetails' => true, 
		
		//Twig settings
		'view' => [
			'template_path' => __DIR__ . '/templates',
			'twig' => [
				'cache' => __DIR__ . '/../cache/twig',
				'debug' => true,
				'auto_reload' => true,
				'optimizations' => -1
			]
		],
		
		//Monolog settings
		'logger' => [
			'name' => 'app',
			'path' => __DIR__ . '/../log/app.log'
		]
	]
];