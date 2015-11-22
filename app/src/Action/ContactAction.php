<?php

namespace App\Action;

use App\Lib\Help as Help;

class ContactAction extends BaseAction {
	
	public function contact($args) {
				
		$this->v->rule('required', ['email', 'message']);
		$this->v->rule('email', 'email');
		
		if(!$this->v->validate()) {
			
			$this->f->addMessage("error", Help::flatErrors($this->v->errors()));
			$this->l->info('Contact form failed validation');
			return false;
		} 
		
		$this->f->addMessage("success", 'Your message was sent');
		$this->l->info('Contact form passed validation');
	
		return true;
			
	}
	
}
