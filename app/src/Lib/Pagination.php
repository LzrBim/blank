<?php 
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/lib/Pagination.php
----------------------------------------------------------------------------- */
class Pagination { 
	
	private $_totalRows;
	private $_page;
	private $_numberOfPages;
	private $_maintainedQueryString = '';
	
	//OPTIONS
	private $_ipp = 0; 
	private $_midRange = 0;
	private $_basePath = '';
	private $_ignoreQsVar = array('page', 'ipp', 'totalRows');
	
	//DEFAULTS
	private $_defaultMidRange = 7;
	private $_defaultIPP = 20;
	private $_ippSelectOption = array(10,20,40,80, 'All');
	
	
	/* LOAD
	----------------------------------------------------------------------------- */

	public function __construct($totalRows, $basePath = '') {
		
		$this->_totalRows = $totalRows;
		
		if(empty($basePath)){
			
			$parts = parse_url($_SERVER['REQUEST_URI']);
			
			$this->_basePath = $parts['path'];
			
		} else{
			$this->_basePath = $basePath;
		}
		
		//GET + SET DEFAULTS
		$this->_page = (!empty($_GET['page'])) ? Sanitize::paranoid($_GET['page']) : 1;
		
		$this->_ipp = (!empty($_GET['ipp'])) ? Sanitize::paranoid($_GET['ipp']) : $this->_defaultIPP;
		
		$this->_midRange = $this->_defaultMidRange;
		
		//INIT
		$this->_resetPageCount();
		
		$this->_resetQs();
		
	}
	
	/* OPTION SETTERS
	----------------------------------------------------------------------------- */
	public function setItemsPerPage($ipp){ /* 1,2,3 or 'All' */
		
		$this->_ipp = $ipp;
		
		if(is_numeric($this->_ipp)){
			$this->_ippSelectOption = array($this->_ipp, 2 * $this->_ipp, 3 * $this->_ipp, 4 * $this->_ipp, 'All');
		}
		
		$this->_resetPageCount();
		
		return $this;
	}
	
	public function setPage($page){ 
		
		$this->_page = $page;
		
		return $this;
	}
	
	public function set_qs_ignore($key){ 
		$this->_ignoreQsVar[] = $key;
		$this->_resetQs();
		return $this;
		
	}
	
	public function set_midrange($midRange){
		$this->_midRange = $midRange;
	}
	
	/* 	MAIN 
	----------------------------------------------------------------------------- */

	public function getLimit(){
		
		$limit = '';
		$limitOffset = 0;
		if($this->_ipp != 'All'){
			$limitOffset = ($this->_page - 1) * $this->_ipp;
			$limit = $limitOffset.",".$this->_ipp;
		} 
		return $limit;
	}
	
	/* DISPLAYS
	----------------------------------------------------------------------------- */
	
	public function paginate($class = 'pagination pagination-sm') {
		
		if($this->_numberOfPages <= 1) {
			return '';
		}
				
		$previousPage = $this->_page - 1;
		$nextPage = $this->_page + 1;
		
		$startRange = $this->_page - floor($this->_midRange/2);
		
		$endRange = $this->_page + floor($this->_midRange/2);

		if($startRange <= 0){
			$endRange += abs($startRange)+1;
			$startRange = 1;
		}
		if($endRange > $this->_numberOfPages){
			$startRange -= $endRange - $this->_numberOfPages;
			$endRange = $this->_numberOfPages;
		}
		
		$range = range($startRange, $endRange);
		
		$link = $this->_basePath.'?ipp='.$this->_ipp.$this->_maintainedQueryString;
		
		//RENDER
		ob_start(); ?>
		
		<ul class="<?= $class; ?>"><? 
		
		//PREVIOUS BUTTON
		if($this->_page != 1 ){ ?>
			<li><a href="<?= $link; ?>&page=<?= $previousPage; ?>">&laquo; Previous</a></li><? 
      
		} else { ?>
			<li class="disabled"><span>&laquo; Previous</span></li><? 
		}
		
		//DIGIT BUTTONS
		for($i = 1; $i <= $this->_numberOfPages; $i++){
			
			if($range[0] > 2 && $i == $range[0]){ ?>
      	<li class="disabled"><span>&hellip;</span></li><? 
			}
			
			if($i == 1 || $i == $this->_numberOfPages || in_array($i, $range)){
				
				if($i == $this->_page){ ?>
					<li class="active"><a href="#"><?= $i; ?></a></li><?
					
				} else { ?>
					<li><a href="<?= $link; ?>&page=<?= $i; ?>"><?= $i; ?></a></li><? 
				}
			}
			
			if($range[$this->_midRange-1] < $this->_numberOfPages-1 && $i == $range[$this->_midRange-1]){ ?>
				<li class="disabled"><span>&hellip;</span></li><? 
			}
		}
		
		//NEXT BUTTON
		if($this->_page != $this->_numberOfPages){ ?>
			<li><a href="<?= $link; ?>&page=<?= $nextPage; ?>">Next &raquo;</a></li><? 
			
		} else { ?>
			<li class="disabled"><span>&raquo; Next</span></li><? 
		} ?>
		
		</ul><!-- /.pagination --><? 
		
		$pagination = ob_get_clean();
		
		return $pagination;
		
	}

	public function getSelectPage() {
		
		$select = '';
		
		if($this->_numberOfPages > 1){ 
    
    	ob_start(); ?>
			
			<select onchange="window.location='<?= $this->_basePath; ?>?page='+this[this.selectedIndex].value+'&ipp=<?= $this->_ipp.$this->_maintainedQueryString; ?>'; return false; "><? 
			
			for($i = 1; $i <= $this->_numberOfPages; $i++) { ?>
      
      	<option value="<?= $i; ?>" <? if($i == $this->_page){ echo ' selected'; } ?>><?= $i; ?></option><?
				
			} ?>
      
      </select><?
			
			$select = ob_get_clean();
	
		}
		return $select;
	}


	public function getSelectItemsPerPage() {
		
		$select = '';
		
		if($this->_numberOfPages > 1){
			
			ob_start(); ?>
			
			<select onchange="window.location='<?= $this->_basePath; ?>?page=<?= $this->_page; ?>&ipp='+this[this.selectedIndex].value+'<?= $this->_maintainedQueryString; ?>'; return false;"><? 
			
			foreach($this->_ippSelectOption as $value){ ?>
				
				<option value="<?= $value; ?>" <? if($value == $this->_ipp){ echo 'selected'; } ?>><?= $value; ?></option><? 
				
				 /*elseif($this->_ipp == $this->_totalRows && $value == 'All') {*/
					
			} ?>
			
			</select><? 
			
			$select = ob_get_clean();
	
		}
		return $select;
	}

	public function display_current_page() {
		return $this->_page."&nbsp;of&nbsp;".$this->_numberOfPages;
	}
	
	
	/* HELPERS
	----------------------------------------------------------------------------- */

	private function _resetPageCount() {
		
		if($this->_ipp == 'All' ) {
			$this->_numberOfPages = 1;
		} else {
			$this->_numberOfPages = ceil($this->_totalRows / $this->_ipp);
		}
		//wLog(1, 'Total Rows: '.$this->_totalRows.' IPP: '.$this->_ipp.' Number of Pages: '.$this->_numberOfPages);
	}
	
	private function _resetQs() {
		
		//if($this->_ipp == 'All') $this->_ipp = $this->_totalRows;
		
		$this->_maintainedQueryString = '';

		if($_GET) {
			foreach($_GET as $key => $val){
				if(!in_array($key, $this->_ignoreQsVar)){
					$this->_maintainedQueryString .= "&".$key.'='.$val;
				}
			}
		} 
		
		$this->_maintainedQueryString .= "&totalRows=".$this->_totalRows;
		
	}
} 