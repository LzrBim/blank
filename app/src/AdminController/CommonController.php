<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/AdminController/PageController.php
----------------------------------------------------------------------------- */
namespace App\AdminController;

use App\Model\Page;

class CommonController extends BaseController {
	
	public function insert($request, $response, $args){
	
		$this->logger->debug("Common Controller Add");
		 
		$obj = new $args['model'];
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
	
	public function update($request, $response, $args){
	
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
