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
			useTinyAbsolutePath: false
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
	
	/* DEFAULT PAGE
	----------------------------------------------------------------------------- */
	tpjc.defaults = function() {
	
		var self = this; 
		self.tLog('- defaults()');
		
		$('.alert').alert(); 		
		
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
		
			return this.href === url || url.href.indexOf(this.href) === 0;
		
		}).addClass('active').parent().parent().addClass('in').parent();
		
		if (element.is('li')) {
			element.addClass('active');
		}
		
		//$('[data-toggle="tooltip"]').tooltip({container: 'body'});
	
	};	
	
	
	/* MODE INIT
	----------------------------------------------------------------------------- */
	main.index = function() {
		
		//this.attach_result_table_checkboxes();
		
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
					//this.attach_permalink_builder();
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
var tpjc = tpjc || {};


tpjc.attach_edit_form_plugins = function() {
		
	this.tLog('- attach_edit_form_plugins()');
	
	//$(".tpjc_multiSelect").chosen(); /* MULTI-SELECTS */
		
	//$(".tpjc_searchSelect").chosen();	
		
	//$('.tpjc_datePicker').datepicker();
	
	/*$('.tpjc_timePicker').timepicker({
	
		defaultTime: '7:00 PM'
		
	});	*/		
	
	//$('.tpjc_price').mask("(999) 999-9999");
	
	//$(".tpjc_phoneMask").mask("(999) 999-9999");
	
	//this.tLog('- formatCurrency()');
	
	//$('.tpjc_priceMask').formatCurrency('.tpjc_priceMaskDestination');
	
	//Tags initialized inside the plugin on data-role=tagsinput
	
	/* IMAGE HOVER CROP BUTTONS*/
	$('.hoverControlsContainer').hover(
		function() { $(this).find('.hoverControls').fadeIn(100);  },
		function() { $(this).find('.hoverControls').fadeOut(300); }
	);
	
};

	



/* RESULT TABLE CHECKBOXES
----------------------------------------------------------------------------- */
tpjc.attach_result_table_checkboxes = function() {
	
	$(".checkAllButton").click(function() {
		$(".resultFormCheckBox").prop( "checked", true );
		return false;
	});
	
	$(".unCheckAllButton").click(function() {
		$(".resultFormCheckBox").prop( "checked", false );
		return false;
	});
	
};


/* CHECKBOX GROUP ACTIONS */
tpjc.checkbox_group_action = function() {
	
	/* current actions: active,inactive,delete*/
	var msg = 'Are you sure you wish to mark all items ';
	
	if(action == 'active'){
		msg += 'active';
		
	} else if(action == 'inactive'){
		msg += 'inactive';
		
	} else if(action == 'delete'){
		msg = 'Are you sure you wish to delete all checked items ';
		
	} else {
		this.tLog('Invalid group action = ' + action);
		return false;
	}
			
	
	if(confirm(msg)){
		$("<input />").attr("type","hidden").attr("name","action").val(action).appendTo("#resultForm");
		$("#resultForm").submit();
		return true;
	} else {
		return false;
	}
	
};


tpjc.attach_toggle_featured = function() {
	
	this.tLog('- attach_toggle_featured()');
	
	var self = this;
	
	$(".featuredButton").click(function () {	
																			 
		//$.blockUI({ message: $('#loading') });
		var model = self.options.model;
		var button = $(this);
		var id = button.data('id');
		var action = 'toggle_featured';
		
		$.ajax({
			type: "POST",
			url: "ajax/common.php",
			data: '&model='+model+'&id='+id+'&action='+action,
			dataType: 'json',
			cache: false,
			success: function(json) {
				
				/*if(isUnique == true){
					$(".featuredButton").find('i').removeClass('glyphicon-star').addClass('glyphicon-star-empty');
				}*/
				
				if(button.find('i').hasClass('glyphicon-star')){
					button.find('i').removeClass('glyphicon-star').addClass('glyphicon-star-empty');
				} else {
					button.find('i').removeClass('glyphicon-star-empty').addClass('glyphicon-star');
					
				}
			},  
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert(errorThrown);
			}
		});
		
		return false;	
	});

};


tpjc.form_change_monitor = function() {
	
	this.tLog('- form_change_monitor()');

	var self = this; 
	self.hasChanged = false; 
	
	var target = '#editForm input.form-control, #editForm textarea.form-control';
	
	if( $('a[data-tpjc-action="formChangeMonitorButton"]').length ){
		
		tinyMCE.triggerSave();
	
		$(target).each(function() { 
																									
				$(this).data('initialValue', $(this).val()); 
				
		});
		
		$('a[data-tpjc-action="formChangeMonitorButton"]').click(function(e) { 
																																			
			tinyMCE.triggerSave();																											

			$(target).each(function() { 
																																									
				if( $(this).data('initialValue') != $(this).val()){
					
					self.tLog($(this).attr('name')+' initialValue = ' + $(this).data('initialValue') + ' new value = ' + $(this).val());
					
					self.hasChanged = true; 
				} 
				
			});
			
			if(self.hasChanged){
			
				return confirm('You have made changes to this item.  Are you sure you want to navigate away from this page?');
			} 
			
			return true;
			
		});

		
	} else {
		this.tLog('Cannot attach form change monitor without buttons with data-tpjc-action="formChangeMonitorButton"');
	}
	
};


/* TINYMCE
----------------------------------------------------------------------------- */
tpjc.attach_editor = function() {
	
	self = this;
	self.tLog('- attach_editor()');
	
	/* THIS REMOVES THE HOST NAME FROM URLS BEING INSERTED */
	var relative_urls = false;
	var convert_urls = true;
	var remove_script_host = true;
	var document_base_url = self.settings.HTTP_PATH;
	
	/* LOCAL - HARD CODED URLS */
	if(window.location.hostname ==  'local.cavanimages.com' || self.options.useTinyAbsolutePath) { 
		relative_urls = false;
		convert_urls = false;
		remove_script_host = false;
		//document_base_url = "";

	}
	
	tinymce.init({
	 
		relative_urls : relative_urls,
		convert_urls: convert_urls,
		remove_script_host : remove_script_host,
		document_base_url : document_base_url,
		
		//skin_url: '3e/js/tinymce/skins/tpSkin/',
	
		content_css : self.settings.HTTP_PATH + "css/style.css",
	
		selector:'.tinyMCE',
		
		object_resizing : "img",
		
		style_formats: [
										
			{	title: 'Heading 2', block: 'h2' },
			{	title: 'Heading 3', block: 'h3'	},
			{	title: 'Heading 4', block: 'h4'	},
			{	title: 'Heading 5', block: 'h5'	},
			{	title: 'Paragraph', block: 'p'	},
			
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


/* TINYMCE  */
tpjc.attach_simple_editor = function() {
	
	self = this;
	self.tLog('- attach_simpleEditor()');
	
	/* THIS REMOVES THE HOST NAME FROM URLS BEING INSERTED */
	var relative_urls = false;
	var convert_urls = true;
	var remove_script_host = true;
	var document_base_url = self.settings.HTTP_PATH;
	
	/* LOCAL - HARD CODED URLS */
	if(window.location.hostname ==  '10.0.1.2' || self.options.useTinyAbsolutePath) { 
		relative_urls = false;
		convert_urls = false;
		remove_script_host = false;
		//document_base_url = "";

	}
	
	tinymce.init({
	 
		relative_urls : relative_urls,
		convert_urls: convert_urls,
		remove_script_host : remove_script_host,
		document_base_url : document_base_url,
		
		skin_url: '3e/js/tinymce/skins/tpSkin/',
	
		content_css : "3e/js/tinymce/style.css",
	
		selector:'.simpleTinyMCE',
	
		plugins: ["paste, code"],
		
		paste_as_text: true,
		
		menubar : false,		
		toolbar: "bold italic | code",

	});		
				
};	


/* SET VALIDATOR DEFAULTS  */
tpjc.set_validator_defaults = function() {
	
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
tpjc.attach_validator = function() {
	
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



/* AJAX INTERACTIONS VIA AJAX/COMMON */
tpjc.attach_remove_image = function() {
	
	var self = this;
	self.tLog('- attach_remove_image()');
	
	$('a[data-tpjc-action="removeImage"]').click(function () {	
																													
		self.tLog('removing image');
																			 
		var model = self.options.model;
		var button = $(this);
		var id = button.data('tpjc-id');
		var action = 'removeImage';
		
		$.ajax({
			type: "POST",
			url: "ajax/common.php",
			data: '&model='+model+'&id='+id+'&action='+action,
			dataType: 'json',
			cache: false,
			success: function(json) {
				
				$('input[name="imageID"]').val(0);
				
				$('#editImage_main').fadeOut(600, function() {
																									 
					$('#editImage_thumb').fadeOut(400, function() { 
						$(this).remove(); 
					});
					
					$(this).remove(); 
				});
				
				return false;	
				
				
			},  
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert(errorThrown);
			}
		});
		
		return false;
		
		
	});
	
};


/* AJAX INTERACTIONS VIA AJAX/COMMON */
tpjc.attach_remove_gallery_image = function() {
	
	this.tLog('- attach_remove_gallery_image()');
	
	var self = this;
	
	$('a[data-tpjc-action="remove_gallery_image"]').click(function () {	
																																	
		if(confirm("Are you sure you want to delete this image?")){
							
			var button = $(this);
			var model = button.data('tpjc-model');
			var id = button.data('tpjc-id');
			var action = 'delete';
			
			$.ajax({
				type: "POST",
				url: "ajax/common.php",
				data: '&model='+model+'&id='+id+'&action='+action,
				dataType: 'json',
				cache: false,
				success: function(json) {
				
					$('#galleryImage_'+id).fadeOut(600, function() {
						$(this).remove(); 	
					});
						
				},  
				error: function(XMLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			});
			
			return false;	
			
		}
		return false;
	});
	
	
};


tpjc.modal_ajax_submit_handler = function(modalID, callback) {
	
	this.tLog('- modal_ajax_submit_handler()');
	
	var self = this;
	var $form = $( '#' + modalID + 'Form'); 
	var action = $form.attr('action');
	
	self.show_modal_loading(modalID);
			
	var data = form.serialize()+'&xhr=1';
		
	$.ajax({
		type: "POST",
		url: action,
		data: data,
		dataType: ($.browser.msie) ? "text" : "json",
		accepts: { text: "application/json" },
		cache: false,
		success: function(json) {
			
			if(json.success){
			
				/* HIDE AND CLEANUP */
				$('#' + modalID ).modal('hide');
				
				self.hide_modal_loading(modalID);
				
				self.tLog('- - json success');
				
				callback(json);
				
			} else {
				
				self.show_modal_message(modalID, 'error', json.message);
				
				self.hide_modal_loading(modalID);
				
				self.tLog('- - json did not return success');
				
			}
			
		},
		error: function(xhr) {
			alert('An error occured while sending your message');
			
			self.hide_modal_loading(modalID);
		}
		
	}); //end ajax call
	
};



tpjc.attach_remove_gallery_image = function() {
	
	this.tLog('- close_all_popovers()');
	
	$('.popOverButton').popover('hide');
	
};


tpjc.show_message = function(level, message) {
	
	var html = this.format_alert_message(level, message);
	
	$('#tpjc_alert').html(html).alert();
	
};


tpjc.show_modal_loading = function(modalID) {
	
	var modal = $( '#' + modalID );
	
	modal.find('input[type=submit]').attr('disabled','disabled');
	
	modal.find('.modal-body').prepend('<div class="loadingOverlay"></div>').show();
	
};


tpjc.hide_modal_loading = function(modalID) {
	
	var modal = $( '#' + modalID );
	
	modal.find('input[type=submit]').attr('disabled','false');
	
	modal.find('.loadingOverlay').remove();
	
};


tpjc.show_modal_message = function(modalID, level, message) {
	
	var html = this.format_alert_message(level, message);
	
	$( '#' + modalID ).find('.tpjc_modalAlert').html(html).alert();
	
};


tpjc.reset_form = function(modalID) {
	
	var modal = $( '#' + modalID );
	
	modal.find('input:text, input:password, input:file, select, textarea').val('');
	
	modal.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
	
	modal.find('.tpjc_modalAlert').html('');
};


tpjc.format_alert_message = function(level, message) {
	
	if(level == 'error'){
		level = 'danger';
	}

	var html = '<div class="alert alert-' +level+ ' alert-dismissable">';
	html 		+= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
	html 		+= message;
	html 		+= '</div>';
	return html;
	
};


/* CROP BUTTON  */
tpjc.attach_crop_button = function() {
	
	this.tLog('- attach_crop_button()');
	
	var self = this;

	/* Button Format */
	/* data-model="ImageLibrary" data-id="55" data-type="main" */
	
	$('.tpjc_cropButton').click(function() {
																	
		var dataObj, model, id, type;
		var width = $(window).innerWidth();
		var height = $(window).innerWidth();
		
		var windowOptions = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,';
		windowOptions += 'width='+width+',height='+height+'';
		
		dataObj = $(this).data();
		
		if(typeof(dataObj.model) != 'undefined' && typeof(dataObj.id) != 'undefined' && typeof(dataObj.type) != 'undefined'){
			
			url = 'js/jCrop/crop.php?model='+ dataObj.model +'&id='+ dataObj.id +'&type='+ dataObj.type;
			
			var cropWindow = window.open( url, 'Crop', windowOptions);
			
		} else {
			alert('An error occured while building crop window');
		}
		
		return false;
	});

};


tpjc.attach_edit_form_submit_button_clicks = function() {
	
	$('#quickSave').click(function() {
		$("<input />").attr("type","hidden").attr("name","quickSave").val("1").appendTo("#editForm");
		return true;
	});
	
	$('#goBack').click(function() {
		$("<input />").attr("type","hidden").attr("name","goBack").val("1").appendTo("#editForm");
		return true;
	});
	
	$('#saveAndAdd').click(function() {
		$("<input />").attr("type","hidden").attr("name","saveAndAdd").val("1").appendTo("#editForm");
		return true;
	});
	
};	


/* EDIT RANK MODE FUNCTIONS
----------------------------------------------------------------------------- */
tpjc.attach_sortable = function() {

	this.tLog('- attach_sortable()');
	
	var self = this;
	
	if($("#sortable").length){
	
		$("#sortable").sortable({
															
			placeholder: "dropZone",
			
			update: function(event, ui) {
				
				$.ajax({
					type: "POST",
					url: "ajax/editRank.php",
					data: $("#sortable").sortable('serialize')+'&model=' + self.options.model,
					success: function(message){
			
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					}
				});
			}
		});
		
	} else {
		self.tLog('WARNING - Nothing to sort');
	}
	
};
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /public/admin_js/dashboard.js
----------------------------------------------------------------------------- */

var tpjc = tpjc || {}; 


/* DASHBOARD
----------------------------------------------------------------------------- */
tpjc.dashboard = function() {
		
	self = this;
	self.tLog('Attaching queueBuildVersions');
	
	$("#tpjc_queueBuildVersions").one("click", function () {	
																								
		var btn = $(this);
		
		$.ajax({
			type: "POST",
			url: "index.php",
			data: '&mode=queueBuildVersions',
			dataType: 'json',
			cache: false,
			success: function(json) {
				
				if(json.success){
					btn.removeClass('btn-success').addClass('btn-default').html('<img src="images/loading.gif" style="width:21px;"/>Processing').blur();
				} else {
					btn.removeClass('btn-success').addClass('btn-warning').html('Ouch...');
				}
			},  
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert(errorThrown);
			}
		});
		
		return false;	
	});
		
};
	
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /public/admin_js/faq.js
----------------------------------------------------------------------------- */

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
/* OVER RIDE THE TINYMCE IMAGE PLUGIN
----------------------------------------------------------------------------- */

tinymce.PluginManager.add('image', function(editor) {
																						
	function getImageSize(url, callback) {
		
		var img = document.createElement('img');

		function done(width, height) {
			if (img.parentNode) {
				img.parentNode.removeChild(img);
			}

			callback({width: width, height: height});
		}

		img.onload = function() {
			done(img.clientWidth, img.clientHeight);
		};

		img.onerror = function() {
			done();
		};

		var style = img.style;
		style.visibility = 'hidden';
		style.position = 'fixed';
		style.bottom = style.left = 0;
		style.width = style.height = 'auto';

		document.body.appendChild(img);
		img.src = url;
	}

	function buildListItems(inputList, itemCallback, startItems) {
		function appendItems(values, output) {
			output = output || [];

			tinymce.each(values, function(item) {
				var menuItem = {text: item.text || item.title};

				if (item.menu) {
					menuItem.menu = appendItems(item.menu);
				} else {
					menuItem.value = item.value;
					itemCallback(menuItem);
				}

				output.push(menuItem);
			});

			return output;
		}

		return appendItems(inputList, startItems || []);
	}

	function createImageList(callback) {
		return function() {
			var imageList = editor.settings.image_list;

			if (typeof(imageList) == "string") {
				tinymce.util.XHR.send({
					url: imageList,
					success: function(text) {
						callback(tinymce.util.JSON.parse(text));
					}
				});
			} else if (typeof(imageList) == "function") {
				imageList(callback);
			} else {
				callback(imageList);
			}
		};
	}

	function showDialog(imageList) {
		
		var win, data = {}, dom = editor.dom, imgElm = editor.selection.getNode();
		var width, height, imageListCtrl, classListCtrl, imageDimensions = editor.settings.image_dimensions !== false;

		function recalcSize() {
			
			var widthCtrl, heightCtrl, newWidth, newHeight;

			widthCtrl = win.find('#width')[0];
			heightCtrl = win.find('#height')[0];

			if (!widthCtrl || !heightCtrl) {
				return;
			}

			newWidth = widthCtrl.value();
			newHeight = heightCtrl.value();

			if (win.find('#constrain')[0].checked() && width && height && newWidth && newHeight) {
				if (width != newWidth) {
					newHeight = Math.round((newWidth / width) * newHeight);
					heightCtrl.value(newHeight);
				} else {
					newWidth = Math.round((newHeight / height) * newWidth);
					widthCtrl.value(newWidth);
				}
			}

			width = newWidth;
			height = newHeight;
		}

		function onSubmitForm() {
			
			function waitLoad(imgElm) {
				
				function selectImage() {
					imgElm.onload = imgElm.onerror = null;

					if (editor.selection) {
						editor.selection.select(imgElm);
						editor.nodeChanged();
					}
				}

				/* MC */
				imgElm.onload = function() {
					
					if (!data.width && !data.height && imageDimensions) {

						dom.setAttribs(imgElm, {
							width: imgElm.clientWidth,
							height: imgElm.clientHeight
						});
						
					}

					selectImage();
				};

				imgElm.onerror = selectImage;
			}

			updateStyle();
			recalcSize();

			data = tinymce.extend(data, win.toJSON());

			if (!data.alt) {
				data.alt = '';
			}

			if (data.width === '') {
				data.width = null;
			}

			if (data.height === '') {
				data.height = null;
			}

			if (!data.style) {
				data.style = null;
			}

			// Setup new data excluding style properties
			data = {
				src: data.src,
				alt: data.alt,
				width: data.width,
				height: data.height,
				style: data.style,
				"class": data["class"]
			};
			
			/* MC */
			
			
			if(win.find('#tpjc_sizes')[0].value() == 'responsive'){
				data.class = 'img-responsive';
			}
			
			if(win.find('#tpjc_sizes')[0].value()  == 'newsletterColumn'){
				data.width = 240;
			}
			
			if(win.find('#tpjc_sizes')[0].value()  == 'newsletterWide'){
				data.width = 638;
			}
			
			//newsletterColumn, newsletterWide
						

			editor.undoManager.transact(function() {
				if (!data.src) {
					if (imgElm) {
						dom.remove(imgElm);
						editor.focus();
						editor.nodeChanged();
					}

					return;
				}

				if (!imgElm) {
					data.id = '__mcenew';
					editor.focus();
					editor.selection.setContent(dom.createHTML('img', data));
					imgElm = dom.get('__mcenew');
					dom.setAttrib(imgElm, 'id', null);
				} else {
					dom.setAttribs(imgElm, data);
				}

				waitLoad(imgElm);
			});
		}

		function removePixelSuffix(value) {
			if (value) {
				value = value.replace(/px$/, '');
			}

			return value;
		}

		function srcChange(e) {
			var meta = e.meta || {};

			if (imageListCtrl) {
				imageListCtrl.value(editor.convertURL(this.value(), 'src'));
			}

			tinymce.each(meta, function(value, key) {
				win.find('#' + key).value(value);
			});

			if (!meta.width && !meta.height) {
				getImageSize(this.value(), function(data) {
					if (data.width && data.height && imageDimensions) {
						width = data.width;
						height = data.height;

						win.find('#width').value(width);
						win.find('#height').value(height);
					}
				});
			}
		}

		width = dom.getAttrib(imgElm, 'width');
		height = dom.getAttrib(imgElm, 'height');
		
		

		if (imgElm.nodeName == 'IMG' && !imgElm.getAttribute('data-mce-object') && !imgElm.getAttribute('data-mce-placeholder')) {
			data = {
				src: dom.getAttrib(imgElm, 'src'),
				alt: dom.getAttrib(imgElm, 'alt'),
				"class": dom.getAttrib(imgElm, 'class'),
				width: width,
				height: height
			};
		} else {
			imgElm = null;
		}

		if (imageList) {
			imageListCtrl = {
				type: 'listbox',
				label: 'Image list',
				values: buildListItems(
					imageList,
					function(item) {
						item.value = editor.convertURL(item.value || item.url, 'src');
					},
					[{text: 'None', value: ''}]
				),
				value: data.src && editor.convertURL(data.src, 'src'),
				onselect: function(e) {
					var altCtrl = win.find('#alt');

					if (!altCtrl.value() || (e.lastControl && altCtrl.value() == e.lastControl.text())) {
						altCtrl.value(e.control.text());
					}

					win.find('#src').value(e.control.value()).fire('change');
				},
				onPostRender: function() {
					imageListCtrl = this;
				}
			};
		}

		if (editor.settings.image_class_list) {
			classListCtrl = {
				name: 'class',
				type: 'listbox',
				label: 'Class',
				values: buildListItems(
					editor.settings.image_class_list,
					function(item) {
						if (item.value) {
							item.textStyle = function() {
								return editor.formatter.getCssText({inline: 'img', classes: [item.value]});
							};
						}
					}
				)
			};
		}

		// General settings shared between simple and advanced dialogs
		var generalFormItems = [
			{
				name: 'src',
				type: 'filepicker',
				filetype: 'image',
				label: 'Source',
				autofocus: true,
				onchange: srcChange
			},
			imageListCtrl
		];

		
		generalFormItems.push({
													
			/*type: 'container',*/
			label: 'Sizes', 
			name: 'tpjc_sizes', 
			layout: 'flex', 
			direction: 'row',
			align: 'left',
			spacing: 5,
			type: 'listbox',
			'values': [
				{value: '',  text: ''},
				{value: 'responsive',  text: 'Full Width'},
				{value: 'newsletterColumn', text: 'Newsletter Column'},
				{value: 'newsletterWide', text: 'Wide Newsletter'}
			]
		});
		
		if (editor.settings.image_description !== false) {
			generalFormItems.push({name: 'alt', type: 'textbox', label: 'Image description'});
		}
		

		if (imageDimensions) {
			generalFormItems.push({
				type: 'container',
				label: 'Dimensions',
				layout: 'flex',
				direction: 'row',
				align: 'center',
				spacing: 5,
				items: [
					{name: 'width', type: 'textbox', maxLength: 5, size: 3, onchange: recalcSize, ariaLabel: 'Width'},
					{type: 'label', text: 'x'},
					{name: 'height', type: 'textbox', maxLength: 5, size: 3, onchange: recalcSize, ariaLabel: 'Height'},
					{name: 'constrain', type: 'checkbox', checked: true, text: 'Constrain proportions'}
				]
			});
		}

		generalFormItems.push(classListCtrl);

		function updateStyle() {
			function addPixelSuffix(value) {
				if (value.length > 0 && /^[0-9]+$/.test(value)) {
					value += 'px';
				}

				return value;
			}

			if (!editor.settings.image_advtab) {
				return;
			}

			var data = win.toJSON();
			var css = dom.parseStyle(data.style);

			delete css.margin;
			css['margin-top'] = css['margin-bottom'] = addPixelSuffix(data.vspace);
			css['margin-left'] = css['margin-right'] = addPixelSuffix(data.hspace);
			css['border-width'] = addPixelSuffix(data.border);

			win.find('#style').value(dom.serializeStyle(dom.parseStyle(dom.serializeStyle(css))));
		}

		if (editor.settings.image_advtab) {
			// Parse styles from img
			if (imgElm) {
				data.hspace = removePixelSuffix(imgElm.style.marginLeft || imgElm.style.marginRight);
				data.vspace = removePixelSuffix(imgElm.style.marginTop || imgElm.style.marginBottom);
				data.border = removePixelSuffix(imgElm.style.borderWidth);
				data.style = editor.dom.serializeStyle(editor.dom.parseStyle(editor.dom.getAttrib(imgElm, 'style')));
			}

			// Advanced dialog shows general+advanced tabs
			win = editor.windowManager.open({
				title: 'Insert/Edit image',
				data: data,
				bodyType: 'tabpanel',
				body: [
					{
						title: 'General',
						type: 'form',
						items: generalFormItems
					},

					{
						title: 'Advanced',
						type: 'form',
						pack: 'start',
						items: [
							{
								label: 'Style',
								name: 'style',
								type: 'textbox'
							},
							{
								type: 'form',
								layout: 'grid',
								packV: 'start',
								columns: 2,
								padding: 0,
								alignH: ['left', 'right'],
								defaults: {
									type: 'textbox',
									maxWidth: 50,
									onchange: updateStyle
								},
								items: [
									{label: 'Vertical space', name: 'vspace'},
									{label: 'Horizontal space', name: 'hspace'},
									{label: 'Border', name: 'border'}
								]
							}
						]
					}
				],
				onSubmit: onSubmitForm
			});
		} else {
			// Simple default dialog
			win = editor.windowManager.open({
				title: 'Insert/edit image',
				data: data,
				body: generalFormItems,
				onSubmit: onSubmitForm
			});
		}
	}

	editor.addButton('image', {
		icon: 'image',
		tooltip: 'Insert/Edit image',
		onclick: createImageList(showDialog),
		stateSelector: 'img:not([data-mce-object],[data-mce-placeholder])'
	});

	editor.addMenuItem('image', {
		icon: 'image',
		text: 'Insert Image',
		onclick: createImageList(showDialog),
		context: 'insert',
		prependToContext: true
	});

	editor.addCommand('mceImage', createImageList(showDialog));
});
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /public/admin_js/menu.js
----------------------------------------------------------------------------- */

var tpjc = tpjc || {};


/* PUBLIC IMAGE PAGE
----------------------------------------------------------------------------- */
tpjc.attach_menu_item_modal = function() {
	
	var self = this;
	self.tLog('- - attach_menu_item_modal()');
	
	self.set_validator_defaults();
	
	var formID = 'ajaxForm';
	var modalID = 'menuItemModal';
	var action = $( '#' + formID ).attr('action');
	
	$( '#' + formID ).validate({
						
		rules: {
			title : "required"
		},
		
		submitHandler: function(form) {
			
			self.show_modal_loading(formID);
			
			var data = $( '#' + formID ).serialize();
				
			$.ajax({
				type: "POST",
				url: action,
				data: data,
				dataType: 'json',
				cache: false,
				success: function(json) {
					
					/* HIDE AND CLEANUP */
					$('#' + modalID ).modal('hide');
					
					self.reset_form(modalID);
					
					/*RE CHECK THE DEFAULT TYPE OPTION */
					$('#type_1').prop("checked", true);
					
					$('input[name=type]').change();
					
					var str = '<li class="dd-item';
					
					if(json.pageID === 0 && json.href === ''){
						str += ' dd-dropDown';
						
					} else {
						str += ' dd-noChild';
					}
					
					str += '" data-id="'+ json.menuItemID +'"><div class="dd-handle">'+ json.title;
					
					if(json.pageID === 0 && json.href === ''){
						str += ' <span class="glyphicon glyphicon-chevron-down sm"></span> ';
						
					} 
					
					str += '</div><button class="nestableRemove" data-action="remove" type="button"><i class="glyphicon glyphicon-remove"></i></button></li>';
					
					$('#nestable > ol').append(str);
					
					$('#nestableInput').val( JSON.stringify($('#nestable').nestable('serialize')) );
					
					self.hide_modal_loading(formID);
					
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
					self.hide_modal_loading(formID);
				}
		
				
			}); //end ajax call
			
			return false;
				
		}
	});//end .validate();*/
	
	
	//ATTACH THE COLLAPSIBLES.

	$('input[name=type]').change(function(){
																																					 
		$('.menuItemContainer').hide();
		
		$( '#pageID' ).rules( "remove" );
		
		$( '#href' ).rules( "remove" );
		
		//values = array(1 => 'Page', 2 => 'Link', 3 => 'Drop Down')
		
		var val = $('input[name=type]:checked').val();
		
		if(val == 1){
			
			$('.menuItemPageContainer').show(300);
		
			$( '#pageID' ).rules( "add", {
				required: true
			});
			
			
		} else if(val == 2){
			
			$('.menuItemHrefContainer').show(300);
			
			$( '#href' ).rules( "add", {
				required: true,
				url: true
			});
		} 
		
	}); 
	
	$('input[name=type]').change();
	
};
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /public/admin_js/page.js
----------------------------------------------------------------------------- */

var tpjc = tpjc || {};

/* 	PAGE VERSION BLOCKS
----------------------------------------------------------------------------- */
tpjc.append_page_version_block = function(pageVersionBlockID, pageVersionID) {
	
	this.tLog('- - - append_pageVersion_block()');
	
	var data = 'pageVersionBlockID='+pageVersionBlockID+'&pageVersionID='+pageVersionID;
	
	$.ajax({
		type: "POST",
		url: "ajax/getPageVersionPanel.php",
		data: data,
		dataType: 'html',
		cache: false,
		success: function(html) {
			
			$('#sortable').append(html);
			
		},  
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert(errorThrown);
		}
	});
	
};


/* ADD NEW BLOCK */
tpjc.attach_add_page_version_block_modal = function() {
	
	var self = this;
	self.tLog('- - attach_add_page_version_block_modal()');
	
	self.set_validator_defaults();
	
	var modalID = 'addPageVersionBlockModal';
	
	var formID = modalID + 'Form';
	
	$( '#' + formID ).validate({
					
		submitHandler: function(form) {
			
			self.tLog('- - - add block submit');
			
			self.modal_ajax_submit_handler(modalID, function(json){
																											 
				//SHOULD BE REDIRECT STRAIGHT TO EDIT IF IT'S A REGULAR BLOCK 					
				if(typeof(json.isModule) != 'undefined' && json.isModule === 0){
					
					var redirect = '/admin/pageVersionBlock/edit/'+json.pageVersionBlockID+'?pageVersionID='+json.pageVersionID;
					
					window.location.href = redirect;
					
				} else {
					
					self.append_page_version_block(json.pageVersionBlockID, json.pageVersionID);
					
				}					
			
			});
			
			return false;
				
		}
	});//end .validate();*/
	
	//CLEAN UP FORM 
	$( '#' + modalID ).on('hide.bs.modal', function () {
		
		//RESET FORMS
		self.tLog('resetting form');
		
		modal = $( '#' + modalID );
		
		if(modal.find('input[name="isRepeating"]').prop('checked')){
			
			self.tLog('checkbox checked');
			
			modal.find('input[name="isRepeating"]').prop('checked', false);
			
			$('#tpjc_repeatingCheckBox').collapse('hide');

		}
		
		//modal.find('collapse in').removeClass('in');
		
		modal.find('input:text, select, textarea').val('');
		
		modal.find('input:radio').removeAttr('checked').removeAttr('selected');		
		
		modal.find('.modalMessageContainer').html('');
	
		modal.find('radio[name="templateID"]').first().click();
		
		modal.find('ul.nav li a').first().click();
		
	});
	
	if(typeof(self.options.triggerPageVersionBlockModal) != 'undefined'){
		self.tLog('triggering modal on first launch');
		$( '#' + modalID ).modal('show');
	}
	
};


/* INSERT REPEATING BLOCK */
tpjc.attach_insert_page_version_block_modal = function() {
	
	var self = this;
	self.tLog('- - attach_insert_page_version_block_modal()');
	
	self.set_validator_defaults();
	
	var modalID = 'insertPageVersionBlockModal';
	
	$( '#' + modalID ).children('form').validate({
														 
		rules: {
			pageVersionBlockID: "required"
		},
		
		submitHandler: function(form) {
			
			self.modal_ajax_submit_handler(modalID, function(json){
			
				self.append_page_version_block(json.pageVersionBlockID, json.pageVersionID);
				
				self.show_message('success', json.message);
			
			});
			
			return false;
				
		}
	});//end .validate();*/
	

};


tpjc.attach_remove_page_version_block = function() {
	
	this.tLog('- - attach_remove_page_version_block()');
	
	var self = this;
	
	$('a[data-tpjc-action="remove_page_version_block"]').click(function () {	
		
		if(confirm('Are you sure?')){
		
			var button = $(this);
			var dumb = button.data('tpjc-dumb-id');
			var pageVersionID = button.data('tpjc-page-version-id');
			var pageVersionBlockID = button.data('tpjc-page-version-block-id');
			
			if(pageVersionID != 'undefined' && pageVersionBlockID != 'undefined'){
				$.ajax({
					type: "POST",
					url: "pageVersionBlock.php",
					data: '&mode=deleteBlockLink&pageVersionBlockID='+pageVersionBlockID+'&pageVersionID='+pageVersionID+'&isAjax=1',
					dataType: 'json',
					cache: false,
					success: function(json) {
						
						$('#pageVersionBlockID_'+json.pageVersionBlockID).fadeOut(600, function(){
							$(this).remove();
						
						});
						
					},  
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					}
				});
			} else {
				self.tLog('pageVersionID = undefined or pageVersionBlockID = undefined');
			}
		}
		
		return false;	
	});
	
};


tpjc.attach_page_version_block_sortable = function() {
	
	this.tLog('- - attach_page_version_block_sortable()');
	
	var self = this;
	
	if($("#sortable").length > 1){
	
		$("#sortable").sortable({
															
			placeholder: "dropZone", 
			items: "> .panel",
			handle: ".tpjc_dragHandle",
		
			update: function(event, ui) {
	
				//self.tLog($("#sortable").sortable('serialize'));
				var pageVersionID = $("#editForm").find('input[name="pageVersionID"]').val();
				var data = $("#sortable").sortable('serialize')+'&pageVersionID='+pageVersionID;
				
				$.ajax({
					type: "POST",
					url: "ajax/editPageVersionBlockRank.php",
					data: data,
					success: function(message){
			
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					}
				});
			}
		});
		
	} else {
		
		self.tLog('WARNING - nothing to sort');
	}

};
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /public/admin_js/user.js
----------------------------------------------------------------------------- */

var tpjc = tpjc || {};


/* PUBLIC IMAGE PAGE
----------------------------------------------------------------------------- */
tpjc.attach_admin_user_change_password = function() {
	
	this.tLog('- attach_admin_user_change_password()');
	
	$('#editForm input[name="password1"]').val('');
	$('#editForm input[name="password2"]').val('');
	
	this.options.validateRules = {
		rules: {
			password1: "required",
			password2: {
				equalTo: "#password1"
			}
		}
	};

};


/* CHANGE ADMIN USER PASSWORD */
tpjc.attach_account_user_change_password = function() {
	
	this.tLog('- attach_account_user_change_password() - YES');
	
	$('#editForm input[name="password1"]').val('');
	$('#editForm input[name="password2"]').val('');
	
	this.options.validateRules = {
		rules: {
			password1: "required",
			password2: {
				equalTo: "#password1"
			}
		}
	};

};



/* ADD ACCOUNT USER MODAL */
tpjc.attach_add_account_user_modal = function() {
	
	self = this;
	self.tLog('- attach_add_account_user_modal()');
	
	this.tLog('- attach_faq_tag_modal()');
	
	var self = this;
	var modalID = 'addAccountUserModal';
	
	$( '#' + modalID ).children('form').validate({
														 
		rules: {
			accountUserGroupID: "required"
		},
		
		submitHandler: function(form) {
			
			self.modal_ajax_submit_handler(modalID, function(json){
			
				var html = '';
				html += '<tr>'+
									'<td class="tableControlColumn">'+
										'<a href="accountUser.php?mode=edit&accountUserID='+json.accountUserID+'">'+
											'<i class="glyphicon glyphicon-pencil"></i>'+
										'</a>'+
									'</td>'+
									'<td>'+json.firstName+' '+json.lastName+'</td>'+
									'<td>'+json.email+'</td>'+
									'<td class="tableControlColumn">'+
										'<a href="#" class="tpjc_removeAccountUser" data-account-user-id="'+json.accountUserID+'" data-tag-id="'+json.tagID+'" onClick="return confirm(\'Are you sure you want to remove this user?\');"><i class="glyphicon glyphicon-remove delete"></i></a></td>'+
								'</tr>';
			
				$('#tpjc_accountUserTable').append(html);
				
				$( '#' + modalID ).find('.tpjc_searchSelect').val('').trigger('chosen:updated');
				
				
				//$( '#addAccountUserModal').find('select[name="accountUserID"]').prop('selectedIndex', 0);
			
				
			}); 
			
			return false;
				
		}
	});//end .validate();*/

};


/* REMOVE USER FROM GROUP TABLE */
tpjc.attach_account_user_group_remove = function() {
	
	self = this;
	self.tLog('- attach_account_user_group_remove()');
	
	$('body').on('click','.tpjc_removeAccountUser', function() {
												
		var btn = $(this);
		var accountUserID = btn.data('account-user-id');
		var tagID = btn.data('tag-id');
																													
		$.ajax({
			type: "POST",
			url: "accountUserGroup.php",
			data: "&mode=deleteLink&accountUserID="+accountUserID+"&tagID="+tagID,
			dataType: 'json',
			cache: false,
			success: function(json) {
					
				btn.parents('tr').first().fadeOut(300, function(){
					$(this).remove();															
				}); 
									
				return false;	
				
				
			},  
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert(errorThrown);
			}
		});
		
		return false;
		
		
	});

};

	
/* CHANGE ADMIN USER PASSWORD */
tpjc.attach_admin_user_add_validate = function() {
	
	self = this;
	self.tLog('- attach_admin_user_add_validate');
	
	$('#tpjc_useTemp').change(function() {
																		 
			if(!$(this).is(":checked")) {
				
				self.tLog('- now requiring password');
					
				$( 'input[name="password"]' ).rules( "add", {
					required: true
				});
					
			} else {
				self.tLog('- removing password require');
				$( 'input[name="password"]' ).rules( "remove" );
				
			}
						
	});

};


