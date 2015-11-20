/*-----------------------------------------------------------------------------
 * SITE: 3E
 * FILE: /js/main.js
----------------------------------------------------------------------------- */
var tpjc = tpjc || {};

var tpjc = (function ($, main) {
											
	'use strict';
		
	var mode,
	options,
	settings;
		
	main.init = function(mode, options) { 
	
		this.mode = mode;
		
		var defaults = {
			model: ''
		};
				
		this.options = $.extend(defaults, options);
		
		this.tLog('MODE = '+this.mode);
		
		var optStr = '';
		$.each(this.options, function(key, val) {                    
				 optStr += key + " = " + val + ", ";
		});
		
		this.tLog('OPTIONS = '+optStr);
		
		//LE CONTROLLER
		if (typeof this.mode != 'undefined' && this.mode !== '') {
			
			/* WHITELIST ALL MODES DEFINED */
			if(this.mode == 'index'){
				
				this.index();
				
			} else if(this.mode == 'edit' || this.mode == 'add'){
				
				this.edit();
				
			} else if(this.mode == 'editRank'){
				
				this.attach_sortable();
				
			} else if(this.mode == 'login'){
				
				this.attach_login();
				
			} else { /* OTHER MODES */
			
				if(this.mode == 'dashboard'){
					this.dashboard();
				}
				
			}
					
		} else {
			this.tLog('tpjc::init() - no mode');
			
		}
		
		this.init_defaults();
	
	};
	
	/* MODE INIT
	----------------------------------------------------------------------------- */
	main.init_defaults = function() {
		
		var self = this; 
		
		self.tLog('wtf')
		
		$('#side-menu').metisMenu();
		
		$(window).bind("load resize", function() {
		
			var topOffset = 50;
		
			var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
			
			if (width < 768) {
				
				$('div.navbar-collapse').addClass('collapse');
				topOffset = 100; // 2-row-menu
				
			} else {
				
				$('div.navbar-collapse').removeClass('collapse');
				
			}
		
			var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
			
			height = height - topOffset;
			
			if (height < 1){
				height = 1;
			}
			if (height > topOffset) {
				$("#page-wrapper").css("min-height", (height) + "px");
			}
		
		});
		
		var url = window.location;
		
		var element = $('ul.nav a').filter(function() {
		
			return this.href == url || url.href.indexOf(this.href) == 0;
		
		}).addClass('active').parent().parent().addClass('in').parent();
		
		if (element.is('li')) {
			element.addClass('active');
		}
		
	};
	
	
	/* MODE INIT
	----------------------------------------------------------------------------- */
	main.dashboard = function() {
		
		this.tLog('dashboard')
		
	};
	
	main.index = function() {
		
		/*$('#tpjc_dataTable').DataTable( {
				data: data
		});*/
		
	};
	
	
	main.edit = function() {
			
		/* OPTION OVERRIDES */
		this.attach_edit_page_model_option_overrides();
		
		/* THE USUAL */
		this.attach_editor();
		
		this.attach_validator();
		
		this.attach_edit_form_plugins();
		
		this.attach_crop_button();
		
		this.attach_remove_image();
		
		this.attach_edit_form_submit_button_clicks();
		
		/* RUN OPTIONS FIRST INCASE OF OVERRIDES */
		this.attach_edit_page_model_options();
					
	};
	
		
	/* MODEL OPTIONS
	----------------------------------------------------------------------------- */
	
	main.attach_edit_page_model_option_overrides = function() {
		
		this.tLog('- attach_edit_page_model_options()');
	
		if(this.options.model.length){
			
			if(this.options.model == 'NewsletterBlock'){ 
				
				this.options.useTinyAbsolutePath = true;			
				
			}
		
		}
		
	};
	
	
	/* CLASS NAME SWITCHES
	----------------------------------------------------------------------------- */
	main.attach_edit_page_model_options = function() {
		
		this.tLog('- attach_edit_page_model_options()');
	
		if(this.options.model.length){
			
			/* ADMIN USERS */
			if(this.options.model == 'AdminUser'){

				
				if(this.mode == 'add'){
					this.attach_admin_user_add_validate();
				}
			
			}
			
			/* ACCOUNT USERS */		
			if(this.options.model == 'AccountUser'){ 
			
				if(this.mode == 'edit'){
					this.attach_account_user_group_tag_modal();
				}
			
				
			}
			
			/* ACCOUNT USERS */
			if(this.options.model == 'AccountUserGroup'){

				if(this.mode == 'edit'){
					
					this.attach_add_account_user_modal();
					
					this.attach_account_user_group_remove();

				}
			
			}
			
			/* EMAIL TEMPLATE */
			if(this.options.model == 'Discount'){ 
			
				this.validate_add_discount_form();
				
			}
			
			
			
			/* EMAIL TEMPLATE */
			if(this.options.model == 'EmailTemplate'){ 
			
				this.attach_email_template_test_modal();
				
			}
			
			/* FAQ */
			if(this.options.model == 'Faq'){ 
			
				this.attach_faq_tag_modal();
				
				this.attach_simple_editor();
				
			}
			
			
			
			/* GALLERY */
			if(this.options.model == 'Gallery'){
				
				if(this.mode == 'add'){
					
					this.gallery_add_type_select();
		
				
				
				} else {
					
					this.form_change_monitor();
				
					/* Inline removal of Slider Images */
					this.attach_remove_gallery_image();
					
				}
				
			}		
			
			/* ITEM */
			if(this.options.model == 'Brief'){ 
			
				this.attach_remove_gallery_image();
				
			}
			
			
			/* MENU */
			if(this.options.model == 'Menu'){ 
				
				this.attach_menu_item_modal();
				
				$('#nestable').nestable({
					group: 1
				
				}).on('change', function(){
					
					$('#nestableInput').val( JSON.stringify($('#nestable').nestable('serialize')) );
				
				}).trigger('change');
		
			}
			
			/* PAGE VERSIONS */
			if(this.options.model == 'Page'){ 
				
				if(this.mode == 'edit'){
					this.attach_permalink_builder();
				}
				
			}
			
			/* PAGE VERSIONS */
			if(this.options.model == 'PageVersion'){ 
				
				this.attach_add_page_version_block_modal();
				
				this.attach_insert_page_version_block_modal();
				
				this.attach_remove_page_version_block();
				
				this.attach_page_version_block_sortable();				
				
			}
			
			
			
			/* PAGE VERSION BLOCKS */
			if(this.options.model == 'PageVersionBlock'){ 
				
				this.options.useTinyAbsolutePath = true;			
				
			}
		
			/* SLIDER */
			if(this.options.model == 'Slider'){ 
			
				this.attach_simple_editor();
				
			}
			
			
			
		}
		
	};	
	
	
	/* LOGIN PAGE
	----------------------------------------------------------------------------- */
	main.attach_login = function() {
		
		this.tLog('- attach_login()');
		
		var self = this;
		
		this.set_validator_defaults();
	
		$("#loginForm").validate({
			rules: {
				email: {
					required: true,
					email:true
				},
				password: {
					required: true,
					minlength:5
				}
			},
			onkeyup: false
		});
		
		$("#forgotPasswordForm").validate({
			rules: {
				email: {
					required: true,
					email:true
				}
			}
		});
		
		$("#resetPasswordForm").validate({
			rules: {
				password: {
					required: true,
					minlength:5
				}
			}
		});
		
		/* TOGGLE */
														 
		$('#forgotPasswordButton').click(function(){
																				
			$('#loginFormContainer').slideUp('slow', function() {
																												
				$('#forgotPasswordFormContainer').removeClass('hide').show(600);
				
			});
				
			return false;
		});		
		
		$('#backToLoginButton').click(function(){
																					 
			$('#forgotPasswordFormContainer').slideUp('slow', function() {
																																 
				$('#loginFormContainer').show('slow');
			});
			
			return false;
		});
		
		/* SET OPTIONS */
		if(typeof(self.options.isForgotPassword) != 'undefined'){
			
			$('#forgotPasswordButton').click();
		}
	
	};
	
	/* UTILS
	----------------------------------------------------------------------------- */
	main.tLog = function(msg) {
		
		if (typeof console !== "undefined"){
			console.log(msg);
		}
		
	};	
	
	return main;

}($, tpjc)); //end main.js
