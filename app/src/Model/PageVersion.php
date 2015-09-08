<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/models/PageVersion.php
----------------------------------------------------------------------------- */

class PageVersion extends Core {  
	
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
	public $blocks;
	
	public $_validateRules = array( 
		'rules' => array( 
			'pageID' => array( 'required' => true )
		)
	);
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	protected function loadChildren(){
		
		$block = new PageVersionBlock();
		
		$this->blocks = $block->fetchCollectionByParent($this->getId());
		
	}

	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function loadActiveByPage($pageID){
		
		if(empty($pageID)){
			wLog(4, 'No pageID supplied');
		}
		
		return $this->loadWhere("pageID = ".$pageID." AND status = 'active'", true);
		
	}
	
	/* PUBLIC FETCH
	----------------------------------------------------------------------------- */
	

	/* ADMIN FETCH 
	----------------------------------------------------------------------------- */
	
	public function fetchAllByPage($parentID, $orderBy = '', $limit = ''){
		
		if(empty($parentID)){
			return array();
		}
		
		$where ="pageID = ".$parentID;
		
		return $this->fetch($where, $orderBy, $limit);	
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
	
	public function _insert(){
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
			(pageID, title, headline, status, dateAdded) 
			VALUES (%d, %s, %s, %s, %s)",
			Sanitize::input($this->pageID, "int"),
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->headline, "text"),
			Sanitize::input($this->status, "text"),
			'NOW()');
		
		if($this->query($insert)){ 
		
			$this->setInsertId();
			
			addMessage('success', $this->_title.' was saved successfully');
			return true;
				
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		} 
	}
	
	public function _update(){
		
		$update = sprintf("UPDATE ".$this->_table."
			SET title=%s, headline=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->headline, "text"),
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
		
			addMessage('success', $this->_title.' was updated successfully');
			return true;
			
		} else { 
			addMessage('error','Error updating '.$this->_title);
			return false;
		}
	}	
	
	public function delete($verbose = TRUE){		
		
		foreach($this->blocks as $block){
			
			$block->deletePageVersionBlockLink($block->getId(), $this->getId());
			
			if(!$block->isRepeating){

				$block->delete();
			
			}
		}
		
		return $this->_delete($verbose);
	}
	
	/* ADMIN METHODS
	----------------------------------------------------------------------------- */
	
	public function publish($pageVersionID, $pageID = 0){
		
		if(empty($pageVersionID)){
			wLog(3, 'No pageVersionID supplied');
			return false;
		}
		
		if(empty($pageID)){
			
			wLog(2, 'No pageID supplied, looking up');
			
			$pv = $this->load($pageVersionID);
			$pageID = $pv->pageID;
		}
		
		if(empty($pageID)){
			wLog(3, 'No pageID found');
			return false;
		}
		
		$query = "UPDATE ".$this->_table." SET status = 'inactive' WHERE pageID = ".$pageID." AND status = 'active' ; ";
	
		if($this->query($query)){ 
		
			$query = "UPDATE ".$this->_table." SET status = 'active' WHERE pageVersionID = ".$pageVersionID.";";
		
			if($this->query($query)){ 
		
				addMessage('success', $this->_title.' was published successfully');
				return true;
				
			} else { 
				addMessage('error','Error publishing '.$this->_title);
				return false;
			}
			
		} else { 
			addMessage('error','Error publishing '.$this->_title);
			return false;
		}
	}	
	
		
	public function makeCopy($pageVersionID){
		
		if(empty($pageVersionID)){
			wLog(3, 'No pageVersionID supplied');
			return false;
		}
		
		$original = new $this;
		$original->load($pageVersionID);
		
		$copy = new $this;
		$copy->pageID = $original->pageID;
		$copy->title = $original->title;
		$copy->headline = $original->headline;
		$copy->status = 'inactive';
		
		reTitleCopied($copy->title);
		
		if($copy->insert()){
			
			$copy->setInsertId();
			
			//DUPLICATE VERSION BLOCKS AND BLOCK LINKS
			foreach($original->blocks as $block){
				
				$newBlockID = $block->makeCopy($block->getId());
				wLog(1, 'copied block');
	
				$block->addPageVersionBlockLink($newBlockID, $copy->getId());
				wLog(1, 'addPageVersionBlockLink('.$copy->getId().', '.$newBlockID.')');
			}			
			
			clearMessages();
			addMessage('success', $this->_title.' was copied successfully');
			
			return $copy->getId();
			
		}
		return false;
		
	}	
	
	
	/* 	CHILD HELPERS
	----------------------------------------------------------------------------- */

	public function block($pageVersionBlockID){
		
		foreach($this->blocks as $block){
			
			if($block->getId() == $pageVersionBlockID){
				
				return $block->display();
			}
		}
		
		wLog(3, 'pageVersionBlock Block not found: '.$pageVersionBlockID);
		
		return '';
		
	}
	
	public function has_block($pageVersionBlockID){
		
		foreach($this->blocks as $block){
			
			if($block->getId() == $pageVersionBlockID){
				
				return true;
			}
		}
		
		return false;
		
	}
}