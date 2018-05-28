function CMMRM_FullscreenFeature(widget) {
	
	this.widget = widget;
	this.map = widget.map;
	this.isFullscreen = false;
	
	var $ = jQuery;
	var that = this;
	var googleMap = that.map.map;
	var prevOverflow = $('body').css('overflow');
	var fullscreen = $('<div/>', {"class":"cmmrm-fullscreen"}).hide().appendTo($('body'));
	fullscreen.height($(window).height());
	$('.cmmrm-map-fullscreen-btn', this.widget.getWidgetElement()).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		that.isFullscreen = true;
		jQuery('html, body').scrollTop(0);
		fullscreen.show();
		
		var mapContainer = $(that.map.container);
		var indexFilters = mapContainer.parents('.cmmrm-routes-archive').find('.cmmrm-route-index-filter');
		fullscreen.append(indexFilters);
		
		mapContainer.data('height', mapContainer.height());
		mapContainer.height('100%');
		mapContainer.appendTo(fullscreen);
		$('body').css('overflow', 'hidden');
		google.maps.event.trigger(googleMap, "resize");
		that.map.center();
	});
	$(window).keydown(function(ev) { // Close fullscreen
		if (that.isFullscreen && ev.keyCode == 27) {
			that.isFullscreen = false;
			var filtersWrapper = fullscreen.find('.cmmrm-route-index-filter');
			var obj = fullscreen.find('.cmmrm-route-map-canvas');
//			console.log(that.widget.getWidgetElement().find('.cmmrm-route-map-canvas-outer'));
			var widgetElement = jQuery(that.widget.getWidgetElement());
			widgetElement.find('.cmmrm-route-index-map').before(filtersWrapper);
			widgetElement.find('.cmmrm-route-map-canvas-outer').prepend(obj);
//			obj.appendTo();
			obj.height(obj.data('height'));
			fullscreen.hide();
			$('body').css('overflow', prevOverflow);
			google.maps.event.trigger(googleMap, "resize");
			that.map.center();
		}
	});
	
}
