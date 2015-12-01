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