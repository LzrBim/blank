<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/Post.php 
----------------------------------------------------------------------------- */
 
class Post extends CorePerma {
	   
	//ATTRIBUTES
	public $_title = 'Post';
	public $_id = 'postID';
	
	public $_table = 'post';
	protected $_imageDirectory = 'blog/';
	public $_modReWritePath = 'blog/';  

	//FIELDS
	public $postID; 
	public $imageID;
	public $postCategoryID;
	public $title;
	public $abstract;
	public $description;
	public $permalink;
	public $noAbstract = 0;
	public $isFeatured = 0;
	public $status = 100; 
	public $rank;
	public $dateAdded ;
	public $dateModified;
	
	//CHILDREN
	public $tag;
	public $category;
	
	//COLLECTION
	public $collection;
	
	//VALIDATION
	public $_validateRules = array(
		'rules' => array( 
			'title' => array( 'required' => true ),
			'description' => array( 'required' => true )
		)
	);
	
	public $_validateRulesUpdate = array(
		'rules' => array( 
			'permalink' => array( 'permalink' => true )
		)
	);
	
	private $_imageSettings = array(
 		
		'uploadMode' => 'hash', 
		'targetDirectory' => 'blog/', 
		'targetFileName' => '', 
		'inputName' => 'uploadFile',
		
		/*ORIGINAL FILE SETTINGS */
		'originalWidth' => 1660,
		'originalHeight' => 1140,
		
		/* MAIN IMAGE SETTINGS */
		'hasMain' => true,
		'mainWidth' => 640,
		'mainHeight' => 400,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => false,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => true,
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
		$this->tag = new PostTag();
		$this->category = new PostCategory();
		
	}
	
	protected function loadHook(){
		
		//IMAGE
		if(!empty($this->imageID)){
			$this->image->load($this->imageID);
		}

	}
	
	protected function loadChildren(){
		
		$this->tag->loadCollectionByParent($this->getId());
		
		if(!empty($this->postCategoryID)){
			$this->category->load($this->postCategoryID);
		}
	}
	
	
	/* PUBLIC FETCH
	----------------------------------------------------------------------------- */
	
	public function fetchFeatured($limit = ''){
		
		$posts = array();
		
		$where = "status = 'active'	AND isFeatured = 1";
		$orderBy = 'rank ASC';
		$posts = $this->fetch($where, $orderBy, $limit);
		
		if(empty($posts)){
			$where = "status = 'active'";
			$orderBy = 'postID DESC';
			$posts = $this->fetch($where, $orderBy, $limit);
		}
		
		return $posts;
	
	}
	
	public function fetchFeaturedCount(){
		
		$where = "status = 'active' AND isFeatured = 1";
		return $this->fetchCount($where);
		
	}
	
	public function fetchRecent($limit = ''){
		
		return $this->fetchActive('postID DESC', $limit);
	
	}
	
	public function fetchRecentCount(){
		
		$where = "status = 'active'";
		return $this->fetchCount($where);
	}
	
	public function fetchMonth($month, $year, $limit = ''){
		
		$where = "YEAR(dateAdded) = ".$year." AND MONTH(dateAdded) = ".$month." AND status = 'active'";
		$orderBy = 'dateAdded ASC';
		return $this->fetch($where, $orderBy, $limit);
		
	}
	
	public function fetchMonthCount($month, $year){
		
		$where = "YEAR(dateAdded) = ".$year."
			AND MONTH(dateAdded) = ".$month."
			AND status = 'active'";
		return $this->fetchCount($where);
		
	}
	
	public function fetchActiveByCategory($postCategoryID, $limit){
		
		$where = "postCategoryID = ".$postCategoryID." AND status = 'active'";
		$orderBy = 'postID DESC';
		
		return $this->fetch($where, $orderBy, $limit);
	}
	
	public function fetchActiveByCategoryCount($postCategoryID){
		
		$where = "postCategoryID = ".$postCategoryID." AND status = 'active'";
		return $this->fetchCount($where);
		
	}
	
	public function fetchActiveByTag($tagID, $orderBy, $limit){

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
	
	public function fetchSideBarArchiveArray(){
		
		$data = array();
		$query = "SELECT count(".$this->_id.") as population, dateAdded 
    	FROM  ".$this->_table."
    	WHERE status = 'active'
    	GROUP BY YEAR(dateAdded), MONTH(dateAdded)
    	ORDER BY dateAdded DESC";
		$result = $this->query($query);
		if($result){
			if($this->numRows($result)){
				while($row = $this->fetchAssoc($result)){
					$data[] = array(
						'population' => $row['population'], 
						'dateAdded' => $row['dateAdded']
					);
				}
			} 
		}
		return $data;
	}
	
	/* CRUD 
	----------------------------------------------------------------------------- */

	public function _insert(){
		
		if($this->image->insert()){
			$this->imageID = $this->image->getId();
		}
		
		if(isset($_POST['noAbstract'])){ /* Clicked Just Shorten Post*/
			$this->noAbstract = 1;
		} else {
			$this->noAbstract = 0;
		}
		
		$insert = sprintf("INSERT INTO ".$this->_table." (
				imageID,
				postCategoryID,
				title,
				abstract,
				description,
				noAbstract,
				isFeatured,
				status,
				rank,
				dateAdded
			)
			VALUES (%d, %d, %s, %s, %s, %d, %d, %s, %d, %s)",
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->postCategoryID, "int"),
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->abstract, "editor"), 
			Sanitize::input($this->description, "editor"), 
			Sanitize::input($this->noAbstract, "int"), 
			Sanitize::input($this->isFeatured, "int"), 
			Sanitize::input($this->status, "text"), 
			Sanitize::input($this->rank, "int"),
			'NOW()');
		
		if($this->query($insert)){ 
			
			$this->setInsertId();
			
			//ADD PERMALINK
			$this->setPermalink($this->title);
			
			$update = sprintf("UPDATE ".$this->_table." SET permalink=%s WHERE ".$this->_id."=%d",
					Sanitize::input($this->permalink, "text"),  
					Sanitize::input($this->getId(), "int"));
			
			if($this->query($update)){ 
						
				//INSERT TAGS
				$this->tag->updateTagsByTitleCsv($this->getId());
				
				addMessage('success','Post was saved successfully');				
				return true;
				
			} else {
				addMessage('error', 'Error adding permalink');
				return false;
			}
			
		} else { 
			addMessage('error','Error saving post');
			return false;
		} 
	}
	
	public function _update(){
		
		//UPDATE IMAGE
		if(!empty($this->imageID)){
			$this->image->update();
		} else {
			if($this->image->insert()){
				$this->imageID = $this->image->getId();
			}
		}
		
		if(isset($_POST['noAbstract'])){ /* Clicked Just Shorten Post*/
			$this->noAbstract= 1;
		} else {
			$this->noAbstract= 0;
		}
		
		$update = sprintf("UPDATE ".$this->_table."
			SET imageID=%d,
			postCategoryID=%d,
			title=%s,
			abstract=%s,
			description=%s,
			permalink=%s,
			noAbstract=%d,
			isFeatured=%d,
			status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->postCategoryID, "int"),
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->abstract, "editor"), 
			Sanitize::input($this->description, "editor"),
			Sanitize::input($this->permalink, "text"),
			Sanitize::input($this->noAbstract, "int"), 
			Sanitize::input($this->isFeatured, "int"),
			Sanitize::input($this->status, "text"), 
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			
			//UPDATE TAGS
			$this->tag->updateTagsByTitleCsv($this->getId());
			
			addMessage('success', $this->_title.' was updated successfully');
			return true;
			
		} else { 
			addMessage('error','Error updating '.$this->_title);
			return false;
		}
	}
	
	
	
	public function delete($verbose = TRUE){
		$this->image->delete();
		$this->tag->deleteAllTagLinksByParent($this->getId());
		$this->_delete($verbose);
	}
	
	
	/* AJAX INTERACTIONS	
	----------------------------------------------------------------------------- */
	
	public function removeImage(){
		
		$update = sprintf("UPDATE ".$this->_table."
			SET imageID=0 WHERE ".$this->_id."=%d",
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			return $this->image->delete();
		} else { 
			return false;
		}
	}
	
	
	public function toggle_featured(){
		
		if($this->isFeatured){
			$this->isFeatured = 0;
		} else {
			$this->isFeatured = 1;
		}
		
		$update = sprintf("UPDATE ".$this->_table."
			SET isFeatured=%d WHERE ".$this->_id."=%d",
			Sanitize::input($this->isFeatured, "int"),
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			return true;
		} else { 
			return false;
		}
	}
	
	
		
	/* HELPERS
	----------------------------------------------------------------------------- */

	public function has_abstract(){
		if($this->noAbstract){
			return false;
		}
		return true;
	}
	
	public function getPrevPost(){
		
		$previous = new $this();
		$result = $this->query("SELECT * FROM ".$this->_table."
			WHERE ".$this->_id." < ".$this->getId()." 
			ORDER BY ".$this->_id." DESC 
			LIMIT 1");
		if($this->numRows($result) == 1){
			$row = $this->fetchAssoc($result);
			$previous->loadByData($row);
		}
		return $previous;
		
	}
	
	public function getNextPost(){
		$next = new $this();
		$result = $this->query("SELECT * FROM ".$this->_table."
			WHERE ".$this->_id." > ".$this->getId()." 
			ORDER BY ".$this->_id." ASC 
			LIMIT 1");
		if($this->numRows($result) == 1){
			$row = $this->fetchAssoc($result);
			$next->loadByData($row);
		}
		return $next;
	}
	
}	