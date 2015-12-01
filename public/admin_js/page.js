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
	
	var formID = modalID + 'Form'
	
	$( '#' + formID ).validate({
					
		submitHandler: function(form) {
			
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