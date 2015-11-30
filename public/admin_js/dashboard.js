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
	
	$("#tpjc_queueSyncReleases").one("click", function () {	
																								
		var btn = $(this);
		
		$.ajax({
			type: "POST",
			url: "index.php",
			data: '&mode=queueSyncReleases',
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
	