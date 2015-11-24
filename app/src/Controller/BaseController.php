<?php

namespace App\Controller;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash;

class BaseController {
	
	protected $view;
	protected $logger;
	protected $flash;
	protected $notFoundHandler;
	
	public function __construct(Twig $view, LoggerInterface $logger, $services = array()){
		
		$this->view = $view;
		
		$this->logger = $logger;
		
		if(isset($services['flash'])){
			
			$this->flash = $services['flash'];
			
		}
		
		if(isset($services['notFoundHandler'])){
			
			$this->notFoundHandler = $services['notFoundHandler'];
			
		}
   
	}
	
	//FOR CSRF PROTECTED ROUTES
	public function getToken($request){
		
		$token = [];
		$token['csrf_name'] = $request->getAttribute('csrf_name');
    $token['csrf_value'] = $request->getAttribute('csrf_value');
		
		return $token;
		
	}
	
	public function error404($response){
		
		return $this->view->render($response, 'front/404.twig')->withStatus(404);
		
	}
	
}
