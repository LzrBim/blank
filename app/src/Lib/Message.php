<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /lib/Message.php
----------------------------------------------------------------------------- */

class Message {
   
	private $messageStack;
	private $types = array('error', 'warning', 'info', 'success');

	public function __construct() {
		
		$this->messageStack = $this->_load_all_messages();
		
	}

	public function add($msg = '', $type = '') {
		
		if(!empty($msg) && !empty($type)){
			
			if(!isset($this->messageStack[$type])) {
				$this->messageStack[$type] = array();
			}
			
			//check for duplicates
			if(!in_array($msg, $this->messageStack[$type])){
				$this->messageStack[$type][] = $msg;			
			}
			return true;
			
		} else {
			return false;	
		}
		return false;
	}

	public function get(){
		
		$string = '';
		foreach($this->types as $type){
			if(isset($this->messageStack[$type])) { 
				$string .='<div class="alert'.$this->_type_to_class($type).' alert-dismissable">';
				$string .='<button type="button" class="close" data-dismiss="alert">&times;</button>';
				foreach($this->messageStack[$type] as $msg){
					$string .= $msg.'<br>';
				} 
				$string .='</div>'; 
			}
		}
		
		$this->clear_messages();
		return $string;
	}  
	
	public function getRaw(){
		
		$string = '';
		foreach($this->types as $type){
			if(isset($this->messageStack[$type])) { 
				foreach($this->messageStack[$type] as $msg){
					$string .= $msg.'<br>';
				} 
			}
		}
		
		$this->clear_messages();
		return $string;
	}  

	public function save_all_messages() {
		$msg = serialize($this->messageStack);
		$_SESSION['flash_messages'] = $msg;
	}
	
	public function clear_messages() {
		if(isset($_SESSION['flash_messages'])) {
			$_SESSION['flash_messages'] = array();
		}
		unset($this->messageStack);
		$this->messageStack = array();
	}
	
	private function _load_all_messages() {
		$msg = array();
		if ( !empty($_SESSION['flash_messages'] )) {
			$msg = unserialize( $_SESSION['flash_messages'] );
		}
		return $msg;
	}
	
	private function _type_to_class($type){
		if($type == 'success'){
			return ' alert-success';
			
		} elseif($type == 'warning') { 
			return ' alert-warning';
			
		} elseif($type == 'info') { 
			return ' alert-info';
			
		} elseif($type == 'error') { 
			return ' alert-danger';
			
		} else { //warning is the default class
			return '';
		}
	}
} 


