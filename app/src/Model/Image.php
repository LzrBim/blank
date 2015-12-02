<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/src/models/Image.php
----------------------------------------------------------------------------- */

namespace App\Model;

use App\Lib\Uploader;
use App\Lib\ImageResizer;
use App\Lib\Sanitize;

class Image extends BaseModel {
		
	/* ATTRIBUTES */
	public $_title = 'Image';
	public $_id = 'imageID';
	public $_table = 'image';
	
	/* FIELDS */
	public $imageID = 0;
	public $fileNameOriginal;
	public $fileNameMain;
	public $fileNameThumb;
	public $fileNameSystem;
	public $dateAdded;
	public $dateModified; 
	
	/* SETTINGS */
	public $settings = array(
														 											 
 		/* FILE */
		'targetDir' => '', 
		'targetFileName' => '',
		'inputName' => 'uploadFile',
		
		/*ORIGINAL FILE SETTINGS */
		'originalWidth' => 1660,
		'originalHeight' => 1140,
		
		/* MAIN IMAGE SETTINGS */
		'mainWidth' => 640,
		'mainHeight' => 400,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => false,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => false,
		'thumbWidth' => 120,
		'thumbHeight' => 100,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => false
		
	);
	
	
	/* SYSTEM THUMB IMAGE SETTINGS */
	private $_systemWidth = 100;
	private $_systemHeight = 100;
	private $_versions = array('original','main','thumb','system');
	
	/* LOAD
	----------------------------------------------------------------------------- */
	public function __construct($settings = array()){
			
		$this->settings = array_merge($this->settings, $settings);
		
		parent::__construct();
		
	}
	
	/* CRUD
	----------------------------------------------------------------------------- */
	
	public function insert(){
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
				(fileNameOriginal, fileNameMain, fileNameThumb, fileNameSystem, dateAdded) 
				VALUES (%s, %s, %s, %s, NOW())",
			Sanitize::input($this->fileNameOriginal, "text"),
			Sanitize::input($this->fileNameMain, "text"),
			Sanitize::input($this->fileNameThumb, "text"),
			Sanitize::input($this->fileNameSystem, "text"));
		
		return $this->queryInsert($insert);
		
	}
	
	public function update(){
	
		$update = sprintf("UPDATE ".$this->_table."
				SET fileNameOriginal=%s, fileNameMain=%s, fileNameThumb=%s, fileNameSystem=%s
				WHERE ".$this->_id."=%d",
			Sanitize::input($this->fileNameOriginal, "text"),
			Sanitize::input($this->fileNameMain, "text"),
			Sanitize::input($this->fileNameThumb, "text"),
			Sanitize::input($this->fileNameSystem, "text"),
			Sanitize::input($this->id(), "int"));
		
		return $this->query($update);
	
	}
	
	public function remove($verbose = TRUE){
		
		if($this->isLoaded()){
	
			$this->deleteImageFiles();
	
			$update = sprintf("UPDATE ".$this->_table."
					SET fileNameOriginal='', fileNameMain='', fileNameThumb='', fileNameSystem=''
					WHERE ".$this->_id."=%d ",
				Sanitize::input($this->id(), "int"));
		
			if($this->query($update)){ 
				addMessage('success', $this->_title.' was removed successfully');
				return true;
				
			} else { 
				addMessage('error','Error removing '.$this->_title);
				return false;
			}
		}
	}
	
	public function delete($verbose = TRUE){
		
		if($this->isLoaded()){
			$this->deleteImageFiles();
			$this->_delete($verbose);
			return true;
		} else {
			wLog(3, 'Delete called on a non-loaded image');
			return false;
		}
	}
	
	private function deleteImageFiles(){
		
		foreach($this->_versions as $version){
			
			$path = $this->getBasePath($version);
			
			if($path){
				
				if(!unlink($path)){
					
					//ehm log?
					
				}
			}
		}
	}
	
	
	/* HELPERS */
	
	
	
	
	
	/* DISPLAY
	----------------------------------------------------------------------------- */
	
	/*
	USAGE
	getMain(array(
		'class' => '',
		'useAbsolutePath' => true
	));
	*/
	
	public function getMain($opts = array()){ 
		return $this->_getImage($this->fileNameMain, $opts);
	}
	
	public function getThumb($opts = array()){ 
		return $this->_getImage($this->fileNameThumb, $opts);
	}
	
	public function getOriginal($opts = array()){ 
		return $this->_getImage($this->fileNameOriginal, $opts);
	}
	
	public function getSystem($opts = array()){ 
		return $this->_getImage($this->fileNameSystem, $opts);
	}
	
	protected function _getImage($imagePath, $opts = array()){
		
		$defaults = array(
			'class' => 'img-responsive',
			'useAbsolutePath' => true
		);
		
		$opts = array_merge($defaults, $opts);
				
		//wLog(1, ASSET_BASE_PATH.$imagePath);		
				
		if (is_file(ASSET_BASE_PATH.$imagePath)) {
			
			$imagePath = ASSET_HTTP_PATH.$imagePath;
			if(!$opts['useAbsolutePath']){
				$imagePath = ASSET_RELATIVE_PATH.$imagePath;
			} 
			
			$class = '';
			if(!empty($opts['class']) ){
				$class = 'class="'.$opts['class'].'"';
			} 
			
      return '<img data-imageID="'.$this->imageID.'" src="'.$imagePath.'" '.$class.' />';
			
		} else {
			return '<!-- No Photo Found -->';
		}
	}	
	
	
	
	/*-----------------------------------------------------------------------------
	  GET IMAGE INFORMATION
	----------------------------------------------------------------------------- */

	public function getMainSrc($useAbsolutePath = true){ 
		return $this->_getSrc($this->fileNameMain, $useAbsolutePath);
	}
	
	public function getThumbSrc($useAbsolutePath = true){ 
		return $this->_getSrc($this->fileNameThumb, $useAbsolutePath);
	}
	
	public function getOriginalSrc($useAbsolutePath = true){ 
		return $this->_getSrc($this->fileNameOriginal, $useAbsolutePath);
	}
	
	public function getSystemSrc($useAbsolutePath = true){ 
		return $this->_getSrc($this->fileNameSystem, $useAbsolutePath);
	}
	
	protected function _getSrc($imagePath, $useAbsolutePath){
		
		if (file_exists(ASSET_BASE_PATH.$imagePath)) {
			
			if($useAbsolutePath){
				
				$imagePath = ASSET_HTTP_PATH.$imagePath;
				
			} else {
				
				$imagePath = ASSET_RELATIVE_PATH.$imagePath;
				
			}
      return $imagePath;
		} else {
			return '';
		}
	}	
	
	public function getWidth($type = 'main'){
		
		if(in_array($version, $this->_versions)){
			list($width, $height, $imageType) = @getimagesize($this->getBasePath($type));
			return $width;
		} else {
			wLog(2, get_class($this).'->'.__FUNCTION__.'() - Invalid image type');
			return false;
		}

	}
	
	public function getWidthSetting($type = 'main'){
		
		if($type == 'main'){
			return $this->settings['mainWidth'];
			
		} elseif($type == 'thumb'){
			return $this->settings['thumbWidth'];
			
		} elseif($type == 'original'){
			return $this->settings['originalWidth'];
			
		} else {
			wLog(1, get_class($this).'->'.__FUNCTION__.'() - Invalid image type');
			return false;
		}

	}
	
	public function getHeight($type = 'main'){
		
		if(in_array($version, $this->_versions)){
			list($width, $height, $imageType) = @getimagesize($this->getBasePath($type));
			return $height;
		} else {
			wLog(2, get_class($this).'->'.__FUNCTION__.'() - Invalid image type');
			return false;
		}

	}
	
	public function getHeightSetting($type = 'main'){
		
		if($type == 'main'){
			return $this->settings['mainHeight'];
			
		} elseif($type == 'thumb'){
			return $this->settings['thumbHeight'];
			
		} elseif($type == 'original'){
			return $this->settings['originalHeight'];
			
		} else {
			wLog(3, 'Invalid image type');
			return false;
		}

	}
	
	
	/*-----------------------------------------------------------------------------
	  HELPERS
	----------------------------------------------------------------------------- */
	
	public function hasMainImage(){ 
	
		if($this->fileNameMain && file_exists(ASSET_BASE_PATH.$this->fileNameMain)){
			return true;
		} else {
			return false;
		}
	}
	
	public function hasThumbImage(){ 
	
		if($this->settings['hasThumb']){
			//wLog(1, 'yes, settings thumb');
			
			if($this->fileNameThumb && file_exists(ASSET_BASE_PATH.$this->fileNameThumb)){
				//wLog(1, 'yes thumb');
				return true;
				
			} else {
				//wLog(1, 'yes no thumb file='.$this->fileNameThumb);
				return false;
			}
		} 
		//wLog(1, 'no thumb');
		return false;
		
	}
	
	public function hasOriginalImage(){ 
		if($this->fileNameOriginal && file_exists(ASSET_BASE_PATH.$this->fileNameOriginal)){
			return true;
		} else {
			return false;
		}
	}
	
	/* GET BASE PATH OF IMAGE / 3E ONLY
	----------------------------------------------------------------------------- */
	
	public function getBasePath($type){
		
		$fileName = '';
		
		if($type == 'main'){
			$fileName = $this->fileNameMain;
			
		} elseif($type == 'thumb'){
			$fileName = $this->fileNameThumb;
			
		} elseif($type == 'original'){
			$fileName = $this->fileNameOriginal;
			
		} elseif($type == 'system'){
			$fileName = $this->fileNameSystem;
			
		} else {
			wLog(1, 'Invalid image type');
			return '';
		}
		
		if (!empty($fileName) && file_exists(ASSET_BASE_PATH.$fileName)) {
			
			return ASSET_BASE_PATH.$fileName;
			
		} else {
			wLog(2, $type.' file did not exist in base path');
			return '';
		}
	}
	
	
	/*-----------------------------------------------------------------------------
	  SETTING HELPERS
	----------------------------------------------------------------------------- */
	
	public function isAspectLocked($type = 'main'){
		
		if($type == 'main'){
			return $this->settings['lockMainAspectRatio'];
			
		} elseif($type == 'thumb'){
			return $this->settings['lockThumbAspectRatio'];
			
		} else {
			wLog(3, 'Invalid image type');
			return false;
		}

	}	
	
	public function isForceOut($type = 'main'){
		
		if($type == 'main'){
			return $this->settings['forceOutMain'];
			
		} elseif($type == 'thumb'){
			return $this->settings['forceOutThumb'];
			
		} else {
			wLog(3, 'Invalid image type');
			return false;
		}

	}	
	
	
	
	public function getAspectRatio($type = 'main'){
		
		if($type == 'main'){
			return $this->settings['mainWidth']/$this->settings['mainHeight'];
			
		} elseif($type == 'thumb'){
			return $this->settings['thumbWidth']/$this->settings['thumbHeight'];
			
		} else {
			wLog(3, 'Invalid image type');
			return false;
		}

	}	
	
	
}