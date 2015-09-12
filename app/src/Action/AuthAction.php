<?php


use Valitron\Validator;

class AuthAction extends BaseAction {
   
	public function login($args) {
		
		$this->logger->info("Login Attempt");
		
		$v = new Validator($_POST); 
		$v->rule('required', ['email', 'password']);
		$v->rule('email', 'email');
		$v->rule('lengthBetween', 'password', 6, 10);
		
		if(!$v->validate()) {
						
			$this->flash->addMessage("error", Help::flatErrors($v->errors()));
			return false;
			
		} else {
			
			$rememberMe = (isset($_POST['rememberMe']) ? 1 : 0);
			
			$user = new User();
						
			if($user->login($_POST['email'], $_POST['password'], $rememberMe)){
				
				return true;
			
			}
			
			$this->logger->info("Login Attempt Failed: ".$_POST['email']);
			return false; 
				
		}		
	}
	
	public function logout($args) {
		
		$v = new Validator($_POST);
		$v->rule('required', ['email', 'password']);
		$v->rule('email', 'email');
		
		if(!$v->validate()) {
			
			$this->flash->addMessage("error", implode(',<br>', $v->errors()));
			return false;
			
		} else {
			
			$rememberMe = (isset($_POST['rememberMe']) ? 1 : 0);
			
			$user = new User();
						
			if($user->login($_POST['email'], $_POST['password'], $rememberMe)){
				
				return true;
			
			}
			
			$this->logger->info("Login Attempt Failed: ".$_POST['email']);
			return false;
				
		}		
	}
}
