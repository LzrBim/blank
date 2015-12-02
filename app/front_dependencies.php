<?php
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/front_dependencies.php
----------------------------------------------------------------------------- */


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