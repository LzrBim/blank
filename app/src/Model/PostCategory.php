<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/PostCategory.php
----------------------------------------------------------------------------- */

class PostCategory extends CorePerma {
	
	//ATTRIBUTES
	
	public $_title = 'Category';
	public $_id = 'postCategoryID';
	public $_table = 'postCategory';
	protected $_modReWritePath = 'blog/category/'; 
	
	//FIELDS
	public $postCategoryID = 0;
	public $title = '';
	public $description = '';
	public $permalink = '';
	public $status = 'active';
	public $rank = 100;
	
	//PICKUPS
	public $population = 0;
	
	
	//VALIDATE RULES
	public $_validateRules = array(
		'rules' => array( 
			'title' => array( 'required' => true )
		)
	);
	
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	
	/* CRUD
	----------------------------------------------------------------------------- */

	public function insert(){
		
		$this->setPermalink($this->title);
		
		if(!$this->permalinkExists($this->permalink)){
		
			$insert = sprintf("INSERT INTO ".$this->_table." 
				(title, description, permalink, status, rank) 
				VALUES (%s, %s, %s, %s, %d)",
				Sanitize::input($this->title, "text"),
				Sanitize::input($this->permalink, "text"),
				Sanitize::input($this->description, "text"),
				Sanitize::input($this->status, "text"),
				Sanitize::input($this->rank, "int"));
			
			if($this->query($insert)){ 

				$this->setInsertId();
			
				return true;
			} else { 
				addMessage('error', 'Error inserting '.$this->_title);
				return false;
			} 
		} else { 
			addMessage('error', 'This category already exists');
			return false;
		} 
		
	}
	
	public function update(){
		
		//DID THE TITLE CHANGE?
		$original = new $this;
		$original->load($this->getId());
		
		if($this->title != $original->title){
			
			//generate a new permalink to see if we're colliding. 
			$this->permalink = $this->buildPermalink($this->title);
			
			if($this->permalinkExists($this->permalink)){
				addMessage('warning', 'Category was not updated because this title already exists');
				return false;
			}
			
		}
		
		$update = sprintf("UPDATE ".$this->_table."
			SET title=%s, description=%s, status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->description, "editor"), 
			Sanitize::input($this->status, "text"), 
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			addMessage('success', $this->_title.' was saved successfully');
			return true;
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
	}
	
	
	/* FORM HELPERS 
	----------------------------------------------------------------------------- */
	
	
	
}