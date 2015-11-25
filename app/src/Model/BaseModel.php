<?php
namespace App\Model;

use \App\Lib\Database;
use \App\Lib\Sanitize;

class BaseModel {
	
	//CORE ATTRIBUTES
	public $_title = null;
	protected $_id = null;
	protected $_table = null;
	
	public function __construct(){ 
		
	}
	
	public function load($id){
		
		if(empty($id)){
			return false;
		}
		
		$query = "SELECT * FROM `".$this->_table."` WHERE `".$this->_id."` = ".$id;
		
		$db = Database::get_instance();
		
		$result = mysqli_query($db, $query);
		
		if($result){
			
			if(mysqli_num_rows($result)){
				
				$row = mysqli_fetch_array($result);
				
				$this->loadByData($row); 
				
				$this->loadHook();
				
				return $this;
				
			} 
			
		} else {
			
			throw new AppException();
			
		}
		
		return false;
	}
		
	public function loadBySlug($slug){
		
		if(empty($slug)){
			die('slug was empty');
			return false;
		}
		
		$query = "SELECT * FROM `".$this->_table."` WHERE `slug` = '".$slug."'";
		
		$db = Database::get_instance();
		
		$result = mysqli_query($db, $query);
		
		if($result){
			
			if(mysqli_num_rows($result)){
				
				$row = mysqli_fetch_array($result);
				
				$this->loadByData($row); 
				
				return $this;
				
			} 
			
		} else {
			
			//throw new AppException();
			return false;
		}
		
		return false;
	}
	
	public function loadByData($data){
		
		if(empty($data)){
			//wLog(2, 'No data supplied');
		}
		
		foreach($data as $var => $value){
			
			if(array_key_exists($var, $this)){
				
				$this->$var = $value;
				
			} else {
				
				//populate child objects:  row['photographer_firstName'] maps to $this->photographer->firstName
				if(strpos($var,'_') !== false){
					
					$parts = explode('_', $var);
					
					if(isset($parts[0]) && isset($parts[1])){
						
						if(array_key_exists($parts[0], $this)){
																			 
							if(array_key_exists($parts[1], $this->$parts[0])){
								
								$this->$parts[0]->$parts[1] = $value;						 
							}
						}
					}
				}
			} 
		}
		
		$this->loadHook();
		
		return true;
	}
	
	public function loadWhere($where, $with = array()){
		
		if(empty($where)){
			die('empty where');
		}
		
		$query = "SELECT * FROM ".$this->_table." WHERE ".$where;
		
		$result = $this->query($query);
		
		if($result){
			
			if($this->numRows($result) == 1){
				
				return $this->loadByData($this->fetchAssoc($result));
				
			} else {
				
				if($this->numRows($result) > 1){
					wLog(3, 'Multiple rows returned');
				}		
			} 
		} 
		
		return false;
		
	}
	
	protected function loadHook(){
		
	}
	
	protected function loadChildren($childArgs = array()){
		
	}
	
	public function delete(){
		
		return $this->_delete();
		
	}
	
	protected function _delete(){
		
		$update = sprintf("DELETE FROM `".$this->_table."`
			WHERE ".$this->_id."=%d",
		Sanitize::input($this->id(), "int"));
		
		return mysqli_query($this->_db, $update);
	}
	
	
	/* SQL
	----------------------------------------------------------------------------- */
	public function query($statement){
		
		$db = Database::get_instance();
		
		$result = mysqli_query($db, $statement);
		
		if($result){ 
		
			return $result;
			
		} else {
			
			die('Invalid Query: '.mysqli_error($db));
			
		}
				
	}
	
	public function queryInsert($statement){
		
		$db = Database::get_instance();
		
		$result = mysqli_query($db, $statement);
		
		if($result){ 
		
			$this->{$this->_id} = mysqli_insert_id($db);
			
			return true;
			
		} 
		
		return false;
		
	}	
	
	public function isLoaded(){ 
	
		if(!empty($this->{$this->_id})){ 
			return true;	
		} else { 
			return false;	
		} 
	}
	
	public function id(){ return $this->{$this->_id}; }
	
	public function fetchAssoc($result){ return mysqli_fetch_assoc($result);  }
	
	public function numRows($result){ return mysqli_num_rows($result);  }
	
	
	public function input($db, $value, $type, $opts = array()) {  
	
		if(!mb_check_encoding($value, 'UTF-8')){
			die('not utf'. $type.' '. $value);										
		}
		
		if(isset($opts['isDie'])){
			$isDie = true;	
		} else {
			$isDie = false;
		}
		
		if($type == 'text' || $type == 'editor' || $type == 'string'){
			$value = $this->stripScripts($value, $isDie);
		}
		
		$value = $this->stripControlCharacters($value);
		
		if(!empty($opts['stripTags'])){
			$value = strip_tags($value);
		} 
		
		$value = mysqli_real_escape_string($db, $value);
	
		switch ($type) {
			case "text": 
				$value = ($value != "") ? "'".trim($value)."'" : "NULL";
			break; 
			
			case "editor":
				$value = ($value != "") ? "'".$value."'" : "NULL";
			break; 
			
			case "date":
				$value = ($value != "") ? "'".date('Y-m-d', strtotime($value))."'" : "NULL";
			break; 
			
			case "datetime":
				$value = ($value != "") ? "'".date('Y-m-d H:i:s', strtotime($value))."'" : "NULL";
			break; 
			
			case "phone":
				$value = ($value != "") ? "'".trim(preg_replace("/[^0-9]/", "", $value))."'" : "NULL";
			break; 
			
			case "int":
				$value = ($value != "") ? intval($value) : "NULL";
			break; 
			
			case "time":
				$value = ($value != "") ? intval($value) : "NULL";
			break; 
			
			case "string":
				$value = ($value != "") ? "'".$value."'" : "NULL";
			break; 
			
			case "dec":
				if($value != ""){
					if(is_numeric($value)){
						$value = $value;
					} else {
						$value = "NULL"; 
					}
				} else {
					$value = "NULL";
				}
			break;
		}
		return $value;
	}
	
	public function stripScripts($string, $isDie = false){
		
		$origString = $string;
		$error = false;
		
		if(empty($string)){
			return $string;
		}	
	
		$naughtyStrings = array(
			'document.cookie'	=> '[removed-1]',
			'document.write'	=> '[removed-2]',
			'.parentNode'		=> '[removed-3]',
			'.innerHTML'		=> '[removed-4]',
			'window.location'	=> '[removed-5]',
			'-moz-binding'		=> '[removed-6]',
			'<!--'				=> '&lt;!--',
			'-->'				=> '--&gt;',
			'<![CDATA['			=> '&lt;![CDATA[' );
	
		foreach ($naughtyStrings as $pattern => $replacement){
			
			$count = 0;
			$string = str_replace($pattern, $replacement, $string, $count); 
			if($count){
				wLog(3, 'Found a naughtyString='.$string.', on pattern='.$pattern.', originalString='.$origString);
				$error = true;
			}
		}
	
		$naughtyRegexs = array(
			"javascript\s*:"					=> "[removed-7]",
			"expression\s*(\(|&\#40;)"			=> "[removed-8]", 
			"vbscript\s*:"						=> "[removed-9]", 
			"Redirect\s+302"					=> "[removed-10]",
			"@<![\s\S]*?--[ \t\n\r]*>@"         => "[removed-11]",
			"(<link[^>]+rel=\"[^\"]*stylesheet\"[^>]*>)|<script[^>]*>.*?<\/script>|<style[^>]*>.*?<\/style>|<!--.*?-->" => "[removed-12]"	
	
		);
	
		foreach ($naughtyRegexs as $pattern => $replacement){
			
			$count = 0;
			$string = preg_replace("/".$pattern."/i", $replacement, $string, 5, $count);
			
			if($string == NULL){
				wLog(3, 'Error during naughtyRegex, pattern='.$pattern);
			}
				
			if($count){
				wLog(3, 'Found a naughtyRegex, pattern='.$pattern.', originalString='.$origString);
				$error = true;
			}
			
		}
	
		if($error && $isDie){
			die('Suspicious input detected. If you copy and pasted, please retype and try again.');	
		}
		
		return $string;
	}
	
	public function stripControlCharacters($str){
		return preg_replace(
			array(
					'/\x00/', '/\x01/', '/\x02/', '/\x03/', '/\x04/',
					'/\x05/', '/\x06/', '/\x07/', '/\x08/', '/\x09/', 
					'/\x0B/', '/\x0E/', '/\x0F/', '/\x10/', '/\x11/',
					'/\x12/','/\x13/','/\x14/','/\x15/', '/\x16/', '/\x17/', '/\x18/',
					'/\x19/','/\x1A/','/\x1B/','/\x1C/','/\x1D/', '/\x1E/', '/\x1F/'
			), 
			array(
					"", "", "", "", "",
					"", "", "", "", "",
					"", "", "", "", "",
					"", "", "", "", "", "", "",
					"", "", "", "", "", "", ""
			), $str);
	} 
	
	
	/* COLLECTIONS
	----------------------------------------------------------------------------- */
	
	protected function fetchCollection($query){
		
		$db = Database::get_instance();
		
		$objList = array();
			
		if(!empty($query)){

			$result = $this->query($query);
			
			if($result){
	
				if(mysqli_num_rows($result)){
					
					while ($row = mysqli_fetch_array($result)){
						
						$tempObject = new $this;
						
						foreach($row as $var => $value){
							
							if(array_key_exists($var, $tempObject)){
								$tempObject->$var = $value;
							} 	
						}
						
						$tempObject->loadHook();
						
						$objList[] = $tempObject;
					} 
				} 
			} 		
		} 
		
		return $objList;
	}
	
	/* CRUD
	----------------------------------------------------------------------------- */
	
				
	
	/* FETCH
	----------------------------------------------------------------------------- */
	
	public function fetch($where = '', $orderBy = '', $limit = ''){
		
		$query = "SELECT * FROM ".$this->_table." ";
		
		if(!empty($where)){
			$query .= "WHERE ".$where." ";
		} 
		
		if(!empty($orderBy)){
			$query .= "ORDER BY ".$orderBy." ";
		} else {
			$query .= "ORDER BY ".$this->_id." DESC ";
		}
		
		if(!empty($limit)){
			$query .= "LIMIT ".$limit." ";
		}
				
		return $this->fetchCollection($query);
		
	}
	
	public function fetchAll($orderBy = '', $limit = '', $loadChildren = false){
		
		return $this->fetch('', $orderBy, $limit, $loadChildren);
	
	}
	
	public function fetchActive($orderBy = '', $limit = '', $loadChildren = true){
		
		return $this->fetch("status='active'", $orderBy, $limit, $loadChildren);
	
	}
	
	public function fetchInactive($orderBy = '', $limit = '', $loadChildren = true){
		
		return $this->fetch("status='inactive'", $orderBy, $limit, $loadChildren);
	
	}
	
	
	
	/* FETCH COUNTS
	----------------------------------------------------------------------------- */
	
	public function queryCount($query){ /* must collate field as 'count'*/
		
		$result = $this->query($query);
		
		if($result){
			
			if(mysqli_num_rows($result) == 1){
				
				$row = mysqli_fetch_array($result);
				
				return $row['count'];
				
			} else {
				wLog(3, 'returned multiple rows?');
				return 0;
			}
		} else {
			wLog(4, mysqli_error($this->_db));
			return 0;
		}
	}
	
	public function fetchCount($where = ''){
		
		$query = "SELECT COUNT(".$this->_id.") as count
			FROM ".$this->_table." ";
		if(!empty($where)){
			$query .= "WHERE ".$where;
		}
		
		return $this->queryCount($query);
		
	}
	
	public function fetchAllCount(){
		return $this->fetchCount();
	}
	
	public function fetchActiveCount(){
		return $this->fetchCount("status='active'");
	}
	
	public function fetchInactiveCount(){
		return $this->fetchCount("status='inactive'");
	}
	
	/* SEARCH
	----------------------------------------------------------------------------- */
	
	protected function buildSearchQuery($searchPhrase){
		
		$searchParts = explode(' ',trim($searchPhrase));
		$str = '';
		foreach($searchParts as $part) {
			$str .= 'title LIKE "%'.$part.'%" OR ';
			
		} 
		return substr($str, 0, -3);
	}
	
	public function fetchSearch($searchPhrase, $and = '', $orderBy = '', $limit = ''){
		
		if(empty($searchPhrase)){
			wLog(3, 'no searchPhrase supplied');
			return array();
		}
		$query = "SELECT * 
			FROM ".$this->_table." 
			WHERE ".$this->buildSearchQuery($searchPhrase)." ";
			
		if(!empty($and)){ 
			$query .= "AND ".$and;
		} 
		
		if(!empty($orderBy)){
			$query .= "ORDER BY ".$orderBy." ";
		} else {
			$query .= "ORDER BY ".$this->_id." ";
		}
		
		if(!empty($limit)){
			$query .= "LIMIT ".$limit;
		}
		
		return $this->fetchCollection($query);
	}
	
	
	public function fetchSearchCount($searchPhrase, $and = ''){
		$where = $this->buildSearchQuery($searchPhrase)." ";
		if(!empty($and)){ 
			$query .= "AND ".$and;
		} 
		return $this->fetchCount($where);
	}
	
	
	public function getSelectOptionArray($id = 0, $field = 'title', $opts = array()){
		
		$defaults = array(
			'status' => 'all'								
		);
		
		$opts = array_merge($defaults, $opts);
		
		$choices = array();
		
		if($opts['status'] == 'all'){
			$list = $this->fetchAll($field);	
			
		} else {
			$list = $this->fetchActive($field);	
			
		}
			
		foreach($list as $model){
			
			$selected = ($model->id() == $id ? true : false);
			
			$choices[] = array($model->{$field}, $model->id(), $selected);
		}
		
		return $choices;
		
	}
	
}
