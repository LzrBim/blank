<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/AdminController/PageController.php
----------------------------------------------------------------------------- */
namespace App\AdminController;

use App\Model\Page;

class PageController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->debug("Admin Page Index");
		
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
	
	public function add($request, $response, $args){
	
		$this->logger->debug("Admin Page Add");
		
		ob_start();
		include('../app/src/crud/Page/add.php');
		$form = ob_get_clean();
        
		$this->view->render($response, 'admin/edit.twig', [
			'title' => 'Pages',
			'form' => $form,
			'jsPage' => 'add',
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
	
	
}
