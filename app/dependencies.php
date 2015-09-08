<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// VIEWS - TWIG
// -----------------------------------------------------------------------------
$container['view'] = function ($c) {
	
	$view = new \Slim\Views\Twig($c['settings']['view']['template_path'], $c['settings']['view']['twig']);

	// Add extensions
	$view->addExtension(new Slim\Views\TwigExtension($c['router'], $c['request']->getUri()));
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
// MONOLOG
// -----------------------------------------------------------------------------
$container['logger'] = function ($c) {
	
	$settings = $c['settings']['logger'];
	$logger = new \Monolog\Logger($settings['name']);
	$logger->pushProcessor(new \Monolog\Processor\UidProcessor());
	$logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Logger::DEBUG));
	return $logger;
	
};


// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

//http://thoughts.silentworks.co.uk/lazy-loading-controller-action/

$container['App\Controller\HomeController'] = function ($c) {	
	return new App\Controller\HomeController($c['view'], $c['logger']);	 
};

$container['App\Controller\PageController'] = function ($c) {
    return new App\Controller\PageController($c['view'], $c['logger']);
};

$container['App\Controller\GalleryController'] = function ($c) {
    return new App\Controller\GalleryController($c['view'], $c['logger']);
};

$container['App\Controller\AuthController'] = function ($c) {
    return new App\Controller\AuthController($c['view'], $c['logger'], array('flash' => $c['flash']));
};

$container['App\Action\AuthAction'] = function ($c) {
    return new App\Action\AuthAction($c['logger'], array('flash' => $c['flash']));
};

$container['App\AdminController\DashboardController'] = function ($c) {
    return new App\AdminController\DashboardController($c['view'], $c['logger'], array('flash' => $c['flash']));
};

$container['App\AdminController\PageController'] = function ($c) {
    return new App\AdminController\PageController($c['view'], $c['logger'], array('flash' => $c['flash']));
};

/*$container['App\Controller\GalleryController'] = function ($c) {
    return new App\Controller\GalleryController($c['view'], $c['logger'], $c['logger']);
};*/
