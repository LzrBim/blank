<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/Controller/GalleryController.php
----------------------------------------------------------------------------- */

namespace App\Controller;

use App\Model\Gallery; 

class GalleryController extends BaseController { 
	
	public function index($request, $response, $args){
		
		$this->logger->info("Gallery index action dispatched");
		
		$gallery = new Gallery();
		
		$galleries = $gallery->fetchActive();
		
		$this->view->render($response, 'front/gallery.twig', [
			'title' => 'Galleries',
			'galleries' => $galleries,
			'jsPage' => 'gallery'																					 
		]);
	
		return $response;
	
	}
	
	public function detail($request, $response, $args){
		
		$gallery = new Gallery();
		$gallery->loadBySlug($args['slug'], array('galleryImages'));
		
		$this->logger->info("Gallery details action dispatched");
		
		//var_dump($gallery);
		
		$this->view->render($response, 'front/gallery_detail.twig', [
			'title' => $gallery->title,
			'images' => $gallery->galleryImages,
			'jsPage' => 'gallery-detail'																					 
		]);
	
		return $response;
	
	}
}
