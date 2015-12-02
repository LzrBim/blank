<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/AdminController/GalleryImageController.php
----------------------------------------------------------------------------- */
namespace App\AdminController;

use App\Model\GalleryImage;

class GalleryImageController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->debug("Admin Gallery Image Index");
		
		$page = new GalleryImage();
		$pages = $page->fetchAll();
        
		$this->view->render($response, 'admin/page/index.twig', [
			'title' => 'GalleryImages',
			'pages' => $pages,
			'jsGalleryImage' => 'index',
			'jsOptions' => array(
				'model' => 'GalleryImage'
			)																			 
		]);	
		
		return $response;
	
	}
	
	public function add($request, $response, $args){
	
		$this->logger->debug("Admin - GalleryImage Add");
		
		ob_start();
		
		include('../app/src/crud/GalleryImage/add.php');
		
		$form = ob_get_clean();
        
		$this->view->render($response, 'admin/edit.twig', [
			'title' => 'GalleryImages',
			'form' => $form,
			'jsGalleryImage' => 'add',
			'jsOptions' => array(
				'model' => 'GalleryImage'
			)																			 
		]);	
		
		return $response;
	
	}
	
	
	public function insert($request, $response, $args){
		
		$uploadCount = Uploader::is_multiple_upload();
	
		$this->logger->debug("Admin GalleryImage Insert");
		
		$page = new GalleryImage();
		$page->loadByData($request->getParsedBody());
		$page->insert();
		
		$this->flash->addMessage('success', 'GalleryImage saved');
		
		return $response->withRedirect('/admin/pageVersion/index/'.$page->id());
	
	}
	
	
	public function edit($request, $response, $args){
	
		$this->logger->debug("Admin GalleryImage Edit");
		
		$page = new GalleryImage();
		$page->load($args['id']);
		ob_start();
		include('../app/src/crud/GalleryImage/edit.php');
		$form = ob_get_clean();
        
		$this->view->render($response, 'admin/edit.twig', [
			'title' => 'GalleryImages',
			'form' => $form,
			'jsGalleryImage' => 'edit',
			'jsOptions' => array(
				'model' => 'GalleryImage'
			)																			 
		]);	
		
		return $response;
	
	}
	
	public function update($request, $response, $args){
	
		$this->logger->debug("Admin GalleryImage Update");
		
		$page = new GalleryImage();
		$page->loadByData($request->getParsedBody());
        
		if($page->update()){
			
			$this->logger->debug("GalleryImage updated successfully");
			$this->flash->addMessage('success', 'GalleryImage saved');
			return $response->withRedirect('/admin/page/edit/'.$page->id());
			
		} else {
			
			$this->flash->addMessage('error', 'Error saving page');
			return $response->withRedirect('/admin/page/edit/'.$page->id());
			
		}
		
	}
	
	
	public function delete($request, $response, $args){
	
		$this->logger->debug("Admin GalleryImage Edit");
		
		$page = new GalleryImage();
		$page->load($args['id']);
		$page->delete();
		
		$this->flash->addMessage('success', 'GalleryImage deleted');
		
		return $response->withRedirect('/admin/page/index');
	
	}
	
	
}
