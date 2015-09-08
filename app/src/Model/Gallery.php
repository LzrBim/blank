<?php
namespace App\Model;

class Gallery extends GalleryBase {
	
	//CHILDREN
	public $galleryImages = array();
	
	public function __construct(){
		
		parent::__construct();
		
		$this->galleryImage = new GalleryImage();
	
	}
	
	public function loadChildren($childArgs = array()){
		
		if(in_array('galleryImages', $childArgs)){
			
			$this->galleryImages = $this->galleryImage->fetchByGallery($this->getId(), 'rank ASC', $limit = '');
			
		}

	}
	
}
