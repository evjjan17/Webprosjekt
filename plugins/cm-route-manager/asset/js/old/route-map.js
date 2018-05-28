function CMMRM_Route(mapId, routeData) {
	
	var $ = jQuery;
	
	this.mapElement = document.getElementById(mapId);
	this.containerElement = $(this.mapElement).parents('.cmmrm-route-single').first();
	this.isFullscreen = false;
	this.markersCounter = 0;
	this.routeData = routeData;
	this.pathColor = routeData.pathColor;
	
	CMMRM_Map.call(this, mapId, routeData.locations);
	
	this.travelMode = routeData.travelMode;
	
	this.requestTrail();
	
	var mapObj = this;
	

	// Add geolocation marker
	if (CMMRM_Map_Settings.routeGeolocation == 1) {
		this.geolocationWatchPosition(function(pos) {
			mapObj.showUserPositionMarker(pos.coords.latitude, pos.coords.longitude);
		}, null, true);
	}
	
	$('.cmmrm-show-terrain input', this.containerElement).change(function(ev) {
		mapObj.map.setMapTypeId(this.checked ? google.maps.MapTypeId.TERRAIN : google.maps.MapTypeId.ROADMAP);
	});
	
	
	$('.cmmrm-directions-steps-btn', this.containerElement).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var wrapper = mapObj.containerElement.find('.cmmrm-route-map-canvas-outer');
		var name = 'data-show-steps';
		wrapper.attr(name, '1' == wrapper.attr(name) ? '0' : '1');
	});
	
	var fullscreen = $('<div/>', {"class":"cmmrm-fullscreen"}).hide().appendTo($('body'));
	fullscreen.height($(window).height());
	$('.cmmrm-map-fullscreen-btn', this.containerElement).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		mapObj.isFullscreen = true;
		jQuery('html, body').scrollTop(0);
		fullscreen.show();
		var obj = $(mapObj.mapElement);
		obj.data('height', obj.height());
		obj.height('100%');
		obj.appendTo(fullscreen);
		google.maps.event.trigger(mapObj.map, "resize");
		mapObj.center();
	});
	$(window).keydown(function(ev) { // Close fullscreen
		if (mapObj.isFullscreen && ev.keyCode == 27) {
			mapObj.isFullscreen = false;
			var obj = fullscreen.children().first();
			mapObj.containerElement.find('.cmmrm-route-map-canvas-outer').prepend(obj);
//			obj.appendTo();
			obj.height(obj.data('height'));
			fullscreen.hide();
			google.maps.event.trigger(mapObj.map, "resize");
			mapObj.center();
		}
	});
	
}


CMMRM_Route.prototype = Object.create(CMMRM_Map.prototype);
CMMRM_Route.prototype.contructor = CMMRM_Route;


CMMRM_Route.prototype.addLocation = function(location) {
	location.container = jQuery('.cmmrm-route-single[data-map-id="'+ this.mapId +'"] .cmmrm-location-details[data-id='+ location.id +']');
	CMMRM_Map.prototype.addLocation.call(this, location);
	if (location.type == 'location') {
		this.requestLocationWeather(location);
	}
};



CMMRM_Route.prototype.createMarker = function(location) {
	
	var label = (this.locations.length+1) +". "+ name;
	var marker = CMMRM_Map.prototype.createMarker.call(this, location);
	var mapObj = this;
	
	google.maps.event.addListener(marker, 'click', function() {
		if (mapObj.isFullscreen) return false;
		var index = mapObj.getLocationIndexByMarker(marker);
	    if (index !== false) {
	    	var location = mapObj.locations[index];
	    	var container = mapObj.containerElement;
	    	jQuery('html, body').animate({
		        scrollTop: container.find('.cmmrm-location-details[data-id='+ location.id +']').offset().top-30
		    }, 500);
	    }
	});
	
	return marker;
	
};


CMMRM_Route.prototype.createWaypointMarker = function(location) {
	var marker = CMMRM_Map.prototype.createWaypointMarker.call(this, location);
	marker.setMap(null);
	return marker;
};


CMMRM_Route.prototype.requestTrail = function() {
	// Don't request Google but use the overview path to display the polyline if this is first request.
	var requestCount = this.requestsCount;
	if (requestCount == 0) {
		var travelMode = this.travelMode;
		this.travelMode = 'DIRECT';
	}
	CMMRM_Map.prototype.requestTrail.call(this);
	if (requestCount == 0) {
		this.travelMode = travelMode;
	}
};


CMMRM_Route.prototype.requestTrailCallback = function(travelMode, response, status) {
	CMMRM_Map.prototype.requestTrailCallback.call(this, travelMode, response, status);
	if (status === google.maps.DirectionsStatus.OK) {
		this.renderDirectionsSteps(travelMode, response);
	}
};
