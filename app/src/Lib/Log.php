<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: app/lib/Log.php
----------------------------------------------------------------------------- */

class Log {
	
	private static $instance;
	
	private $levelArray = array(
		1 => 'DEBUG',
		2 => 'INFO',
		3 => 'NOTICE',
		4 => 'ERROR',
		5 => 'CRITICAL',
		6 => 'EMERGENCY' 
	);  

	private $logFile;
	private $filePointer;
	private $level;
	private $msg;
	
	private $MAX_FILE_SIZE = 5242880; //5MB
	//private $MAX_FILE_SIZE = 1048576;
	private $LOG_FILE_NAME = 'log';
	
	public function __construct(){
		
		$this->logFile = APP_PATH.'logs/'.$this->LOG_FILE_NAME;

		if (file_exists($this->logFile)){
			
			if(is_writable($this->logFile)){
				
				//LOG ROTATE
				if (filesize($this->logFile) > $this->MAX_FILE_SIZE) {
					
					$archive = APP_PATH.'logs/'.date('Ymd').'_'.$this->LOG_FILE_NAME;
					
					if (copy($this->logFile, $archive)) {
						
						//OPEN POINTER AND w TO TRUNCATE
						$this->filePointer = @fopen( $this->logFile , 'w');
						
					} else {
						die("Log::construct() - error copying()");
					}
					
				} else {
						
					//OPEN POINTER
					$this->filePointer = @fopen( $this->logFile , 'a');
				}
				
				if(!$this->filePointer){
					die("Log::construct() - Error during fopen()");
				}
				
			} else {
				die("Log::construct() - Log is not writeable");
			}
		} else {
			die("Log::construct() - Log file does not exist");	
		}
	}
	
	private function __clone() { } 
	
	public static function get_instance()  {   
		
		if(!isset(self::$instance)) {  
				
			self::$instance = new Log();  
			
		}   
		
		return self::$instance;  
  }
	
	public function __destruct(){
		if ( $this->filePointer ){
			fclose( $this->filePointer );
		}
	}
	
	private function _errorEmail($text) { 
	
		$formMailer = new FormMailer();
		if($formMailer->send(array(
			'to' => 'matt@thirdperspective.com',
			'bcc' => '',
			'from' => 'matt@thirdperspective.com',
			'textFrom' => 'Matt',
			'subject' => 'Error on '.$_SERVER['SERVER_NAME'],
			'text' => $text
		))){
			return true;
		}
		return false;
	}
	
	public function get_file_backtrace(){
		
    $traces = debug_backtrace(); 
		
		//die('<pre>'.print_r($traces).'</pre>');
		
		$count = count($traces);
		if($count >= 3){
			$count = 2;
			return str_replace(BASE_PATH, '', $traces[$count]['file']).'('.$traces[$count]['line'].')';
		}
		
 		return '';
	}
	
	public function get_debug_backtrace($traces_to_ignore){
		
    $traces = debug_backtrace();
		
    $ret = array();
		
		foreach($traces as $i => $call){
			
			if($i <= $traces_to_ignore){
				continue;
			}
			
			$object = '';
			
			if (isset($call['class'])) {
				
				$object = $call['class'].$call['type'];
				
				if (is_array($call['args'])) {
					
					foreach ($call['args'] as &$arg) {
						
						$this->get_arg($arg);
						
					}
				}
			}
			
			if(isset($call['file']) && !empty($call['file'])){
				$file = str_replace(BASE_PATH, '', $call['file']);
			} else {
				$file = '';
			} 
			
			$str1 = str_pad($i - $traces_to_ignore, 3, ' ');
			
			$str2 = json_encode($call['args']);
		
			$ret[] = '#'.$str1.$object.$call['function'].'('.$str2.') called at /'.$file.'['.$call['line'].']';
		}
		
		return implode("\n  ",$ret);
	}
		
	public function get_arg(&$arg) {
		if (is_object($arg)) {
			$arr = (array)$arg;
			$args = array();
			foreach($arr as $key => $value) {
				if (strpos($key, chr(0)) !== false) {
					$key = '';    // Private variable found
				}
				$args[] =  '['.$key.'] => '.$this->get_arg($value);
			}
		
			$arg = get_class($arg) . ' Object ('.implode(',', $args).')';
		}
	}
	
	public function write($level, $msg){
		
		if($level <= 3){
			
			$line = date('m/d G:i:s')." ".$this->levelArray[$level].":".$this->get_file_backtrace().' '.$msg."\r\n";
			
		} else {
			
			$line = "-------------------------------------------------------------------\n";
			$line .= date('m/d G:i:s')." ".$this->levelArray[$level].": ".$msg."\r\n";
			$line .= "  ".$this->get_debug_backtrace(5)."\n";
			
		}
		
		//DECIDE WHETHER WE'RE TRIMMING LOG ENTRIES
		if($level >= LOG_LEVEL) { 
		
			if (fwrite( $this->filePointer, $line ) === false) {
			
				die("Log::logEntry() - Error during fwrite()");
			
			} 
			
			//SEND ERROR MESSAGE EMAIL
			if($level >= 5 && SEND_ERROR_EMAILS){
				
				if($this->_errorEmail($msg)){
					$line = date('m/d G:i:s')." ".$this->levelArray[$level].": Log error mail sent\r\n";
				} else {
					$line = date('m/d G:i:s')." ".$this->levelArray[$level].": Error sending log email error\r\n";
				}
				
				fwrite( $this->filePointer, $line );
				
			}
			return true;
		
		}
	}
}