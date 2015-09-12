<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/User.php
----------------------------------------------------------------------------- */ 
namespace App\Model; 

class User extends BaseModel { 
	
	//CORE ATTRIBUTES
	public $_title = 'User';
	public $_id = 'userId';
	public $_table = 'user'; 
	
	//FIELDS
	public $email; 
	public $password;
	public $salt;
	public $firstName;	
	public $lastName;	
	public $role;
	public $cookieHash;
	public $forgotPasswordToken;
	public $forgotPasswordExpires;
	public $lastFailedLogin;
	public $failedAttempts = 0;
	public $status = 'active'; //'active', 'inactive', 'locked'
	
	//SETTINGS
	protected $COOKIE_TIME = 2592000; //30 days
	protected $FORGOT_PASSWORD_EXPIRES = 86400; //1 day
	protected $MAX_LOGIN_ATTEMPTS = 5; //1 day
	
	
	/* LOAD
	----------------------------------------------------------------------------- */

	
	/* LOGIN
	----------------------------------------------------------------------------- */
	
	public function login($email, $password, $rememberMe = 0 ){
		
		$app = Slim::getInstance();
		$app->logger->info("Login Attempt Failed: ".$_POST['email']);
		
		//HARD ERRORS
		if(empty($email) || empty($password)){
			wLog(2, 'Email or username not set');
			return false;
		}
		
		$email = $this->_formatEmail($email); /* Trim and Lower */
		
		if(!$this->isValidPassword($password)){ 
			addMessage('error', 'An error occured during login.  Your password contained invalid characters.');
			wLog(2, 'Invalid password: "'.$password.'"');
			return false;
		}
		
		//BEGIN THE DATABASE VALIDATION
		$unauthorizedUser = new $this;
		$unauthorizedUser->loadByEmail($email);
		
		//DOES THIS USER EXIST?
		if(!$unauthorizedUser->isLoaded()){
			wLog(2, 'User '.$email.' failed logged in');
			addMessage('error', 'Your email was incorrect');
			return false;
		}
		
		//ARE THEY ACTIVE?
		if($unauthorizedUser->status != 'active'){
			wLog(2, 'User '.$email.' tried to login while marked inactive');
			addMessage('error', 'Your account has been disabled');
			return false;
		}
		
		//ARE THEY LOCKED OUT?
		if($unauthorizedUser->failedAttempts >= $this->MAX_LOGIN_ATTEMPTS){
			wLog(2, 'User '.$email.' was booted for exceeding max_login_attmps');
			addMessage('error', 'You have been locked out for security reasons');
			return false;
		}
		
		//EDGE CASE - NO SALT
		if(empty($unauthorizedUser->salt)){
			wLog(3, 'User '.$email.' has not salt');
			addMessage('error', 'Your user account is invalid - please contact administrator');
			return false;
		}
		
		//OKAY, PASSWORD MATCH?
		
		if($unauthorizedUser->password == $this->_getPasswordHash($password, $unauthorizedUser->salt)){
		
				$this->load($unauthorizedUser->getId());
				
				$this->_setSession();
				
				$this->_rememberMe($rememberMe);
				
				$this->_resetFailedAttemptInformation();
				
				addMessage('success', 'You were logged in successfully');
				
				wLog(2, 'User '.$email.' logged in');
				
				return true;
		
		} else {
			
			//ADD HAMMER PREVENTION
			$this->_updateFailedAttempt($email);
			
			addMessage('error', 'Your password was incorrect');
				
			wLog(2, 'User '.$email.' failed login in');
			
			return false;
			
		} 
			
		return false;
	}
	
	public function logout(){
		
		unset($_SESSION[$this->_realm.'_auth']);
		unset($_SESSION[$this->_id]);
		$this->_deleteCookie();
		addMessage('success', 'You were logged out successfully');
	
	}
	
	/*  LOAD HELPERS
	----------------------------------------------------------------------------- */
	
	public function loadByEmail($email){ 
	
		if(empty($email)){
			wLog(3, 'No email supplied');
			return false;
		}
		
		return $this->loadWhere("email = '".$email."'");
		
	}
	 
	public function loadByForgotPasswordToken($token){ 
	
		if(empty($token)){
			wLog(1, 'No token supplied');
			return false; 
		}
		
		//$token = Sanitize::clean($token);
		
		$where = "forgotPasswordToken = '".$token."'";
		
		$this->loadWhere($where);
		
		if($this->isLoaded()){
			
			if(strtotime($this->forgotPasswordExpires) > time()){	
				return true;
				
			} else {
				addMessage('error', 'The time limit for submitting a password reset has expired.  Please retry.');
				wLog(2, 'token expired');
				return false;
			}
		} else {
			addMessage('error', 'You submitted an invalid reset password link.  Please retry.');
			wLog(2, 'user::loadByForgotPasswordToken()');
			return false;
		} 
		
		return false;
		
	}
		
		 
	/* 	CRUD	
	----------------------------------------------------------------------------- */

	public function insert(){
		
		//HARD ERRORS
		if(empty($this->email) || empty($this->password)) { 
			return false;
		}
			
		if(!$this->isValidPassword($this->password)){
			return false;
		}	
		
		//SET DEFAULT ROLE
		if(empty($this->role)){
			$this->role = 'user';
		}
		
		$this->salt = $this->_getSalt();
		$this->password = $this->_getPasswordHash($this->password, $this->salt);
		
		
		
		if(!$this->emailExists($this->email)){
			
			$db = \App\Lib\Database::get_instance();
		
			$insert = sprintf("INSERT INTO ".$this->_table." 
				(email, password, salt, firstName, lastName, role, status) 
				VALUES (%s, %s, %s, %s, %s, %s, %s)",
				Sanitize::input($this->_formatEmail($this->email), "text"), 
				Sanitize::input($this->password, "text"), 
				Sanitize::input($this->salt, "text"), 
				Sanitize::input($this->firstName, "text"),
				Sanitize::input($this->lastName, "text"),
				Sanitize::input($this->role, "text"),
				Sanitize::input($this->status, "text"));
			
			if($this->query($insert)){ 
			
				$this->setInsertId();
				
				addMessage('success', 'User added successfully');
						
				return true;
				
			} else { 
				return false;
			} 
			
		} else {
			addMessage('error', 'A User with this email already exists.');
			wLog(2, 'A User with this email already exists: '.$this->email);
			return false;
		}
	}
	
	public function update(){
		
		if(!$this->isLoaded()) { 
			wLog(2, 'User not loaded'); 
			return false; 
		}
		
		$update = sprintf("UPDATE ".$this->_table."
			SET email=%s, firstName=%s, lastName=%s, status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->_formatEmail($this->email), "text"),
			Sanitize::input($this->firstName, "text"),
			Sanitize::input($this->lastName, "text"),
			Sanitize::input($this->status, "text"),
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			addMessage('success', 'User updated successfully');
			return true;
		} else { 
			return false;
		}
	}
	
	
	/* ACCESSORY CRUD
	----------------------------------------------------------------------------- */
	
	public function updatePassword($password){
		
		if(!$this->isLoaded()) { 
			wLog(4, 'User not loaded');
			return false;
		}
		
		if(empty($password)) { 
			wLog(4, 'Password was empty');
			return false;
		}
		
		if(!Sanitize::isValidPassword($password)){ 
			addMessage('error', 'Your password contained invalid characters.');
			wLog(2, 'Invalid password: "'.$password.'"');
			return false;
		}
		
		//wLog(1, 'setting password to: '.$password);
		
		$this->salt = $this->_getSalt();
		$this->password = $this->_getPasswordHash($password, $this->salt);
		
		$update = sprintf("UPDATE ".$this->_table."
			SET password=%s, salt=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->password, "text"),
			Sanitize::input($this->salt, "text"),
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			addMessage('success','Password updated successfully');
			return true;
			
		} else { 
			addMessage('error','Error updating your password');
			return false;
		}
	}
	
	public function clearForgotPasswordToken(){
		
		if(!$this->isLoaded()) { 
			wLog(4, 'User not loaded');
			return false;
		}
		
		$update = sprintf("UPDATE ".$this->_table."
			SET forgotPasswordToken = NULL, forgotPasswordExpires = NULL
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			
			return true;
			
		} 
		
		return false;
		
	}
	
	
	public function forgotPassword(){
		
		$this->_setForgotPasswordInformation();
		
		$update = sprintf("UPDATE ".$this->_table."
			SET forgotPasswordToken=%s, forgotPasswordExpires=%s 
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->forgotPasswordToken, "text"), 
			Sanitize::input($this->forgotPasswordExpires, "text"),
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			return true;
		} else { 
			return false;
		}
	}
	
	
	private function _updateCookieHash(){
		
		$this->hash = $this->_getCookieHash();
		
		$update = sprintf("UPDATE ".$this->_table."
			SET cookieHash=%s 
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->hash, "text"), 
			Sanitize::input($this->getId(), "int"));
		
		$this->query($update);
		
		return true;
		
	}
	
	
	/* FORGOT PASSWORD ROUTINE
	----------------------------------------------------------------------------- */
	
	function resetPasswordRoutine($token, $password){
			
		if(empty($token)){ 
			addMessage('error', 'No token supplied');
			wLog(2, 'No token supplied');
			return false;
		}
		
		if(empty($password)){ 
			addMessage('error', 'No password supplied');
			wLog(2, 'No password supplied');
			return false;
		}
		
		if($this->loadByForgotPasswordToken($token)){
			
			if($this->updatePassword($password)){
				
				$this->clearForgotPasswordToken();
				
				clearMessages();
				
				//LOG THEM IN
				$this->_setSession();
				addMessage('success', 'Your password was reset successfully');
				return true;
				
			} else {
				addMessage('error', 'An error occurred while resetting your password.  Please retry.');
				return false;
			}
			
		} else {
			wLog(1, 'Error during loadByForgotPasswordToken() '.$token);
			return false;
		}
	}
	
	private function isValidPassword($password){
		preg_replace("/[^a-zA-Z0-9!@#\%\^&\*\._-]/", ' ', $password, -1 , $count);  
		if(!$count){
			return true;
		} else {
			return false;
		}
	}
	
	
	/* SESSION
	----------------------------------------------------------------------------- */
	
	public function userSessionStart(){
	
		$sessionID = session_id();
		session_start();
		
		if (empty($sessionID)) {
			$_SESSION['sessionID'] = session_id();
		} else {
			$_SESSION['sessionID'] = $sessionID;
		} 
		

		//AUTH
		if($this->isAuthorized()){ 
		
			if(isset($_SESSION[$this->_id]) && !empty($_SESSION[$this->_id])){
				
				$userID = Sanitize::paranoid($_SESSION[$this->_id], 'userSessionStart() - userID');
				
				if(!empty($userID) && $this->load($userID)){
					return true;
				} else {
					$this->logout();
					clearMessages();
					return false;
				}
				
			} else {
				wLog(1, 'User::userSessionStart() - session pk not set');
				return false;
			}

		} elseif(isset($_COOKIE[$this->_id]) && isset($_COOKIE['hash'])){
			
			return $this->cookieLogin($_COOKIE[$this->_id], $_COOKIE['hash']);
			
		} else {
			$_SESSION[$this->_realm.'_auth'] = 0;
			return false;
		}
	}
	
	public function isAuthorized(){
		if(isset($_SESSION[$this->_realm.'_auth']) 
			&& $_SESSION[$this->_realm.'_auth'] == 1){    
			return true;
		} else {
			return false;
		}
	}
	
	
	private function _setSession(){
		$_SESSION[$this->_realm.'_auth'] = 1;
		$_SESSION[$this->_id] = $this->getId();
		$_SESSION['role'] = $this->role;
	}
	
	
	/* COOKIES
	----------------------------------------------------------------------------- */
	
	public function 
	cookieLogin($userID, $hash){
		
		//validate
		if(empty($userID)){ 
			wLog(3, 'No pk supplied');
			return false;
		} 
		
		if(empty($hash)){ 
			wLog(3, 'No hash supplied');
			return false;
		} 
		
		$userID = Sanitize::paranoid($userID);
		
		$hash = Sanitize::clean($hash);
		
		if($this->loadWhere($this->_id." = ".$userID."	AND cookieHash = '".$hash."' AND status = 'active'")){
	
			$this->_setSession();
			wLog(1, 'cookie_login success for userID='.$userID);
			return true;
		
		} 
		return false;
	}
	
	
	private function _rememberMe($rememberMe = 0){
		if($rememberMe == 1){
			$this->_updateCookieHash();
			$this->_setCookie();
		}
	}
	
	
	private function _setCookie(){
		
		if(setcookie($this->_id, $this->getId(), time()+$this->COOKIE_TIME, "/", "")){
			//wLog(1, '_set_cookie - userID set');									   
		} else {
			wLog(1, '_set_cookie failed userID');
		}
		if(setcookie("hash", $this->hash, time()+$this->COOKIE_TIME, "/", "")){
			//wLog(1, '_set_cookie - hash set');									   
		} else {
			wLog(1, '_set_cookie failed hash');
		}	
	}
	
	
	private function _deleteCookie(){
		setcookie($this->_id, '', time()-60*60*24*30, "/");
		setcookie("hash", '', time()-60*60*24*30, "/");
	}
	
	
	private function _getCookieHash(){
		return md5('DustyPockets'.$this->getId().time());
	}
	
	
	/*  SECURITY
	----------------------------------------------------------------------------- */

	private function _updateFailedAttempt($email){
		
		if($this->loadByEmail($email)){
				
			if($this->lastFailedLogin){ /* we already set it, increase the failed attempts*/
				
				if(strtotime($this->lastFailedLogin) > (time() - (60 * 60)) ){ /* in the last hour */
					$this->failedAttempts++;
				}
				
			} else {

				$this->lastFailedLogin = date(MYSQL_DATETIME_FORMAT, time());
				$this->failedAttempts = 1;
			}

			$this->_updateFailedAttemptInformation();
						
			return true;
			
		} else {
			wLog(1, 'should not happen');
			return false;
		}
		
	}
	
	private function _updateFailedAttemptInformation(){
		
		if(!$this->isLoaded()){
			wLog(1, 'User not loaded');
			return false;
		}
		
		$update = sprintf("UPDATE ".$this->_table."
			SET lastFailedLogin=%s, failedAttempts=%d 
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->lastFailedLogin, "text"), 
			Sanitize::input($this->failedAttempts, "int"),
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			return true;
		} else { 
			return false;
		}
		
	}
	
	private function _resetFailedAttemptInformation(){
		
		if(!$this->isLoaded()){
			wLog(1, 'User not loaded');
			return false;
		}
		
		$update = "UPDATE ".$this->_table."
			SET lastFailedLogin=NULL, failedAttempts=0 
			WHERE ".$this->_id."=".$this->getId();
	
		if($this->query($update)){ 
			return true;
		} else { 
			return false;
		}
		
	}


	/* HELPERS
	----------------------------------------------------------------------------- */
	
	public function getName(){
		
		$name = '';
		$name .= $this->firstName;
		if(!empty($this->lastName)){
			$name .= ' '.$this->lastName;
		}
		
		return $name;
		
	}
	
	public function emailExists($email){
		$where = "email = '".$email."'";	
		return $this->fetchCount($where);
		
	}
	
	
	public function _formatEmail($email){
		return trim(strtolower($email));
	}
	
	protected function getTempPassword( $length = 8 ) {

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);

	}
	
	protected function _getSalt(){
		
		$salt = uniqid(mt_rand(), true);
		$salt = base64_encode($salt);
		return str_replace('+', '.', $salt);
	
	}
	
	protected function _getPasswordHash($password, $salt){
		
		return crypt($password, '$2y$10$'.$salt.'$');
	}
	
	
	protected function _setForgotPasswordInformation(){
		
		$rand = uniqid(mt_rand(), true);
		$rand = base64_encode($rand);
		$rand = str_replace('+', '.', $rand);
		
		$this->forgotPasswordToken = md5($rand.microtime());
		$this->forgotPasswordExpires = date(MYSQL_DATETIME_FORMAT, time() + $this->FORGOT_PASSWORD_EXPIRES);
	}
	
	
} //end class User