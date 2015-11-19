<?php
namespace App\AdminController;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash;

class BaseController {
	
	protected $view;
	protected $logger;
	protected $flash;
	
	public function __construct(Twig $view, LoggerInterface $logger, $services = array()){
		
		$this->view = $view;
		$this->logger = $logger;
		
		if(isset($services['flash'])){
			$this->flash = $services['flash'];
		}
   
	}
	
}
