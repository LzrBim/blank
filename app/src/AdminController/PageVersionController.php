<?php

namespace App\AdminController;
use App\Model\Page;
use App\Model\PageVersion;

class PageVersionController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->debug("Admin Page Version Index, pageID = ".$args['pageID']);
		
		$pageVersion = new PageVersion();
		$pageVersions = $pageVersion->fetchAllByPage($args['pageID']);
		
		$page = new Page();
		$page->load($args['pageID']);	
        
		$this->view->render($response, 'admin/page/version_index.twig', [
			'title' => $page->title.' - Versions',
			'page' => $page,
			'pageVersions' => $pageVersions,
			'jsPage' => 'index',
			'jsOptions' => array(
				'model' => 'Page'
			)																			 
		]);	
		
		return $response;
	
	}
	
	public function add($request, $response, $args){
	
		$this->logger->debug("Admin PageVersion Add");
		
		$page = new Page();
		$page->load($args['pageID']);
		
		ob_start();
		include('../app/src/crud/PageVersion/add.php');
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
	
	public function insert($request, $response, $args){
	
		$this->logger->debug("Admin PageVersion Insert");
		
		$pageVersion = new PageVersion();
		$pageVersion->loadByData($request->getParsedBody());
		
		if($pageVersion->insert()){
		
			$this->flash->addMessage('success', 'PageVersion saved');
		
			return $response->withRedirect('/admin/pageVersion/edit/'.$pageVersion->id());
			
		} else {
			
			$this->flash->addMessage('error', 'Error saving version');
		
			return $response->withRedirect('/admin/pageVersion/add/'.$pageVersion->pageID);
			
		}
	
	}
	
	
	public function edit($request, $response, $args){
	
		$this->logger->debug("Admin PageVersion Edit");
		
		$pageVersion = new PageVersion();
		$pageVersion->load($args['id'])
								->with('block');
		
		ob_start();
		include('../app/src/crud/PageVersion/edit.php');
		include('../app/src/crud/PageVersionBlock/modal_add.php');
		include('../app/src/crud/PageVersionBlock/modal_insert.php');
		$form = ob_get_clean();
        
		$this->view->render($response, 'admin/page/version_edit.twig', [
			'title' => 'Pages',
			'form' => $form,
			'jsPage' => 'edit',
			'jsOptions' => array(
				'model' => 'PageVersion'
			)																			 
		]);	
		
		return $response;
	
	}	
	
	
	public function copy($request, $response, $args){
	
		$this->logger->debug("Copy PageVersion");
		
		$pageVersion = new PageVersion();
		$copy = $pageVersion->makeCopy($args['id']);
		if($copy){
			
			$this->flash->addMessage('success', 'Version copied');
			
		} else {
			
			$this->flash->addMessage('error', 'Error creating copy');
		
		}
		
		return $response->withRedirect('/admin/pageVersion/index/'.$copy->pageID);
	
	}
}
