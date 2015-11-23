<?php

namespace App\Action;

use Psr\Log\LoggerInterface;
use Valitron\Validator;
use Slim\Flash;

class BaseAction {
	
	protected $logger; //Logger Interface
	protected $flash; //Flash
	protected $validator; //Validator
	
	public function __construct(LoggerInterface $logger, $services = array()){
		
		$this->logger = $logger;
		
		$this->validator = new Validator($_POST);
		
		if(isset($services['flash'])){
			
			$this->flash = $services['flash'];
			
		}
		
	}
	
}