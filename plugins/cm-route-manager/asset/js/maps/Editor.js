function CMMRM_Editor(containerId, routeData, waypointsString, locations) {
	
	CMMRM_WidgetSingleRoute.call(this, containerId, routeData, waypointsString, locations);
	
	var that = this;
	
	this.map.map.setOptions({
		draggableCursor: 'crosshair',
	});
	
	this.editorMode = 'location';
	this.locationsCounter = locations.length;
	this.waypointsRenderers = [];
	this.firstTrailRequest = true;
	
	this.initToolMenu();
	this.initSearchBox();
	this.initImportTool();
	this.initCreatingLocations();
	this.initCreatingPolyline();
	this.initViewpoint();
	this.initPolylinesDivision();
	this.initSortableLocations();
	this.initParamsEditor();
	
	jQuery(this.routeModel).bind('RouteModel:setWaypointsString', function(ev, data) {
		jQuery('input[name="waypoints-string"]', that.getWidgetElement()).val(this.getWaypointsString());
	});
	
	jQuery(this.routeModel).bind('RouteModel:setPolylineString', function(ev, data) {
		jQuery('input[name=overview-path]', that.getWidgetElement()).val(data.polylineString);
	});
	
	jQuery(this.routeModel).bind('RouteModel:setTravelMode', function(ev, data) {
		jQuery('input[name=travel-mode]', that.getWidgetElement()).val(data.travelMode);
	});
	
	jQuery(this.routeRenderer).bind('RouteRenderer:trailRequestSuccess', function(ev, data) {
		if (that.firstTrailRequest) {
			console.log('firstTrailRequest');
			that.firstTrailRequest = false;
		} else {
			console.log('another TrailRequest');
			var distance = data.request.getDistance();
			that.updateDistance(distance);
			var duration = data.request.getDuration();
			that.updateDuration(duration);
			var speed = distance/duration;
			that.updateAvgSpeed(speed);
		}
	});
	
	jQuery(this.blockRouteParams.elevationGraph).bind('ElevationGraph:successResponse', function(ev, data) {
		if (that.firstTrailRequest) {
			return;
		}
		// Update hidden input fields with the elevation data
		that.updateMaxElevation(this.getMaxElevation());
		that.updateMinElevation(this.getMinElevation());
		that.updateElevationGain(this.getElevationGain());
		that.updateElevationDescent(this.getElevationDescent());
	});
	
	
	// Create waypoints renderers
	var waypointsCoords = this.routeModel.getWaypointsCoords();
	if (waypointsCoords.length < CMMRM_Map_Settings.editorWaypointsLimit) {
		// Display waypoints dots for smaller routes
		jQuery(waypointsCoords).each(function(index, value) {
			that.createWaypointRenderer(this, index);
		});
	} else {
		// Hide some features for big routes
		jQuery('.cmmrm-locations-editor-mode a[data-mode=waypoint]').parents('li').first().hide();
		jQuery('.cmmrm-route-travel-mode a[data-mode!=DIRECT]').hide();
	}
	
	jQuery('.cmmrm-editor-instructions-btn').click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		jQuery(this).parents('form').find('.cmmrm-editor-instructions').slideToggle();
	});
	
	jQuery(this).trigger('Editor:ready');
	
}


CMMRM_Editor.prototype = Object.create(CMMRM_WidgetSingleRoute.prototype);
CMMRM_Editor.prototype.contructor = CMMRM_WidgetSingleRoute;


CMMRM_Editor.prototype.getDependencies = function() {
	var deps = CMMRM_WidgetSingleRoute.prototype.getDependencies.call(this);
	deps.LocationRenderer = CMMRM_LocationRendererEditor;
	return deps;
};


CMMRM_Editor.prototype.getWidgetElement = function() {
	return jQuery(this.container).parents('.cmmrm-route-editor').first().get(0);
};


CMMRM_Editor.prototype.initSearchBox = function() {
	var $ = jQuery;
	var that = this;
	var searchBoxInput = $('.cmmrm-find-location', this.getWidgetElement());
	searchBoxInput.keypress(function(e) {
		e = e || event;
		 var txtArea = /textarea/i.test((e.target || e.srcElement).tagName);
		 var result = txtArea || (e.keyCode || e.which || e.charCode || 0) !== 13;
		 if (!result) this.blur();
		 return result;
	})
	this.searchBox = new google.maps.places.SearchBox(searchBoxInput[0]);
	this.searchBox.addListener('places_changed', function() {
		var places = that.searchBox.getPlaces();
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
		that.map.map.fitBounds(bounds);
	});
};


CMMRM_Editor.prototype.initImportTool = function() {
	var $ = jQuery;
	$('.cmmrm-import-kml-btn').click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		$(this).parents('form').find('.cmmrm-import-kml-wrapper').slideToggle();
	});
};


CMMRM_Editor.prototype.initToolMenu = function() {
	var $ = jQuery;
	var that = this;
	$('.cmmrm-locations-editor-mode a', this.getWidgetElement()).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var obj = $(this);
		that.editorMode = obj.data('mode');
		obj.parents('ul').find('li.current').removeClass('current');
		obj.parents('li').first().addClass('current');
	});
};


CMMRM_Editor.prototype.initCreatingLocations = function() {
	var $ = jQuery;
	var that = this;
	
	// Click listener
	google.maps.event.addListener(this.map.map, 'click', function(ev) {
//		console.log(that.map.suspendAddWaypoints);
		if (that.map.suspendAddWaypoints) return;
		if ('location' == that.editorMode) {
			that.addLocation(ev.latLng.lat(), ev.latLng.lng());
		}
	});
	
};


CMMRM_Editor.prototype.addLocation = function(lat, lng) {
	this.locationsCounter++;
	
	var locationModel = this.routeModel.addLocation({
		id: 0,
		name: CMMRM_Editor_Settings.newLocationLabel.replace('%d', this.locationsCounter),
		lat: lat,
		lng: lng,
		description: "",
		type: "location",
		address: "",
		icon: "",
		images: []
	});
	
	var renderer = new (this.resolve('LocationRenderer'))(this, locationModel);
};




CMMRM_Editor.prototype.initCreatingPolyline = function() {
	var $ = jQuery;
	var that = this;
	google.maps.event.addListener(this.map.map, 'click', function(ev) {
		if (that.map.suspendAddWaypoints) return;
		if ('waypoint' == that.editorMode) {
			that.createWaypoint(ev.latLng);
		}
	});
};


CMMRM_Editor.prototype.createWaypoint = function(coords, index) {
	this.routeModel.addWaypoint(coords, index);
	this.createWaypointRenderer(coords, index);
	this.routeModel.updateWaypointsString();
};


CMMRM_Editor.prototype.createWaypointRenderer = function(waypointCoords, index) {
	
	if (typeof index == 'undefined') {
		index = this.waypointsRenderers.length;
	}
	
//	console.log('create '+ index);
	
	var that = this;
	var renderer = new (this.resolve('WaypointRenderer'))(this, waypointCoords, index);
	this.waypointsRenderers.splice(index, 0, renderer);
	
	// Update renderers index
	for (var i=index+1; i<this.waypointsRenderers.length; i++) {
		this.waypointsRenderers[i].setWaypointIndex(i+1);
	}
	
	// Check if this is correct
//	for (var i=0; i<this.waypointsRenderers.length; i++) {
//		console.log('i='+ i +' index='+ this.waypointsRenderers[i].index);
//	}
	
	jQuery(renderer).bind('WaypointRenderer:updatePosition', function(ev, data) {
		that.routeModel.waypointsCoords[this.getWaypointIndex()] = this.getWaypointCoords();
		that.routeModel.updateWaypointsString();
	});
	jQuery(renderer).bind('WaypointRenderer:remove', function(ev, data) {
		
//		console.log('remove '+ this.index);
		that.routeModel.removeWaypointByIndex(this.index);
		that.routeModel.updateWaypointsString();
		
		// Update renderers index
		for (var i=this.index+1; i<that.waypointsRenderers.length; i++) {
			that.waypointsRenderers[i].setWaypointIndex(i-1);
		}
		
		var removed = that.waypointsRenderers.splice(this.index, 1);
		
		// Check if this is correct
//		for (var i=0; i<that.waypointsRenderers.length; i++) {
//			console.log('i='+ i +' index='+ that.waypointsRenderers[i].index);
//		}
		
	});
	return renderer;
};


CMMRM_Editor.prototype.initViewpoint = function() {
	var waypointsString = this.routeModel.getWaypointsString();
	if (0 == this.routeModel.getLocations().length && (!waypointsString || 0 == waypointsString.length)) {
		this.routeRenderer.widget.map.map.panTo(new google.maps.LatLng(CMMRM_Editor_Settings.defaultLat, CMMRM_Editor_Settings.defaultLong));
		this.routeRenderer.widget.map.map.setZoom(parseInt(CMMRM_Editor_Settings.defaultZoom));
	}
};


CMMRM_Editor.prototype.initPolylinesDivision = function() {
	if (this.routeModel.getWaypointsCoords().length < CMMRM_Map_Settings.editorWaypointsLimit) {
		var that = this;
		jQuery(this.routeRenderer).bind('RouteRenderer:trailRequestSuccess', function(ev, data) {
			that.setPolylinesDivisionListeners();
		});
	}
};

/**
 * Divide polyline
 */
CMMRM_Editor.prototype.setPolylinesDivisionListeners = function() {
	var that = this;
	for (var i=0; i<this.routeRenderer.polylines.length; i++) {
		var p = this.routeRenderer.polylines[i];
		p.addListener('click', function(ev) {
			console.log('legIndex = '+ this.legIndex);
			that.createWaypoint(ev.latLng, this.legIndex+1);
//			var location = {name: "Waypoint", lat: ev.latLng.lat(), long: ev.latLng.lng(), id: null, type: 'waypoint'};
//			mapObj.addLocation(location, legIndex+1);
//			mapObj.requestTrail();
		});
	}
};


CMMRM_Editor.prototype.initSortableLocations = function() {
	
	var $ = jQuery;
	$('#cmmrm-editor-locations .cmmrm-locations-list', this.getWidgetElement).sortable({
		update: function(event, ui) {
//			var obj = $(ui.item[0]);
//			var index = mapObj.getLocationIndexByItem(ui.item[0]);
//			var newIndex = obj.index()-1;
//			console.log('index '+ index +' new '+ newIndex);
//			if (index != newIndex) {
//				var location = mapObj.locations.splice(index, 1)[0];
//				mapObj.locations.splice(newIndex, 0, location);
//				mapObj.requestTrail();
//			}
		}
	});
	
};


CMMRM_Editor.prototype.updateDistance = function(val) {
	jQuery(this.getWidgetElement()).find('input[name=distance]').val(val);
	this.blockRouteParams.updateDistance(val);
	return this;
};

CMMRM_Editor.prototype.updateDuration = function(val, dontUpdateSpeed) {
	jQuery(this.getWidgetElement()).find('input[name=duration]').val(Math.round(val));
	this.blockRouteParams.updateDuration(val, dontUpdateSpeed);
	return this;
};


CMMRM_Editor.prototype.updateAvgSpeed = function(val) {
	jQuery(this.getWidgetElement()).find('input[name="avg-speed"]').val(val);
	var withDurationUpdate = true;
	this.blockRouteParams.updateAvgSpeed(val);
//	var newDuration = this.blockRouteParams.distance / val;
//	var dontUpdateSpeed = true;
//	this.updateDuration(newDuration, dontUpdateSpeed);
	return this;
};

CMMRM_Editor.prototype.updateMaxElevation = function(val) {
	jQuery(this.getWidgetElement()).find('input[name=max-elevation]').val(val);
	this.blockRouteParams.updateMaxElevation(val);
	return this;
};

CMMRM_Editor.prototype.updateMinElevation = function(val) {
	jQuery(this.getWidgetElement()).find('input[name=min-elevation]').val(val);
	this.blockRouteParams.updateMinElevation(val);
	return this;
};

CMMRM_Editor.prototype.updateElevationGain = function(val) {
	jQuery(this.getWidgetElement()).find('input[name=elevation-gain]').val(val);
	this.blockRouteParams.updateElevationGain(val);
	return this;
};

CMMRM_Editor.prototype.updateElevationDescent = function(val) {
	jQuery(this.getWidgetElement()).find('input[name=elevation-descent]').val(val);
	this.blockRouteParams.updateElevationDescent(val);
	return this;
};


CMMRM_Editor.prototype.initParamsEditor = function() {
	
	var $ = jQuery;
	var that = this;
	
	// Change route params
	$('.cmmrm-route-params li', this.getWidgetElement()).each(function() {
		var item = $(this);
		var label = item.find('strong').text();
		var name = item[0].className.replace('cmmrm-editable', '').replace('cmmrm-route-', '').replace('cmmrm-', '').replace(/\s/, '');
		if (name == 'duration') label += ' (format: 1 h 20 min 30 s)';
		item.addClass('cmmrm-editable');
		item.attr('title', 'Edit');
		item.click(function(ev) {
			
			var input = item.parents('form').find('input[name='+ name +']');
			var promptValue = input.val();
			if (name == 'duration') promptValue = CMMRM_BlockRouteParams.prototype.getDurationLabel(promptValue);
			else if (name == 'avg-speed') promptValue = CMMRM_BlockRouteParams.prototype.getSpeedLabel(promptValue);
			else if (name == 'distance') promptValue = CMMRM_BlockRouteParams.prototype.getDistanceLabel(promptValue);
			else promptValue = Math.round(promptValue);
			
			var val = window.prompt(label, promptValue);
			if (val !== false && val !== null) {
				switch (name) {
					case 'distance':
						that.updateDistance(that.parseDistance(val));
						break;
					case 'duration':
						var dontUpdateSpeed = true;
						that.updateDuration(that.parseDuration(val), dontUpdateSpeed);
						break;
					case 'max-elevation':
						val = parseInt(val);
						if (isNaN(val)) return;
						that.updateMaxElevation(val);
						break;
					case 'min-elevation':
						val = parseInt(val);
						if (isNaN(val)) return;
						that.updateMinElevation(val);
						break;
					case 'elevation-gain':
						val = parseInt(val);
						if (isNaN(val)) return;
						that.updateElevationGain(val);
						break;
					case 'elevation-descent':
						val = parseInt(val);
						if (isNaN(val)) return;
						that.updateElevationDescent(val);
						break;
					case 'avg-speed':
						that.updateAvgSpeed(that.parseSpeed(val));
						break;
				}
			}
		});
	});
	
};

CMMRM_Editor.prototype.parseDuration = function(val) {
	val = val.replace(/[^0-9hms]/g, '').match(/([0-9]+h)?([0-9]+m)?([0-9]+s)?/);
	for (var i=1; i<=3; i++) {
		val[i] = parseInt(val[i]);
		if (isNaN(val[i])) val[i] = 0;
	}
//	console.log(val);
	return val[1] * 3600 + val[2] * 60 + val[3];
};


CMMRM_Editor.prototype.parseDistance = function(val) {
	var value = val.match(/[0-9]+/);
	var unit = val.match(/[a-z]+/);
	if (unit == 'm') {
		return value;
	}
	else if (unit == 'km') {
		return value * 1000;
	}
	else if (unit == 'mi' || unit == 'mil' || unit == 'mile' || unit == 'miles') {
		// convert miles to meters
		return value * CMMRM_Map_Settings.feetToMeter * CMMRM_Map_Settings.feetInMile;
	} else {
		// Unknown unit
		return value;
	}
};


/**
 * Returns speed in m/s
 */
CMMRM_Editor.prototype.parseSpeed = function(val) {
	console.log('parseSpeed', val)
	var value = parseInt(val.replace(/[^0-9]/g, ''));
	
	if (val.match('mph')) {
//		meterPerSec/CMMRM_Map_Settings.feetToMeter/CMMRM_Map_Settings.feetInMile*3600
		// convert mph to m/s
		return value * CMMRM_Map_Settings.feetToMeter * CMMRM_Map_Settings.feetInMile / 3600;
	}
	
	var units = val.match(/(km|m)\/(h|min|s)/);
//	console.log(units);
	
	var lengthUnit = 'm';
	if (units && typeof units[1] != 'undefined') lengthUnit = units[1];
	
	var timeUnit = 's';
	if (units && typeof units[2] != 'undefined') timeUnit = units[2];
	
	var lengthMultipler = 1;
	if (lengthUnit == 'km') lengthMultipler = 1000;
	
	var timeMultipler = 1;
	if (timeUnit == 'min') timeMultipler = 60;
	else if (timeUnit == 'h') timeMultipler = 60*60;
	
	var result = value * lengthMultipler / timeMultipler;
//	console.log(value, lengthMultipler, timeMultipler, result);
	return result;
	
};

CMMRM_Editor.prototype.createLocationClusterer = function() {
	// don't
};
