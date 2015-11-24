<?php

namespace App\Middleware;

use Psr\Log\LoggerInterface;
use \App\Model\User;

class AdminGuard { 

	protected $logger;
	
	public function __construct(LoggerInterface $logger){
		
		$this->logger = $logger;
		
	}
	
	public function __invoke($request, $response, $next){
			
		$route = $request->getAttribute('route');
		
		$uri = $request->getUri();
		
		$this->logger->debug('Guarding ');
		
		//die(print_r($uri, true));
		
		if(!User::isAuthorized('admin')){
			
			return $response->withRedirect('/login');
			
		} 
		
		$response = $next($request, $response);

		return $response;
	}
}
