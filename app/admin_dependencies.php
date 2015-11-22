<?php
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/admin_dependencies.php
----------------------------------------------------------------------------- */

$container['App\AdminController\DashboardController'] = function ($c) {
	return new App\AdminController\DashboardController($c['view'], $c['logger'], array('flash' => $c['flash']));
};

$container['App\AdminController\CommonController'] = function ($c) {
	return new App\AdminController\CommonController($c['view'], $c['logger'], array('flash' => $c['flash']));
};

$container['App\AdminController\PageController'] = function ($c) {
	return new App\AdminController\PageController($c['view'], $c['logger'], array('flash' => $c['flash']));
};

$container['App\AdminController\PageVersionController'] = function ($c) {
	return new App\AdminController\PageVersionController($c['view'], $c['logger'], array('flash' => $c['flash']));
};

$container['App\AdminController\PageBlockController'] = function ($c) {
	return new App\AdminController\PageBlockController($c['view'], $c['logger'], array('flash' => $c['flash']));
};

$container['App\Controller\GalleryController'] = function ($c) {
    return new App\Controller\GalleryController($c['view'], $c['logger'], $c['logger'], array('flash' => $c['flash']));
};
