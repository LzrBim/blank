<?php
// -----------------------------------------------------------------------------
// ADMIN ROUTES
// -----------------------------------------------------------------------------
$app->group('/admin', function () use ($app) {
																 
	$app->get('', function ($request, $response, $args) { 
     return $response->withRedirect('/dashboard'); 
	});
	
	$app->get('/dashboard', 'App\AdminController\DashboardController:index');
	
	//PAGE
	$app->get('/page/index', 'App\AdminController\PageController:index');	
	
		$app->get('/page/add', 'App\AdminController\PageController:add');
		
		$app->post('/page/add', 'App\AdminController\PageController:insert');
	
		$app->get('/page/edit/{id:[0-9]+}', 'App\AdminController\PageController:edit');
		
		$app->post('/page/edit/{id:[0-9]+}', 'App\AdminController\PageController:update');
		
		$app->get('/page/delete/{id:[0-9]+}', 'App\AdminController\PageController:delete');
		
		
	//PAGE VERSIONS
	$app->get('/pageVersion/index/{pageID:[0-9]+}', 'App\AdminController\PageVersionController:index');	
	
		$app->get('/pageVersion/add/{pageID:[0-9]+}', 'App\AdminController\PageVersionController:add');
		
		$app->post('/pageVersion/add/{pageID:[0-9]+}', 'App\AdminController\PageVersionController:insert');
	
		$app->get('/pageVersion/edit/{id:[0-9]+}', 'App\AdminController\PageVersionController:edit');
		
		$app->post('/pageVersion/edit/{id:[0-9]+}', 'App\AdminController\PageVersionController:update');
	
		//EXTRA METHODS
		$app->get('/pageVersion/copy/{id:[0-9]+}', 'App\AdminController\PageVersionController:copy');
		
		$app->get('/pageVersion/publish/{id:[0-9]+}', 'App\AdminController\PageVersionController:publish');
		
		$app->get('/pageVersion/preview/{id:[0-9]+}', 'App\AdminController\PageVersionController:preview');
		
		//VERSION BLOCKS
		$app->post('/pageVersionBlock/insert/{id:[0-9]+}', 'App\AdminController\PageVersionBlockController:insert');
		
		$app->post('/pageVersionBlock/insertLink/{id:[0-9]+}', 'App\AdminController\PageVersionBlockController:insertLink');
		
		$app->get('/pageVersionBlock/preview/{id:[0-9]+}', 'App\AdminController\PageVersionBlockController:preview');
		
		
	//GALLERY
	$app->get('/gallery/index', 'App\AdminController\GalleryController:index');	
	
		$app->get('/gallery/add', 'App\AdminController\GalleryController:add');
	
		$app->get('/gallery/edit', 'App\AdminController\GalleryController:edit');

		
})->add( 'adminGuard' );