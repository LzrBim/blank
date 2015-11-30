/*-----------------------------------------------------------------------------
 * SITE: 3E
 * FILE: /js/main.js
----------------------------------------------------------------------------- */

/*MC*/
jQuery.validator.addMethod("slug", function(value, element) {
return this.optional(element) || /^[a-z0-9\-]+$/.test(value);
}, "Lower case letters, numbers and hyphens only please.  No white space.");

var tpjc = tpjc || {};

var tpjc = (function ($, main) {
											
	'use strict';
	
	var mode;
	var options;
	var settings;
		
	main.init = function(mode, options) { 
	
		this.mode = mode;
		
		var defaults = {
			validateRules: '',
			model: '',
			useTinyAbsolutePath: false,
			images: []
		};
				
		this.options = $.extend(defaults, options);
		
		//SETTINGS
		this.settings = {
			HTTP_PATH : window.location.protocol + '//' + window.location.hostname,
		};
				
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
				
				if(this.mode == 'search'){
					this.index();
				}
				
				/* ADMIN USERS */
				if(this.options.model == 'AdminUser'){
					
					if(this.mode == 'editPassword'){
						this.attach_admin_user_change_password();
						this.attach_validator();
					}
								
				}
				
				/* ACCOUNT USERS */
				if(this.options.model == 'AccountUser'){
					
					if(this.mode == 'editPassword'){
						this.attach_account_user_change_password();
						this.attach_validator();
						
					}

				}
				
				if(this.options.model == 'Curated'){
					
					if(this.mode == 'addImages'){
					
						this.set_algolia_client();
						
						this.bind_curated_add_images();
						
						
					}
					
					if(this.mode == 'reviewImages'){
					
						this.set_algolia_client();
						
						this.bind_curated_review_images();				
						
					}
					
					if(this.mode == 'thumbRank'){
					
						this.attach_curated_sortable();
						
					}
					
				}
				
				
				if(this.options.model == 'Gallery'){
					
					if(this.mode == 'addImages'){
					
						this.brief_gallery_add_images();	
						this.attach_brief_gallery_file_name_add_modal();
						
					}
					
					if(this.mode == 'reviewImages'){
					
						this.brief_gallery_review_images();		
						
					}
					
					if(this.mode == 'thumbRank'){
					
						this.attach_brief_gallery_sortable(0);
						
					}
					
					if(this.mode == 'thumbRankFeatured'){
					
						this.attach_brief_gallery_sortable(1);
						
					}
				
				}
				
				
			}
					
		} else {
			this.tLog('tpjc::init() - no mode');
			
		}
		
		this.defaults();
	
	};
	
	
	/* MODE INIT
	----------------------------------------------------------------------------- */
	main.index = function() {
		
		this.attach_result_table_checkboxes();
		
		if(this.options.model == 'Item' || this.options.model == 'Post'){
			this.attach_toggle_featured();
		}
		
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
