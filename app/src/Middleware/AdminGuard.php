<?php

namespace App\Middleware;

use \App\Model\User;

class AdminGuard { 
	
	public function __invoke($request, $response, $next){
	
		if(false){
			
			return $response->withRedirect('/login/');
			
		}
		
		$response = $next($request, $response);

		return $response;
	}
}
