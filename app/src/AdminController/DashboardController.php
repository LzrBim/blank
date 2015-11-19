<?php

namespace App\AdminController;

class DashboardController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->info("Admin Dashboard");
        
		$this->view->render($response, 'admin/dashboard.twig');
		
		return $response;
	
	}
}
