<?php
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/admin_dependencies.php
----------------------------------------------------------------------------- */



/*$container['App\Lib\ImageResizer'] = function ($c) {
	return new App\Lib\Uploader($c->get('logger'), $c->get('flash'));
};
*/

$container['App\AdminController\DashboardController'] = function ($c) {
	return new App\AdminController\DashboardController($c->get('view'), $c->get('logger'), array('flash' => $c->get('flash')));
};

$container['App\AdminController\CommonController'] = function ($c) {
	return new App\AdminController\CommonController($c->get('view'), $c->get('logger'), array('flash' => $c->get('flash')));
};

$container['App\AdminController\PageController'] = function ($c) {
	return new App\AdminController\PageController($c->get('view'), $c->get('logger'), array('flash' => $c->get('flash')));
};

	$container['App\AdminController\PageVersionController'] = function ($c) {
		return new App\AdminController\PageVersionController($c->get('view'), $c->get('logger'), array('flash' => $c->get('flash')));
	};
	
	$container['App\AdminController\PageBlockController'] = function ($c) {
		return new App\AdminController\PageBlockController($c->get('view'), $c->get('logger'), array('flash' => $c->get('flash')));
	};
	
	$container['App\AdminController\PageVersionBlockController'] = function ($c) {
		return new App\AdminController\PageVersionBlockController($c->get('view'), $c->get('logger'), array('flash' => $c->get('flash')));
	};
	
$container['App\AdminController\StaffController'] = function ($c) {
	return new App\AdminController\StaffController($c->get('view'), $c->get('logger'), array(
		'flash' => $c->get('flash'),
		'uploader' => $c->get('uploader'),
		'imageResizer' => $c->get('imageResizer')
	));
};