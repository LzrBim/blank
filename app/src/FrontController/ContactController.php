<?php

namespace App\FrontController;

class ContactController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->info("Contact action dispatched");
        
		$this->view->render($response, 'front/contact.twig');
		
		return $response;
	
	}
}
