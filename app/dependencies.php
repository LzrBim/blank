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


//CSRF PROTECTED ROUTES
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

//Override the default Not Found Handler
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


// -----------------------------------------------------------------------------
// CONTROLLERS
// -----------------------------------------------------------------------------

//VIEW ONLY
$container['App\Controller\HomeController'] = function ($c) {	
	return new App\Controller\HomeController($c->get('view'), $c->get('logger'));	 
};

$container['App\Controller\PageController'] = function ($c) {
	return new App\Controller\PageController($c->get('view'), $c->get('logger'), array(
		'notFoundHandler' => $c['notFoundHandler']
	));
};

$container['App\Controller\GalleryController'] = function ($c) {
	return new App\Controller\GalleryController($c->get('view'), $c->get('logger'));
};


//WITH FLASH - ACTIONS
$container['App\Controller\ContactController'] = function ($c) {
	return new App\Controller\ContactController($c->get('view'), $c->get('logger'), array('flash' => $c['flash']));
};

$container['App\Controller\AuthController'] = function ($c) {
	return new App\Controller\AuthController($c->get('view'), $c->get('logger'), array('flash' => $c['flash']));
};


//FRONT ACTIONS
$container['App\Action\ContactAction'] = function ($c) {
	return new App\Action\ContactAction($c->get('logger'), array('flash' => $c['flash']));
};

$container['App\Action\AuthAction'] = function ($c) {
	return new App\Action\AuthAction($c->get('logger'), array('flash' => $c['flash']));
};
