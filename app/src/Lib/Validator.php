<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /app/lib/validate.php
----------------------------------------------------------------------------- */

class Validator {
	
	private $_obj;
	private $_rules;
	
	public function __construct( $obj, $rules ){
		
		if(!isset($obj) || !is_object($obj)){
			wLog(2, 'Validate::__construct() - recieved invalid object');
		}
		
		if(!isset($rules) || !is_array($rules)){
			wLog(2, 'Validate::__construct() - recieved invalid rules');
		}
		
		$this->_obj = $obj;
		$this->_rules = $rules;
		
		
	}
	
	public function validate(){
		
		$success = true;
		
		foreach($this->_rules['rules'] as $field => $ruleArray){
			
			foreach($ruleArray as $function => $param){
				
				if(method_exists($this, $function) && array_key_exists($field, $this->_obj)){
					
					if( !$this->$function( $this->_obj->$field, $param ) ){
						
						wLog(3, 'Validator Fail - '.$field.' - function - '.$function );
						
						$success = false;
					}
				}
			}
		}
		return $success;
	}
	
	/*-----------------------------------------------------------------------------
     VALIDATOR METHODS - Must match jquery.validator.js
	----------------------------------------------------------------------------- */
	
	//required
	private function required($data, $param = '' ){
		if(isset($data) && !empty($data)){
			return true;																									 
		}
		return false;
	}
	
	/* remote function is missing */

	private function email($data, $param = ''){
		return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $data);
	}
	
	private function url($data, $param = ''){
		return filter_var($data, FILTER_VALIDATE_URL);
	}
	
	private function date($data, $param = ''){
		//TBD
	}
	
	private function dateISO($data, $param = ''){
		if(!empty($data)){	
			return preg_match("^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$", $data); 
		} 
		return true;
	}
	
	private function number($data, $param = ''){
		if(!empty($data)){
			return is_numeric($data);
		}
		return true;
	}
	
	private function digits($data, $param = ''){
		if(!empty($data)){
			return is_int($data);
		}
		return true;
	}
	
	/* creditcard */

	private function equalTo($data, $param = ''){


	}
	
	//maxlength
	private function maxlength($data, $param = 255){
		if(strlen($data) <= $param) {
			return true;
		}
		return false;
	}
	
	//minlength
	private function minlength($data, $param = 0){
		if(strlen($data) >= $param) {
			return true;
		}
		return false;
	}
	
	private function rangelength($data, $param = ''){

	}
	
	private function range($data, $param = array()){
		
	}
	
	private function max($data, $param = 255){
		if(is_numeric($data) && $data <= $param){
			return true;
		}
		return false;
	}
	
	private function min($data, $param = 1){
		if(is_numeric($data) && $data >= $param){
			return true;
		}
		return false;
	}
	
	/* Additional Methods */
	private function alphanumeric($data, $param = true) {
		return !preg_match('/^[a-zA-Z0-9_]+$/', $data);
	}
	
	private function alphanumericwhitespace($data, $param = true) {
		return !preg_match('/^[a-zA-Z0-9\s]+$/*', $data);
	}
	
	private function permalink($data, $param = true){
		if(preg_match('/[^a-z0-9\-]+/', $data)){
			wLog(3, 'Invalid Permalink = '.$data);
			addMessage('error','Invalid Permalink = '.$data);
			return false;
			
		} else {
			return true;
		}
	}
	
	
} //EOF Validator ?>