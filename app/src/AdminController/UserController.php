<?php

namespace App\AdminController;

use App\Model\Page;

class UserController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->info("Admin User Index");
		
		$user = new User();
		$pages = $page->fetchAll();
        
		$this->view->render($response, 'admin/user.index.twig', [
			'title' => 'Pages',
			'pages' => $pages,
			'jsPage' => 'index',
			'jsOptions' => array(
				'model' => 'Page'
			)																			 
		]);
		
		
		return $response;
	
	}
}
