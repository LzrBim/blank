<?php

namespace App\AdminController;
use App\Model\Page;
use App\Model\PageVersion;
use App\Model\PageVersionBlock;

class PageVersionController extends BaseController {
	
	public function index($request, $response, $args){
	
	
	
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
		$pageVersion->load($args['id']);
		
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
	
	public function insertLink($request, $response, $args){
	
		$this->logger->debug("Admin Page Version Block Link  Insert");
		
		$pVBID = Sanitize::paranoid($request->getParam('pageVersionBlockID')));
		$pID = Sanitize::paranoid($request->getParam('pageVersionID')));
		
		$pageVersionBlock = new PageVersionBlock();
		$linkID = $pageVersionBlock->insertLink($pVBID, $pID);
		
		if($linkID){
			return $response->write(json_encode(['pageVersionBlockLinkID' => $link]));
		}
		
		return $response->write(json_encode([
			'pageVersionBlockLinkID' => 0,
			'message' = 'Crap'
		]));
		
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
