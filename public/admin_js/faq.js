var tpjc = tpjc || {};

/* 	FAQ
----------------------------------------------------------------------------- */
tpjc.attach_account_user_group_tag_modal = function() {
	
	this.tLog('- attach_account_user_group_tag_modal()');
	
	var self = this;
	var modalID = 'accountUserGroupTagModal';
	
	$( '#' + modalID ).children('form').validate({
														 
		rules: {
			title: "required"
		},
		
		submitHandler: function(form) {
			
			self.modal_ajax_submit_handler(modalID, function(json){
			
				$('#accountUserGroupTagID').append('<option value="'+ json.tagID +'" selected>'+ json.title +'</option>');
				
				$(".tpjc_multiSelect").trigger("chosen:updated");
			
			});
			
			return false;
				
		}
	});//end .validate();*/
	
};

/* 	FAQ
----------------------------------------------------------------------------- */
tpjc.attach_faq_tag_modal = function() {
	
	this.tLog('- attach_faq_tag_modal()');
	
	var self = this;
	var modalID = 'faqTagModal';
	
	$( '#' + modalID ).children('form').validate({
														 
		rules: {
			title: "required"
		},
		
		submitHandler: function(form) {
			
			self.modal_ajax_submit_handler(modalID, function(json){
			
				$('#faqTagID').append('<option value="'+ json.tagID +'" selected>'+ json.title +'</option>');
				
				$(".tpjc_multiSelect").trigger("chosen:updated");
			
			});
			
			return false;
				
		}
	});//end .validate();*/
	
};