<?php

session_start();

if (PHP_SAPI == 'cli-server') {
	
	$file = __DIR__ . $_SERVER['REQUEST_URI'];
	if (is_file($file)) {
			return false;
	}
}

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../app/config.php';

$app = new \Slim\App($settings); 

// Set up dependencies
require __DIR__ . '/../app/dependencies.php'; 

// Register middleware
require __DIR__ . '/../app/middleware.php';

// Register routes
require __DIR__ . '/../app/routes.php';

// Run!
$app->run();
