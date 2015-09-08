<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/models/GalleryImage.php
----------------------------------------------------------------------------- */

namespace App\Model;

class GalleryImage extends GalleryImageBase {
	
	public $image; 
		
	private $_imageSettings = array(
 		
		'uploadMode' => 'hashInsertOverWriteUpdate',
		'targetDirectory' => 'gallery/', 
		
		/* ORIGINAL FILE SETTINGS */
		'originalWidth' => 1660,
		'originalHeight' => 1140,
		
		/* MAIN IMAGE SETTINGS */
		'hasMain' => true,
		'mainWidth' => 400,
		'mainHeight' => 400,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => false,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => true,
		'thumbWidth' => 200,
		'thumbHeight' => 300,
		'hasThumbCrop' => true,
		'forceOutThumb' => false,
		'lockThumbAspectRatio' => true

	);
	
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function __construct(){
		
		parent::__construct();
		
		$this->image = new Image($this->_imageSettings);
		
	}
	
	
	protected function loadHook(){
		
		//IMAGE
		if(!empty($this->imageID)){
			$this->image->load($this->imageID);
		}

	}
	
	public function fetchByGallery($parentID, $orderBy = 'rank ASC', $limit = ''){
		
		$galleryImages = array();
		
		if(empty($parentID)){
			return $galleryImages; 
		}

		$galleryImages = $this->fetch("galleryID = ".$parentID." AND status = 'active'", $orderBy, $limit);	

		//load in their children
		$children = array();
		
		foreach($galleryImages as $galleryImage){
			$children[] = $galleryImage->imageID; 
		}
		
		//$image
		$image = new Image();
		
		$images = $image->fetch('imageID IN ('.implode(',',$children).')');
		
		foreach($images as $i){
			
			$image = new Image();
			$image->loadByData($i);
			
			foreach($galleryImages as $galleryImage){
				
				if($galleryImage->imageID == $i->imageID){
					
					$galleryImage->image = $image;
					
				}
			}
						
		}
		
		return $galleryImages;
		
	}	
	
	public function fetchActiveByParentCount($parentID){
		
		if(empty($parentID)){
			return 0;
		}
		
		$where = "galleryID = ".$parentID." AND status = 'active'";
		
		return $this->fetchCount($where);
		
	}
	
	public function fetchAllByParent($parentID, $orderBy = '', $limit = ''){
		
		if(empty($parentID)){
			return array();
		}
		
		$where ="galleryID = ".$parentID;
		
		return $this->fetch($where, $orderBy, $limit);	
		
	}
	
	public function fetchAllByParentCount($parentID){
		
		if(empty($parentID)){
			return 0;
		}
		
		$where = "galleryID = ".$parentID;
		
		return $this->fetchCount($where);
		
	}
	
}