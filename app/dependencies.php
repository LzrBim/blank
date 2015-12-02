<?php
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/dependencies.php
----------------------------------------------------------------------------- */

// DIC configuration
$container = $app->getContainer();

// -----------------------------------------------------------------------------
// VIEWS - TWIG
// -----------------------------------------------------------------------------
$container['view'] = function ($c) {
	
	$settings = $c->get('settings');
	
	$view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
	
	// Add extensions
	$view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
	
	$view->addExtension(new Twig_Extension_Debug());
	
	return $view;
	
};

// -----------------------------------------------------------------------------
// FLASH
// -----------------------------------------------------------------------------
$container['flash'] = function ($c) {

	return new \Slim\Flash\Messages;
	
};


// -----------------------------------------------------------------------------
// CSRF
// -----------------------------------------------------------------------------
$container['csrf'] = function ($c) {
	return new \Slim\Csrf\Guard;
};


// -----------------------------------------------------------------------------
// MONOLOG
// -----------------------------------------------------------------------------
$container['logger'] = function ($c) {
	
	$settings = $c->get('settings');
  
	$logger = new \Monolog\Logger($settings['logger']['name']);
  
	$logger->pushProcessor(new \Monolog\Processor\UidProcessor());
  
	$logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], \Monolog\Logger::DEBUG));
  
	return $logger;
	
};

// -----------------------------------------------------------------------------
// UPLOADER
// -----------------------------------------------------------------------------
$container['uploader'] = function ($c) {
	
	return new App\Lib\Uploader($c->get('logger'), $c->get('flash'));
	
};

// -----------------------------------------------------------------------------
// IMAGE RESIZER
// -----------------------------------------------------------------------------
$container['imageResizer'] = function ($c) {
	
	return new App\Lib\ImageResizer($c->get('logger'));
	
};


// -----------------------------------------------------------------------------
// 404 HANDLER
// -----------------------------------------------------------------------------
$container['notFoundHandler'] = function ($c) {
	
	return function ($request, $response) use ($c) {
		
		return $c->get('view')->render($response, 'front/404.twig', [
			
		]);
		
	};
		
};

// -----------------------------------------------------------------------------
// MIDDLEWARE
// -----------------------------------------------------------------------------
$container['adminGuard'] = function ($c) {
	
	return new App\Middleware\AdminGuard($c->get('logger'));
	
};
