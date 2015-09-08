<? 
/* -----------------------------------------------------------------------------
 * SITE: Blanksite
 * FILE: app/lib/sanitize.php
----------------------------------------------------------------------------- */    

class Sanitize {
	
	public static function input($value, $type, $opts = array()) {  
	
		if(!mb_check_encoding($value, 'UTF-8')){
			die('not utf'. $type.' '. $value);										
		}
		
		if(isset($opts['isDie'])){
			$isDie = true;	
		} else {
			$isDie = false;
		}
		
		if($type == 'text' || $type == 'editor' || $type == 'string'){
			$value = self::stripScripts($value, $isDie);
		}
		
		$value = self::stripControlCharacters($value);
		
		if(!empty($opts['stripTags'])){
			$value = strip_tags($value);
		} 
		
		$_db = Database::get_instance();
		
		$value = mysqli_real_escape_string($_db,$value);
	
		switch ($type) {
			case "text": 
				$value = ($value != "") ? "'".trim(htmlentities($value))."'" : "NULL";
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
	
	public static function clean($var){
		
		$oVar = $var;		
		$var = preg_replace("/[^a-zA-Z0-9_-]/", ' ', $var, 20, $count);
		
		if($count){
			wLog(1, 'Sanitize::basic() var = '.$oVar.' - '.self::backtrace());
		}
		
		return $var;   
		
	} 
	
	public static function permalink($var){
	
		$oVar = $var;		
		$var = preg_replace("/[^a-zA-Z0-9\-]/", ' ', $var, 20, $count);
		
		if($count){
			wLog(1, 'Sanitize::permalink() var = '.$oVar.' - '.self::backtrace());
		}
		
		return $var;   
		
	}
	
	 
	public static function paranoid($var){ 
	
		$var = (int)$var;
	
		$oVar = $var;		
		$var = preg_replace("/\D/", ' ', $var, 5, $count);
		
		if($count){
			wLog(1, 'Sanitize::paranoid() var = '.$oVar.' - '.self::backtrace().' - IP: '.$_SERVER['REMOTE_ADDR']);
		}
		
		if(is_numeric($var)){
			return $var; 
		} else {
			wLog(1, 'Sanitize::paranoid() - recieved='.$oVar.' - setting var = 0');
			return 0;
		}
	
	} 
	
	public static function search($str){
		
		$str = trim(strip_tags($str));
		$str = preg_replace("/[^a-zA-Z0-9\-]/", " ", $str);
		$str = trim($str);
		$str = preg_replace("/\s+/", ' ', $str);
	
		return $str;
		
	}
	
	/* RETURNS A SANITIZED FILENAME OR empty string*/
	public static function fileName($var) {
		
		if(empty($var) || strlen($var) < 4) {
			return '';
		}
		
		$lastDotPos = strrpos($var, "."); 
		
		if ($lastDotPos !== false) {
			
			$name = substr($var, 0, $lastDotPos);
	
			$ext = substr($var, -(strlen($var) - ($lastDotPos + 1)));
			
			$name = cleanUrlString($name, 245, '-', true);
			
			$ext =  trim(preg_replace("/[^a-z0-9]/", '', strtolower($ext)));
			
			return $name.'.'.$ext;
			
		} else {
			return '';
		}
		
	}
	
	
	/* global validate functions */
	public static function isValidEmail($email){
		if(preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $email)){
			return true;
		} else {
			return false;
		}
	}
	
	public static function isValidPassword($password){
		preg_replace("/[^a-zA-Z0-9!@#\%\^&\*\._-]/", ' ', $password, -1 , $count);  
		if(!$count){
			return true;
		} else {
			return false;
		}
	}
	
	public static function isMySqlDate($date){ 
		return preg_match( '#^(?P<year>\d{2}|\d{4})([- /.])(?P<month>\d{1,2})\2(?P<day>\d{1,2})$#', $date, $matches )
						 && checkdate($matches['month'],$matches['day'],$matches['year']);
	} 
	
	
	public static function stripScripts($string, $isDie = false){
		
		$origString = $string;
		$error = false;
		
		if(empty($string)){
			return $string;
		}	
		
		if(!mb_check_encoding($string, 'UTF-8')){
			die('not utf'. $string);										
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
	
	public static function stripControlCharacters($str){
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
	
	
	private function backtrace() {
		$c = '';
		$file = '';
		$func = '';
		$class = '';
		$trace = debug_backtrace();
		if (isset($trace[2])) {
				$file = $trace[1]['file'];
				$func = $trace[2]['function'];
				if ((substr($func, 0, 7) == 'include') || (substr($func, 0, 7) == 'require')) {
						$func = '';
				}
		} else if (isset($trace[1])) {
				$file = $trace[1]['file'];
				$func = '';
		}
		if (isset($trace[3]['class'])) {
				$class = $trace[3]['class'];
				$func = $trace[3]['function'];
				$file = $trace[2]['file'];
		} else if (isset($trace[2]['class'])) {
				$class = $trace[2]['class'];
				$func = $trace[2]['function'];
				$file = $trace[1]['file'];
		}
		if ($file != '') $file = basename($file);
		$c = $file . ": ";
		$c .= ($class != '') ? ":" . $class . "->" : "";
		$c .= ($func != '') ? $func . "(): " : "";
		return($c);
	}

}  /* EOF Sanitize */