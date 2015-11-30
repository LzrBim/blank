var tpjc = tpjc || {};


/* ADD ACCOUNT USER MODAL */
tpjc.validate_add_discount_form = function() {
	
	self = this;
	self.tLog('- validate_add_discount_form()');
	
	
	$('#tpjc_discountTypeID').change(function(){
	
		var id = $(this).val();
		var optCon = 'discountType_'+id;

		$('.tpjc_discountType').hide();

		$('#'+optCon).fadeIn(200);
	
	}).trigger('change');
	
	
	

};

