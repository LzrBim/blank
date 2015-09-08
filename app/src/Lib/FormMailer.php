<?  
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: app/lib/FormMailer.php
----------------------------------------------------------------------------- */

class FormMailer {
	
	
	private $isSuccess;
	private $errors;
	private $_sendDefaultOptions;
	
	public function __construct(){
		
		$this->isSuccess = false;	
		$this->errors = array();
		$this->_sendDefaultOptions = array(
			'to' => '',
			'cc' => '',
			'bcc' => '',
			'from' => '',
			'textFrom' => '',
			'subject' => '',
			'html' => '',
			'text' => ''								 
		);
	
	}
	
	/* SEND
	----------------------------------------------------------------------
	 
	 $formMailer = new FormMailer();
	 $html = $formMailer->getEmailHtml('title', $data)
	 $formMailer->send(array(
			'to' => '',
			'bcc' => '',
			'bcc' => '',
			'from' => '',
			'textFrom' => '',
			'subject' => '',
			'html' => $html, // or 'text' => $text
	 ));
	 
 	---------------------------------------------------------------------- */
	public function send($arr) {
		
		$arr = array_merge($this->_sendDefaultOptions, $arr);	
		
		if($this->_sanitizeEmailData($arr)){
			
			$sendgrid = new SendGrid(SEND_GRID_USER, SEND_GRID_PASS);
			
			$mail = new SendGrid\Email();
			
			$mail->
				addTo($arr['to'])->
				setFrom($arr['from'])->
				setSubject($arr['subject']);
			
			//DECIDE WHETHER IT'S HTML OR TEXT
			if(!empty($arr['html'])){
				$mail->setHtml($arr['html']);
				
			} elseif(!empty($arr['text'])) {
				$mail->setText($arr['text']);
				
			} else {
				wLog(3, 'This should not happen');
				return false;
			}
			
			//OPTIONS
			if(!empty($arr['txtFrom'])){
				$mail->setFromName(trim($arr['txtFrom']));
			}

			if(!empty($arr['cc'])){
				$mail->addBcc(trim($arr['bcc']));
			}

			if(!empty($arr['bcc'])){
				$mail->addBcc(trim($arr['bcc']));
			}
			
			//DEBUG ERRORS
			if($arr['from'] == 'error@thirdperspective.com'){
				$this->errors[] = 'error@thirdperspective.com generated error';
				wLog(1, 'Yes, error@thirdperspective.com generated error'); 
				return false;
			}
			
			//DEBUG HTML
			if($arr['from'] == 'html@thirdperspective.com'){
				die($arr['html']);  exit();
			}
			if($arr['from'] == 'text@thirdperspective.com'){
				die($arr['text']);  exit();
			}
			
			//SEND
			$response = $sendgrid->send($mail);
			
			//die('<pre>'.print_r($response).'</pre>');
			
			if(is_object($response)){
			
				if($response->message == 'success'){
					$this->isSuccess = true;
					return true;
					
				} else {
						
					foreach($response->errors as $key => $error){
						
						if(!empty($error)){
						
								$this->errors[] = $error;
								wLog(3, 'SendGrid Error: '.$error);
								
						} else {
							wLog(3, 'SendGrid Error: empy'); 
						}
					}
				}
				
			} else {
				wLog(3, 'SendGrid response not an object '.$response); 
			}
			
		} 
		
		return false;
	}
	
	/*-----------------------------------------------------------------------------
		Email HTML Formatting
	----------------------------------------------------------------------------- */
	
		
	public function getEmailHtml($title, $data){
		
		if(empty($title) || empty($data)){
			wLog(3, 'No title or data recieved');
			$this->errors[] = 'No title or data recieved';
			return false;
		}
		
		$html = '<h2>'.$title.'</h2>';
		
		if(is_array($data)){
			
			ob_start(); ?>
			
      <table cellpadding="5" cellspacing="0" border="0" style="border-collapse:collapse; border:0;"><? 
			
			foreach($data as $label => $value){ ?>
				<tr><td><strong><?= $label; ?></strong></td>
				<td><?= $value; ?></td></tr><? 
			} ?>
      
			</table><?
			
			$html .= ob_get_clean();
			
		} else {
			$html .= $data;
		}
		
		$tpl = new Template('form_mail.php');
		$tpl->set('body', $html);		
		return $tpl->render();

	}
	
	/*-----------------------------------------------------------------------------
		PUBLIC HELPERS
	----------------------------------------------------------------------------- */

	
	public function getErrors(){
		
		if(!empty($this->errors)){
			
			return implode('<br>', $this->errors); 
			
		} 
		
		return 'No errors found';
				
	}
	
	

	/* SANITIZE
	----------------------------------------------------------------------------- */

	
	private function _sanitizeEmailData(&$arr){  
	
		//REQUIRED
		
		//TO
		if(empty($arr['to'])){
			wLog(3, 'No To: email address supplied');
			$this->errors[] = 'No To: email address supplied';
			return false;
		}
		
		if(!Sanitize::isValidEmail($arr['to'])){
			wLog(3, 'To: email address was invalid: '.$arr['to']);
			$this->errors[] = 'To: email address was invalid';
			return false;
		}
		
		if($this->_isInjected($arr['to'])){
			wLog(3, 'Injection detected on To: address: '.$arr['to']);	
			return false;											 
		}
		
		//FROM
		if(empty($arr['from'])){
			wLog(3, 'No From: email address supplied');
			$this->errors[] = 'No From: email address supplied';
			return false;
		}
		
		if(!Sanitize::isValidEmail($arr['from'])){
			wLog(3, 'From: email address was invalid: '.$arr['from']);
			$this->errors[] = 'From: email address was invalid';
			return false;
		}
		
		if($this->_isInjected($arr['from'])){
			wLog(3, 'Injection detected on From: address: '.$arr['from']);								 
			return false;
		}
		
		//TEXT FROM
		if($this->_isInjected($arr['textFrom'])){
			wLog(3, 'Injection detected on TextFrom: address: '.$arr['textFrom']);								 
			return false;
		}
		
		//SUBJECT
		if(empty($arr['subject'])){
			wLog(3, 'No Subject supplied');
			$this->errors[] = 'No Subject supplied';
			return false;
		}
		
		if($this->_isInjected($arr['subject'])) { 
			wLog(3, 'Injection detected on subject: '.$arr['subject']);
			return false;
															 
		}
		
		//TEXT OR HTML
		if(empty($arr['html']) && empty($arr['text'])){
			wLog(3, 'No html or text provided');
			$this->errors[] = 'No message supplied';	
			return false;
		}
																				 
		if(!empty($arr['html'])){
			
			if($this->_isSpam($arr['html'])) {
				wLog(3, 'HTML denied due to spam likely content'); 
				$this->errors[] = 'Spam detected.  Please give us a call instead.';
				return false;
			}
			
		}	
		
		if(!empty($arr['text'])){
			
			if($this->_isSpam($arr['text'])) {
				wLog(3, 'Text denied due to spam likely content: '.$arr['text']); 
				$this->errors[] = 'Spam detected.  Please give us a call instead.';
				return false;
			}
			
		}	

		//OPTIONAL
		if(!empty($arr['cc'])){
			
			if(!Sanitize::isValidEmail($arr['cc'])){
				wLog(3, 'CC: email address was invalid: '.$arr['cc']);
				$this->errors[] = 'CC: email address was invalid';
			}
			
			if($this->_isInjected($arr['cc'])){
				wLog(3, 'Injection detected on CC: address: '.$arr['cc']);			 											 
			}
		}
		
		if(!empty($arr['bcc'])){
			
			if(!Sanitize::isValidEmail($arr['bcc'])){
				
				wLog(5, 'BCC: email address was invalid: '.$arr['bcc']);
				$this->errors[] = 'BCC: email address was invalid';
				$arr['bcc'] = '';
			}
			
			if($this->_isInjected($arr['bcc'])){										 
				wLog(5, 'Injection detected on BCC: address: '.$arr['bcc']);				
				$arr['bcc'] = '';
			}
			
		}
		
		return true;
		
	}
	
	private function _isInjected($str) {  
	
		$count = 0;
		$tests = array(
			"/bcc\:/i", 
			"/Content\-Type\:/i", 
			"/Mime\-Version\:/i", 
			"/cc\:/i", 
			"/from\:/i", 
			"/to\:/i", 
			"/Content\-Transfer\-Encoding\:/i",
			"/%0A/i"); 
		preg_replace($tests, "", $str, -1, $count); 
		
		if($count){
			
			wLog(3, 'Mail injection detected: '.$_SERVER['REMOTE_ADDR']);
			
			$this->errors[] = 'Mail injection detected';
			
			return true;
			
		} 
		
		return false;
	} 
	
	private function _isSpam($str){
		
		$str = strtolower($str);
	
		if(substr_count($str, '<a href=') <= 5 
				&& substr_count($str, '[url=') < 1
				&& substr_count($str, 'viagra') < 1
				&& substr_count($str, 'seo') < 5
				&& substr_count($str, 'optimization') < 2
				&& substr_count($str, 'facebook') < 4
				&& substr_count($str, 'twitter') < 4 
				&& substr_count($str, 'reviewincus.com') < 1
				&& substr_count($str, 'effexor') < 1
				&& substr_count($str, 'clomid') < 1
				&& substr_count($str, 'zithromax') < 1
				&& substr_count($str, 'zoloft') < 1
				&& substr_count($str, 'duloxetine') < 1
				&& substr_count($str, 'retin-a') < 1
				&& substr_count($str, 'orlistat') < 1
				&& substr_count($str, 'rxitem') < 1
				&& substr_count($str, 'nolvadex') < 1
				&& substr_count($str, 'flagyl') < 1
				&& substr_count($str, 'minoxidil') < 1
				&& substr_count($str, 'cymbalta') < 1
				&& substr_count($str, 'maxilene') < 1
				&& substr_count($str, 'cytotec') < 1){
					
			return false;
			
		} 
		
		return true; 
		
	}

	
} /* end Class FormMailer */