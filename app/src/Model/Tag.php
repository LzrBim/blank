<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/Tag.php
----------------------------------------------------------------------------- */

/* 
CREATE TABLE IF NOT EXISTS `<model>Tag` (  
	`tagID` int(11) unsigned NOT NULL AUTO_INCREMENT,  
	`title` varchar(160) NOT NULL, 
	`permalink` varchar(160) NOT NULL, 
	`status` varchar(20) DEFAULT NULL,  
	PRIMARY KEY (`tagID`),  
	KEY `title` (`title`)
)	ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `<model>TagLink` (
  `tagLinkID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parentID` int(11) unsigned NOT NULL,
  `tagID` int(11) unsigned NOT NULL,
  PRIMARY KEY (`tagLinkID`),
  KEY `parentID` (`parentID`)
) ENGINE=MyISAM  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

*/

class Tag extends CorePerma {
	
	//ATTRIBUTES
	public $_title = 'Tag';
	protected $_table = '';
	protected $_linkTable = '';
	protected $_id = '';
	
	//FIELDS
	public $tagID = 0;
	public $title;
	public $permalink;
	public $status;
	
	//PICKUPS
	public $population = 0; 
	
	//COLLECTIONS
	public $collection = array(); 
	
	public $_validateRules = array(
		'rules' => array( 
			'title' => array( 'required' => true )
		)
	);
	
		
	/* FETCH
	----------------------------------------------------------------------------- */
	public function fetchActive($orderBy = '', $limit = ''){
		
		$query = "SELECT ".$this->_table.".*,	count(*) as population
			FROM ".$this->_table."
			LEFT JOIN ".$this->_linkTable." 
				ON ".$this->_linkTable.".tagID = ".$this->_table.".".$this->_id."
			WHERE status = 'active' 
			GROUP BY ".$this->_table.".".$this->_id." 
			ORDER BY population DESC";
			
		return $this->loadCollection($query);
		
	}
	
	
	public function fetchActiveByParent($parentID){
		
		if(empty($parentID)){
			return array();
		}
		
		$query = "SELECT tag_table.* 
			FROM ".$this->_linkTable." tag_link_table, ".$this->_table." tag_table
			WHERE tag_link_table.parentID = ".$parentID."
			AND tag_link_table.tagID = tag_table.".$this->_id."
			AND tag_table.status = 'active'";
			
		return $this->loadCollection($query);
	}
	
	public function loadCollectionByParent($parentID){
		
		if(empty($parentID)){
			return array();
		}
		
		$this->collection = $this->fetchActiveByParent($parentID);
	}
	
	/* CRUD
	----------------------------------------------------------------------------- */
		
	public function insert(){
		
		$this->status = 'active';
		
		$this->setPermalink($this->title);
		
		if(!$this->permalinkExists($this->permalink)){
		
			$insert = sprintf("INSERT INTO ".$this->_table." 
				(title, permalink, status) 
				VALUES (%s, %s, %s)",
				Sanitize::input($this->title, "text"),
				Sanitize::input($this->permalink, "text"),
				Sanitize::input($this->status, "text"));
			
			if($this->query($insert)){ 
				$this->setInsertId();
			
				return true;
			} else { 
			
				addMessage('error', 'Error inserting '.$this->_title);
				
				return false;
				
			} 
		} else { 
			addMessage('error', 'This tag already exists');
			return false;
		} 
	}
	
	public function update(){
		
		//DID THE TITLE CHANGE?
		$originalTag = new $this;
		$originalTag->load($this->getId());
		
		if($this->title != $originalTag->title){
			
			//generate a new permalink to see if we're colliding. 
			$this->permalink = $this->buildPermalink($this->title);
			
			if($this->permalinkExists($this->permalink)){
				addMessage('warning', 'Tag was not updated because this tag title already exists');
				return false;
			}
			
		}
		
		$update = sprintf("UPDATE ".$this->_table."
			SET title=%s, permalink=%s, status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->permalink, "text"),
			Sanitize::input($this->status, "text"),  
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
		
			addMessage('success', $this->_title.' was updated successfully');
			return true;
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
		
	}
	
	public function delete(){
		
		$this->_deleteAllTagLinks();
		
		$this->_delete();
		
	}
	
	private function _deleteAllTagLinks(){
		
		$delete = sprintf("DELETE FROM ".$this->_linkTable." WHERE ".$this->_id."=%d",
			Sanitize::input($this->getId(), "int"));
		
		if($this->query($delete)){
			return true;
		} 
		
	}
	
	/* 	FORMAT THE TAG
	----------------------------------------------------------------------------- */
	private function formatTag($title){
		
		return strtolower(trim($title));
		
	}
	
	/* LINK TABLE CRUD
	----------------------------------------------------------------------------- */
	public function updateTagsByTagId($parentID){
		
		wLog(1, 'updateTagsByTagId('.$parentID.')');
		
		if(empty($parentID)){
			wLog(1, 'no parentID supplied');
		}
		
		if(isset($_POST[$this->_id])){
			
			if(is_array($_POST[$this->_id])){
				$tags = $_POST[$this->_id];
				
			} else {
				$tags = array($_POST[$this->_id]);
			}
			
		} else {
			$tags = array();
			wLog(2, 'POST key not set'.$this->_id);
		}
		
		//GET PRESENT TAGS
		$query = "SELECT tagID 
			FROM ".$this->_linkTable." 
			WHERE parentID = ".$parentID;
		
		$result = $this->query($query);
		
		if($result){
			
			$existingTags = array();
		
			while ($row = $this->fetchAssoc($result)){ 	
				$existingTags[] = $row['tagID'];
			}	
	
			$additions = array_diff($tags, $existingTags);
			
			$subtractions = array_diff($existingTags, $tags);
			
			if(!empty($additions)){

				foreach($additions as $tagID) {
					$this->_insertTagLink($parentID, $tagID);
				}
			}
			
			if(!empty($subtractions)){
				
				foreach($subtractions as $tagID) {
					$this->_deleteTagLink($parentID, $tagID);
				}
			}
			
			return true;
			
		} 
		
		return false;
		
		
	}
	
	
	public function updateTagsByTitleCsv($parentID, $name = 'tags' ){
		
		if(empty($parentID)){
			wLog(3, 'no parentID supplied');
		}
		
		$tags = array();
		
		if(!empty($_POST[$name])){
			
			$titles = explode(',', $_POST[$name]);
			
			//um duplicates?
			
			foreach($titles as $title){
				$tags[$this->buildPermalink($title)] = $title;
			}
			
		}
		
		wLog(1, 'updateTagsByTitleCsv found '.count($tags).' on QS');
		
		$query = "SELECT tag_table.".$this->_id." as tagID, tag_table.permalink 
			FROM ".$this->_table." tag_table, ".$this->_linkTable." tag_link_table 
			WHERE tag_link_table.parentID = ".$parentID."
			AND tag_link_table.tagID = tag_table.".$this->_id;
			
		$result = $this->query($query);
		
		if($result){
			
			$existings = array();
		
			while ($row = $this->fetchAssoc($result)){ 	
				$existings[$row['permalink']] = $row['tagID'];
			}	
			
			wLog(1, 'model has '.count($existings).' existing tags');
			
			$additions = array();
			$subtractions = array();
			
			$additions = array_diff_key($tags, $existings);
			$subtractions = array_diff_key($existings, $tags);
			
			//die('<pre>TAGS='.print_r($tags, true).'</pre><br>existing<pre>'.print_r($existings, true).'</pre><br>add<pre>'.print_r($additions, true).'</pre><br>sub<pre>'.print_r($subtractions, true).'</pre><br>');
			
			
			if(!empty($additions)){
				
				foreach($additions as $permalink => $title) {
					
					if(!empty($permalink)){
					
						$addition = new $this;
												
						if(!$addition->loadByPermalink($permalink)){
						
							$addition->title = $title;
							$addition->insert();
							
							wLog(1, 'Added new tag '.$addition->title);
						}	
						
						$this->_insertTagLink($parentID, $addition->getId());
					
					} else {
						wLog(2, 'Addition of empty tag');
					}
				}
			}
			
			if(!empty($subtractions)){
				
				foreach($subtractions as $permalink => $id) {
					
					$subtraction = new $this;

					if($subtraction->load($id)){
						
						$this->_deleteTagLink($parentID, $subtraction->getId());
							
					} else {
						wLog(1, 'Subtracting a tag that doesnt exist, title='.$title);
					}					
						
				}
			}
			
		} 
		
		return true;
		
	}
	
	private function _insertTagLink($parentID, $tagID){
		
		if(empty($parentID)){
			wLog(3, 'no parentID supplied');
			return false;
		}
		
		if(empty($tagID) || !is_numeric($tagID)){
			wLog(3, 'no tagID supplied');
			return false;
		}
		
		$insert = sprintf("INSERT INTO ".$this->_linkTable." (parentID, tagID) 
				VALUES (%d, %d)",
			Sanitize::input($parentID, "int"),
			Sanitize::input($tagID, "int"));
		
		if($this->query($insert)){ 
		
			wLog(1, 'Tag Link inserted '.$parentID.', '.$tagID);
			
			return true;
			
		} else { 
		
			addMessage('error', 'Error saving tag');
			return false;
		} 
		
	}
	
	private function _deleteTagLink($parentID, $tagID){
		
		if(empty($parentID)){
			wLog(3, 'no parentID supplied');
		}
		
		if(empty($tagID)){
			wLog(3, 'no tagID supplied');
		}
		
		$update = sprintf("DELETE FROM ".$this->_linkTable."
			WHERE parentID=%d 
			AND tagID=%d",
		Sanitize::input($parentID, "int"),
		Sanitize::input($tagID, "int"));
		
		if($this->query($update)){
			wLog(1, 'Tag Link deleted '.$parentID.', '.$tagID);
			return true;
		} else {
			return false;
		}
		
	}
	
	public function deleteAllTagLinksByParent($parentID){
		
		if(empty($parentID)){
			wLog(3, 'no parentID supplied');
			return false;
		}
				
		$update = sprintf("DELETE FROM ".$this->_linkTable." WHERE parentID=%d",
			Sanitize::input($parentID, "int"));
		
		if($this->query($update)){
			return true;
		} 
		return false;
	}
	
	public function has_tag($tagID){
		foreach($this->collection as $tag){
			if($tag->getId() == $tagID){
				return true;
			}
		}
		return false;
	}
	
	
	/* FORM BUILDER
	----------------------------------------------------------------------------- */
	public function getTagString(){
		
		if(!empty($this->collection)){
			$string = '';
			foreach($this->collection as $tag){
				$string .= $tag->title.', ';
			}
			return substr($string, 0, -2);
		} else {
			return '';
		}
	}
	
	public function getTagLabel($max = 1){
		
		if(!empty($this->collection)){
			$string = '';
			
			foreach($this->collection as $tag){
				if($max > 0){
					$string .= '<span class="label label-default">'.$tag->title.'</span> ';
				}
				$max--;
			}
			if($max < 0){
				$string .= '<span class="label label-default">'.abs($max).' More</span> ';
			}
			
			return $string;
		} else {
			return '';
		}
	}
	
	public function getTagCount(){
		
		return count($this->collection);
	}
	
	/*
		$choices = array(
			array('title', 'value', isSelected = false),
		);
	*/
	
	public function getSelectOptionArray($parentID = 0){
		
		$choices = array();
		$list = $this->fetchAll();			
	
		if(empty($parentID)){ /* if the parent pk is empty => must be mode=add */
		
			foreach($list as $tag){
				$choices[] = array($tag->title, $tag->getId(), false);
			}
			
		} else {
			
			foreach($list as $tag){
				$choices[] = array($tag->title, $tag->getId(), $this->has_tag($tag->getId()));
			}
			
		}
		return $choices;
		
	}
	
}