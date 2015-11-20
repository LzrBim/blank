<?php
// -----------------------------------------------------------------------------
// ADMIN ROUTES
// -----------------------------------------------------------------------------
$app->group('/admin', function () use ($app) {
																 
	$app->get('/dashboard', 'App\AdminController\DashboardController:index');
	
	//PAGE
	$app->get('/page/index', 'App\AdminController\PageController:index');	
	
		$app->get('/page/add', 'App\AdminController\PageController:add');
	
		$app->get('/page/edit', 'App\AdminController\PageController:edit');
		
		
	//GALLERY
	$app->get('/gallery/index', 'App\AdminController\GalleryController:index');	
	
		$app->get('/gallery/add', 'App\AdminController\GalleryController:add');
	
		$app->get('/gallery/edit', 'App\AdminController\GalleryController:edit');
																 
	/*$this->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, $args) {
			// Find, delete, patch or replace user identified by $args['id']
	})->setName('user');*/
		
})->add(new \App\Middleware\AdminGuard);