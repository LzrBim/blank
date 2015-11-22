<?php

namespace App\Controller;

use App\Model\Page;

class PageController extends BaseController {
	
	public function detail($request, $response, $args){

		$this->logger->info("Page action dispatched");
		
		$page = new Page();
		$page->loadBySlug($args['slug'])
					->with(['pageBlock', 'pageVersion']);
        
		$this->view->render($response, 'front/page.twig', array(
			'page' => $page			
		));
		
		return $response;
	
	}
}
