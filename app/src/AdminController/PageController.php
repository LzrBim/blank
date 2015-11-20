<?php

namespace App\AdminController;

use App\Model\Page;

class PageController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->info("Admin Page Index");
		
		$page = new Page();
		$pages = $page->fetchAll();
        
		$this->view->render($response, 'admin/page.index.twig', [
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
