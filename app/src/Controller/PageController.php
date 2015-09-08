<?php

namespace App\Controller;

class PageController extends BaseController {
	
	public function index($request, $response, $args){

		$this->logger->info("Page action dispatched");
        
		$this->view->render($response, 'front/page.twig');
		
		return $response;
	
	}
}
