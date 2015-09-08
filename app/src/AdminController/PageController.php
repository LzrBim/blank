<?php

namespace App\AdminController;

use \App\Controller\BaseController;

class PageController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->info("Admin Page Index");
        
		$this->view->render($response, 'admin/index.twig');
		
		return $response;
	
	}
}
