<?php

namespace App\AdminController;

use App\Model\PageVersion;

class PageVersionController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->debug("Admin Page Version Index");
		
		$page = new Page();
		$pages = $page->fetchAll();
        
		$this->view->render($response, 'admin/pageVersion.index.twig', [
			'title' => 'Pages',
			'pages' => $pages,
			'jsPage' => 'index',
			'jsOptions' => array(
				'model' => 'Page'
			)																			 
		]);	
		
		return $response;
	
	}
	
	public function edit($request, $response, $args){
	
		$this->logger->debug("Admin Page Edit");
		
		$page = new Page();
		$page->load($args['id']);
		ob_start();
		include('../app/src/crud/Page/edit.php');
		$form = ob_get_clean();
        
		$this->view->render($response, 'admin/edit.twig', [
			'title' => 'Pages',
			'form' => $form,
			'jsPage' => 'edit',
			'jsOptions' => array(
				'model' => 'Page'
			)																			 
		]);	
		
		return $response;
	
	}	
	
	
	public function copy($request, $response, $args){
	
		$this->logger->debug("Copy PageVersion");
		
		$pageVersion = new PageVersion();
		
		if($pageVersion->makeCopy($args['id'])){
			
			return $response;
			
		} else {
			
		}
		
		
	
	}
}
