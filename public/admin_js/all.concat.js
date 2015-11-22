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

/*! jQuery Validation Plugin - v1.14.0 - 6/30/2015
 * http://jqueryvalidation.org/
 * Copyright (c) 2015 Jörn Zaefferer; Licensed MIT */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a(jQuery)}(function(a){a.extend(a.fn,{validate:function(b){if(!this.length)return void(b&&b.debug&&window.console&&console.warn("Nothing selected, can't validate, returning nothing."));var c=a.data(this[0],"validator");return c?c:(this.attr("novalidate","novalidate"),c=new a.validator(b,this[0]),a.data(this[0],"validator",c),c.settings.onsubmit&&(this.on("click.validate",":submit",function(b){c.settings.submitHandler&&(c.submitButton=b.target),a(this).hasClass("cancel")&&(c.cancelSubmit=!0),void 0!==a(this).attr("formnovalidate")&&(c.cancelSubmit=!0)}),this.on("submit.validate",function(b){function d(){var d,e;return c.settings.submitHandler?(c.submitButton&&(d=a("<input type='hidden'/>").attr("name",c.submitButton.name).val(a(c.submitButton).val()).appendTo(c.currentForm)),e=c.settings.submitHandler.call(c,c.currentForm,b),c.submitButton&&d.remove(),void 0!==e?e:!1):!0}return c.settings.debug&&b.preventDefault(),c.cancelSubmit?(c.cancelSubmit=!1,d()):c.form()?c.pendingRequest?(c.formSubmitted=!0,!1):d():(c.focusInvalid(),!1)})),c)},valid:function(){var b,c,d;return a(this[0]).is("form")?b=this.validate().form():(d=[],b=!0,c=a(this[0].form).validate(),this.each(function(){b=c.element(this)&&b,d=d.concat(c.errorList)}),c.errorList=d),b},rules:function(b,c){var d,e,f,g,h,i,j=this[0];if(b)switch(d=a.data(j.form,"validator").settings,e=d.rules,f=a.validator.staticRules(j),b){case"add":a.extend(f,a.validator.normalizeRule(c)),delete f.messages,e[j.name]=f,c.messages&&(d.messages[j.name]=a.extend(d.messages[j.name],c.messages));break;case"remove":return c?(i={},a.each(c.split(/\s/),function(b,c){i[c]=f[c],delete f[c],"required"===c&&a(j).removeAttr("aria-required")}),i):(delete e[j.name],f)}return g=a.validator.normalizeRules(a.extend({},a.validator.classRules(j),a.validator.attributeRules(j),a.validator.dataRules(j),a.validator.staticRules(j)),j),g.required&&(h=g.required,delete g.required,g=a.extend({required:h},g),a(j).attr("aria-required","true")),g.remote&&(h=g.remote,delete g.remote,g=a.extend(g,{remote:h})),g}}),a.extend(a.expr[":"],{blank:function(b){return!a.trim(""+a(b).val())},filled:function(b){return!!a.trim(""+a(b).val())},unchecked:function(b){return!a(b).prop("checked")}}),a.validator=function(b,c){this.settings=a.extend(!0,{},a.validator.defaults,b),this.currentForm=c,this.init()},a.validator.format=function(b,c){return 1===arguments.length?function(){var c=a.makeArray(arguments);return c.unshift(b),a.validator.format.apply(this,c)}:(arguments.length>2&&c.constructor!==Array&&(c=a.makeArray(arguments).slice(1)),c.constructor!==Array&&(c=[c]),a.each(c,function(a,c){b=b.replace(new RegExp("\\{"+a+"\\}","g"),function(){return c})}),b)},a.extend(a.validator,{defaults:{messages:{},groups:{},rules:{},errorClass:"error",validClass:"valid",errorElement:"label",focusCleanup:!1,focusInvalid:!0,errorContainer:a([]),errorLabelContainer:a([]),onsubmit:!0,ignore:":hidden",ignoreTitle:!1,onfocusin:function(a){this.lastActive=a,this.settings.focusCleanup&&(this.settings.unhighlight&&this.settings.unhighlight.call(this,a,this.settings.errorClass,this.settings.validClass),this.hideThese(this.errorsFor(a)))},onfocusout:function(a){this.checkable(a)||!(a.name in this.submitted)&&this.optional(a)||this.element(a)},onkeyup:function(b,c){var d=[16,17,18,20,35,36,37,38,39,40,45,144,225];9===c.which&&""===this.elementValue(b)||-1!==a.inArray(c.keyCode,d)||(b.name in this.submitted||b===this.lastElement)&&this.element(b)},onclick:function(a){a.name in this.submitted?this.element(a):a.parentNode.name in this.submitted&&this.element(a.parentNode)},highlight:function(b,c,d){"radio"===b.type?this.findByName(b.name).addClass(c).removeClass(d):a(b).addClass(c).removeClass(d)},unhighlight:function(b,c,d){"radio"===b.type?this.findByName(b.name).removeClass(c).addClass(d):a(b).removeClass(c).addClass(d)}},setDefaults:function(b){a.extend(a.validator.defaults,b)},messages:{required:"This field is required.",remote:"Please fix this field.",email:"Please enter a valid email address.",url:"Please enter a valid URL.",date:"Please enter a valid date.",dateISO:"Please enter a valid date ( ISO ).",number:"Please enter a valid number.",digits:"Please enter only digits.",creditcard:"Please enter a valid credit card number.",equalTo:"Please enter the same value again.",maxlength:a.validator.format("Please enter no more than {0} characters."),minlength:a.validator.format("Please enter at least {0} characters."),rangelength:a.validator.format("Please enter a value between {0} and {1} characters long."),range:a.validator.format("Please enter a value between {0} and {1}."),max:a.validator.format("Please enter a value less than or equal to {0}."),min:a.validator.format("Please enter a value greater than or equal to {0}.")},autoCreateRanges:!1,prototype:{init:function(){function b(b){var c=a.data(this.form,"validator"),d="on"+b.type.replace(/^validate/,""),e=c.settings;e[d]&&!a(this).is(e.ignore)&&e[d].call(c,this,b)}this.labelContainer=a(this.settings.errorLabelContainer),this.errorContext=this.labelContainer.length&&this.labelContainer||a(this.currentForm),this.containers=a(this.settings.errorContainer).add(this.settings.errorLabelContainer),this.submitted={},this.valueCache={},this.pendingRequest=0,this.pending={},this.invalid={},this.reset();var c,d=this.groups={};a.each(this.settings.groups,function(b,c){"string"==typeof c&&(c=c.split(/\s/)),a.each(c,function(a,c){d[c]=b})}),c=this.settings.rules,a.each(c,function(b,d){c[b]=a.validator.normalizeRule(d)}),a(this.currentForm).on("focusin.validate focusout.validate keyup.validate",":text, [type='password'], [type='file'], select, textarea, [type='number'], [type='search'], [type='tel'], [type='url'], [type='email'], [type='datetime'], [type='date'], [type='month'], [type='week'], [type='time'], [type='datetime-local'], [type='range'], [type='color'], [type='radio'], [type='checkbox']",b).on("click.validate","select, option, [type='radio'], [type='checkbox']",b),this.settings.invalidHandler&&a(this.currentForm).on("invalid-form.validate",this.settings.invalidHandler),a(this.currentForm).find("[required], [data-rule-required], .required").attr("aria-required","true")},form:function(){return this.checkForm(),a.extend(this.submitted,this.errorMap),this.invalid=a.extend({},this.errorMap),this.valid()||a(this.currentForm).triggerHandler("invalid-form",[this]),this.showErrors(),this.valid()},checkForm:function(){this.prepareForm();for(var a=0,b=this.currentElements=this.elements();b[a];a++)this.check(b[a]);return this.valid()},element:function(b){var c=this.clean(b),d=this.validationTargetFor(c),e=!0;return this.lastElement=d,void 0===d?delete this.invalid[c.name]:(this.prepareElement(d),this.currentElements=a(d),e=this.check(d)!==!1,e?delete this.invalid[d.name]:this.invalid[d.name]=!0),a(b).attr("aria-invalid",!e),this.numberOfInvalids()||(this.toHide=this.toHide.add(this.containers)),this.showErrors(),e},showErrors:function(b){if(b){a.extend(this.errorMap,b),this.errorList=[];for(var c in b)this.errorList.push({message:b[c],element:this.findByName(c)[0]});this.successList=a.grep(this.successList,function(a){return!(a.name in b)})}this.settings.showErrors?this.settings.showErrors.call(this,this.errorMap,this.errorList):this.defaultShowErrors()},resetForm:function(){a.fn.resetForm&&a(this.currentForm).resetForm(),this.submitted={},this.lastElement=null,this.prepareForm(),this.hideErrors();var b,c=this.elements().removeData("previousValue").removeAttr("aria-invalid");if(this.settings.unhighlight)for(b=0;c[b];b++)this.settings.unhighlight.call(this,c[b],this.settings.errorClass,"");else c.removeClass(this.settings.errorClass)},numberOfInvalids:function(){return this.objectLength(this.invalid)},objectLength:function(a){var b,c=0;for(b in a)c++;return c},hideErrors:function(){this.hideThese(this.toHide)},hideThese:function(a){a.not(this.containers).text(""),this.addWrapper(a).hide()},valid:function(){return 0===this.size()},size:function(){return this.errorList.length},focusInvalid:function(){if(this.settings.focusInvalid)try{a(this.findLastActive()||this.errorList.length&&this.errorList[0].element||[]).filter(":visible").focus().trigger("focusin")}catch(b){}},findLastActive:function(){var b=this.lastActive;return b&&1===a.grep(this.errorList,function(a){return a.element.name===b.name}).length&&b},elements:function(){var b=this,c={};return a(this.currentForm).find("input, select, textarea").not(":submit, :reset, :image, :disabled").not(this.settings.ignore).filter(function(){return!this.name&&b.settings.debug&&window.console&&console.error("%o has no name assigned",this),this.name in c||!b.objectLength(a(this).rules())?!1:(c[this.name]=!0,!0)})},clean:function(b){return a(b)[0]},errors:function(){var b=this.settings.errorClass.split(" ").join(".");return a(this.settings.errorElement+"."+b,this.errorContext)},reset:function(){this.successList=[],this.errorList=[],this.errorMap={},this.toShow=a([]),this.toHide=a([]),this.currentElements=a([])},prepareForm:function(){this.reset(),this.toHide=this.errors().add(this.containers)},prepareElement:function(a){this.reset(),this.toHide=this.errorsFor(a)},elementValue:function(b){var c,d=a(b),e=b.type;return"radio"===e||"checkbox"===e?this.findByName(b.name).filter(":checked").val():"number"===e&&"undefined"!=typeof b.validity?b.validity.badInput?!1:d.val():(c=d.val(),"string"==typeof c?c.replace(/\r/g,""):c)},check:function(b){b=this.validationTargetFor(this.clean(b));var c,d,e,f=a(b).rules(),g=a.map(f,function(a,b){return b}).length,h=!1,i=this.elementValue(b);for(d in f){e={method:d,parameters:f[d]};try{if(c=a.validator.methods[d].call(this,i,b,e.parameters),"dependency-mismatch"===c&&1===g){h=!0;continue}if(h=!1,"pending"===c)return void(this.toHide=this.toHide.not(this.errorsFor(b)));if(!c)return this.formatAndAdd(b,e),!1}catch(j){throw this.settings.debug&&window.console&&console.log("Exception occurred when checking element "+b.id+", check the '"+e.method+"' method.",j),j instanceof TypeError&&(j.message+=".  Exception occurred when checking element "+b.id+", check the '"+e.method+"' method."),j}}if(!h)return this.objectLength(f)&&this.successList.push(b),!0},customDataMessage:function(b,c){return a(b).data("msg"+c.charAt(0).toUpperCase()+c.substring(1).toLowerCase())||a(b).data("msg")},customMessage:function(a,b){var c=this.settings.messages[a];return c&&(c.constructor===String?c:c[b])},findDefined:function(){for(var a=0;a<arguments.length;a++)if(void 0!==arguments[a])return arguments[a];return void 0},defaultMessage:function(b,c){return this.findDefined(this.customMessage(b.name,c),this.customDataMessage(b,c),!this.settings.ignoreTitle&&b.title||void 0,a.validator.messages[c],"<strong>Warning: No message defined for "+b.name+"</strong>")},formatAndAdd:function(b,c){var d=this.defaultMessage(b,c.method),e=/\$?\{(\d+)\}/g;"function"==typeof d?d=d.call(this,c.parameters,b):e.test(d)&&(d=a.validator.format(d.replace(e,"{$1}"),c.parameters)),this.errorList.push({message:d,element:b,method:c.method}),this.errorMap[b.name]=d,this.submitted[b.name]=d},addWrapper:function(a){return this.settings.wrapper&&(a=a.add(a.parent(this.settings.wrapper))),a},defaultShowErrors:function(){var a,b,c;for(a=0;this.errorList[a];a++)c=this.errorList[a],this.settings.highlight&&this.settings.highlight.call(this,c.element,this.settings.errorClass,this.settings.validClass),this.showLabel(c.element,c.message);if(this.errorList.length&&(this.toShow=this.toShow.add(this.containers)),this.settings.success)for(a=0;this.successList[a];a++)this.showLabel(this.successList[a]);if(this.settings.unhighlight)for(a=0,b=this.validElements();b[a];a++)this.settings.unhighlight.call(this,b[a],this.settings.errorClass,this.settings.validClass);this.toHide=this.toHide.not(this.toShow),this.hideErrors(),this.addWrapper(this.toShow).show()},validElements:function(){return this.currentElements.not(this.invalidElements())},invalidElements:function(){return a(this.errorList).map(function(){return this.element})},showLabel:function(b,c){var d,e,f,g=this.errorsFor(b),h=this.idOrName(b),i=a(b).attr("aria-describedby");g.length?(g.removeClass(this.settings.validClass).addClass(this.settings.errorClass),g.html(c)):(g=a("<"+this.settings.errorElement+">").attr("id",h+"-error").addClass(this.settings.errorClass).html(c||""),d=g,this.settings.wrapper&&(d=g.hide().show().wrap("<"+this.settings.wrapper+"/>").parent()),this.labelContainer.length?this.labelContainer.append(d):this.settings.errorPlacement?this.settings.errorPlacement(d,a(b)):d.insertAfter(b),g.is("label")?g.attr("for",h):0===g.parents("label[for='"+h+"']").length&&(f=g.attr("id").replace(/(:|\.|\[|\]|\$)/g,"\\$1"),i?i.match(new RegExp("\\b"+f+"\\b"))||(i+=" "+f):i=f,a(b).attr("aria-describedby",i),e=this.groups[b.name],e&&a.each(this.groups,function(b,c){c===e&&a("[name='"+b+"']",this.currentForm).attr("aria-describedby",g.attr("id"))}))),!c&&this.settings.success&&(g.text(""),"string"==typeof this.settings.success?g.addClass(this.settings.success):this.settings.success(g,b)),this.toShow=this.toShow.add(g)},errorsFor:function(b){var c=this.idOrName(b),d=a(b).attr("aria-describedby"),e="label[for='"+c+"'], label[for='"+c+"'] *";return d&&(e=e+", #"+d.replace(/\s+/g,", #")),this.errors().filter(e)},idOrName:function(a){return this.groups[a.name]||(this.checkable(a)?a.name:a.id||a.name)},validationTargetFor:function(b){return this.checkable(b)&&(b=this.findByName(b.name)),a(b).not(this.settings.ignore)[0]},checkable:function(a){return/radio|checkbox/i.test(a.type)},findByName:function(b){return a(this.currentForm).find("[name='"+b+"']")},getLength:function(b,c){switch(c.nodeName.toLowerCase()){case"select":return a("option:selected",c).length;case"input":if(this.checkable(c))return this.findByName(c.name).filter(":checked").length}return b.length},depend:function(a,b){return this.dependTypes[typeof a]?this.dependTypes[typeof a](a,b):!0},dependTypes:{"boolean":function(a){return a},string:function(b,c){return!!a(b,c.form).length},"function":function(a,b){return a(b)}},optional:function(b){var c=this.elementValue(b);return!a.validator.methods.required.call(this,c,b)&&"dependency-mismatch"},startRequest:function(a){this.pending[a.name]||(this.pendingRequest++,this.pending[a.name]=!0)},stopRequest:function(b,c){this.pendingRequest--,this.pendingRequest<0&&(this.pendingRequest=0),delete this.pending[b.name],c&&0===this.pendingRequest&&this.formSubmitted&&this.form()?(a(this.currentForm).submit(),this.formSubmitted=!1):!c&&0===this.pendingRequest&&this.formSubmitted&&(a(this.currentForm).triggerHandler("invalid-form",[this]),this.formSubmitted=!1)},previousValue:function(b){return a.data(b,"previousValue")||a.data(b,"previousValue",{old:null,valid:!0,message:this.defaultMessage(b,"remote")})},destroy:function(){this.resetForm(),a(this.currentForm).off(".validate").removeData("validator")}},classRuleSettings:{required:{required:!0},email:{email:!0},url:{url:!0},date:{date:!0},dateISO:{dateISO:!0},number:{number:!0},digits:{digits:!0},creditcard:{creditcard:!0}},addClassRules:function(b,c){b.constructor===String?this.classRuleSettings[b]=c:a.extend(this.classRuleSettings,b)},classRules:function(b){var c={},d=a(b).attr("class");return d&&a.each(d.split(" "),function(){this in a.validator.classRuleSettings&&a.extend(c,a.validator.classRuleSettings[this])}),c},normalizeAttributeRule:function(a,b,c,d){/min|max/.test(c)&&(null===b||/number|range|text/.test(b))&&(d=Number(d),isNaN(d)&&(d=void 0)),d||0===d?a[c]=d:b===c&&"range"!==b&&(a[c]=!0)},attributeRules:function(b){var c,d,e={},f=a(b),g=b.getAttribute("type");for(c in a.validator.methods)"required"===c?(d=b.getAttribute(c),""===d&&(d=!0),d=!!d):d=f.attr(c),this.normalizeAttributeRule(e,g,c,d);return e.maxlength&&/-1|2147483647|524288/.test(e.maxlength)&&delete e.maxlength,e},dataRules:function(b){var c,d,e={},f=a(b),g=b.getAttribute("type");for(c in a.validator.methods)d=f.data("rule"+c.charAt(0).toUpperCase()+c.substring(1).toLowerCase()),this.normalizeAttributeRule(e,g,c,d);return e},staticRules:function(b){var c={},d=a.data(b.form,"validator");return d.settings.rules&&(c=a.validator.normalizeRule(d.settings.rules[b.name])||{}),c},normalizeRules:function(b,c){return a.each(b,function(d,e){if(e===!1)return void delete b[d];if(e.param||e.depends){var f=!0;switch(typeof e.depends){case"string":f=!!a(e.depends,c.form).length;break;case"function":f=e.depends.call(c,c)}f?b[d]=void 0!==e.param?e.param:!0:delete b[d]}}),a.each(b,function(d,e){b[d]=a.isFunction(e)?e(c):e}),a.each(["minlength","maxlength"],function(){b[this]&&(b[this]=Number(b[this]))}),a.each(["rangelength","range"],function(){var c;b[this]&&(a.isArray(b[this])?b[this]=[Number(b[this][0]),Number(b[this][1])]:"string"==typeof b[this]&&(c=b[this].replace(/[\[\]]/g,"").split(/[\s,]+/),b[this]=[Number(c[0]),Number(c[1])]))}),a.validator.autoCreateRanges&&(null!=b.min&&null!=b.max&&(b.range=[b.min,b.max],delete b.min,delete b.max),null!=b.minlength&&null!=b.maxlength&&(b.rangelength=[b.minlength,b.maxlength],delete b.minlength,delete b.maxlength)),b},normalizeRule:function(b){if("string"==typeof b){var c={};a.each(b.split(/\s/),function(){c[this]=!0}),b=c}return b},addMethod:function(b,c,d){a.validator.methods[b]=c,a.validator.messages[b]=void 0!==d?d:a.validator.messages[b],c.length<3&&a.validator.addClassRules(b,a.validator.normalizeRule(b))},methods:{required:function(b,c,d){if(!this.depend(d,c))return"dependency-mismatch";if("select"===c.nodeName.toLowerCase()){var e=a(c).val();return e&&e.length>0}return this.checkable(c)?this.getLength(b,c)>0:b.length>0},email:function(a,b){return this.optional(b)||/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(a)},url:function(a,b){return this.optional(b)||/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(a)},date:function(a,b){return this.optional(b)||!/Invalid|NaN/.test(new Date(a).toString())},dateISO:function(a,b){return this.optional(b)||/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(a)},number:function(a,b){return this.optional(b)||/^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(a)},digits:function(a,b){return this.optional(b)||/^\d+$/.test(a)},creditcard:function(a,b){if(this.optional(b))return"dependency-mismatch";if(/[^0-9 \-]+/.test(a))return!1;var c,d,e=0,f=0,g=!1;if(a=a.replace(/\D/g,""),a.length<13||a.length>19)return!1;for(c=a.length-1;c>=0;c--)d=a.charAt(c),f=parseInt(d,10),g&&(f*=2)>9&&(f-=9),e+=f,g=!g;return e%10===0},minlength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(b,c);return this.optional(c)||e>=d},maxlength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(b,c);return this.optional(c)||d>=e},rangelength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(b,c);return this.optional(c)||e>=d[0]&&e<=d[1]},min:function(a,b,c){return this.optional(b)||a>=c},max:function(a,b,c){return this.optional(b)||c>=a},range:function(a,b,c){return this.optional(b)||a>=c[0]&&a<=c[1]},equalTo:function(b,c,d){var e=a(d);return this.settings.onfocusout&&e.off(".validate-equalTo").on("blur.validate-equalTo",function(){a(c).valid()}),b===e.val()},remote:function(b,c,d){if(this.optional(c))return"dependency-mismatch";var e,f,g=this.previousValue(c);return this.settings.messages[c.name]||(this.settings.messages[c.name]={}),g.originalMessage=this.settings.messages[c.name].remote,this.settings.messages[c.name].remote=g.message,d="string"==typeof d&&{url:d}||d,g.old===b?g.valid:(g.old=b,e=this,this.startRequest(c),f={},f[c.name]=b,a.ajax(a.extend(!0,{mode:"abort",port:"validate"+c.name,dataType:"json",data:f,context:e.currentForm,success:function(d){var f,h,i,j=d===!0||"true"===d;e.settings.messages[c.name].remote=g.originalMessage,j?(i=e.formSubmitted,e.prepareElement(c),e.formSubmitted=i,e.successList.push(c),delete e.invalid[c.name],e.showErrors()):(f={},h=d||e.defaultMessage(c,"remote"),f[c.name]=g.message=a.isFunction(h)?h(b):h,e.invalid[c.name]=!0,e.showErrors(f)),g.valid=j,e.stopRequest(c,j)}},d)),"pending")}}});var b,c={};a.ajaxPrefilter?a.ajaxPrefilter(function(a,b,d){var e=a.port;"abort"===a.mode&&(c[e]&&c[e].abort(),c[e]=d)}):(b=a.ajax,a.ajax=function(d){var e=("mode"in d?d:a.ajaxSettings).mode,f=("port"in d?d:a.ajaxSettings).port;return"abort"===e?(c[f]&&c[f].abort(),c[f]=b.apply(this,arguments),c[f]):b.apply(this,arguments)})});
/*
 * metismenu - v2.2.0
 * A jQuery menu plugin
 * https://github.com/onokumus/metisMenu#readme
 *
 * Made by Osman Nuri Okumuş <onokumus@gmail.com> (https://github.com/onokumus)
 * Under MIT License
 */

!function(a){"use strict";function b(){var a=document.createElement("mm"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}function c(b){return this.each(function(){var c=a(this),d=c.data("mm"),f=a.extend({},e.DEFAULTS,c.data(),"object"==typeof b&&b);d||c.data("mm",d=new e(this,f)),"string"==typeof b&&d[b]()})}a.fn.emulateTransitionEnd=function(b){var c=!1,e=this;a(this).one("mmTransitionEnd",function(){c=!0});var f=function(){c||a(e).trigger(d.end)};return setTimeout(f,b),this};var d=b();d&&(a.event.special.mmTransitionEnd={bindType:d.end,delegateType:d.end,handle:function(b){return a(b.target).is(this)?b.handleObj.handler.apply(this,arguments):void 0}});var e=function(b,c){this.$element=a(b),this.options=a.extend({},e.DEFAULTS,c),this.transitioning=null,this.init()};e.TRANSITION_DURATION=350,e.DEFAULTS={toggle:!0,doubleTapToGo:!1,preventDefault:!0,activeClass:"active",collapseClass:"collapse",collapseInClass:"in",collapsingClass:"collapsing",onTransitionStart:!1,onTransitionEnd:!1},e.prototype.init=function(){var b=this,c=this.options.activeClass,d=this.options.collapseClass,e=this.options.collapseInClass;this.$element.find("li."+c).has("ul").children("ul").attr("aria-expanded",!0).addClass(d+" "+e),this.$element.find("li").not("."+c).has("ul").children("ul").attr("aria-expanded",!1).addClass(d),this.options.doubleTapToGo&&this.$element.find("li."+c).has("ul").children("a").addClass("doubleTapToGo"),this.$element.find("li").has("ul").children("a").on("click.metisMenu",function(d){var e=a(this),f=e.parent("li"),g=f.children("ul");return b.options.preventDefault&&d.preventDefault(),f.hasClass(c)&&!b.options.doubleTapToGo?(b.hide(g),e.attr("aria-expanded",!1)):(b.show(g),e.attr("aria-expanded",!0)),b.options.onTransitionStart&&b.options.onTransitionStart(),b.options.doubleTapToGo&&b.doubleTapToGo(e)&&"#"!==e.attr("href")&&""!==e.attr("href")?(d.stopPropagation(),void(document.location=e.attr("href"))):void 0})},e.prototype.doubleTapToGo=function(a){var b=this.$element;return a.hasClass("doubleTapToGo")?(a.removeClass("doubleTapToGo"),!0):a.parent().children("ul").length?(b.find(".doubleTapToGo").removeClass("doubleTapToGo"),a.addClass("doubleTapToGo"),!1):void 0},e.prototype.show=function(b){var c=this.options.activeClass,f=this.options.collapseClass,g=this.options.collapseInClass,h=this.options.collapsingClass,i=a(b),j=i.parent("li");if(!this.transitioning&&!i.hasClass(g)){j.addClass(c),this.options.toggle&&this.hide(j.siblings().children("ul."+g).attr("aria-expanded",!1)),i.removeClass(f).addClass(h).height(0),this.transitioning=1;var k=function(){this.transitioning&&this.options.onTransitionEnd&&this.options.onTransitionEnd(),i.removeClass(h).addClass(f+" "+g).height("").attr("aria-expanded",!0),this.transitioning=0};return d?void i.one("mmTransitionEnd",a.proxy(k,this)).emulateTransitionEnd(e.TRANSITION_DURATION).height(i[0].scrollHeight):k.call(this)}},e.prototype.hide=function(b){var c=this.options.activeClass,f=this.options.collapseClass,g=this.options.collapseInClass,h=this.options.collapsingClass,i=a(b);if(!this.transitioning&&i.hasClass(g)){i.parent("li").removeClass(c),i.height(i.height())[0].offsetHeight,i.addClass(h).removeClass(f).removeClass(g),this.transitioning=1;var j=function(){this.transitioning&&this.options.onTransitionEnd&&this.options.onTransitionEnd(),this.transitioning=0,i.removeClass(h).addClass(f).attr("aria-expanded",!1)};return d?void i.height(0).one("mmTransitionEnd",a.proxy(j,this)).emulateTransitionEnd(e.TRANSITION_DURATION):j.call(this)}};var f=a.fn.metisMenu;a.fn.metisMenu=c,a.fn.metisMenu.Constructor=e,a.fn.metisMenu.noConflict=function(){return a.fn.metisMenu=f,this}}(jQuery);