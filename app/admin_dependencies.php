<?php
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/admin_dependencies.php
----------------------------------------------------------------------------- */

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