<?php
namespace App\Model;

class GalleryBase extends BaseModel {
	
	use SluggableTrait;
	
	//CORE ATTRIBUTES
	public $_title = 'Gallery';
	protected $_id = 'galleryID';
	protected $_table = 'gallery';
	
	public $galleryID = null;
	public $title;
	public $description;
	public $permalink;
	public $status; 
	public $dateAdded;
	public $dateModified;	

	/* LOAD
	----------------------------------------------------------------------------- */
	public function insert(){
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
			(title, description, status, dateAdded) 
			VALUES (%s, %s, %s, %s)",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->description, "editor"), 
			Sanitize::input($this->status, "text"),
			'NOW()');
		
		if($this->queryInsert($insert)){ 
			
			return true;
			
		} 
		
		return false; 
		
	}
	
	public function update(){
		
		$update = sprintf("UPDATE ".$this->_table."
				SET title=%s, description=%s, permalink=%s, status=%s
				WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->description, "editor"), 
			Sanitize::input($this->permalink, "text"), 
			Sanitize::input($this->status, "text"),  
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 			
			return true;
			
		} 
		
		return false;
		
	}
	
}
