Number.prototype.toRadians = function() {
   return this * Math.PI / 180;
}

Number.prototype.toDegrees = function() {
	   return this * 180 /  Math.PI;
}


function CMMRM_Map(mapId, locations) {
	
	this.mapId = mapId;
	this.map = new google.maps.Map(this.mapElement);
	this.map.setMapTypeId(CMMRM_Map_Settings.mapType);
	this.locations = [];
	this.directionsService = this.createDirectionsService();
	this.directionsDisplay = this.createDirectionsRenderer();
	this.elevationService = new google.maps.ElevationService();
	this.trailPolylines = [];
	this.trailResponse = null;
	this.totalDistance = 0;
	this.totalDuration = 0;
	this.travelMode = google.maps.TravelMode.WALKING;
	this.maxElevation = 0;
	this.minElevation = 0;
	this.elevationGain = 0;
	this.elevationDescent = 0;
	this.requestsCount = 0;
	this.geocoder = new google.maps.Geocoder;
	
	// Add locations
	for (var i=0; i<locations.length; i++) {
		this.addLocation(locations[i]);
	}
	
	this.createViewPointBound();
	
	var mapObj = this;
	this.mapElement.mapObj = this;
	
	
	var $ = jQuery;
	
	$('.cmmrm-map-center-btn', this.containerElement).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		mapObj.center();
	});
	
	$('.cmmrm-route-travel-mode a', this.containerElement).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var obj = $(this);
		obj.parents('.cmmrm-route-travel-mode').find('.current').removeClass('current');
		obj.addClass('current');
		mapObj.travelMode = obj.data('mode');
		mapObj.requestTrail();
	});
	
};


CMMRM_Map.prototype.createDirectionsService = function() {
	return new google.maps.DirectionsService;
};


CMMRM_Map.prototype.createDirectionsRenderer = function() {
	var directionsDisplay = new google.maps.DirectionsRenderer;
	directionsDisplay.setMap(this.map);
	directionsDisplay.setOptions({suppressMarkers: true, preserveViewport: true, suppressBicyclingLayer: true, draggable: false});
	return directionsDisplay;
};


CMMRM_Map.prototype.createViewPointBound = function() {
	var map = this;
	this.bounds = new google.maps.LatLngBounds ();
	for (var i=0; i<this.locations.length; i++) {
		var location = this.locations[i];
		// Extend by start point
		this.bounds.extend(new google.maps.LatLng(this.locations[i].lat, this.locations[i].long));
		// Extend by overview path
		if (this.containerElement.data('showParamOverviewPath') == 1) {
			if (location.path) {
//				console.log(location.path);
				var bounds = this.bounds;
				var decoded = google.maps.geometry.encoding.decodePath(location.path);
//				console.log(decoded.length);
				google.maps.geometry.encoding.decodePath(location.path).forEach(function(e) {
					bounds.extend(e);
				});
				setTimeout(function() {
					map.center();
				}, 1000);
			}
		} else {
			map.center();
		}
	}
};


CMMRM_Map.prototype.addLocation = function(location, index) {
//	location.marker = this.createMarker(location);
	if (location.type == 'location') { // Location
		location.marker = this.createMarker(location);
	} else { // Waypoint
		location.marker = this.createWaypointMarker(location);
	}
	this.pushLocation(location, index);
};



CMMRM_Map.prototype.pushLocation = function(location, index) {
	if (typeof index == 'undefined') {
		this.locations.push(location);
	} else {
		this.locations.splice(index, 0, location);
	}
};


CMMRM_Map.prototype.addWaypoint = function(lat, lng, index) {
	if (typeof index == 'undefined') {
		this.routeData.waypoints.push([lat, lng]);
	} else {
		this.routeData.waypoints.splice(index, 0, [lat, lng]);
	}
};


CMMRM_Map.prototype.requestLocationWeather = function(location) {
	if (!CMMRM_Map_Settings.openweathermapAppKey) return;
	var units = ('temp_f' == CMMRM_Map_Settings.temperatureUnits ? 'imperial' : 'metric');
	var url = '//api.openweathermap.org/data/2.5/weather?APPID='+ encodeURIComponent(CMMRM_Map_Settings.openweathermapAppKey)
				+'&lat='+ encodeURIComponent(location.lat) + '&lon=' + encodeURIComponent(location.long) + '&units=' + encodeURIComponent(units);
	this.pushRequest(url, function(response) {
//		console.log(response);
		if (200 == response.cod) {
			var iconUrl = 'http://openweathermap.org/img/w/'+ response.weather[0].icon +'.png';
			var container = location.container.find('.cmmrm-weather');
			var tempUnit = ('temp_f' == CMMRM_Map_Settings.temperatureUnits ? 'F' : 'C');
			container.attr('href', 'http://openweathermap.org/city/' + response.id);
			container.append(jQuery('<img/>', {src: iconUrl}));
			container.append(jQuery('<div/>', {"class" : "cmmrmr-weather-temperature"}).html(Math.round(response.main.temp) + "&deg;"+ tempUnit));
			container.append(jQuery('<div/>', {"class" : "cmmrmr-weather-pressure"}).html(Math.round(response.main.pressure) + " hPa"));
		}
	});
};


CMMRM_Map.prototype.pushRequest = function(url, callback) {
	var callbackName = 'cmmrm_callback_' + Math.floor(Math.random()*99999999);
	window[callbackName] = callback;
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = url + '&callback=' + callbackName;
	document.getElementsByTagName('body')[0].appendChild(script);
};


CMMRM_Map.prototype.createMarker = function(location) {
	
	return new CMMRM_Marker(this, new google.maps.LatLng(location.lat, location.long),
			   {draggable: false, style: 'cursor:pointer;', icon: location.icon},
			   {text: location.name, style: 'cursor:pointer;'}
			 );
	
	var marker = new MarkerWithLabel({
		   position: new google.maps.LatLng(location.lat, location.long),
		   draggable: false,
//		   raiseOnDrag: true,
		   map: this.map,
		   cursor: 'pointer',
		   labelContent: location.name,
		   labelAnchor: new google.maps.Point(this.getTextWidth(location.name, 10), 0),
		   labelClass: "cmmrm-map-label" // the CSS class for the label
		 });
	
	
	
	return marker;
};


CMMRM_Map.prototype.createWaypointMarker = function(location) {
	
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(location.lat, location.long),
		map: this.map,
		icon: 'https://maps.gstatic.com/mapfiles/dd-via.png',
		draggable: false,
	});
	
	return marker;
	
};


CMMRM_Map.prototype.getLocationIndexByMarker = function(marker) {
	for (var i=0; i<this.locations.length; i++) {
		if (this.locations[i].marker == marker) {
			return i;
		}
	}
	return false;
};


CMMRM_Map.prototype.getLocationIndexByItem = function(item) {
	for (var i=0; i<this.locations.length; i++) {
		if (this.locations[i].item == item) {
			return i;
		}
	}
	return false;
};


CMMRM_Map.prototype.getLocationIndexById = function(id) {
	for (var i=0; i<this.locations.length; i++) {
		if (this.locations[i].id == id) {
			return i;
		}
	}
	return false;
};


CMMRM_Map.prototype.center = function() {
	if (this.locations.length > 0) {
		this.map.fitBounds(this.bounds);
	}
};


CMMRM_Map.prototype.getMapElement = function() {
	return jQuery(this.mapElement);
};


CMMRM_Map.prototype.getTextWidth = function(text, fontSize) {
	var narrow = '1tiIfjJl';
	var wide = 'WODGKXZBM';
	var result = 0;
	for (var i=0; i<text.length; i++) {
		var letter = text.substr(i, 1);
		var rate = 1.0 + (0.5*(wide.indexOf(letter) >= 0 ? 1 : 0)) - (0.5*(narrow.indexOf(letter) >= 0 ? 1 : 0));
//		console.log(letter +' : '+ rate);
		result += rate;
	}
	return result * fontSize*0.7/2;
};



CMMRM_Map.prototype.requestTrail = function() {
	
	this.requestsCount++;
	
	this.removeTrailPolylines();
	this.removeElevationGraph();
	
	if (this.locations.length < 2) return false;
	
	if (this.travelMode == 'DIRECT') {
		this.requestTrailDirect();
		return;
	}
	
	var mapObj = this;
	
	var waypoints = [];
	var routeWaypoints = this.getWaypoints();
	for (var i=1; i<routeWaypoints.length-1; i++) {
		var waypoint = routeWaypoints[i];
		waypoints.push({
			location: new google.maps.LatLng(waypoint[0], waypoint[1]),
			stopover: true,
		});
	}
	
	this.directionsService.route({
		origin: new google.maps.LatLng(this.locations[0].lat, this.locations[0].long),
		destination: new google.maps.LatLng(this.locations[this.locations.length-1].lat, this.locations[this.locations.length-1].long),
		waypoints: waypoints,
		travelMode: mapObj.travelMode,
		optimizeWaypoints: false
	  }, function(response, status) {
		  mapObj.requestTrailCallback(mapObj.travelMode, response, status);
	});
	
};


CMMRM_Map.prototype.getWaypoints = function() {
	return this.routeData.waypoints;
};


CMMRM_Map.prototype.getOverviewPathCoords = function() {
	return google.maps.geometry.encoding.decodePath(this.getOverviewPath());
};


CMMRM_Map.prototype.getOverviewPath = function() {
	return this.routeData.overviewPath;
};


CMMRM_Map.prototype.requestTrailDirect = function() {
	
	var overview_path = [];
	var legs = [];
	var newLeg = {duration: {value: 0}, distance: {value: 0}, steps: [{path: []}]};
	var leg = jQuery.extend(true, {}, newLeg);
	var step = null;
	var lastCoord = null;
	var coords = this.getOverviewPathCoords();
	for (var i=0; i<coords.length; i++) {
		
		var coord = coords[i];
//		var coord = new google.maps.LatLng(location.lat, location.long);
		overview_path.push(coord);
		
		if (lastCoord) {
			var distance = this.calculateDistance(lastCoord, coord);
			leg.distance.value += distance;
			leg.duration.value += distance * (3600/4000);
		}
		
		if (i > 0) {
			leg.steps[0].path.push(coord);
			legs.push(leg);
		}
		leg = jQuery.extend(true, {}, newLeg);
		
		
		
		leg.steps[0].path.push(coord);
		lastCoord = coord;
		
	}
	
	var status = google.maps.DirectionsStatus.OK;
	var response = {
		routes: [{
		   overview_path: overview_path,
		   legs: legs
		}]
	};
	
	this.requestTrailCallback('DIRECT', response, status);
	
};


CMMRM_Map.prototype.requestTrailCallback = function(travelMode, response, status) {
	if (status === google.maps.DirectionsStatus.OK) {
		
		var init = (typeof this.trailResponse != 'object');
		
		this.trailResponse = response;
//		this.directionsDisplay.setDirections(response);
//		console.log(JSON.stringify(response));
//		console.log(response);
//		if (this.trailPolyline) this.trailPolyline.setMap(null);
//		this.trailPolyline = this.createTrailPolyline(response.routes[0].overview_path);
		this.removeTrailPolylines();
		this.trailPolylines = this.createTrailPolylines(response);
		
		this.totalDistance = this.getTrailDistance(response);
		this.updateDistance(this.totalDistance);
		
		if (this.requestsCount > 1 ||  this.shouldRecalculate()) {
			this.totalDuration = this.getTrailDuration(response);
			this.updateDuration(this.totalDuration);
		}
 		
		if (!init) {
//			this.calculateElevationAlongPath(this.getPath(response));
			this.calculateElevationAlongPath(response.routes[0].overview_path);
		}
		
	} else {
		var errorMsg = this.getDirectionErrorMessage(status);
		window.CMMRM.Utils.toast(errorMsg, null, Math.ceil(errorMsg.length/10));
		console.log(status);
		console.log(response);
	}
};


CMMRM_Map.prototype.renderDirectionsSteps = function(travelMode, response) {
	var wrapper = this.containerElement.find('.cmmrm-directions-steps-wrapper');
	var list = wrapper.children('ul');
	var template = list.find('.cmmrm-template').clone();
	list.children('li:not(.cmmrm-template)').remove();
	
	if (travelMode == 'DIRECT') return;
	
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
};


CMMRM_Map.prototype.createTrailPolyline = function(path, legIndex) {
//	console.log(this.pathColor);
	var p = new google.maps.Polyline({
		path: path,
		strokeColor: (this.pathColor ? this.pathColor : '#3377FF'),
		opacity: 0.1,
		map: this.map
	});
	p.legIndex = legIndex;
	return p;
};


CMMRM_Map.prototype.createTrailPolylines = function(response) {
	var result = [];
	var legs = response.routes[0].legs;
	for (var legIndex=0; legIndex<legs.length; legIndex++) {
		var path = [];
		var steps = legs[legIndex].steps;
		for (var j=0; j<steps.length; j++) {
			path = path.concat(steps[j].path);
		}
		result.push(this.createTrailPolyline(path, legIndex));
	}
	return result;
};


CMMRM_Map.prototype.getPath = function(response) {
	var result = [];
	var legs = response.routes[0].legs;
	for (var legIndex=0; legIndex<legs.length; legIndex++) {
		var steps = legs[legIndex].steps;
		for (var j=0; j<steps.length; j++) {
			result = result.concat(steps[j].path);
		}
	}
	return result;
};


CMMRM_Map.prototype.removeTrailPolylines = function() {
	for (var i=0; i<this.trailPolylines.length; i++) {
		this.trailPolylines[i].setMap(null);
	}
	this.trailPolylines = [];
};


CMMRM_Map.prototype.calculateElevation = function() {
//	console.log('calc elev');
	var elevator = new google.maps.ElevationService;
	var path = this.trailResponse.routes[0].overview_path;
	var points = [];
	for (var i=0; i<path.length; i++) {
		points.push(path[i]);
	}
	
	var mapObj = this;
//	console.log('ele = '+ points.length);
	
	elevator.getElevationForLocations({
		'locations': points
	  }, function(results, status) {
		mapObj.calculateElevationCallback(results, status);
	  });
	
};


CMMRM_Map.prototype.calculateElevationAlongPath = function(path) {
	var elevator = new google.maps.ElevationService;
	var mapObj = this;
	var dist = 0;
	if (path.length > 1) dist = this.calculateDistance(path[0], path[path.length-1]);
	var samples = 450; //Math.min(450, Math.max(2, Math.floor(dist/5)));
//	console.log('dist = '+ dist + ' samples = '+ samples);
	
	this.elevationService.getElevationAlongPath({
		'path': path,
		'samples': samples,
	  }, function(results, status) {
//		  console.log(results);
		mapObj.calculateElevationCallback(results, status);
	  });
};


CMMRM_Map.prototype.calculateElevationCallback = function(results, status) {
	if (status === google.maps.ElevationStatus.OK) {
		
		this.showElevationGraph(results);
		
		this.maxElevation = 0;
		this.minElevation = 99999;
		this.elevationGain = 0;
		this.elevationDescent = 0;
		var prev = null;
		for (var i=0; i<results.length; i++) {
			var elevation = results[i].elevation;
			if (elevation > this.maxElevation) {
				this.maxElevation = elevation;
			}
			if (elevation < this.minElevation) {
				this.minElevation = elevation;
			}
//			console.log('elev '+ elevation +' --- '+(elevation-prev));
			if (typeof prev == 'number') {
				if (elevation-prev > 0) {
					this.elevationGain += (elevation-prev);
				} else {
					this.elevationDescent += (prev-elevation);
				}
			}
			prev = elevation;
		}
		
		if (this.requestsCount > 1 || this.shouldRecalculate()) {
			if (this.minElevation == 99999) this.minElevation = 0;
			this.updateMaxElevation(this.maxElevation);
			this.updateMinElevation(this.minElevation);
			this.updateElevationGain(this.elevationGain);
			this.updateElevationDescent(this.elevationDescent);
		}
		
	} else {
	  console.log('Elevation service failed due to: ' + status);
	}
};


CMMRM_Map.prototype.updateMaxElevation = function(maxElevation) {
	this.containerElement.find('.cmmrm-max-elevation span').text(this.getElevationLabel(maxElevation));
};

CMMRM_Map.prototype.updateMinElevation = function(minElevation) {
	this.containerElement.find('.cmmrm-min-elevation span').text(this.getElevationLabel(minElevation));
};


CMMRM_Map.prototype.updateElevationGain = function(elevationGain) {
	this.containerElement.find('.cmmrm-elevation-gain span').text(this.getElevationLabel(elevationGain));
};

CMMRM_Map.prototype.updateElevationDescent = function(elevationDescent) {
	this.containerElement.find('.cmmrm-elevation-descent span').text(this.getElevationLabel(elevationDescent));
};


CMMRM_Map.prototype.getTrailDistance = function(response) {
	var totalDistance = 0;
	var legs = response.routes[0].legs;
	for (var i=0; i<legs.length; ++i) {
		totalDistance += legs[i].distance.value;
	}
	return totalDistance;
};


CMMRM_Map.prototype.getDistanceLabel = function(distanceMeters, useMinorUnits) {
	
	if (typeof useMinorUnits == 'undefined') {
		useMinorUnits = false;
	}
	
	if ('feet' == CMMRM_Map_Settings.lengthUnits) {
		var num = distanceMeters/CMMRM_Map_Settings.feetToMeter;
		if (!useMinorUnits && num > CMMRM_Map_Settings.feetInMile) {
			return Math.round(num/CMMRM_Map_Settings.feetInMile) +' miles';
		} else {
			return Math.floor(num) + ' ft';
		}
	} else {
	
		var dist = distanceMeters;
		var distLabel = '' + Math.round(dist) + ' m';
		if (!useMinorUnits && dist > 2000) {
			distLabel = '' + Math.round(dist/1000) + ' km';
		}
		return distLabel;
		
	}
	
};


CMMRM_Map.prototype.getElevationLabel = function(elev) {
	if ('feet' == CMMRM_Map_Settings.lengthUnits) {
		var num = elev/CMMRM_Map_Settings.feetToMeter;
		return Math.floor(num) + ' ft';
	} else {
		return '' + Math.round(elev) + ' m';
	}
};


CMMRM_Map.prototype.updateDistance = function(dist) {
	var elem = this.containerElement.find('.cmmrm-route-distance span');
	var useMinorUnits = (1 == elem.parents('.cmmrm-route-params').first().data('useMinorLengthUnits'));
	elem.text(this.getDistanceLabel(dist, useMinorUnits));
};


CMMRM_Map.prototype.updateAvgSpeed = function(meterPerSec) {
	this.avgSpeed = meterPerSec;
	var elem = this.containerElement.find('.cmmrm-route-avg-speed span');
	elem.text(this.getSpeedLabel(meterPerSec));
};


CMMRM_Map.prototype.getTrailDuration = function(response) {
	var totalDuration = 0;
	var legs = response.routes[0].legs;
	for (var i=0; i<legs.length; ++i) {
		totalDuration += legs[i].duration.value;
	}
	return totalDuration; // seconds
};


CMMRM_Map.prototype.getSpeedLabel = function(meterPerSec) {
	if ('feet' == CMMRM_Map_Settings.lengthUnits) {
		return '' + Math.round(meterPerSec/CMMRM_Map_Settings.feetToMeter/CMMRM_Map_Settings.feetInMile*3600) + ' mph';
	} else {
		return '' + Math.round(meterPerSec * 3.6) + ' km/h';
	}
};


CMMRM_Map.prototype.getDurationLabel = function(durationSec) {
		
	var durationNumber = Math.ceil(durationSec);
	var durationLabel = '' + durationNumber +' s';
	if (durationNumber > 60) {
		durationNumber /= 60;
		var min = Math.ceil(durationNumber);
		if (min > 0) {
			durationLabel = '' + min + ' min';
		}
	}
	if (durationNumber > 60) {
		durationLabel = '' + Math.floor(durationNumber/60) + ' h '+ Math.floor(durationNumber)%60 +' min';
	}
	return durationLabel;
		
};

CMMRM_Map.prototype.updateDuration = function(duration) {
	this.containerElement.find('.cmmrm-route-duration span').text(this.getDurationLabel(duration));
	this.updateAvgSpeed(duration > 0 ? this.totalDistance/duration : 0);
};



CMMRM_Map.prototype.calculateDistance = function(p1, p2) {
	
	var R = 6371000; // metres
	var k = p1.lat().toRadians();
	var l = p2.lat().toRadians();
	var m = (p2.lat() - p1.lat()).toRadians();
	var n = (p2.lng() - p1.lng()).toRadians();
	
	var a = Math.sin(m/2) * Math.sin(m/2) +
    	Math.cos(k) * Math.cos(l) *
    	Math.sin(n/2) * Math.sin(n/2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	
	return R * c;
	
};


CMMRM_Map.prototype.calculateMidpoint = function(p1, p2) {
	
	var lat1 = p1.lat().toRadians();
	var lon1 = p1.lng().toRadians();
	var lat2 = p2.lat().toRadians();
	var lon2 = p2.lng().toRadians();
	
	var bx = Math.cos(lat2) * Math.cos(lon2 - lon1);
	var by = Math.cos(lat2) * Math.sin(lon2 - lon1);
	var lat3 = Math.atan2(Math.sin(lat1) + Math.sin(lat2), Math.sqrt((Math.cos(lat1) + bx) * (Math.cos(lat1) + bx) + by*by));
	var lon3 = lon1 + Math.atan2(by, Math.cos(lat1) + Bx);
	
	return new google.maps.LatLng(lat3.toDegrees(), lon3.toDegrees());
	
};


CMMRM_Map.prototype.calculateMidpoints = function(p1, p2, maxDist) {
	var dist = this.calculateDistance(p1, p2);
	if (dist <= maxDist) return [];
	var num = dist / maxDist;
	
	
	
};


CMMRM_Map.prototype.showElevationGraph = function(elevations) {
//	console.log('showElevationGraph');
	var graphDiv = this.containerElement.find('.cmmrm-elevation-graph');
	if (graphDiv.length == 0 || typeof google == 'undefined' || typeof google.visualization == 'undefined' || typeof google.visualization.ColumnChart == 'undefined') {
		return;
	}
	var graph = new google.visualization.ColumnChart(graphDiv[0]);
	var data = new google.visualization.DataTable();
	var unit = ('feet' == CMMRM_Map_Settings.lengthUnits ? 'ft' : 'm');
	data.addColumn('string', 'Sample');
	data.addColumn('number', 'Elevation');
	for (var i = 0; i < elevations.length; i++) {
		var num = elevations[i].elevation / (unit == 'ft' ? CMMRM_Map_Settings.feetToMeter : 1);
		data.addRow(['', num]);
	}
	graph.draw(data, {
	    height: 150,
	    legend: 'none',
	    titleY: 'Elevation ('+ unit +')'
	  });
	
	var marker = new google.maps.Marker({
//		position: new google.maps.LatLng(location.lat, location.long),
//		map: this.map,
		icon: 'https://maps.gstatic.com/mapfiles/dd-via.png',
		draggable: false,
	});
	
	var mapObj = this.map;
	google.visualization.events.addListener(graph, 'onmouseover', function(ev) {
		if (typeof elevations[ev.row] != 'undefined') {
			marker.setMap(mapObj);
			marker.setPosition(elevations[ev.row].location);
		}
	});
	
	graphDiv.mouseout(function() {
		marker.setMap(null);
	});
	
};


CMMRM_Map.prototype.removeElevationGraph = function() {
	this.containerElement.find('.cmmrm-elevation-graph').html('');
};


CMMRM_Map.prototype.parseDuration = function(val) {
	val = val.replace(/[^0-9hms]/g, '').match(/([0-9]+h)?([0-9]+m)?([0-9]+s)?/);
	for (var i=1; i<=3; i++) {
		val[i] = parseInt(val[i]);
		if (isNaN(val[i])) val[i] = 0;
	}
	console.log(val);
	return val[1] * 3600 + val[2] * 60 + val[3];
};


CMMRM_Map.prototype.findAddress = function(pos, successCallback) {
	this.geocoder.geocode({'location': pos}, function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			
			var findPostalCode = function(results) {
				for (var j=0; j<results.length; j++) {
					var address = results[j];
					var components = address.address_components;
//					console.log(components);
					for (var i=0; i<components.length; i++) {
						var component = components[i];
						if (component.types[0]=="postal_code"){
					        return component.short_name;
					    }
					}
				}
				return "";
			};
			
			if (results.length > 0) {
				var address = results[0];
				successCallback({
					results: results,
					postal_code: findPostalCode(results),
					formatted_address: address.formatted_address,
				});
			}
		}
	});
};


CMMRM_Map.prototype.shouldRecalculate = function() {
	return (location.search.indexOf('recalculate=1') >= 0);
};



CMMRM_Map.prototype.geolocationGetPosition = function(callback, errorCallback, highAccuracy) {
	if ("geolocation" in navigator) {
		if (typeof highAccuracy != 'boolean') highAccuracy = true;
		var geo_options = {
				  enableHighAccuracy: highAccuracy, 
				  maximumAge        : 60, 
				  timeout           : 600
				};
		errorCallback = function(err) {
			console.log(err);
			window.CMMRM.Utils.toast('Geolocation error: [' + err.code + '] ' + err.message, null, Math.ceil(err.message.length/5));
		};
		return navigator.geolocation.getCurrentPosition(callback, errorCallback, geo_options);
	}
};


CMMRM_Map.prototype.geolocationWatchPosition = function(callback, errorCallback, highAccuracy) {
	if ("geolocation" in navigator) {
		if (typeof highAccuracy != 'boolean') highAccuracy = true;
		var geo_options = {
		  enableHighAccuracy: highAccuracy, 
		  maximumAge        : 60, 
		  timeout           : 600
		};
		errorCallback = function(err) {
			console.log(err);
			window.CMMRM.Utils.toast('Geolocation error: [' + err.code + '] ' + err.message, null, Math.ceil(err.message.length/5));
		};
		return navigator.geolocation.watchPosition(callback, errorCallback, geo_options);
	}
};


CMMRM_Map.prototype.showUserPositionMarker = function(lat, long) {
	var pos = new google.maps.LatLng(lat, long);
	if (!this.userPositionMarker) {
		this.userPositionMarker = new CMMRM_Marker(this, pos,
		   {draggable: false, style: 'padding-top:20px;padding-right:20px;', icon: CMMRM_Map_Settings.geolocationIcon},
		   {text: '', style: ''}
		 );
	} else {
		this.userPositionMarker.setPosition(pos);
	}
};


CMMRM_Map.prototype.getDirectionErrorMessage = function(status) {
	switch (status) {
	case google.maps.DirectionsStatus.INVALID_REQUEST:
		return 'Invalid request';
	case google.maps.DirectionsStatus.MAX_WAYPOINTS_EXCEEDED:
		return 'This website has reached the limit of the Google Maps API waypoints number per directions request. '
				+ 'The total allowed waypoints is 8, plus the origin and destination. '
				+ 'Maps API for Work customers are allowed 23 waypoints, plus the origin, and destination.';
	case google.maps.DirectionsStatus.NOT_FOUND:
		return 'At least one of the origin, destination, or waypoints could not be geocoded.';
	case google.maps.DirectionsStatus.OVER_QUERY_LIMIT:
		return 'This website has gone over the requests limit in too short a period of time.';
	case google.maps.DirectionsStatus.REQUEST_DENIED:
		return 'The webpage is not allowed to use the directions service.';
	case google.maps.DirectionsStatus.ZERO_RESULTS:
		return 'No route could be found between the origin and destination.';
	case google.maps.DirectionsStatus.UNKNOWN_ERROR:
	default:
		return 'A directions request could not be processed due to a server error. The request may succeed if you try again.';
	}
};



CMMRM_Map.prototype.updateOverviewPath = function() {
	var waypoints = this.getWaypoints();
	if (google && google.maps && google.maps.geometry && google.maps.geometry.encoding) {
		var coords = [];
		for (var i=0; i<waypoints.length; i++) {
			coords.push(new google.maps.LatLng(waypoints[i][0], waypoints[i][1]));
		}
//		console.log(coords);
		var path =  google.maps.geometry.encoding.encodePath(coords);
//		console.log(path);
		this.routeData.overviewPath = path;
	}
};


