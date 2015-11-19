<?php

namespace App\FrontController;

class AuthController extends BaseController {
	
	public function index($request, $response, $args){
		
		$this->view->render($response, 'front/login.twig', [
			'title' => 'Login',
			'flash' => $this->flash->getMessages(),
			'jsPage' => 'login'																					 
		]);
	
		return $response;
	
	}
}
