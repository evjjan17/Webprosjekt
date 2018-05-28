function CMMRM_Index_Map(mapId, locations) {
	
	var $ = jQuery;
	
	this.mapElement = document.getElementById(mapId);
	this.containerElement = $(this.mapElement).parents('.cmmrm-routes-archive').first();
	this.isFullscreen = false;
	
	var mapObj = this;
	
	CMMRM_Map.call(this, mapId, locations);
	if (locations.length < 2) {
		setTimeout(function() {
			if (locations.length == 0) {
				mapObj.map.panTo(new google.maps.LatLng(0,0));
				mapObj.map.setZoom(2);
			} else {
				mapObj.map.setZoom(12);
			}
		}, 500);
	}
	
	
	
	// Add geolocation marker
	if (CMMRM_Map_Settings.indexGeolocation == 1) {
		this.geolocationWatchPosition(function(pos) {
			mapObj.showUserPositionMarker(pos.coords.latitude, pos.coords.longitude);
		}, null, false);
	}
	
	
	// Display overview path
	if (this.containerElement.data('showParamOverviewPath') == 1) {
//		setTimeout(function() {
			for (var i=0; i<locations.length; i++) {
				var location = locations[i];
				if (location.path) {
					var p = new google.maps.Polyline({
						path: google.maps.geometry.encoding.decodePath(location.path),
						strokeColor: (location.pathColor ? location.pathColor : '#3377FF'),
						opacity: 0.1,
						map: mapObj.map
					});
				}
			}
//		}, 500);
	}
	
	// Display map thumbs on the routes list
	for (var i=0; i<locations.length; i++) {
		break;
		var location = locations[i];
		var image = this.containerElement.find('.cmmrm-route-snippet[data-route-id='+ location.id +'] .cmmrm-route-featured-image img');
		if (image.length == 1) {
			var pathParams = {weight: 3, color: location.pathColor, enc: location.path};
			var pathParamsVal = [];
			for (var name in pathParams) {
				pathParamsVal.push(name +':'+ pathParams[name]);
			}
			pathParamsVal = pathParamsVal.join('|');
			console.log(pathParamsVal);
			var url = 'https://maps.googleapis.com/maps/api/staticmap?path='+ encodeURIComponent(pathParamsVal)
				+'&size='+ image.width() +'x'+ image.height() +'&maptype=roadmap&key='+ CMMRM_Map_Settings.googleMapAppKey;
			image.attr('src', url);
		}
	}
	
	
	$('.cmmrm-show-terrain input', this.containerElement).change(function(ev) {
		mapObj.map.setMapTypeId(this.checked ? google.maps.MapTypeId.TERRAIN : google.maps.MapTypeId.ROADMAP);
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
			obj.appendTo(mapObj.containerElement.find('.cmmrm-route-map-canvas-outer'));
			obj.height(obj.data('height'));
			fullscreen.hide();
			google.maps.event.trigger(mapObj.map, "resize");
			mapObj.center();
		}
	});
	
	
	
}


CMMRM_Index_Map.prototype = Object.create(CMMRM_Map.prototype);
CMMRM_Index_Map.prototype.contructor = CMMRM_Index_Map;



CMMRM_Index_Map.prototype.createMarker = function(location) {
	
	var marker = new CMMRM_Marker(this, new google.maps.LatLng(location.lat, location.long),
			   {draggable: false, style: 'cursor:pointer;', color: location.pathColor},
			   {text: location.name, style: 'cursor:pointer;'}
			 );
	
//	var marker = CMMRM_Map.prototype.createMarker.call(this, location);
	var mapObj = this;
	
//	google.maps.event.addDomListener(marker.get('container'), 'click'
	
	google.maps.event.addListener(marker, 'click', function() {
		var index = mapObj.getLocationIndexByMarker(marker);
		
	    if (index !== false) {
	    	var loc = mapObj.locations[index];
	    	window.location.href = loc.permalink;
	    }
	});
	
	return marker;
	
};


CMMRM_Index_Map.prototype.requestTrail = function(travelMode) {
	
};
