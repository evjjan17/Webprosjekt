jQuery(function($) {
	
	$('.cmmrm-rating li').click(function() {
		
		var obj = $(this);
		var container = obj.parents('ul').first();
		var wrapper = container.parents('.cmmrm-route-rating').first();
		
		if (container.attr('data-can-rate') == '0') return;
		
		var routeId = obj.parents('.cmmrm-route-single').data('routeId');
		if (!routeId) routeId = obj.parents('.cmmrm-route-snippet').data('routeId');
		
		$.post(CMMRM_Route_Rating.url, {
			action: 'cmmrm_route_rating',
			nonce: CMMRM_Route_Rating.nonce,
			routeId: routeId,
			rate: obj.data('rate')
		}, function(response) {
			console.log(response);
			if (response.success == '1') {
				container.attr('data-rating', Math.round(response.rate));
				container.attr('data-can-rate', 0);
				var number = wrapper.find('.cmmrm-votes-number');
				console.log(number);
				var num = parseInt(number.text().replace(/[^0-9]/g, '')) + 1;
				number.text('(' + num + ')');
			}
		});
		
	});
	
});