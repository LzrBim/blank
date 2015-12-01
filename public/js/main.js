/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /public/js/main.js
----------------------------------------------------------------------------- */

var tpjc = tpjc || {};

var tpjc = (function ($, main) {
											
	'use strict';
		
	var settings, options, win,	page;
		
	main.init = function(page, options) { 
		
		var self = this;
		
		self.page = page; 
		
		//OPTIONS
		var defaults = {
			isAuthorized : 0
		};
		
		self.options = $.extend(defaults, options);
		
		//SETTINGS
		self.settings = {
  		
		};
		
		//WIN		
		self.win = {};
				
		self.tLog('init('+ page +', '+ JSON.stringify(options) +')');
		
		self.init_defaults();		
			
		/* CONTROLLER */
		if (typeof(self.page) != 'undefined' && self.page !== '') {
						
			if(self.page == 'home'){
		
		
			/* LOGIN */
			} else if (self.page == 'contact'){
				
				self.validate_contact_form();
			
			/* LOGIN */
			} else if (self.page == 'login'){
				
				self.bind_login();
				
				self.validate_login();

			} else if (self.page == 'login-forgot-password'){
				
				self.bind_forgot_password();
				
				self.validate_forgot_password();
				
			} else if (self.page == 'login-reset-password'){
				
				self.bind_reset_password();	
				
				self.validate_reset_password();			
		
			
			} else {
				self.tLog('- no whitelisted page ');
			}
					
		} else {
			self.tLog('- page undefined ');
		}
		
	};
	

	/* INIT DEFAULTS
	----------------------------------------------------------------------------- */
	main.init_defaults = function() {
		
		this.tLog('init_defaults()');
		
		//GATHER WINDOW INFO
		this.set_window_info();
		
		this.set_menu_active();
		
	};
	
	main.set_menu_active = function(){
	
		var section = this.page.split("-",1);
		var $el = $('#tpjc_section_'+section);
		
		if($el.length){
			
			$el.addClass('active');
			
		} else {
			this.tLog('Error finding nav section');
			
		}	
		
	};
	
	/* USER AUTH CHECK
	----------------------------------------------------------------------------- */
	main.is_authorized = function(){
	
		if(this.options.isAuthorized === "1"){
			return true;
		}
		return false;
		
	};
	
	
	/* CONTACT PAGE VALIDATOR
	----------------------------------------------------------------------------- */
	
	main.validate_contact_form = function() {
		
		self = this;
		self.set_validator_defaults();
		self.tLog('validate_contact_form()');
		
		var formID = 'contactForm';
	
		$( '#' + formID ).validate({
			rules: {
				message: {
					required: true
				},
				email: {
					required: true,
					email:true
				}
			},
			onkeyup: false
		}); 
		

		/* FORM WITH AJAX SUBMIT */
		
		/*$( '#' + formID ).validate({
			rules: {
				name: "required",
				email: {
					required: true,
					email: true
				}
			},
			submitHandler: function(form) {
				
				$( '#' + formID ).find('input[type=submit]').attr('disabled','disabled');
				$( '#' + formID ).find('loadingOverlay').show();
				
				var data = $( '#' + formID ).serialize();
					
				$.ajax({
					type: "POST",
					url: "ajax/inquire_submit.php",
					data: data,
					dataType: 'json',
					cache: false,
					success: function(html) {
						
						$( '#' + formID ).find('.modalSuccess').show();
						
					},
					error: function(xhr) {
						alert('An error occured while sending your message.  Please call 631-283-0042 for your inquiry.')
						
					}
				}); //end ajax call
			}
		});//end .validate();*/
		
		
	};
	
	
	
	
	
	/* ATTACH HOME
	----------------------------------------------------------------------------- */
	main.attach_home = function() { 
		
		var self = this; 
		
		self.tLog('attach_home()');
		
		$('#tpjc_thirdSlider').height((self.win.height - self.win.navHeight)).css('opacity', 1);
		
		var slider = $('#tpjc_thirdSlider').thirdSlider({
			images : self.options.images													 
		});
		
		$(window).on("debouncedresize", function( event ) {
																						 
			self.set_window_info();
								
			$('#tpjc_thirdSlider').height((self.win.height - self.win.navHeight));
				
		});
		
	
		//Page title opacity on scroll
		$(window).on('scroll', function(){
	
			var fadeStart =  100;
			var fadeUntil =  450;
	
			var offset = $(document).scrollTop();
			
			var opacity = 0;
			if(offset <= fadeStart){
				opacity = 1;
				
			} else if( offset <= fadeUntil ){
				opacity = 1 - ( offset / fadeUntil);
				
			}
	
			$('.vacCell').css('opacity',opacity).css('padding-top', offset* 0.9 );
			
			$('.topArrow').css('opacity',opacity);
			
			if(offset >= (self.win.height - self.win.navHeight)){
				
				$('#homeCuratedCon').find('.toolBar').addClass('homeToolBarFixed');
				
			} else {
				
				$('#homeCuratedCon').find('.toolBar').removeClass('homeToolBarFixed');
				
			}
			
		});

		
		//CURATED
		var $grid = $('.grid');
		
		$grid.imagesLoaded( function() {
			
			$grid.masonry({
				itemSelector: '.grid-item',
				columnWidth: '.grid-sizer',
				percentPosition: true,
				gutter: 5
				
			});
			
			$grid.masonry('layout');
			
			//load the remainder of the big slider images
			slider.lazy();
			
		});
		
	}; /* END attach_home() */
	
	

	
	
	
	/* GET WINDOW INFO
	----------------------------------------------------------------------------- */
	main.set_window_info = function() {
		
		this.tLog('set_window_info()');
		
		this.win.width = $(window).width();
		
		this.win.height = $(window).height();
		
		this.win.navHeight = $('#nav').outerHeight(false);
		
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			this.win.isMobile = true;
		} else {
			this.win.isMobile = false;
		}

		if( this.win.width < 768){
			
			this.win.breakpoint = 'xs';
			
		} else if( this.win.width >= 768 && this.win.width < 992) {
			
			this.win.breakpoint = 'sm';
			
		} else if( this.win.width >= 992 && this.win.width < 1280) {
			
			this.win.breakpoint = 'md';
			
		} else {
			this.win.breakpoint = 'lg';
		}

	};
	

	/* SET VALIDATOR DEFAULTS  */
	main.set_validator_defaults = function() {
		
		var self = this;
		
		self.tLog('set_validator_defaults()');
		
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
				form.submit();
				
			}
		
		});
		
	};
	
	/* MODAL
	----------------------------------------------------------------------------- */
	main.show_modal_loading = function(modalEl) {
		
		modalEl.find('input[type=submit]').attr('disabled',true);
		modalEl.find('.modal-body').prepend('<div class="loadingOverlay"></div>').show();
		
		
	};
	
	main.hide_modal_loading = function(modalEl) {
	
		modalEl.find('button[type=submit]').attr('disabled', false);
		modalEl.find('.loadingOverlay').remove();
		
		
	};
	
	main.show_modal_message = function(modalEl, level, message) {
		
		var html = this.format_alert_message(level, message);
		modalEl.find('.modal-body').prepend(html).alert();
		
		
	};
	
	main.format_alert_message = function(level, message) { 
		
		if(level == 'error'){
			level = 'danger';
		}
	
		var html = '<div class="alert alert-' +level+ ' alert-dismissable">';
		html 		+= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
		html 		+= message;
		html 		+= '</div>';
    return html;
		
	};
	
	
	
	/* TOOL TIPS
	----------------------------------------------------------------------------- */
	main.attach_tool_tips = function(){
		
		this.tLog('attach_tool_tips()');
		$('[data-toggle="tooltip"]').tooltip({container: 'body'});
		
		
	};

	/* RESET FORM
	----------------------------------------------------------------------------- */
	main.reset_form = function(formID) { 
		
		$('#' + formID).find('input:text, input:password, input:file, select, textarea').val('');
    $('#' + formID).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		
	};
	
	
	/* UTILS
	----------------------------------------------------------------------------- */
	
	main.tLog = function(msg) {
		
		window.log=function(){
			log.history=log.history||[];log.history.push(arguments);
			if(this.console){
				console.log(Array.prototype.slice.call(arguments));
			}
		};
		console.log(msg);
		
	};
	
	return main;

}($, tpjc)); //end main.js