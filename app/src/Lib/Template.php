<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /lib/Template.php
----------------------------------------------------------------------------- */

class Template {
	
	private $data = array();
	private $_file = '';
	private $_template = '';
	 
	public function __construct($file = ''){
		
		if(!empty($file)){
			$this->_file = APP_PATH.'tpl/'.$file;
		}

	}
	
	public function setTemplateFile($file){
		
		$this->_file = APP_PATH.'tpl/'.$file;

	}
	
	public function setTemplate($tpl){
		
		$this->_template = $tpl;

	}
	
	public function set($key, $value = NULL) {
		
		if (!is_array($key)) {
			$this->data[$key] = $value;
		} else {
			$this->data = array_merge($this->data, $key);
		}
		return $this;
	}
	
	public function setData($arr) {
		
		$this->data = $arr;
		return $this;
	}
	
	private function _loadTemplate(){
		
		if(!defined('APP_PATH')){
			wLog(1, 'APP_PATH not defined');
			return false;
		}
		
		if(empty($this->_file) && empty($this->_template)){
			wLog(4, 'No template supplied');
			return false;
		}
		
		if(empty($this->_template)){
			
			if(file_exists($this->_file)) {	
			
				$this->_template = file_get_contents($this->_file);
			
			} else {
				wLog(4, 'Template not found file='.$this->_file);

			}
			
		}
		
		if(!empty($this->_template)){
			return true;
		}
		return false;
		
	}
	
	
	public function render() {
		
		if($this->_loadTemplate()){
			
			if(empty($this->data)){
				wLog(2, 'Template rendered with no data');
			}
		
			if (preg_match_all("/{{(.*?)}}/", $this->_template, $matches)) {
					
				$errors = '';
				
				foreach ($matches[0] as $i => $patternMatch) { 
					
					if(array_key_exists($matches[1][$i], $this->data)){
						
						$this->_template = str_replace($matches[0][$i], $this->data[$matches[1][$i]], $this->_template);					
					
					} else {
						
						$errors .= 'Unfilled var: '.$matches[0][$i].'<br>';
						$this->_template = str_replace($matches[0][$i], '<!-- Unfilled var: '.$matches[0][$i].' -->', $this->_template);	
						wLog(3, 'Template variable unfilled: '.$matches[0][$i]);
						
					}				
					
				}			
				
			} else {
				wLog(2, 'Template did not contain any variables');
			}
			
			if(!empty($errors)){
				$this->_template .= '<div style="position:absolute; top:0; z-index:10001; left:0; background:#f00; color:#fff;padding:5px;">'.$errors.'</div>';
			}
			
		}
		
		return $this->_template;
		
		
	}
		
	/* 	ADMIN HELPER
	----------------------------------------------------------------------------- */
	public function getVars() {
		
		if($this->_loadTemplate()){
		
			preg_match_all("/{{(.*?)}}/", $this->_template, $matches);
					
			return $matches[1];	
			
		}
		
		return array();
		
		
	}
} 