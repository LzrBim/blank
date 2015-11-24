<?php

namespace App\Controller;

use App\Model\Page;

class PageController extends BaseController {
	
	public function detail($request, $response, $args){

		$this->logger->info("Page action dispatched");
		
		$page = new Page();
		$page->loadBySlug($args['slug'])
					->with(['pageBlock', 'pageVersion']);
    
		if($page->isLoaded()){
		
			$this->view->render($response, 'front/page.twig', array(
				'page' 		=> $page,
				'jsPage' 	=> $args['slug']
			));
		
			return $response;
			
		} else {
			
			return $this->error404($response);
			
			//return $response->withStatus(404);
		}
	
	}
}
