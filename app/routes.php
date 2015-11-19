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

// -----------------------------------------------------------------------------
// ADMIN ROUTES
// -----------------------------------------------------------------------------

$app->group('/admin', function () use ($app) {
																 
		$app->get('/dashboard', 'App\AdminController\DashboardController:index');				
		
		$app->get('/page', 'App\AdminController\PageController:index');										 
																 
    /*$this->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, $args) {
        // Find, delete, patch or replace user identified by $args['id']
    })->setName('user');*/
		
})->add(new \App\Middleware\AdminGuard);