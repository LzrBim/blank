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


