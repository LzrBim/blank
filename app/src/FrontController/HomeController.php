<?php

namespace App\FrontController;

class HomeController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->info("Home page action dispatched");
        
		$this->view->render($response, 'front/home.twig');
		
		return $response;
	
	}
}
