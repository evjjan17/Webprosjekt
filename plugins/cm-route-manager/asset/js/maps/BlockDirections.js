function CMMRM_BlockDirections(widget, routeRenderer) {
	
	this.widget = widget;
	this.routeRenderer = routeRenderer;
	this.container = jQuery(this.widget.getWidgetElement()).find('.cmmrm-directions-steps-wrapper');
	
	this.renderer = new google.maps.DirectionsRenderer;
	this.renderer.setMap(this.map);
	this.renderer.setOptions({suppressMarkers: true, preserveViewport: true, suppressBicyclingLayer: true, draggable: false});
	
	var that = this;
	jQuery(this.routeRenderer).bind('RouteRenderer:trailRequestSuccess', function(ev, data) {
		
//		that.renderer.setDirections(data.request.getResponse());
		
		var response = data.request.getResponse();
		var list = that.container.children('ul');
		if (list.find('.cmmrm-template').length == 0) return;
		var template = list.find('.cmmrm-template').clone();
		list.children('li:not(.cmmrm-template)').remove();
		
		if (that.routeRenderer.routeModel.travelMode == 'DIRECT') return;
		
		for (var i=0; i<response.routes[0].legs.length; i++) {
			var leg = response.routes[0].legs[i];
			for (var j=0; j<leg.steps.length; j++) {
				var step = leg.steps[j];
				var item = template.clone();
				item.removeClass('cmmrm-template');
				item.find('.cmmrm-step-distance').text(step.distance.text);
				item.find('.cmmrm-step-instructions').html(step.instructions);
				list.append(item);
			}
		}
		
	});
	
}
