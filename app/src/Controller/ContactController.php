<?php

namespace App\Controller;

class ContactController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->debug("Contact page action dispatched");
		
		$token = [];
		$token['csrf_name'] = $request->getAttribute('csrf_name');
    $token['csrf_value'] = $request->getAttribute('csrf_value');
		
		$this->view->render($response, 'front/contact.twig', [
			'title' => 'Login',
			'flash' => $this->flash->getMessages(),
			'token' => $token,
			'jsPage' => 'login'																					 
		]);
	
		
		return $response;
	
	}
}
