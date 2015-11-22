<?php
// -----------------------------------------------------------------------------
// ADMIN ROUTES
// -----------------------------------------------------------------------------
$app->group('/admin', function () use ($app) {
																 
	$app->get('/dashboard', 'App\AdminController\DashboardController:index');
	
	//PAGE
	$app->get('/page/index', 'App\AdminController\PageController:index');	
	
		$app->get('/page/add', 'App\AdminController\PageController:add');
		
		$app->post('/page/add', 'App\AdminController\CommonController:insert');
	
		$app->get('/page/edit/{id:[0-9]+}', 'App\AdminController\PageController:edit');
		
		$app->post('/page/edit/{id:[0-9]+}', 'App\AdminController\PageController:update');
		
		
	//PAGE VERSIONS
	$app->get('/pageVersion/index/{pageID:[0-9]+}', 'App\AdminController\PageVersionController:index');	
	
		$app->get('/pageVersion/add/{pageID:[0-9]+}', 'App\AdminController\PageVersionController:add');
	
		$app->get('/pageVersion/edit/{id:[0-9]+}', 'App\AdminController\PageVersionController:edit');
		
		$app->get('/pageVersion/copy/{id:[0-9]+}', 'App\AdminController\PageVersionController:copy');
		
		
	//GALLERY
	$app->get('/gallery/index', 'App\AdminController\GalleryController:index');	
	
		$app->get('/gallery/add', 'App\AdminController\GalleryController:add');
	
		$app->get('/gallery/edit', 'App\AdminController\GalleryController:edit');
																 
	/*$this->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, $args) {
			// Find, delete, patch or replace user identified by $args['id']
	})->setName('user');*/
		
})->add(new \App\Middleware\AdminGuard);