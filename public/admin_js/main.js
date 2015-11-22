/*-----------------------------------------------------------------------------
 * SITE: 3E
 * FILE: /js/main.js
----------------------------------------------------------------------------- */
var tpjc = tpjc || {};

var tpjc = (function ($, main) {
											
	'use strict';
	
		
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
		
		//SETTINGS
		this.settings = {};
		this.settings.HTTP_PATH = 'local.blank.com';
		
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
		//this.attach_edit_page_model_option_overrides();
		
		/* THE USUAL */
		this.attach_editor();
		
		this.attach_validator();
		
		//this.attach_edit_form_plugins();
		
		//this.attach_crop_button();
		
		//this.attach_remove_image();
		
		//this.attach_edit_form_submit_button_clicks();
		
		/* RUN OPTIONS FIRST INCASE OF OVERRIDES */
		//this.attach_edit_page_model_options();
					
	};
	
	/* TINYMCE  */
	main.attach_editor = function () {
		
		var self = this;
		self.tLog('- attach_editor()');
		
		/* THIS REMOVES THE HOST NAME FROM URLS BEING INSERTED */
		var relative_urls = false;
		var convert_urls = true;
		var remove_script_host = true;
		var document_base_url = self.settings.HTTP_PATH;
		
		/* LOCAL - HARD CODED URLS */
		/*if(window.location.hostname ==  '10.0.1.2' || self.options.useTinyAbsolutePath) { 
			relative_urls = false;
			convert_urls = false;
			remove_script_host = false;
			//document_base_url = "";

		}*/
		
		tinymce.init({
		 
			relative_urls : relative_urls,
			convert_urls: convert_urls,
			remove_script_host : remove_script_host,
			document_base_url : document_base_url,
			
			//skin_url: '3e/js/tinymce/skins/tpSkin/',
		
			content_css : "http://local.blank.com/css/style.css",
		
			selector:'.tinyMCE',
			
			object_resizing : "img",
			
			style_formats: [
											
				{	title: 'Heading 2', block: 'h2' },
				{	title: 'Heading 3', block: 'h3'	},
				{	title: 'Heading 4', block: 'h4'	},
				{	title: 'Heading 5', block: 'h5'	},
				
				{	title: 'Image Left', selector: 'img',	styles: {
					'float': 'left', 
					'margin': '0 20px 0 20px'
				}},
				
				{	title: 'Image Right', selector: 'img', styles: {
					'float': 'right', 
					'margin': '0 0 20px 20px'
				}},
				
				{	title: 'Red', inline: 'span', classes: 'red' },
				
				{	title: 'Green', inline: 'span', classes: 'green' }
				
			
			],
		
			plugins: ["advlist autolink lists link image charmap anchor searchreplace visualblocks code media table contextmenu paste"],
			
			
			image_advtab: false, /* enable the Advance Image tab */
			
			setup : function(editor) {
        
				editor.addButton('tpjc_mediaButton', {
					title : 'Add Video',
					icon : 'mce-ico mce-i-media',
					onclick : function() {
						
						editor.windowManager.open({
							title: 'Insert Video',
							body: [{
								type:'label',
								text:'Embed Code',
								style:"font-weight:bold;"
								
							},{
								type: 'textbox', 
								name: 'embedCode', 
								minWidth:320,
								minHeight:150,
								multiline: true,
								tooltip: 'Paste the embed code obtained from Youtube Share => Embed'
							}],
							onsubmit: function(e) {
								editor.insertContent('<div class="video-responsive">' + e.data.embedCode + '</div>');
							}
            });
					}
				});
    	},

	
			menubar : false,		
			toolbar: "bold italic | styleselect | alignleft aligncenter alignright | bullist table outdent indent hr | link unlink |  tpjc_mediaButton image | code",
	
			
			/* type = file,image,flash */
			file_browser_callback: function(field_name, url, type, win) {
				
				var windowTitle = '';
				if(type == 'file'){
					windowTitle = 'File & Link Browser';
					
				} else if(type == 'image'){
					windowTitle = 'Image Browser';
				} else {
					windowTitle = 'Media';
				}
				
				tinymce.activeEditor.windowManager.open({
					title: windowTitle,
					url: 'js/file_browser/index.php',
					width: 640,
					height: 400,
					close_previous : "no",
					buttons: [{
							text: 'Close',
							onclick: 'close'
					}]
				}, {
					 field_name: field_name,
					 type: type,
					 win: win 
				});	
				
				return false;
					
			}
			
		});		
				
	};
	
	/* SET VALIDATOR DEFAULTS  */
	main.set_validator_defaults = function () {
		
		self = this;
		self.tLog('- set_validator_defaults()');
		
		$.validator.setDefaults({
								
			/*ignore: ':hidden',*/
			ignore: '',
			errorElement: 'span',
			errorClass: 'help-block',
			
			highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
			},
			
			unhighlight: function(element) {
					$(element).closest('.form-group').removeClass('has-error');
			},
			
			errorPlacement: function(error, element) {
				if(element.parent('.input-group').length) {
					error.insertAfter(element.parent());
				} else {
					error.insertAfter(element);
				}
			}, 
		 
			submitHandler: function(form) {
				
				self.tLog('- - submitHandler called');
				
				$.blockUI({ message: $('#loading') });
				
				form.submit();
			}
		
		});
		
	};
	
	/* VALIDATOR  */
	main.attach_validator = function () {
		
		self = this;
		self.tLog('- attach_validator()');
		
		if(typeof(self.options.validateRules) != 'undefined'){
		
			self.set_validator_defaults(); 
			
			var validator = $("#editForm").submit(function() {
				// update underlying textarea before submit validation
				tinyMCE.triggerSave();
				
			}).validate( self.options.validateRules );
			
			self.tLog('- - validate('+JSON.stringify(self.options.validateRules)+')');
			
		} else {
			
			self.tLog('- - attaching submitHandler w/o validation');
			
			/* since blockUI is called from the submit handler, we need to re-add it here*/

			$("#editForm").submit(function() {
																		 
				self.tLog('- - submit() called');
				
				//$.blockUI({ message: '<h3><img src="images/loading.gif" /> Just a moment...</h3>' });
				
				$.blockUI({ message: $('#loading') });
				
				return true;
			});
			
		}
		
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
