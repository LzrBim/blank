/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /public_html/js/main.js
----------------------------------------------------------------------------- */

"use strict";

var tpjc = tpjc || {};

var tpjc = (function ($, main) {
		
	var settings, options, win, page;
		
	main.init = function(page, options) { 
		
		this.page = page;
		
		this.options = options;		
		
		//SETUP		
		this.win = {};
		this.settings = {};
		
		this.tLog('init('+ page +', '+ JSON.stringify(options) +')');
		
		this.init_defaults();		
			
		/* CONTROLLER */
		if (typeof(page) != 'undefined' && page != '') {
						
			if(this.page == 'home'){
		
					
			} else if(this.page == 'blog'){
				
				
			} else if(this.page == 'blog-detail'){

			
			} else if (this.page == 'contact'){
				
				this.validate_contact_form();
				
			

				
			} else if (this.page == 'checkout'){
				
				this.validate_checkout();
				
			} else {
				this.tLog('- no whitelisted page ');
			}
					
		} else {
			this.tLog('- page undefined ');
		}
		
	};
	

	/* INIT DEFAULTS
	----------------------------------------------------------------------------- */
	main.init_defaults = function() {
		
		this.tLog('init_defaults()');
		
		this.set_window_info();
		
    $('#side-menu').metisMenu();
		
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
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

	
	
	/* GET WINDOW INFO
	----------------------------------------------------------------------------- */
	main.set_window_info = function() {
		
		self = this;
		self.tLog('get_window_info()');
		
		self.win.width = $(window).width();
		self.win.height = $(window).height();
		
		self.win.navHeight = $('#nav').outerHeight(false);
		
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			self.win.isMobile = true;
		} else {
			self.win.isMobile = false;
		}

		if( self.win.width < 768){
			
			self.win.breakpoint = 'xs';
			
		} else if( self.win.width >= 768 && self.win.width < 992) {
			
			self.win.breakpoint = 'sm';
			
		} else if( self.win.width >= 992 &&self. win.width < 1280) {
			
			self.win.breakpoint = 'md';
			
		} else {
			self.win.breakpoint = 'lg'
		}
	
		self.tLog('winheight='+self.win.height);

	};
	
	
	
	/* ATTACH WINDOW RESIZE
	----------------------------------------------------------------------------- */
	
	main.handle_resize = function(callback) {
		
		self = this;
		self.tLog('handle_resize()');
		
		//DEBOUNCE
		var delay = (function(){
			var timer = 0;
			return function(fx, ms){
				clearTimeout (timer);
				timer = setTimeout(fx, ms);
			};
		})();
		
		//BIND
		$(window).resize(function() {										
			delay(function(){
				self.set_window_info();
				callback();
			}, 400);
		});
		
		//TRIGGER
		$( window ).load(function() {
			//self.set_window_info();
			callback();	
		});
		
	}
	
	
	/* ATTACH WINDOW RESIZE
	----------------------------------------------------------------------------- */
	 
	main.attach_home_resize = function() { 
		
		self = this;
		self.tLog('attach_home_resize()');
		
		this.resize(function () {
			
			//self.normalize_heights('.newsEventsCol', '.newsCarouselItem');
			
		}); 
		
	};
	
	
	
	/*-----------------------------------------------------------------------------
		NORMALIZE HEIGHTS
	----------------------------------------------------------------------------- */
	
	main.normalize_heights = function(parentElement, element, setLineHeight) {
		
		this.tLog('normalize_heights('+parentElement+', '+element+')');
		
		var items = $(parentElement).find(element), 
    heights = [], 
    tallest;

		if (items.length) {
			
			items.each(function() { //add heights to array
					heights.push($(this).outerHeight()); 
			});
			tallest = Math.max.apply(null, heights); //cache largest value
			items.each(function() {
													
				$(this).css('min-height', tallest + 'px');
			
				if(setLineHeight){
			
					$(this).css('line-height', tallest + 'px');
				}
				
			});
			
		}
				
	};
	
	/* DISPLAY MAP
	|	<div id="mapCanvas1" class="mapCanvas" data-map-address="34 elm st."></div>
	----------------------------------------------------------------------------- */
	 
	main.display_map = function() {
		
		self = this;
		self.tLog('display_map()');
		
		$('.mapCanvas').each(function() {
			var w = $(this).parent().width();
			var h = w * ( 1 / (16/9) );
			$(this).width(w).height(h);
		});
		
		var geocoder;
		var map;
		
		if($('.mapCanvas').length > 0) {
			
			$('.mapCanvas').each(function() {
																		
				var mapCanvas = $(this);
				var mapCanvasID = mapCanvas.attr('id');
				var address = mapCanvas.data('map-address');
				
				if(typeof(address) != 'undefined'){
				
					geocoder = new google.maps.Geocoder();
					var latlng = new google.maps.LatLng(40.9648136, -72.1871827); /*TP Office*/
					var mapOptions = {
						zoom: 12,
						center: latlng,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					
					if(mapCanvasID != 'undefined'){
						
						map = new google.maps.Map(document.getElementById(mapCanvasID), mapOptions);
						
						geocoder.geocode( { 'address': address}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								map.setCenter(results[0].geometry.location);
								var marker = new google.maps.Marker({
										map: map,
										position: results[0].geometry.location
								});
								
							} else {
								
								$(this).fadeOut('fast');
								
								mapCanvas.after('<p>No map available.</p>');
							}
						});
					} else {
						self.tLog('tpjc::display_map() - .mapCanvas had no ID');
					}
					
				} else {
					self.tLog('tpjc::display_map() - did not gather address from data attribute');
				}
						
			});	
				
			
		}
	};
	
	
	
	/* SET VALIDATOR DEFAULTS  */
	main.set_validator_defaults = function() {
		
		self = this;
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
	
	
	
	/* CONTACT PAGE VALIDATOR
	----------------------------------------------------------------------------- */
	
	main.validate_contact_form = function() {
		
		self = this;
		self.set_validator_defaults();
		self.tLog('validate_contact_form()');
		
		var formID = 'contactForm';
	
		$( '#' + formID ).validate({
			rules: {
				name: {
					required: true
				},
				email: {
					required: true,
					email:true
				}
			},
			onkeyup: false
		}); 
		


	};
	
	
	/* LOGIN PAGE
	----------------------------------------------------------------------------- */
	
	main.attach_login = function() {
		
		this.tLog('attach_login()');
		
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
	
	
	/* TOOL TIPS
	----------------------------------------------------------------------------- */
	main.attach_tool_tips = function(){
		
		this.tLog('init_defaults()');
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
			if(this.console){console.log(Array.prototype.slice.call(arguments))}
		};
		console.log(msg);
	};
	
	return main;

}($, tpjc)); //end main.js
