<?php
// -----------------------------------------------------------------------------
// FRONT ROUTES
// -----------------------------------------------------------------------------

$app->get('/', 'App\Controller\HomeController:index');

$app->get('/gallery', 'App\Controller\GalleryController:index');

$app->get('/gallery/{slug}', 'App\Controller\GalleryController:detail');

$app->get('/contact', 'App\Controller\ContactController:index');

$app->get('/login', 'App\Controller\AuthController:index');

$app->post('/login', function ($request, $response, $args) use ($app) {
																			
	$container = $app->getContainer();
	
	$authAction = new App\Action\AuthAction($container['logger'], array('flash' => $container['flash']));
																		
	if($authAction->login($args)){
		
		return $response->withRedirect('/admin/dashboard');
		
	} else {
		
		return $response->withRedirect('/login');
		
	}									
	
});