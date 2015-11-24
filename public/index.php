<?php

session_start();

if (PHP_SAPI == 'cli-server') {
	
	$file = __DIR__ . $_SERVER['REQUEST_URI'];
	if (is_file($file)) {
			return false;
	}
}

//CONFIG
require __DIR__ . '/../app/macbook_config.php';

//AUTOLOADER
require __DIR__ . '/../vendor/autoload.php';

//BOOT APP
$app = new \Slim\App($settings); 
$app->config('debug', true);
$app->config('displayErrorDetails', true);

//DI DEPENDENCIES
require __DIR__ . '/../app/dependencies.php'; 
require __DIR__ . '/../app/admin_dependencies.php'; 

//EVIRONMENT
require __DIR__ . '/../app/environment.php';

//MIDDLEWARE
require __DIR__ . '/../app/middleware.php';

//ROUTES
require __DIR__ . '/../app/routes.php';
require __DIR__ . '/../app/admin_routes.php';

//FRONTBUTT
$app->run();
