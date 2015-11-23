<?php

namespace App\Action;
use App\Model\User;
use App\Lib\Help as Help;

class AuthAction extends BaseAction {
	
	public function login($args) {
		
		$this->validator->rule('required', ['email', 'password']);
		$this->validator->rule('email', 'email');
		$this->validator->rule('lengthBetween', 'password', 6, 10);
		
		if(!$this->validator->validate()) {
			$this->flash->addMessage("error", Help::flatErrors($this->validator->errors()));
			return false;
		} 
		
		$unauthorizedUser = new User();
	
		$email = trim(strtolower($_POST['email'])); /* Trim and Lower */
		
		$password = $_POST['password']; 
		
		$rememberMe = (isset($_POST['rememberMe']) ? 1 : 0);
	
		if(!$unauthorizedUser->isValidPassword($password)){ 
			$this->flash->addMessage('error', 'An error occured during login.  Your password contained invalid characters.');
			$this->logger->debug( 'Invalid password: "'.$password.'"');
			return false;
		}
	
		//BEGIN THE DATABASE VALIDATION
		$unauthorizedUser->loadByEmail($email);
		
		//die(var_dump($unauthorizedUser));
		
		//DOES THIS USER EXIST?
		if(!$unauthorizedUser->isLoaded()){
			$this->flash->addMessage('error', 'Email not found');
			$this->logger->debug('User '.$email.' not found');
			return false;
		}
	
		//ARE THEY ACTIVE?
		if($unauthorizedUser->status != 'active'){
			$this->flash->addMessage('error', 'Your account has been disabled');
			$this->logger->debug('User '.$email.' tried to login while marked inactive');
			return false;
		}
	
		//ARE THEY LOCKED OUT?
		if($unauthorizedUser->isLockedOut()){
			$this->flash->addMessage('error', 'ou have been locked out for security reasons');
			$this->logger->notice('User '.$email.' was booted for exceeding max_login_attemps');
			return false;
		}
	
		//OKAY, PASSWORD MATCH?
		
		if($unauthorizedUser->password == $unauthorizedUser->getPasswordHash($password, $unauthorizedUser->salt)){
		
				$this->setSession($unauthorizedUser);
				
				$unauthorizedUser->rememberMe($rememberMe);
				
				$unauthorizedUser->resetFailedAttemptInformation();
				
				return true;
		
		} else {
			
			//ADD HAMMER PREVENTION
			$unauthorizedUser->updateFailedAttempt();
			
		} 
		
		return false;
			
	}
	
	public function logout(){
		
		$user = new User();
		
		if(isset($_SESSION[$user->_id])){
		
			$user->load($_SESSION[$user->_id]);
		
			$user->deleteCookie();
			
		}
		
		unset($_SESSION['auth']);
		
		unset($_SESSION['role']);
		
		unset($_SESSION[$user->_id]);
		
		$this->flash->addMessage("success", 'You were logged out');
		
	}
	
	private function setSession($user){
		
		$_SESSION['auth'] = 1;
		$_SESSION[$user->_id] = $user->id();
		$_SESSION['role'] = $user->role;
		
	}
}
