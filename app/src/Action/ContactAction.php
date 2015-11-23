<?php

namespace App\Action;

use App\Lib\Help as Help;

class ContactAction extends BaseAction {
	
	public function contact($args) {
				
		$this->validator->rule('required', ['email', 'message']);
		$this->validator->rule('email', 'email');
		
		if(!$this->validator->validate()) {
			
			$this->flash->addMessage("error", Help::flatErrors($this->v->errors()));
			$this->logger->info('Contact form failed validation');
			return false;
		} 
		
		$this->flash->addMessage("success", 'Your message was sent');
		$this->logger->info('Contact form passed validation');
	
		return true;
			
	}
	
}
