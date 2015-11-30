<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/src/model/Video.php
----------------------------------------------------------------------------- */

namespace App\Model;
use \App\Lib\Sanitize;

class Video extends BaseModel { 
	
	//ATTRIBUTES
	public $_title = 'Video';
	public $_id = 'videoID';
	public $_table = 'video';
	
	//FIELDS
	public $videoID = 0;
	public $imageID = 0;
	public $title;
	public $embed;
	public $permalink;
	public $status;
	public $rank = 100;
	
	private $_imageSettings = array(
 		
		'uploadMode' => 'hash',  //hashInsertOverWriteUpdate, overwrite,	hash
		'targetDirectory' => 'video/', 
		'targetFileName' => '',
		'inputName' => 'uploadFile',
		
		/*ORIGINAL FILE SETTINGS */
		'originalWidth' => 1660,
		'originalHeight' => 1140,
		
		/* MAIN IMAGE SETTINGS */
		'hasMain' => true,
		'mainWidth' => 1280,
		'mainHeight' => 500,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => true,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => false,
		'thumbWidth' => 120,
		'thumbHeight' => 100,
		'hasThumbCrop' => true,
		'forceOutThumb' => false,
		'lockThumbAspectRatio' => false

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
		
		$this->_setData();

	}
	
	
	/* FETCH
	----------------------------------------------------------------------------- */
	
	
	/* CRUD
	----------------------------------------------------------------------------- */
	public function insert(){
		
		/* IMAGE */
		if($this->image->insert()){
			$this->imageID = $this->image->id();
		}
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
			(imageID, title, embed, status, dateAdded) 
			VALUES (%d, %s, %s, %s, NOW())",
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->embed, "string"), 
			Sanitize::input($this->status, "text"));
		
		if($this->query($insert)){ 
			
			$this->setInsertId();
			
			//ADD PERMALINK
			$this->setPermalink($this->title);
			
			$update = sprintf("UPDATE ".$this->_table." SET permalink=%s WHERE ".$this->_id."=%d",
					Sanitize::input($this->permalink, "text"),  
					Sanitize::input($this->id(), "int"));
			
			if($this->query($update)){ 
						
				addMessage('success', $this->_title.' was saved successfully');				
				return true;
				
			} else {
				addMessage('error', 'Error adding permalink');
				return false;
			}
			
		} else { 
			addMessage('error','Error updating '.$this->_title);
			return false;
		} 
		
				
	}
	
	public function update(){
		
		//UPDATE IMAGE
		if(!empty($this->imageID)){
			$this->image->update();
		} else {
			if($this->image->insert()){
				$this->imageID = $this->image->id();
			}
		}
		
		$update = sprintf("UPDATE ".$this->_table."
				SET imageID=%d, title=%s, embed=%s, permalink=%s, status=%s
				WHERE ".$this->_id."=%d",
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->embed, "string"), 
			Sanitize::input($this->permalink, "text"),
			Sanitize::input($this->status, "text"),  
			Sanitize::input($this->id(), "int"));
	
		if($this->query($update)){ 
															
			addMessage('success', $this->_title.' was saved successfully');
			return true;
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
	}
	
	
	
	public function delete($verbose = TRUE){
		
		$this->image->delete();
		
		$this->_delete($verbose);
		
	}
	
	
	/* HELPERS
	----------------------------------------------------------------------------- */
	
	private function _setData(){
		
		$this->data = array(
			'src' 		=> '',
			'width' 	=> '',
			'height' 	=> '',
			'aspect' 	=> '',
			'thumb' 	=> ''
		);
		
		if(!empty($this->embed)){
		
			$doc = new \DOMDocument();
			$doc->loadHTML($this->embed);
			$tag = $doc->getElementsByTagName('iframe');
			
			$this->data['src'] = $tag->item(0)->getAttribute('src');
			
			$this->data['width'] = $tag->item(0)->getAttribute('width');
			
			$this->data['height'] = $tag->item(0)->getAttribute('height');
			
			//SET ASPECT RATIO
			if(!empty($this->data['width']) && !empty($this->data['height'])){

				//16/9 = 1.77   4/3 = 1.33;
				
				if( ($this->data['width'] / $this->data['height']) > 1.55 ){
					$this->data['aspect'] = '16by9';
				} else {
					$this->data['aspect'] = '4by3';
				}
				
			}
			
			//GATHER YOUTUBE THUMBNAIL
			//medium - http://img.youtube.com/vi/<insert-youtube-video-id-here>/mqdefault.jpg
			//hires - http://img.youtube.com/vi/<insert-youtube-video-id-here>/maxresdefault.jpg
			
			//Embed <iframe src="https://www.youtube.com/embed/oazFK9awniE" frameborder="0" allowfullscreen></iframe>
			//embed/oazFK9awniE?
			
			$pos = strpos($this->data['src'], 'embed/');
			if($pos === false){
				wLog(3, 'Invalid video embed url');
			}
			$hash = substr($this->data['src'], $pos + 6 );			
			$pos = strpos($hash, '?');			
			if($pos !== false){
				$hash = substr($hash, 0, $pos);
			}
			
			$this->data['thumb'] = 'http://img.youtube.com/vi/'.$hash.'/mqdefault.jpg';
			http://img.youtube.com/vi/www.youtube.com/embed/oazFK9awniE/mqdefault.jpg
			
			
		} else {
			wLog(2, 'No video embed yet');
		}
		

	}
	
	
	public function getAspect(){
		return $this->data['aspect'];
	}
	
	
	public function getSrc(){
		return $this->data['src'];
	}
	
	
	
	/* CHOOSES BETWEEN IMAGE AND YOUTUBE THUMB */
	public function hasImage(){
		
		if($this->image->hasMainImage()){
			return true;
		}
		
		if(!empty($this->data['thumb'])){
			return true;
		}
		
		return false;
		
	}
	
	public function getThumbSrc(){
		
		if($this->image->hasMainImage()){
			return $this->image->getMainSrc();
		}
		
		return $this->data['thumb'];
		
	}
		
	
}