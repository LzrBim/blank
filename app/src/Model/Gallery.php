<?php

namespace App\Model;

class Gallery extends GalleryBase {
	
	//CHILDREN
	public $galleryImage;
	public $galleryImages = array();
	
	public function __construct(){
		
		parent::__construct();
		
		$this->galleryImage = new GalleryImage();
	
	}
	
	public function with($with){
		
		if(!$this->isLoaded()){	return false;	}
		
		if(!is_array($with)){	$with = array($with);	}
		
		if($this->isLoaded()){
			
			foreach($with as $relation){
				
				if($relation == '*' || $relation == 'block'){
					
					$this->blocks = $this->block->fetchByPageAndSiteWide($this->id());
					
				}
				
				if($relation == '*' || $relation == 'version'){
					
					$this->version->loadActiveByPage($this->id());
					
				}
			}
			
			return $this;
			
		} 
		
		return false;	
		
	}
	
}
