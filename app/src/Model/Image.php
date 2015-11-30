<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/src/models/Image.php
----------------------------------------------------------------------------- */

namespace App\Model;

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
	
	
	/* UPLOADER SETTINGS */	
	private $_uploaderSettings = array(
		'targetFileName' 	=> '',
		'allowedList' 		=> 'jpg,gif,png',
		'restrictToMime' 	=> true,
		'overwrite' 			=> false,
		'useHashPrevent' 	=> true,
		'maxFileSize' 		=> 20971520
	);

	
	/* SETTINGS */
	private $_settings = array(
														 											 
 		/* FILE */
		'uploadMode' => 'hashInsertOverWriteUpdate',  //hashInsertOverWriteUpdate, overwrite,	hash
		'targetDirectory' => '', 
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
			
		$this->_settings = array_merge($this->_settings, $settings);
		
		parent::__construct();
		
	}
	
	
	/* CRUD
	----------------------------------------------------------------------------- */
	
	public function insert($fileArrayIndex = 0){
		
		if($this->upload('insert', $fileArrayIndex)){
				
			$insert = sprintf("INSERT INTO ".$this->_table." 
					(fileNameOriginal, fileNameMain, fileNameThumb, fileNameSystem, dateAdded) 
					VALUES (%s, %s, %s, %s, NOW())",
				Sanitize::input($this->fileNameOriginal, "text"),
				Sanitize::input($this->fileNameMain, "text"),
				Sanitize::input($this->fileNameThumb, "text"),
				Sanitize::input($this->fileNameSystem, "text"));
			
			if($this->query($insert)){ 
				$this->setInsertId();
				return true;
				
			} else { 
				addMessage('error','Error inserting '.$this->_title);
				return false;
			} 
		}
		return false;
	}
	
	public function update(){
		
		if($this->upload('update')){
		
			$update = sprintf("UPDATE ".$this->_table."
					SET fileNameOriginal=%s, fileNameMain=%s, fileNameThumb=%s, fileNameSystem=%s
					WHERE ".$this->_id."=%d",
				Sanitize::input($this->fileNameOriginal, "text"),
				Sanitize::input($this->fileNameMain, "text"),
				Sanitize::input($this->fileNameThumb, "text"),
				Sanitize::input($this->fileNameSystem, "text"),
				Sanitize::input($this->id(), "int"));
		
			if($this->query($update)){ 
				addMessage('success', $this->_title.' was saved successfully');
				return true;
				
			} else { 
				addMessage('error','Error saving '.$this->_title);
				return false;
			}
		} 
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
					wLog(3, 'This should NEVER happen');
				}
			}
		}
	}
	
	
	/* HELPERS */
	
	public function upload($action, $fileArrayIndex = -1){
		
		//synthesize the uploader settings based on uploaderMode
		if($action == 'insert'){
			
			if($this->_settings['uploadMode'] == 'hashInsertOverWriteUpdate'){
				$this->_uploaderSettings['overwrite'] = false;
				$this->_uploaderSettings['useHashPrevent'] = true;

			} elseif($this->_settings['uploadMode'] == 'hash'){
				
				$this->_uploaderSettings['overwrite'] = false;
				$this->_uploaderSettings['useHashPrevent'] = true;
				
			} elseif($this->_settings['uploadMode'] == 'overwrite'){
				
				$this->_uploaderSettings['overwrite'] = true;
				
			} else {
				wLog(2, 'Image::upload(insert) - invalid setting uploadMode');
			}
			
		} else { /* update */
			
			if($this->_settings['uploadMode'] == 'hashInsertOverWriteUpdate'){
				$this->_uploaderSettings['overwrite'] = true;

			} elseif($this->_settings['uploadMode'] == 'hash'){
				
				$this->_uploaderSettings['overwrite'] = false;
				$this->_uploaderSettings['useHashPrevent'] = true;
				
			} elseif($this->_settings['uploadMode'] == 'overwrite'){
				
				$this->_uploaderSettings['overwrite'] = true;
				
			} else {
				wLog(2, 'Image::upload(update) - invalid setting uploadMode');
			}
			
			/* if we're overwriting and there's already a file... */
			if($this->_uploaderSettings['overwrite'] == true && !empty($this->fileNameMain)){ 
			
				$info = pathinfo($this->fileNameMain);
				$this->_uploaderSettings['targetFileName'] = $info['filename'];
				$this->_uploaderSettings['allowedList'] = $info['extension'];
			}

		}
		
		$uploader = new Uploader($this->_settings['inputName'], $this->_settings['targetDirectory'], $this->_uploaderSettings, $fileArrayIndex);
		
		if($uploader->isUploaded()){

			if($uploader->upload()){
				
				$this->fileNameMain = $this->_settings['targetDirectory'].$uploader->getFileName();
			
				$info = pathinfo($uploader->getBaseFilePath());
				$originalName = $info['filename'].'_o.'.$info['extension'];
				$thumbName = $info['filename'].'_t.'.$info['extension'];
				$systemName = $info['filename'].'_st.'.$info['extension'];
				
				$originalPath = $info['dirname'].'/'.$originalName;
				$thumbPath = $info['dirname'].'/'.$thumbName;
				$systemPath = $info['dirname'].'/'.$systemName;
				
				/* box fit the original to a manageable size from settings and rename it _o suffix */
				$imageResizer = new ImageResizer($uploader->getBaseFilePath(), $originalPath);
				
				wLog(1, 'Image Upload - boxFit original');
				if($imageResizer->boxFit($this->_settings['originalWidth'], $this->_settings['originalHeight'], FALSE, TRUE)){
					
					$this->fileNameOriginal = $this->_settings['targetDirectory'].$originalName;
					
					/* box fit the main image */
					
					$imageResizer = new ImageResizer($uploader->getBaseFilePath());
					
					wLog(1, 'Image Upload - boxFit main');
					
					if($imageResizer->boxFit($this->_settings['mainWidth'], $this->_settings['mainHeight'], FALSE, FALSE)){
						
						wLog(1, 'Main image was boxFit successfully');
					
						if($this->_settings['hasThumb']){
											
							$imageResizer = new ImageResizer($uploader->getBaseFilePath(), $thumbPath);
	
							wLog(1, 'Image Upload - boxCrop thumb');
							
							if($imageResizer->boxCrop($this->_settings['thumbWidth'], $this->_settings['thumbHeight'], FALSE)){
								
								$this->fileNameThumb = $this->_settings['targetDirectory'].$thumbName;
								
							  wLog(1, 'Thumb image was boxCrop successfully');
								
							} else {
								wLog(1, 'box fitting thumb failed');
							}
						}
						
						/* Generate the system thumb  100 x 100*/
						$imageResizer = new ImageResizer($uploader->getBaseFilePath(), $systemPath);
						
						wLog(1, 'Image Upload - boxCrop System thumb');
						
						if($imageResizer->boxCrop($this->_systemWidth, $this->_systemHeight, FALSE, FALSE)){
							$this->fileNameSystem = $this->_settings['targetDirectory'].$systemName;
							wLog(1, 'System image was boxCrop successfully');
						}
					
					}
				}
				
				return true;
				
			} else {
				wLog(1, 'Upload() failed');
				return false;
			}
		}	else {
			wLog(1, 'No picture uploaded');
			return false;
		}
	}
	
	
	
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
			return $this->_settings['mainWidth'];
			
		} elseif($type == 'thumb'){
			return $this->_settings['thumbWidth'];
			
		} elseif($type == 'original'){
			return $this->_settings['originalWidth'];
			
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
			return $this->_settings['mainHeight'];
			
		} elseif($type == 'thumb'){
			return $this->_settings['thumbHeight'];
			
		} elseif($type == 'original'){
			return $this->_settings['originalHeight'];
			
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
	
		if($this->_settings['hasThumb']){
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
			return $this->_settings['lockMainAspectRatio'];
			
		} elseif($type == 'thumb'){
			return $this->_settings['lockThumbAspectRatio'];
			
		} else {
			wLog(3, 'Invalid image type');
			return false;
		}

	}	
	
	public function isForceOut($type = 'main'){
		
		if($type == 'main'){
			return $this->_settings['forceOutMain'];
			
		} elseif($type == 'thumb'){
			return $this->_settings['forceOutThumb'];
			
		} else {
			wLog(3, 'Invalid image type');
			return false;
		}

	}	
	
	
	
	public function getAspectRatio($type = 'main'){
		
		if($type == 'main'){
			return $this->_settings['mainWidth']/$this->_settings['mainHeight'];
			
		} elseif($type == 'thumb'){
			return $this->_settings['thumbWidth']/$this->_settings['thumbHeight'];
			
		} else {
			wLog(3, 'Invalid image type');
			return false;
		}

	}	
	
	
}