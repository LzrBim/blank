<?php
namespace App\Action;

class AuthAction extends BaseAction {
   
	public function login($args) {
		
		$this->logger->info("Login Attempt");
		
		$this->flash->addMessage("error", "Login Failed");
		
		$this->flash->addMessage("success", "Login pas");

		return true;
	}
}
