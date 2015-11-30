<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/Faq.php
----------------------------------------------------------------------------- */

namespace App\Model;

class Faq extends BaseModel {
	
	//ATTRIBUTES
	public $_title = 'FAQ';
	public $_id = 'faqID';
	public $_table = 'faq';
	protected $_modReWritePath = 'faq/';
	 
	//FIELDS
	public $faqID = 0; 
	public $title;
	public $description;
	public $permalink;
	public $status;
	public $rank = 100;
	
	//CHILDREN
	public $tag;
	
	public $_validateRules = array(
		'rules' => array(
			'title' => array( 'required' => true ),
			'description' => array( 'required' => true )
		)
	);
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function __construct(){
		
		parent::__construct();
		
		$this->tag = new FaqTag();
		
	}
		
	protected function loadChildren(){
		
		$this->tag->loadCollectionByParent($this->id());
		
	}
	
	/* FETCH
	----------------------------------------------------------------------------- */
	public function fetchActiveByTag($tagID, $orderBy = '', $limit = ''){

		$query = "SELECT ".$this->_table.".*
			FROM ".$this->_table.", ".$this->tag->_linkTable."
			WHERE  ".$this->tag->_linkTable.".tagID = ".$tagID."
			AND ".$this->_table.".".$this->_id." = ".$this->tag->_linkTable.".parentID
			AND ".$this->_table.".status = 'active'";
			
		if(!empty($orderBy)){
			$query .= "ORDER BY ".$orderBy." ";
		} else {
			$query .= "ORDER BY ".$this->_id." DESC ";
		}
		
		if(!empty($limit)){
			$query .= "LIMIT ".$limit;
		}
		
		return $this->loadCollection($query);
	}

	public function fetchActiveByTagCount($tagID){
		
		$query = "SELECT COUNT(tagLinkID) as count
			FROM ".$this->tag->_linkTable.", ".$this->_table."
			WHERE tagID = ".$tagID."
			AND ".$this->_table.".".$this->_id." = ".$this->tag->_linkTable.".parentID
			AND ".$this->_table.".status = 'active'";
			
		return $this->queryCount($query);
	}
	
	
	/* CRUD
	----------------------------------------------------------------------------- */
		
	public function insert(){
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
				(title, description, status, rank) 
				VALUES (%s, %s, %s, %d)",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->description, "editor", false), 
			Sanitize::input($this->status, "text"), 
			Sanitize::input($this->rank, "int"));
		
		if($this->query($insert)){ 
			
			$this->setInsertId();
			
			//ADD PERMALINK
			$this->setPermalink($this->title);
			
			$update = sprintf("UPDATE ".$this->_table." SET permalink=%s WHERE ".$this->_id."=%d",
					Sanitize::input($this->permalink, "text"),  
					Sanitize::input($this->id(), "int"));
			
			if($this->query($update)){ 
						
				//INSERT TAGS
				$this->tag->updateTagsByTagId($this->id());
				
				addMessage('success','FAQ was saved successfully');				
				return true;
				
			} else {
				addMessage('error', 'Error adding permalink');
				return false;
			}
			
		} else { 
			addMessage('error','Error saving FAQ');
			return false;
		}  
	}
	
	public function update(){
		
		$update = sprintf("UPDATE ".$this->_table."
				SET title=%s, description=%s, status=%s, rank=%d
				WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->description, "editor", false), 
			Sanitize::input($this->status, "text"), 
			Sanitize::input($this->rank, "int"), 
			Sanitize::input($this->id(), "int"));
	
		if($this->query($update)){ 
			
			//UPDATE TAGS
			$this->tag->updateTagsByTagId($this->id());
			
			addMessage('success', $this->_title.' was updated successfully');
			return true;
			
		} else { 
			addMessage('error','Error updating '.$this->_title);
			return false;
		}
	}
	
	public function delete($verbose = TRUE){
		
		$this->tag->deleteAllTagLinksByParent($this->id());
		$this->_delete($verbose);
	}
	
}
