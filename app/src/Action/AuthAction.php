<?php

namespace App\Action;
use App\Model\User;
use App\Lib\Help as Help;

class AuthAction extends BaseAction {
	
	public function login($args) {
		
		$this->v->rule('required', ['email', 'password']);
		$this->v->rule('email', 'email');
		$this->v->rule('lengthBetween', 'password', 6, 10);
		
		if(!$this->v->validate()) {
			$this->f->addMessage("error", Help::flatErrors($this->v->errors()));
			return false;
		} 
		
		$unauthorizedUser = new User();
	
		$email = trim(strtolower($_POST['email'])); /* Trim and Lower */
		$password = $_POST['password']; 
		$rememberMe = (isset($_POST['rememberMe']) ? 1 : 0);
	
		if(!$unauthorizedUser->isValidPassword($password)){ 
			$this->f->addMessage('error', 'An error occured during login.  Your password contained invalid characters.');
			$this->l->debug( 'Invalid password: "'.$password.'"');
			return false;
		}
	
		//BEGIN THE DATABASE VALIDATION
		$unauthorizedUser->loadByEmail($email);
		
		//die('<pre>'.var_dump($unauthorizedUser, true).'</pre>');
		$this->val = $unauthorizedUser->isLoaded();
		$this->l->debug('bork - '.$this->val.' = '.$unauthorizedUser->userID);
		
		//DOES THIS USER EXIST?
		if(!$unauthorizedUser->isLoaded()){
			$this->f->addMessage('error', 'Email not found2');
			$this->l->debug('User '.$email.' not found');
			return false;
		}
	
		//ARE THEY ACTIVE?
		if($unauthorizedUser->status != 'active'){
			$this->f->addMessage('error', 'Your account has been disabled');
			$this->l->debug('User '.$email.' tried to login while marked inactive');
			return false;
		}
	
		//ARE THEY LOCKED OUT?
		if($unauthorizedUser->isLockedOut()){
			$this->f->addMessage('error', 'ou have been locked out for security reasons');
			$this->l->notice('User '.$email.' was booted for exceeding max_login_attemps');
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
	
	public function logout($user){
		
		unset($_SESSION['auth']);
		unset($_SESSION[$user->_id]);
		$user->_deleteCookie();
		addMessage('success', 'You were logged out successfully');
	
	}
	
	private function setSession($user){
		
		$_SESSION['auth'] = 1;
		$_SESSION[$user->_id] = $user->getId();
		$_SESSION['role'] = $user->role;
		
	}
}
