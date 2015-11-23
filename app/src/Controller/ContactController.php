<?php

namespace App\Controller;

class ContactController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->debug("Contact page action dispatched");
		
		$token = $this->getToken($request);
		
		$this->view->render($response, 'front/contact.twig', [
			'flash' => $this->flash->getMessages(),
			'token' => $token,
			'jsPage' => 'contact'																					 
		]);
	
		
		return $response;
	
	}
}
