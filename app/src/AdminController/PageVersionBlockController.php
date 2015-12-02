<?php

namespace App\AdminController;
use App\Model\Page;
use App\Model\PageVersion;
use App\Model\PageVersionBlock;

class PageVersionBlockController extends BaseController {
	
	public function index($request, $response, $args){
	
	}
	
	public function insert($request, $response, $args){
	
		$this->logger->debug("Admin PageVersion Insert");
		
		$pageVersionBlock = new PageVersionBlock();
		$pageVersionBlock->loadByData($request->getParsedBody());
		
		if($pageVersionBlock->insert()){
			
			$pageVersionBlock->insertLink($pageVersionBlock->id(), $request->getParam('pageVersionID'));
		
			$this->flash->addMessage('success', 'Block saved');
		
			return $response->withRedirect('/admin/pageVersionBlock/edit/'.$pageVersionBlock->id());
			
		} else {
			
			$this->flash->addMessage('error', 'Error saving block');
		
			return $response->withRedirect('/admin/pageVersion/edit/'.$pageVersion->pageID);
			
		}
	
	}
	
	
	public function edit($request, $response, $args){
	
		$this->logger->debug("Admin PageVersion Edit");
		
		$pageVersionBlock = new PageVersionBlock();
		$pageVersionBlock->load($args['id']);
		
		$pageVersion = new PageVersion();
		$pageVersion->load($request->getParam('pageVersionID'));
		
		$page = new Page();
		$page->load($pageVersion->pageID);
		
		ob_start();
		include('../app/src/crud/PageVersionBlock/edit.php');
		$form = ob_get_clean();
        
		$this->view->render($response, 'admin/page/block_edit.twig', [
			'title' => 'Block',
			'subTitle' => $page->title.' - '.$pageVersion->title,
			'pageVersionID' => $pageVersion->id(),
			'form' => $form,
			'jsPage' => 'edit',
			'jsOptions' => array(
				'model' => 'PageVersionBlock'
			)																			 
		]);	
		
		return $response;
	
	}	
	
	public function insertLink($request, $response, $args){
	
		$this->logger->debug("Admin Page Version Block Link  Insert");
		
		$pVBID = Sanitize::paranoid($request->getParam('pageVersionBlockID'));
		$pID = Sanitize::paranoid($request->getParam('pageVersionID'));
		
		$pageVersionBlock = new PageVersionBlock();
		$linkID = $pageVersionBlock->insertLink($pVBID, $pID);
		
		if($linkID){
			return $response->write(json_encode(['pageVersionBlockLinkID' => $link]));
		}
		
		return $response->write(json_encode([
			'pageVersionBlockLinkID' => 0,
			'message' => 'Crap'
		]));
		
	}
	
	
	public function delete($request, $response, $args){
	
		$this->logger->debug("Admin Page Version Block Delete");
		
		$pageVersionBlockID = $request->getParam('pageVersionBlockID');
		$pageVersionID = $request->getParam('pageVersionBlockID');		
		
		$pageVersionBlock = new PageVersionBlock();
		
		if($pageVersionBlock->deleteLink($pageVersionBlockID, $pageVersionID)){
		
			if($pageVersionBlock->delete()){
		
				return $response->write(json_encode([
					'success' => 1,
					'message' => 'Block removed'
				]));
						
			} 
		
		}
		
		return $response->write(json_encode([
			'success' => 0,
			'message' => 'Error removing Block'
		]));
	
	}
	
}
