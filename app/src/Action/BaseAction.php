<?php

namespace App\Action;

use Psr\Log\LoggerInterface;
use Slim\Flash;

class BaseAction {
	
	protected $logger;
	protected $flash;
	
	public function __construct(LoggerInterface $logger, $services = array()){
		
		$this->logger = $logger;
		
		if(isset($services['flash'])){
			
			$this->flash = $services['flash'];
			
		}
   
	}
	
}