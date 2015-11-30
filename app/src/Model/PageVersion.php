<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/models/PageVersion.php
----------------------------------------------------------------------------- */

namespace App\Model;
use \App\Lib\Help;
use \App\Lib\Sanitize;

class PageVersion extends BaseModel {  
	
	//ATTRIBUTES
	public $_title = 'Page Version';
	public $_id = 'pageVersionID'; 
	public $_table = 'pageVersion';

	//FIELDS
	public $pageVersionID = 0;
	public $pageID;
	public $title; //this is Version Note
	public $headline;
	public $status; // active, inactive, archived 
	public $dateAdded;
	public $dateModified;
	
	//CHILDREN
	public $block;
	public $blocks = array();
	
	public function __construct(){
		
		parent::__construct();
		
		$this->block = new PageVersionBlock();
		
	}
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function loadActiveByPage($pageID){
		
		if($this->loadWhere("pageID = ".$pageID." AND status = 'active'")){
			
			$this->blocks = $this->block->fetchCollectionByParent($this->id());
			
		}	
		
	}
	
	/* PUBLIC FETCH
	----------------------------------------------------------------------------- */
	

	/* ADMIN FETCH 
	----------------------------------------------------------------------------- */
	
	public function fetchAllByPage($pageID, $orderBy = '', $limit = ''){
		
		if(empty($pageID)){
			return array();
		}
		
		return $this->fetch("pageID = ".$pageID, $orderBy, $limit);	
	}	
	
	public function fetchAllByPageCount($parentID){
		
		if(empty($parentID)){
			return 0;
		}
		
		$where = "pageID = ".$parentID;
		
		return $this->fetchCount($where);
		
	}

	/* CRUD
	----------------------------------------------------------------------------- */
	
	public function insert(){
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
			(pageID, title, headline, status, dateAdded) 
			VALUES (%d, %s, %s, %s, %s)",
			Sanitize::input($this->pageID, "int"),
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->headline, "text"),
			Sanitize::input($this->status, "text"),
			'NOW()');
		
		return $this->queryInsert($insert);
	}
	
	public function update(){
		
		$update = sprintf("UPDATE ".$this->_table."
			SET title=%s, headline=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->headline, "text"),
			Sanitize::input($this->id(), "int"));
	
		return $this->query($update);
	}	
	
	public function delete($verbose = TRUE){		
		
		foreach($this->blocks as $block){
			
			$block->deletePageVersionBlockLink($block->id(), $this->id());
			
			if(!$block->isRepeating){

				$block->delete();
			
			}
		}
		
		return $this->_delete($verbose);
	}
	
	/* ADMIN METHODS
	----------------------------------------------------------------------------- */
	
	public function publish($pageVersionID, $pageID){
		
		if(empty($pageVersionID) || empty($pageID)){
			die('No pageVersion ID or pageID supplied');
		}
		
		$query = "UPDATE ".$this->_table." 
			SET status = IF( pageVersionID = ".$pageVersionID.", 'active', 'inactive')
			WHERE pageID = ".$pageID;
	
		return $this->query($query);
	}	
	
		
	public function makeCopy($pageVersionID){
		
		if(empty($pageVersionID)){
			die('No pageVersionID supplied');
			return false;
		}
		
		$original = new $this;
		$original->load($pageVersionID);
		
		$copy = new $this;
		$copy->pageID = $original->pageID;
		$copy->title = $original->title;
		$copy->headline = $original->headline;
		$copy->status = 'inactive';
		
		Help::copyTitle($copy->title);
		
		if($copy->insert()){
				
			//DUPLICATE VERSION BLOCKS AND BLOCK LINKS
			foreach($original->blocks as $block){
				
				$newBlockID = $block->makeCopy($block->id());
	
				$block->addPageVersionBlockLink($newBlockID, $copy->id());
				
			}			
			
			return $copy;
			
		}
		return false;
		
	}	
	
	
	/* 	CHILD HELPERS
	----------------------------------------------------------------------------- */

	public function block($pageVersionBlockID){
		
		foreach($this->blocks as $block){
			
			if($block->id() == $pageVersionBlockID){
				
				return $block->display();
			}
		}
		
		return '';
		
	}
	
	public function has_block($pageVersionBlockID){
		
		foreach($this->blocks as $block){
			
			if($block->id() == $pageVersionBlockID){
				
				return true;
			}
		}
		
		return false;
		
	}
}