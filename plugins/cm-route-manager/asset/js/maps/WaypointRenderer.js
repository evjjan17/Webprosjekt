function CMMRM_WaypointRenderer(widget, coords, index) {
	
	this.index = index;
	this.widget = widget;
	this.coords = coords;
	this.marker = this.createMarker();
	
	var that = this;
	
	google.maps.event.addListener(this.marker, 'dragend', function() {
		var pos = that.marker.getPosition();
		that.updatePosition(pos);
//	    that.waypointModel.setPosition(pos.lat(), pos.lng());
	});
	
	google.maps.event.addListener(this.marker, 'click', function(ev) {
		that.remove();
	});
	
//	google.maps.event.addListener(this.marker, 'rightclick', function(ev) {
//		var pos = that.marker.getPosition();
//		that.widget.addLocation(pos.lat(), pos.lng());
//		that.waypointModel.remove();
//	});
	
	jQuery(this.waypointModel).bind('WaypointModel:remove', function() {
		that.remove();
		that.widget.routeModel.updateWaypointsString();
		that.widget.routeRenderer.renderPolylines();
	});
	
//	this.widget.routeRenderer.renderPolylines();
	
	jQuery(this).trigger('WaypointRenderer:ready');
	
}


CMMRM_WaypointRenderer.prototype.createMarker = function() {
	var markerImage = new google.maps.MarkerImage('https://maps.gstatic.com/mapfiles/dd-via.png',
		    new google.maps.Size(11, 11), //size
		    new google.maps.Point(0, 0), //origin point
		    new google.maps.Point(6, 5)); // offset point
	var marker = new google.maps.Marker({
		position: this.coords,
		map: this.widget.map.map,
		icon: markerImage,
		draggable: true,
	});
	return marker;
};


CMMRM_WaypointRenderer.prototype.remove = function() {
	this.marker.setMap(null);
	jQuery(this).trigger('WaypointRenderer:remove');
};


CMMRM_WaypointRenderer.prototype.updatePosition = function(coords) {
	this.coords = coords;
	jQuery(this).trigger('WaypointRenderer:updatePosition', {coords: coords});
};



CMMRM_WaypointRenderer.prototype.getWaypointCoords = function() {
	return this.coords;
};

CMMRM_WaypointRenderer.prototype.getWaypointIndex = function() {
	return this.index;
};

CMMRM_WaypointRenderer.prototype.setWaypointIndex = function(index) {
	this.index = index;
	return this.index;
};