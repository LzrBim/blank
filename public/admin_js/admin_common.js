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
	var $form = $( '#' + modalID + 'Form')
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