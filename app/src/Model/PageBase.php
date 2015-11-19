<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /models/Page.php
----------------------------------------------------------------------------- */
namespace App\Model;

class PageBase extends BaseModel {  
	
	//ATTRIBUTES
	public $_title = 'Page';
	public $_id = 'pageID'; 
	public $_table = 'page';

	//FIELDS
	public $pageID = 0;
	public $title;
	public $permalink;
	public $metaTitle ;
	public $metaDescription; 
	public $metaKeywords; 
	public $status;
	public $type;
	public $isHardCoded;
	public $noDelete;
	public $dateAdded;
	public $dateModified;
	
	public function insert(){
		
		$this->setPermalink($this->title);
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
			(title, permalink, metaTitle, metaDescription, metaKeywords, status, dateAdded) 
			VALUES (%s, %s, %s, %s, %s, %s, %s)",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->permalink, "text"),
			Sanitize::input($this->metaTitle, "text"),
			Sanitize::input($this->metaDescription, "text"),
			Sanitize::input($this->metaKeywords, "text"),
			Sanitize::input($this->status, "text"),
			'NOW()');
		
		return $this->queryInsert($insert);
		
	}
	
	public function _update(){
	
		$update = sprintf("UPDATE ".$this->_table."
			SET title=%s, permalink=%s, metaTitle=%s, metaDescription=%s, metaKeywords=%s, status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->permalink, "text"),
			Sanitize::input($this->metaTitle, "text"),
			Sanitize::input($this->metaDescription, "text"), 
			Sanitize::input($this->metaKeywords, "text"),
			Sanitize::input($this->status, "text"),
			Sanitize::input($this->getId(), "int"));
	
		return $this->query($update);
	}
	
	/* 	EXTEND CORE-PERMA FOR HARD CODED
	----------------------------------------------------------------------------- */

	public function getSlug(){
		
		if($this->isHardCoded){
			return $this->permalink.'/'; 
		} else {
			return $this->_modReWritePath.$this->permalink.'/'; 
		}
		
	}
	
	/* FRONT HELPERS
	----------------------------------------------------------------------------- */
	public function isActive(){
		
		if($this->status != 'active'){
			return false;
		}
		
		if(!$this->isHardCoded){
			
			if($this->version->isLoaded()){
				
				return true;
				
			}
		}
	}
	
	/* ADMIN HELPERS
	----------------------------------------------------------------------------- */
	public function isActiveButUnpublished(){
		
		if(!$this->isHardCoded){
			
			$this->version->loadActiveByPage($this->getId());
			
			if(!$this->version->isLoaded()){
				return true;
			}
			
		}
		return false;
	}
}