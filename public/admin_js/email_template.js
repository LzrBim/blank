var tpjc = tpjc || {};

tpjc.attach_email_template_test_modal = function() {

	var self = this;
	self.tLog('- - attach_email_template_test_modal2()');
	
	self.set_validator_defaults();
	
	var modalID = 'emailTemplateModal';
	
	$( '#' + modalID ).children('form').validate({
																							 
		rules: {
			email: {
				required: true,
				email:true
			}
		},
		onkeyup: false,
		
		submitHandler: function(form) {
			
			self.modal_ajax_submit_handler(modalID, function(json){
																											 
			});
			
			return false;
				
		}
		
	});//end .validate();*/
		
};