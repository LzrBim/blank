<?php
// -----------------------------------------------------------------------------
// FRONT ROUTES
// -----------------------------------------------------------------------------

$app->get('/', 'App\Controller\HomeController:index');

$app->get('/page/{slug}', 'App\Controller\PageController:detail');

$app->get('/gallery', 'App\Controller\GalleryController:index');

$app->get('/gallery/{slug}', 'App\Controller\GalleryController:detail');


// -----------------------------------------------------------------------------
// FRONT WITH ACTIONS
// -----------------------------------------------------------------------------
$app->get('/contact', 'App\Controller\ContactController:index')->add($container->get('csrf'));

$app->post('/contact', function ($request, $response, $args) use ($app) {
   
	$container = $app->getContainer();
	
	$container->get('App\Action\ContactAction')->contact($args);
		
	return $response->withRedirect('/contact');
	 
})->add($container->get('csrf'));


$app->get('/login', 'App\Controller\AuthController:index');

$app->post('/login', function ($request, $response, $args) use ($app) {
																			
	$container = $app->getContainer();
	
	$authAction = $container->get('App\Action\AuthAction');
																		
	if($authAction->login($args)){
		
		return $response->withRedirect('/admin/dashboard');
		
	} else {
		
		return $response->withRedirect('/login');
		
	}									
	
});

$app->get('/logout', function ($request, $response, $args) use ($app) {
																			
	$container = $app->getContainer();
	
	$authAction = $container->get('App\Action\AuthAction');
																		
	$authAction->logout();
	
	return $response->withRedirect('/login');									
	
});