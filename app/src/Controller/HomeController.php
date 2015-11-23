<?php

namespace App\Controller;

class HomeController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->info("Home page action dispatched");
        
		$this->view->render($response, 'front/home.twig', array(
			'jsPage' => 'home'																												
		));
		
		return $response;
	
	}
}
