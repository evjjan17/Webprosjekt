function CMMRM_Editor(routeData) {
	
	var $ = jQuery;
	
	this.mapId = 'cmmrm-editor-map-canvas';
	this.locationsCounter = 0;
	this.mapElement = document.getElementById(this.mapId);
	this.containerElement = $(this.mapElement).parents('.cmmrm-route-editor').first();
	this.editorMode = 'location';
	this.lastLocation = null;
	this.suspendAddWaypoints = false;
	this.routeData = routeData;
	
	CMMRM_Map.call(this, this.mapId, routeData.locations);
	
	this.travelMode = routeData.travelMode;
	
	this.map.setOptions({
		draggableCursor: 'crosshair',
	});
	
	this.requestTrail();
	
	var mapObj = this;
	
	// Add geolocation marker
	if (CMMRM_Map_Settings.editorGeolocation == 1) {
		this.geolocationGetPosition(function(pos) {
			mapObj.showUserPositionMarker(pos.coords.latitude, pos.coords.longitude);
		}, null, true);
	}
	
	// Add waypoint
	google.maps.event.addListener(this.map, 'click', function(ev) {
		if (mapObj.suspendAddWaypoints) return;
		mapObj.addWaypoint(ev.latLng.lat(), ev.latLng.lng());
		var location = {lat: ev.latLng.lat(), long: ev.latLng.lng(), id: null};
		switch (mapObj.editorMode) {
			case 'location':
				location.name = CMMRM_Editor_Settings.newLocationLabel.replace("%d", (mapObj.locationsCounter+1));
				location.type = 'location';
				mapObj.addLocation(location);
				break;
			case 'waypoint':
//				location.name = "Waypoint";
//				location.type = 'waypoint';
				break;
		}
		if (mapObj.travelMode == 'DIRECT') {
			mapObj.updateOverviewPath();
		}
		mapObj.requestTrail();
	});
	
	
	$('.cmmrm-editor-instructions-btn').click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		mapObj.containerElement.find('.cmmrm-editor-instructions').slideToggle();
	});
	
	
	$('.cmmrm-import-kml-btn').click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		$(this).parents('form').find('.cmmrm-import-kml-wrapper').slideToggle();
	});
	
	
	// Change travel mode
	$('.cmmrm-route-travel-mode a', this.containerElement).click(function(ev) {
		mapObj.containerElement.find('input[name=travel-mode]').val($(this).data('mode'));
	});
	
	var searchBoxInput = $('.cmmrm-find-location', this.containerElement);
	searchBoxInput.keypress(function(e) {
		e = e || event;
		 var txtArea = /textarea/i.test((e.target || e.srcElement).tagName);
		 var result = txtArea || (e.keyCode || e.which || e.charCode || 0) !== 13;
		 if (!result) this.blur();
		 return result;
	})
	this.searchBox = new google.maps.places.SearchBox(searchBoxInput[0]);
	this.searchBox.addListener('places_changed', function() {
		var places = mapObj.searchBox.getPlaces();
		if (places.length == 0) return;
		var bounds = new google.maps.LatLngBounds();
		places.forEach(function(place) {
			if (place.geometry.viewport) {
		        // Only geocodes have viewport.
		        bounds.union(place.geometry.viewport);
		      } else {
		        bounds.extend(place.geometry.location);
		      }
		});
		mapObj.map.fitBounds(bounds);
	});
	
	
	// Change route params
	$('.cmmrm-route-params li:not(.cmmrm-route-distance):not(.cmmrm-route-avg-speed)', this.containerElement).each(function() {
		var item = $(this);
		var label = item.find('strong').text();
		item.addClass('cmmrm-editable');
		item.attr('title', 'Change '+ label.toLowerCase());
		item.click(function(ev) {
			var name = item[0].className.replace('cmmrm-editable', '').replace('cmmrm-route-', '').replace('cmmrm-', '').replace(/\s/, '');
			var input = item.parents('form').find('input[name='+ name +']');
			var promptValue = Math.round(input.val());
			if (name == 'duration') promptValue = mapObj.getDurationLabel(promptValue);
			var val = window.prompt(label, promptValue);
			if (val !== false) {
				switch (name) {
					case 'duration':
						mapObj.updateDuration(mapObj.parseDuration(val));
						break;
					case 'max-elevation':
						val = parseInt(val);
						if (isNaN(val)) return;
						mapObj.updateMaxElevation(val);
						break;
					case 'min-elevation':
						val = parseInt(val);
						if (isNaN(val)) return;
						mapObj.updateMinElevation(val);
						break;
					case 'elevation-gain':
						val = parseInt(val);
						if (isNaN(val)) return;
						mapObj.updateElevationGain(val);
						break;
					case 'elevation-descent':
						val = parseInt(val);
						if (isNaN(val)) return;
						mapObj.updateElevationDescent(val);
						break;
				}
			}
		});
	});
	
//	this.directionsDisplay.addListener('directions_changed', function() {
//		var waypoints = mapObj.directionsDisplay.getDirections().request.waypoints;
//		for (var i=0; i<waypoints.length; i++) {
//			var waypoint = waypoints[i];
//			if (!waypoint.stopover) {
//				var location = {name: "Waypoint", lat: waypoint.location.lat(), long: waypoint.location.lng(), id: null, stopover: false};
//				location.marker = new google.maps.Marker({
//					position: waypoint.location,
//					map: mapObj.map,
//					icon: 'https://maps.gstatic.com/mapfiles/dd-via.png',
//				});
//				mapObj.addLocation(location);
//			}
//		}
//	});
	
	$('#cmmrm-editor-locations .cmmrm-locations-list', this.containerElement).sortable({
		update: function(event, ui) {
			var obj = $(ui.item[0]);
			var index = mapObj.getLocationIndexByItem(ui.item[0]);
			var newIndex = obj.index()-1;
			console.log('index '+ index +' new '+ newIndex);
			if (index != newIndex) {
				var location = mapObj.locations.splice(index, 1)[0];
				mapObj.locations.splice(newIndex, 0, location);
				mapObj.requestTrail();
			}
		}
	});
	
	$('.cmmrm-locations-editor-mode a', this.containerElement).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var obj = $(this);
		mapObj.editorMode = obj.data('mode');
		obj.parents('ul').find('li.current').removeClass('current');
		obj.parents('li').first().addClass('current');
	});
	
	
};


CMMRM_Editor.prototype = Object.create(CMMRM_Map.prototype);
CMMRM_Editor.prototype.contructor = CMMRM_Editor;




CMMRM_Editor.prototype.createDirectionsRenderer = function() {
	var directionsDisplay = CMMRM_Map.prototype.createDirectionsRenderer.call(this);
//	directionsDisplay.setOptions({draggable: true, suppressMarkers: true});
	return directionsDisplay;
};



CMMRM_Editor.prototype.addLocation = function(location, index) {
	location.item = this.addLocationViewItem(location, index).get(0);
//	if (location.type == 'location') { // Location
//		location.marker = this.createMarker(location);
//	} else { // Waypoint
//		location.marker = this.createWaypointMarker(location);
//	}
	CMMRM_Map.prototype.addLocation.call(this, location, index);
//	this.pushLocation(location, index);
	this.locationsCounter++;
};


CMMRM_Editor.prototype.createWaypointMarker = function(location) {
	
	var marker = CMMRM_Map.prototype.createWaypointMarker.call(this, location);
	marker.setDraggable(true);
	var mapObj = this;
	
	google.maps.event.addListener(marker, 'dragend', function() {
		var pos = marker.getPosition();
	    var index = mapObj.getLocationIndexByMarker(marker);
	    if (index !== false) {
	    	mapObj.updateLocationPosition(index, pos.lat(), pos.lng());
	    }
	    mapObj.requestTrail();
	});
	
	google.maps.event.addListener(marker, 'click', function(ev) {
		var index = mapObj.getLocationIndexByMarker(marker);
	    if (index !== false) {
	    	mapObj.removeLocation(index);
	    	mapObj.requestTrail();
	    }
	});
	
	google.maps.event.addListener(marker, 'rightclick', function(ev) {
		var index = mapObj.getLocationIndexByMarker(marker);
	    if (index !== false) {
	    	var location = mapObj.locations[index];
	    	location.marker.setMap(null);
	    	location.marker = mapObj.createMarker(location);
	    	location.type = 'location';
	    	var item = jQuery(location.item);
	    	item.show();
	    	item.find('input.location-type').val(location.type);
	    	mapObj.findAddress(new google.maps.LatLng(location.lat, location.long), function(result) {
				item.find('.location-address').val(result.formatted_address);
			});
//	    	mapObj.requestTrail();
	    }
	});
	
	return marker;
	
};



CMMRM_Editor.prototype.addLocationViewItem = function(location, index) {
	
	var mapObj = this;
	var container = jQuery('#cmmrm-editor-locations .cmmrm-locations-list');
	var item = container.find('li:first-child').first().clone();
	
	if (typeof index == 'number') {
		container.children('li:nth-child('+ (index+1) +')').after(item);
	} else {
		container.append(item);
	}
	
	item.attr('data-id', location.id ? location.id : 0);
	item.find('input[type=hidden][name*=id]').val(location.id ? location.id : 0);
	item.find('.location-name').val(location.name).change(function() { location.name = this.value; location.marker.setTitle(this.value); });
	item.find('.location-lat').val(location.lat).change(function() { location.lat = this.value; location.marker.setPosition(new google.maps.LatLng(this.value, location.marker.getPosition().lng())); });;
	item.find('.location-long').val(location.long).change(function() { location.lat = this.value; location.marker.setPosition(new google.maps.LatLng(location.marker.getPosition().lat(), this.value)); });;
	item.find('.location-description').val(location.description ? location.description : '');
	item.find('.location-type').val(location.type);
	
	if (location.type == 'location' && (this.requestsCount > 0 || this.shouldRecalculate())) {
		this.findAddress(new google.maps.LatLng(location.lat, location.long), function(result) {
			item.find('.location-address').val(result.formatted_address);
		});
	}
	else if (typeof location.address == 'string') {
		item.find('.location-address').val(location.address);
	}
	
//	item.find('input[type=hidden][name*=images]').val(location.images ? location.images.join(',') : '');
	
	if (location.type == 'location') {
		item.show();
	}
		
	item.find('.cmmrm-images').each(CMMRM_Editor_Images_init);
	

	if (location.images && location.images.length > 0) {
		var imageFileInput = item.find('input[type=hidden][name*=images]');
		var imageFileList = item.find('.cmmrm-images-list');
		for (var i=0; i<location.images.length; i++) {
			var image = location.images[i];
			CMMRM_Editor_Images_add(imageFileInput, imageFileList, image.id, image.thumb, image.url);
		}
	}
	
	if (typeof CMMRM_Location_Icon_init == 'function') {
		CMMRM_Location_Icon_init(item, location.icon);
	}
	
	jQuery('.cmmrm-location-remove', item).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var item = jQuery(this).parents('li').first();
		var index = mapObj.getLocationIndexByItem(item.get(0));
		if (index !== false) {
			mapObj.removeLocation(index);
			mapObj.requestTrail();
		}
	});
	
	jQuery('.cmmrm-location-convert', item).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var item = jQuery(this).parents('li').first();
		var index = mapObj.getLocationIndexByItem(item.get(0));
		if (index !== false) {
			if (index == 0 || index == mapObj.locations.length-1) {
				console.log('Only waypoint');
			} else {
				var location = mapObj.locations[index];
				item.hide();
				location.type = 'waypoint';
				location.marker.setMap(null);
				location.marker = mapObj.createWaypointMarker(location);
				item.find('input.location-type').val(location.type);
			}
		}
	});
		
	
	return item;
	
};

//CMMRM_Editor.prototype.addWaypointViewItem = function(location, index) {
//	var mapObj = this;
//	var container = jQuery('#cmmrm-editor-locations .cmmrm-locations-list');
//	var $ = jQuery;
//	var item = $('<li/>');
//	item.append($('<input/>', {type: "text", name: "location[type][]", value: "waypoint"}));
//	item.append($('<input/>', {type: "text", name: "location[lat][]", value: location.lat}));
//	item.append($('<input/>', {type: "text", name: "location[long][]", value: location.long}));
//	container.children(':nth-child('+ (index+1) +')').after(item);
//	return item;
//};




CMMRM_Editor.prototype.createMarker = function(location) {
	
	var label = location.name;
	
//	var marker = new MarkerWithLabel({
//		   position: new google.maps.LatLng(location.lat, location.long),
//		   draggable: true,
////		   raiseOnDrag: true,
//		   map: this.map,
//		   cursor: 'pointer',
//		   labelContent: label,
//		   labelAnchor: new google.maps.Point(this.getTextWidth(label, 10), 0),
//		   labelClass: "cmmrm-map-label" // the CSS class for the label
//		 });
	
	var marker = new CMMRM_Marker(this, new google.maps.LatLng(location.lat, location.long),
		   {draggable: true, style: 'cursor:pointer;'},
		   {text: label, style: 'cursor:pointer;'}
		 );
	
	var mapObj = this;
	
	google.maps.event.addListener(marker, 'positionUpdated', function() {
	    var pos = marker.getPosition();
	    var index = mapObj.getLocationIndexByMarker(marker);
	    if (index !== false) {
	    	mapObj.updateLocationPosition(index, pos.lat(), pos.lng());
	    }
	    mapObj.requestTrail();
	    mapObj.findAddress(new google.maps.LatLng(location.lat, location.long), function(result) {
			jQuery(location.item).find('.location-address').val(result.formatted_address);
		});
	});
	
	google.maps.event.addListener(marker, 'click', function() {
		if (mapObj.suspendAddWaypoints) return;
		var index = mapObj.getLocationIndexByMarker(marker);
	    if (index !== false) {
	    	var nameInput = jQuery(mapObj.locations[index].item).find('.location-name');
	    	nameInput.select();
	    	jQuery('html, body').animate({
		        scrollTop: nameInput.offset().top
		    }, 500);
	    }
	});
	
	return marker;
	
};


CMMRM_Editor.prototype.updateLocationPosition = function(index, lat, long) {
	var location = this.locations[index];
	var item = jQuery(location.item);
	location.lat = lat;
	location.long = long;
	if (item.length > 0) {
		item.find('input[class=location-lat]').val(lat);
		item.find('input[class=location-long]').val(long);
	}
};


CMMRM_Editor.prototype.updateMaxElevation = function(maxElevation) {
	CMMRM_Map.prototype.updateMaxElevation.call(this, maxElevation);
	this.containerElement.find('input[name=max-elevation]').val(maxElevation);
};

CMMRM_Editor.prototype.updateMinElevation = function(minElevation) {
	CMMRM_Map.prototype.updateMinElevation.call(this, minElevation);
	this.containerElement.find('input[name=min-elevation]').val(minElevation);
};


CMMRM_Editor.prototype.updateElevationGain = function(elevationGain) {
	CMMRM_Map.prototype.updateElevationGain.call(this, elevationGain);
	this.containerElement.find('input[name=elevation-gain]').val(elevationGain);
};

CMMRM_Editor.prototype.updateElevationDescent = function(elevationDescent) {
	CMMRM_Map.prototype.updateElevationDescent.call(this, elevationDescent);
	this.containerElement.find('input[name=elevation-descent]').val(elevationDescent);
};


CMMRM_Editor.prototype.calculateElevationCallback = function(results, status) {
	CMMRM_Map.prototype.calculateElevationCallback.call(this, results, status);
//	this.containerElement.find('input[name=elevation-response]').val(JSON.stringify(results));
	
	if (this.requestsCount == 1 && this.shouldRecalculate()) {
		this.sendParamsUpdate();
	}
	
};


CMMRM_Editor.prototype.sendParamsUpdate = function() {
	
	var items = this.containerElement.find('.cmmrm-locations-list > li:not([data-id=0])');
	
	var locations = [];
	for (var i=0; i<items.length; i++) {
		var item = jQuery(items[i]);
		if (item.find('.location-type').val() == 'location') {
			locations.push({id: item.find('.location-id').val(), addr: item.find('.location-address').val()});
		}
	}
	
	var data = {
		action: 'cmmrm_route_params_save',
		nonce: CMMRM_Editor_Settings.updateParamsNonce,
		routeId: this.containerElement.find('form').data('routeId'),
		duration: this.totalDuration,
		minElevation: this.minElevation,
		maxElevation: this.maxElevation,
		elevationGain: this.elevationGain,
		elevationDescent: this.elevationDescent,
		avgSpeed: this.avgSpeed,
		locations: locations,
	};
	jQuery.post(CMMRM_Editor_Settings.ajaxUrl, data, function(response) {});
	
};



CMMRM_Editor.prototype.requestTrailCallback = function(travelMode, response, status) {
	CMMRM_Map.prototype.requestTrailCallback.call(this, travelMode, response, status);
	//this.containerElement.find('input[name=directions-response]').val(JSON.stringify(response));
	var mapObj = this;
	
	var getOverviewPath = function() {
		if (google && google.maps && google.maps.geometry && google.maps.geometry.encoding && response.routes.length > 0) {
			var path = google.maps.geometry.encoding.encodePath(response.routes[0].overview_path);
			mapObj.containerElement.find('input[name=overview-path]').val(path);
		} else {
			setTimeout(getOverviewPath, 500);
		}
	};
	getOverviewPath();
	
};



CMMRM_Editor.prototype.removeLocation = function(index) {
	var location = this.locations[index];
	if (location.item) location.item.remove();
	if (location.marker) location.marker.setMap(null);
	this.locations.splice(index, 1);
};


CMMRM_Editor.prototype.createTrailPolyline = function(path, legIndex) {
	var p = CMMRM_Map.prototype.createTrailPolyline.call(this, path, legIndex);
	var mapObj = this;
	p.addListener('click', function(ev) {
		console.log('legIndex = '+ legIndex);
		var location = {name: "Waypoint", lat: ev.latLng.lat(), long: ev.latLng.lng(), id: null, type: 'waypoint'};
		mapObj.addLocation(location, legIndex+1);
		mapObj.requestTrail();
	});
	
	return p;
}


CMMRM_Editor.prototype.updateDistance = function(dist) {
	CMMRM_Map.prototype.updateDistance.call(this, dist);
	this.containerElement.find('input[name=distance]').val(dist);
};

CMMRM_Editor.prototype.updateDuration = function(duration) {
	CMMRM_Map.prototype.updateDuration.call(this, duration);
	this.containerElement.find('input[name=duration]').val(duration);
};

CMMRM_Editor.prototype.createViewPointBound = function() {
	if (this.locations.length == 0) {
		this.map.panTo(new google.maps.LatLng(CMMRM_Editor_Settings.defaultLat, CMMRM_Editor_Settings.defaultLong));
		this.map.setZoom(parseInt(CMMRM_Editor_Settings.defaultZoom));
	} else {
		CMMRM_Map.prototype.createViewPointBound.call(this);
	}
	
};


