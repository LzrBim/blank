<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/PageVersionBlock.php
----------------------------------------------------------------------------- */

namespace App\Model;

class PageVersionBlock extends BaseModel {
	
	//ATTRIBUTES
	public $_title = 'PageVersion Block';
	public $_id = 'pageVersionBlockID';
	public $_table = 'pageVersionBlock';
	public $_linkTable = 'pageVersionBlockLink';
	
	//FIELDS
	public $pageVersionBlockID = 0;
	public $templateID;
	public $galleryID;
	public $videoID;
	public $faqTagID;
	public $title;
	public $headline1;
	public $headline2;
	public $headline3;
	public $description1;
	public $description2;
	public $description3;
	public $href1;
	public $href2;
	public $href3;
	public $isRepeating;
	public $status;
	public $dateAdded;
	public $dateModified;
	
	//PICKUPS
	protected $_templates = array(
		
		1 => array(
			'title' => 'Regular',
			'description' => '',
			'status' => 'active'
		),
		2 => array(
			'title' => 'Two Column',
			'description' => '',
			'status' => 'active'
		),
		3 => array(
			'title' => 'Section Headline',
			'description' => '',
			'status' => 'active'
		)
		
	);
	
	
	/* 	FETCH
	----------------------------------------------------------------------------- */
	public function fetchCollectionByParent($pageVersionID){
		
		if(empty($pageVersionID)){
			wLog(3, 'No pageVersionID supplied');
			return array();
		}
		
		//wLog(1, 'fetching all page version blocks for pageVersionID='.$pageVersionID);
		$query = "SELECT nb.* 
			FROM pageVersionBlock nb, pageVersionBlockLink nbl
			WHERE nb.pageVersionBlockID = nbl.pageVersionBlockID
			AND nbl.pageVersionID = ".$pageVersionID."
			ORDER BY nbl.rank ASC";
		
		return $this->fetchCollection($query);
		
	}	
	
	
	public function fetchActiveRepeating(){

		$query = "SELECT * 
			FROM pageVersionBlock
			WHERE isRepeating = 1
			AND status = 'active'
			ORDER BY dateAdded DESC";
		
		return $this->fetchCollection($query);
		
	}	
	
	/* CRUD
	----------------------------------------------------------------------------- */

	
	public function insert(){
		
		$this->status = 'active';
		
		if(!$this->isModule()){
			
			if(empty($this->templateID)){   
				die('no templateID supplied');
				return false;
			}
			
			//REQUIRE A TITLE IF IT'S REPEATING
			if($this->isRepeating && empty($this->title)){
				die('Repeating blocks require title');
				return false;
			}
			
		}
		
		$insert = sprintf("INSERT INTO ".$this->_table." SET 
			templateID=%d,
			galleryID=%d,
			videoID=%d,
			faqTagID=%d,
			title=%s,
			headline1=%s, headline2=%s, headline3=%s,
			description1=%s, description2=%s, description3=%s,
			href1=%s, href2=%s,
			dateAdded=NOW(),
			dateModified=NOW(),
			isRepeating=%d,
			status=%s;",
			Sanitize::input($this->templateID, "int"), 
			Sanitize::input($this->galleryID, "int"), 
			Sanitize::input($this->videoID, "int"),
			Sanitize::input($this->faqTagID, "int"),
			Sanitize::input($this->title, "text"), 
			Sanitize::input($this->headline1, "text"), 
			Sanitize::input($this->headline2, "text"), 
			Sanitize::input($this->headline3, "text"), 
			Sanitize::input($this->description1, "editor"), 
			Sanitize::input($this->description2, "editor"), 
			Sanitize::input($this->description3, "editor"), 
			Sanitize::input($this->href1, "text"), 
			Sanitize::input($this->href2, "text"), 	
			Sanitize::input($this->isRepeating, "int"), 	
			Sanitize::input($this->status, "text"));
		
		return $this->queryInsert($insert);
	}
	
	public function update(){
		
		//we should never update gallery or video links
		$update = sprintf("UPDATE ".$this->_table."
			SET 
			title=%s,
			headline1=%s, headline2=%s, headline3=%s,
			description1=%s, description2=%s, description3=%s,
			href1=%s, href2=%s,
			isRepeating=%d,
			status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"), 
			Sanitize::input($this->headline1, "text"), 
			Sanitize::input($this->headline2, "text"), 
			Sanitize::input($this->headline3, "text"), 
			Sanitize::input($this->description1, "editor"), 
			Sanitize::input($this->description2, "editor"), 
			Sanitize::input($this->description3, "editor"), 
			Sanitize::input($this->href1, "text"), 
			Sanitize::input($this->href2, "text"), 
			Sanitize::input($this->isRepeating, "int"),
			Sanitize::input($this->status, "text"), 
			Sanitize::input($this->id(), "int"));
	
		return $this->query($update);
		
	}

	/* 
	----------------------------------------------------------------------------- */
	public function makeCopy($pageVersionBlockID){
		
		if(empty($pageVersionBlockID)){
			wLog(3, 'No pageVersionBlockID supplied');
			return false;
		}
		
		$copy = new $this;
		$copy->load($pageVersionBlockID);
		$copy->status = 'active';
		
		//IF IT'S A REPEATING BLOCK || MODULE WE DONT NEED A COPY- RETURN 
		if($copy->isRepeating || $copy->isModule()){
			return $copy->id();
		}	
		
		if($copy->insert()){
			
			return $copy->id();
			
		}
		return false;
		
	}	
	
	
	/* LINK TABLE CRUD
	----------------------------------------------------------------------------- */
	public function insertLink($pageVersionBlockID, $pageVersionID){
		
		//HARD ERRORS
		if(empty($pageVersionBlockID) || empty($pageVersionID)){
			die('empty ids');
		}
		
		$insert = sprintf("INSERT INTO ".$this->_linkTable." 
			(pageVersionBlockID, pageVersionID, rank) 
			VALUES (%d, %d, %d)",
			Sanitize::input($pageVersionBlockID, "int"), 
			Sanitize::input($pageVersionID, "int"), 
			1000);
		
		return $this->queryInsert($insert);
		
	}
	
	public function deletePageVersionBlockLink($pageVersionBlockID, $pageVersionID){
		
		//HARD ERRORS
		if(empty($pageVersionBlockID) || empty($pageVersionID)){
			die('empty ids');
		}
		
		$insert = "DELETE FROM ".$this->_linkTable." 
			WHERE pageVersionBlockID = ".$pageVersionBlockID."
			AND pageVersionID = ".$pageVersionID;
		
		return $this->query($insert);
		
	}
	
	public function updatePageVersionBlockLinkRank($pageVersionBlockID, $pageVersionID, $counter){
	
		//HARD ERRORS
		if(empty($pageVersionBlockID) || empty($pageVersionID)){
			die('empty ids');
		}
		
		$update = "UPDATE ".$this->_linkTable." SET 
			rank=".$counter."
			WHERE pageVersionBlockID=".$pageVersionBlockID."
			AND pageVersionID=".$pageVersionID;
		
		return $this->query($insert);
	
	
	}
	
	/* HELPERS
	----------------------------------------------------------------------------- */

	public function getParentArray(){
		
		$pageVersions = array();
		
		$query = "SELECT n.pageVersionID, n.title 
			FROM pageVersion n, pageVersionBlockLink nbl
			WHERE n.pageVersionID = nbl.pageVersionID
			AND nbl.pageVersionBlockID=".$this->id();
			
		$result = $this->query($query);
		if($result){
			while($row = $this->fetchAssoc($result)){
				$pageVersions[$row['pageVersionID']] = $row['title'];
			}
		}
		return $pageVersions;
	}
	
	
	public function getParentCount(){
		
		return count($this->getParentArray());
	}

	
	//FETCH REPEATING ITEMS FOR 'INSERT REPEATING BLOCKS BUTTON'
	public function getInsertBlockSelectArray($pageVersionID = 0){
		
		/* EXCLUDE ITEMS ALREADY INCLUDED */
		
		$pageVersion = new PageVersion();
		$pageVersion->load($pageVersionID);
		
		$choices = array();
		
		$list = $this->fetchActiveRepeating();

		foreach($list as $block){
			
			if(!$pageVersion->has_block($block->id())){
				$choices[] = array(date('m/d/Y', strtotime($block->dateAdded)).' - '.$block->title, $block->id(), false);
			}
		}

		return $choices;
		
	}
	
	public function getTemplateOptions(){
		
		$choices = array();

		foreach($this->_templates as $key => $properties){
			$choices[$key] = $properties['title'];
		}
		
		return $choices;
		
	}
	
	public function getPanelTitle(){
		
		if($this->isModule()){
				
			echo $this->getModuleTitle();
		
		} elseif(!$this->isRepeating){
			
			echo $this->getTemplateTitle();
			
		} else {
			
			echo $this->title;
			
		}
		
	}
      
			
	public function getTemplateTitle(){
		
		if(!empty($this->templateID)){

			return $this->_templates[$this->templateID]['title'];
			
		}
		
		return '';
		
	}
	
	public function getModuleTitle(){
		
		if(!empty($this->galleryID)){

			$gallery = new Gallery();
			$gallery->load($this->galleryID, false);
			return 'Gallery: '.$gallery->title;
			
		} elseif(!empty($this->videoID)){

			$video = new Video();
			$video->load($this->videoID, false);
			return 'Video: '.$video->title;
			
		} elseif(!empty($this->faqTagID)){

			$faqTag = new FaqTag();
			$faqTag->load($this->faqTagID);
			return 'FAQ: '.$faqTag->title;
			
			
		} else {
			
			wLog(3, 'No module title found');
			return ' ';
		}
		
	}
	
	public function isModule(){
		
		if(!empty($this->galleryID) || !empty($this->videoID) || !empty($this->faqTagID) ){

			return 1;
			
		} 
		
		return 0;	
		
	}
	
	
}