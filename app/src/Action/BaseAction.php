<?php

namespace App\Action;

use Psr\Log\LoggerInterface;
use Valitron\Validator;
use Slim\Flash;

class BaseAction {
	
	protected $l; //Logger Interface
	protected $f; //Flash
	protected $v; //Validator
	
	public function __construct(LoggerInterface $logger, $services = array()){
		
		$this->l = $logger;
		
		$this->v = new Validator($_POST);
		
		if(isset($services['flash'])){
			
			$this->f = $services['flash'];
			
		}
		
	}
	
}