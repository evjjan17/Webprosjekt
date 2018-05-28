function CMMRM_RequestTrail(travelMode, waypointsCoords) {
	this.travelMode = travelMode;
	this.waypointsCoords = waypointsCoords;
	this.response = null;
	this.status = null;
}


CMMRM_RequestTrail.prototype.run = function(mapRenderer, callback) {
	if ('DIRECT' == this.travelMode) {
		this.requestTrailDirect(mapRenderer, callback);
	} else {
		this.requestTrailGoogle(callback);
	}
};


CMMRM_RequestTrail.prototype.getDistance = function() {
	var totalDistance = 0;
	var legs = this.response.routes[0].legs;
	for (var i=0; i<legs.length; ++i) {
		totalDistance += legs[i].distance.value;
	}
	return totalDistance;
};


CMMRM_RequestTrail.prototype.getResponse = function() {
	return this.response;
};

CMMRM_RequestTrail.prototype.requestTrailGoogle = function(callback) {
	var that = this;
	var requestWaypoints = [];
	var coords = this.waypointsCoords;
//	console.log(coords);
	for (var i=1; i<coords.length-1; i++) {
		requestWaypoints.push({
			location: coords[i],
			stopover: true,
		});
	}
	
	var directionsService = new google.maps.DirectionsService();
	var origin = coords[0];
	var destination = coords[coords.length-1];
	
	directionsService.route({
		origin: origin,
		destination: destination,
		waypoints: requestWaypoints,
		travelMode: this.travelMode,
		optimizeWaypoints: false
	  }, function(response, status) {
		  that.response = response;
		  that.status = status;
		  callback(response, status);
//		  that.requestTrailCallback(response, status);
	});
	
};


CMMRM_RequestTrail.prototype.requestTrailDirect = function(mapRenderer, callback) {
	
	var overview_path = [];
	var legs = [];
	var newLeg = {duration: {value: 0}, distance: {value: 0}, steps: [{path: []}]};
	var leg = jQuery.extend(true, {}, newLeg);
	var step = null;
	var lastCoord = null;
	var coords = this.waypointsCoords;
//	console.log(coords);
	for (var i=0; i<coords.length; i++) {
		
		var coord = coords[i];
//		var coord = new google.maps.LatLng(location.lat, location.long);
		overview_path.push(coord);
		
		if (lastCoord) {
			var distance = CMMRM_GoogleMap.prototype.calculateDistance(lastCoord, coord);
			leg.distance.value += distance;
			leg.duration.value += distance * (3600/4000);
			leg.duration.text = leg.duration.value.toString();
		}
		
		if (i > 0) {
			leg.steps[0].path.push(coord);
			leg.steps[0].distance = {text: distance.toString(), value: distance};
			if (coords.length < CMMRM_Map_Settings.editorWaypointsLimit) {
				// Created new legs only if it's simple trail to avoid too many legs for the imported GPX files
				legs.push(leg);
			}
		}
		if (coords.length < CMMRM_Map_Settings.editorWaypointsLimit) {
			// Created new legs only if it's simple trail to avoid too many legs for the imported GPX files
			leg = jQuery.extend(true, {}, newLeg); // commented to avoid too many legs for imported files
		}
		
		leg.steps[0].path.push(coord);
		lastCoord = coord;
		
	}
	
	legs.push(leg); // added to create only one leg for direct travel mode
	
	this.status = google.maps.DirectionsStatus.OK;
	this.response = {
		routes: [{
		   overview_path: overview_path,
		   overview_polyline: google.maps.geometry.encoding.encodePath(overview_path),
		   legs: legs
		}]
	};
	
	callback(this.response, this.status);
//	this.requestTrailCallback('DIRECT', response, status);
	
};



CMMRM_RequestTrail.prototype.createTrailPolylines = function(map, color, showDirectionalArrows) {
	var result = [];
	var legs = this.response.routes[0].legs;
	for (var legIndex=0; legIndex<legs.length; legIndex++) {
		var path = [];
		var steps = legs[legIndex].steps;
		for (var j=0; j<steps.length; j++) {
			path = path.concat(steps[j].path);
		}
		result.push(this.createTrailPolyline(path, legIndex, map, color, showDirectionalArrows));
	}
	return result;
};



CMMRM_RequestTrail.prototype.createTrailPolyline = function(path, legIndex, map, color, showDirectionalArrows) {
//	console.log(this.pathColor);
	
	var icons = [];
	if (showDirectionalArrows) {
		var icon = {
          icon: {
                  path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                  strokeColor:color,
                  strokeOpacity: 0,
                  fillColor: color,
                  fillOpacity: 1,
                  offset: '0'
                },
          repeat:'100px',
          path:[]
        };
		icons.push(icon);
	}
	
	var p = new google.maps.Polyline({
		path: path,
		strokeColor: color,
		opacity: 0.1,
		map: map,
		icons: icons
	});
	p.legIndex = legIndex;
	return p;
};


CMMRM_RequestTrail.prototype.getDirectionErrorMessage = function(status) {
	switch (status) {
	case google.maps.DirectionsStatus.INVALID_REQUEST:
		return 'Invalid request';
	case google.maps.DirectionsStatus.MAX_WAYPOINTS_EXCEEDED:
		return 'This website has reached the limit of the Google Maps API waypoints number per directions request. '
				+ 'The total allowed waypoints is 23, plus the origin and destination. '
				+ 'Please use the Direct Travel Mode if using more waypoints.';
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


CMMRM_RequestTrail.prototype.getDuration = function() {
	var totalDuration = 0;
	var legs = this.response.routes[0].legs;
	for (var i=0; i<legs.length; ++i) {
		totalDuration += legs[i].duration.value;
	}
	return totalDuration; // seconds
};


CMMRM_RequestTrail.prototype.getOverviewPath = function() {
	if (typeof this.response.routes == 'object' && this.response.routes.length > 0) {
		return this.response.routes[0].overview_path;
	} else {
		return '';
	}
};

CMMRM_RequestTrail.prototype.getOverviewPolyline = function() {
	if (typeof this.response.routes == 'object' && this.response.routes.length > 0) {
		return this.response.routes[0].overview_polyline;
	} else {
		return '';
	}
};
