<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /lib/Json
----------------------------------------------------------------------------- */

class Json { 
		
	private $data; 
		
	public function __construct() { 
		
		$this->data = array(
			'success' => 1,
			'message' => ''
		
		);
	}   

	public function setData($key, $val)  {  
		
		if(empty($key)){
			wLog(3, 'Key was empty');
		}
		
		$this->data[$key] = $val;
		
		return $this;
			
  }
	
	public function setFail()  {  
		
		$this->data['success'] = 0;
		
		return $this;
			
  }
	
	public function setMessage($message){
		
		$this->data['message'] = $message;
		
		return $this;
		
	}
	
	public function getJson(){
		return json_encode($this->data);
	}
	
	public function getResponse(){
		header('Content-type: application/json');
		exit($this->getJson());
	}
	
	
}