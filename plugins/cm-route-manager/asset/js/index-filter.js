jQuery(function($) {
	
	$('.cmmrm-filter select').change(function() {
		var obj = $(this);
		obj.parents('.cmmrm-route-index-filter').find('select').prop('disabled', true);
		location.href = obj.val();
	});
	
});