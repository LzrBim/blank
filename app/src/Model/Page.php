<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: app/src/Model/Page.php
----------------------------------------------------------------------------- */

namespace App\Model;

class Page extends PageBase {  
	
	//ATTRIBUTES
	
	//CHILDREN	
	public $pageVersion;
	public $pageBlocks = array();
	
	
	/* LOAD
	----------------------------------------------------------------------------- */
	public function __construct(){
		
		parent::__construct();
		
		$this->version = new PageVersion();
		
		$this->promoBlock = new PagePromoBlock();
		
	}
	
	public function with($with){
		
		if(!is_array($with)){
			
			$with = array($with);
			
		}
		
		if($this->isLoaded()){
		
			if(count($with)){
				
				foreach($with as $relation){
					
					if($relation == '*' || $relation == 'pageBlock'){
						
						$this->promoBlocks = $this->promoBlock->fetchByPageAndSiteWide($this->id());
						
					}
					
					if($relation == '*' || $relation == 'pageVersion'){
						
						$this->version->loadActiveByPage($this->id());
						
					}
					
				}
				
			}
		
		} else {
			return false;
		}
		
	}
	
	
	/* ADMIN FETCH 
	----------------------------------------------------------------------------- */

	
	
	/* 	EXTEND CORE-PERMA FOR HARD CODED
	----------------------------------------------------------------------------- */

	public function getPermalink(){
		if($this->isHardCoded){
			return $this->permalink.'/'; 
		} else {
		
			return $this->_modReWritePath.$this->permalink.'/'; 
		}
		
	}
	
	/* 	CHILD HELPERS
	----------------------------------------------------------------------------- */
	public function getPromoBlock($pagePromoBlockID){
		
		foreach($this->promoBlocks as $promoBlock){
			
			if($promoBlock->getId() == $pagePromoBlockID){
				
				return $promoBlock;
			}
		}
		
		return '';
		
	}
	
	public function getPromoBlockChildrenCount(){
		
		$count = 0;
		
		if(count($this->promoBlocks)){
			
			foreach($this->promoBlocks as $promoBlock){
				
				if($promoBlock->pageID == $this->getId()){
					$count++;
				}
			}
		}
		
		return $count;
		
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
	
	
	
	public function fetchFrontSearch($searchPhrase, $limit = ''){
		
		if(empty($searchPhrase)){
			wLog(3, 'no searchPhrase supplied');
			return array();
		}
		
		$searchParts = explode(' ',trim($searchPhrase));
		$str = '';
		foreach($searchParts as $part) {
			$str .= 'searchConcat LIKE "%'.$part.'%" OR headline LIKE "%'.$part.'%" OR ';
			
		} 
		$str = substr($str, 0, -3);
		
		
		$query = "SELECT page.pageID, page.title, page.permalink, pageVersion.pageVersionID, pageVersion.headline, 
				CONCAT(
					IFNULL(GROUP_CONCAT(pageVersionBlock.headline1), ''),
					IFNULL(GROUP_CONCAT(pageVersionBlock.headline2), ''),
					IFNULL(GROUP_CONCAT(pageVersionBlock.description1), ''), 
					IFNULL(GROUP_CONCAT(pageVersionBlock.description2), '')
				) AS searchConcat
			FROM page, pageVersion, pageVersionBlockLink
			LEFT JOIN pageVersionBlock 
				ON pageVersionBlock.pageVersionBlockID = pageVersionBlockLink.pageVersionBlockID
			WHERE page.pageID = pageVersion.pageID
			AND pageVersionBlockLink.pageVersionID = pageVersion.pageVersionID 
			AND pageVersion.status = 'active' 
			AND page.isHardCoded IS NULL
			AND page.status = 'active'
			GROUP BY page.pageID
			HAVING ".$str;
	
		return $this->loadCollection($query);
	}
	
	public function fetchFrontSearchCount($searchPhrase){
		
		return count($this->fetchFrontSearch($searchPhrase));
		
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