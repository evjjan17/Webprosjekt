function CMMRM_RouteModel(data, waypointsString, locations) {
	
//	console.log(data);
//	console.log('waypointsString', waypointsString);
	
	this.data = data;
	
	this.waypointsString = (typeof waypointsString == 'string' ? waypointsString : '');
	this.waypointsCoords = [];
	this.waypoints = [];
	this.decodeWaypoints();
	
	this.locations = [];
	this.addLocations(locations);
	
}

CMMRM_RouteModel.prototype.addLocations = function(locations) {
	for (var i=0; i<locations.length; i++) {
		this.addLocation(locations[i]);
	}
};

CMMRM_RouteModel.prototype.addLocation = function(locationData) {
	var locationModel = new CMMRM_LocationModel(locationData, this);
	var that = this;
	this.locations.push(locationModel);
	jQuery(this).trigger('RouteModel:addLocation', {locationData: locationData, locationModel: locationModel});
	jQuery(locationModel).bind('LocationModel:remove', function() {
		that.removeLocationByModel(this);
	});
	return locationModel;
};


CMMRM_RouteModel.prototype.removeLocationByIndex = function(index) {
	var removed = this.locations.splice(index, 1);
	return (removed.length == 1);
};


CMMRM_RouteModel.prototype.removeLocationByModel = function(obj) {
	for (var i=0; i<this.locations.length; i++) {
		if (this.locations[i] === obj) {
			return this.removeLocationByIndex(i);
		}
	}
	return false;
};


CMMRM_RouteModel.prototype.addWaypoints = function(waypoints) {
	var that = this;
	for (var i=0; i<waypoints.length; i++) {
		this.addWaypoint(waypoints[i]);
	}
	return this;
};


CMMRM_RouteModel.prototype.removeWaypointByIndex = function(index) {
	var removed = this.waypointsCoords.splice(index, 1);
	return (removed.length == 1);
};


CMMRM_RouteModel.prototype.removeWaypointByModel = function(obj) {
	for (var i=0; i<this.waypoints.length; i++) {
		if (this.waypoints[i] === obj) {
			return this.removeWaypointByIndex(i);
		}
	}
	return false;
};


CMMRM_RouteModel.prototype.decodeWaypoints = function() {
	if (typeof this.waypointsString == 'string') {
		this.waypointsCoords = google.maps.geometry.encoding.decodePath(this.waypointsString);
	} else {
		this.waypointsCoords = [];
	}
	return this;
//	return this.addWaypoints(coords);
};

CMMRM_RouteModel.prototype.addWaypoint = function(waypointData, index) {
	if (Object.prototype.toString.call(waypointData) == '[object Array]') {
//		waypointData = [waypointData.lat(), waypointData.lng()];
		waypointData = new google.maps.LatLng(waypointData[0], waypointData[1]);
	}
	
	if (typeof index == 'undefined') {
		index = this.waypointsCoords.length;
		this.waypointsCoords.push(waypointData);
	} else {
		this.waypointsCoords.splice(index, 0, waypointData);
	}
	
	jQuery(this).trigger('RouteModel:addWaypoint', {waypointData: waypointData});
//	google.maps.event.addListener(waypointData, 'WaypointModel:remove', function() {
//		that.removeWaypointByIndex(index);
//	});
	
	return this;
	
//	var that = this;
//	var waypointModel = new CMMRM_WaypointModel(waypointData, this);
//	
//	if (typeof index == 'undefined') this.waypoints.push(waypointModel);
//	else this.waypoints.splice(index, 0, waypointModel);
//	
//	jQuery(this).trigger('RouteModel:addWaypoint', {waypointData: waypointData, waypointModel: waypointModel});
//	jQuery(waypointModel).bind('WaypointModel:remove', function() {
////		console.log('CCCCCCCCC')
//		that.removeWaypointByModel(this);
//	});
//	return waypointModel;
};


CMMRM_RouteModel.prototype.getTravelMode = function() {
	return this.data.travelMode;
};

CMMRM_RouteModel.prototype.setTravelMode = function(mode) {
	var old = this.data.travelMode;
	this.data.travelMode = mode;
	jQuery(this).trigger('RouteModel:setTravelMode', {old: old, travelMode: mode});
	return this;
};

CMMRM_RouteModel.prototype.getLocations = function() {
	return this.locations;
};

CMMRM_RouteModel.prototype.getWaypoints = function() {
	return this.waypoints;
};


CMMRM_RouteModel.prototype.getWaypointsString = function() {
	return this.waypointsString;
};


CMMRM_RouteModel.prototype.setWaypointsString = function(val) {
	var old = this.waypointsString;
	this.waypointsString = val;
	jQuery(this).trigger('RouteModel:setWaypointsString', {old: old, waypointsString: val});
	return this;
};

CMMRM_RouteModel.prototype.getWaypointsCoords = function() {
	return this.waypointsCoords;
//	var coords = [];
//	for (var i=0; i<this.waypoints.length; i++) {
//		coords.push([this.waypoints[i].getLat(), this.waypoints[i].getLng()]);
//	}
//	return coords;
};


CMMRM_RouteModel.prototype.getWaypointsGoogleLatLng = function() {
	var coords = [];
	for (var i=0; i<this.waypoints.length; i++) {
		coords.push(this.waypoints[i].getGoogleLatLng());
	}
	return coords;
};

CMMRM_RouteModel.prototype.getPolylineString = function() {
	return this.data.overviewPath;
};


CMMRM_RouteModel.prototype.setPolylineString = function(str) {
	var old = this.data.overviewPath;
	this.data.overviewPath = str;
	jQuery(this).trigger('RouteModel:setPolylineString', {old: old, polylineString: str});
	return this;
};


CMMRM_RouteModel.prototype.getPolylineCoords = function() {
	var str = this.getPolylineString();
	if (str) {
		return google.maps.geometry.encoding.decodePath(str);
	} else {
		return [];
	}
};


CMMRM_RouteModel.prototype.getPathColor = function() {
	return (this.data.pathColor ? this.data.pathColor : '#3377FF');
};

CMMRM_RouteModel.prototype.showDirectionalArrows = function() {
	return this.data.showDirectionalArrows;
};


CMMRM_RouteModel.prototype.getIcon = function() {
	return null;
};


CMMRM_RouteModel.prototype.getName = function() {
	return this.data.name;
};


CMMRM_RouteModel.prototype.getBounds = function() {
	var coords = [];
	
	// Add waypoints
//	var waypoints = this.getWaypoints();
//	for (var i=0; i<waypoints.length; i++) {
//		coords.push(waypoints[i].getPosition());
//	}
	
	// Add locations
	var locations = this.getLocations();
	for (var i=0; i<locations.length; i++) {
		coords.push(locations[i].getPosition());
	}
	
	// Add polyline
	var polyline = this.getPolylineCoords();
	for (var i=0; i<polyline.length; i++) {
		coords.push(polyline[i]);
	}
	
	// Add default lat lng
	var latLng = this.getGoogleLatLng();
	if (latLng) {
		coords.push(latLng);
	}
	
	return coords;
};


CMMRM_RouteModel.prototype.setRouteParam = function(name, val) {
	var old = this.data[name];
	this.data[name] = val;
	jQuery(this).trigger('RouteModel:setRouteParam', {name: name, old: old, value: val});
	return this;
};

CMMRM_RouteModel.prototype.getRouteParam = function(name) {
	return this.data[name];
};

CMMRM_RouteModel.prototype.updateWaypointsString = function() {
//	var waypoints = this.getWaypointsCoords();
	if (google && google.maps && google.maps.geometry && google.maps.geometry.encoding) {
		var coords = this.getWaypointsCoords();
//		for (var i=0; i<waypoints.length; i++) {
//			coords.push(new google.maps.LatLng(waypoints[i].getLat(), waypoints[i].getLng()));
//		}
//		console.log(coords);
		var path =  google.maps.geometry.encoding.encodePath(coords);
//		console.log(path);
		this.setWaypointsString(path);
	}
};


CMMRM_RouteModel.prototype.getGoogleLatLng = function() {
//	console.log(this.data.lat, this.data.long);
	if (this.data.lat && this.data.long) {
		return new google.maps.LatLng(this.data.lat, this.data.long);
	}
};
