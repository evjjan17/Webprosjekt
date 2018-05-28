function CMMRM_RouteIndexRenderer(widget, routeModel) {
	
	this.widget = widget;
	this.widgetContainer = jQuery(this.widget.map.container).parents('.cmmrm-routes-archive').first();
	this.routeModel = routeModel;
	
	this.widget.map.extendBounds(this.routeModel.getBounds()).center();
	
	this.marker = this.renderMarker();
	
	if (this.widgetContainer.data('showParamOverviewPath') == 1) {
		this.polyline = this.renderPolyline();
	} else {
		this.polyline = null;
	}
	
	var that = this;
	
	jQuery(this).trigger('RouteIndexRenderer:ready');
	
}


CMMRM_RouteIndexRenderer.prototype.renderMarker = function() {
	var that = this;
	var waypoints = this.routeModel.getWaypointsCoords();
	var coords = null;
	if (waypoints.length > 0) {
		coords = waypoints[0];
	} else {
		var locations = this.routeModel.getLocations();
//		console.log('locations', locations);
		if (locations.length > 0) {
			coords = locations[0].getGoogleLatLng();
		} else {
			coords = this.routeModel.getGoogleLatLng();
		}
	}
	
	if (coords) {
		var marker = new CMMRM_Marker(this.widget.map,
			coords,
			{draggable: false, style: 'cursor:pointer;', icon: this.routeModel.getIcon(), color: this.routeModel.getPathColor()},
			{text: this.routeModel.getName(), style: 'cursor:pointer;'}
		);
		
		google.maps.event.addListener(marker, 'click', function() {
			window.location.href = that.routeModel.data.permalink;
		});
		
		return marker;
		
	} else {
		return null;
	}
		
};




CMMRM_RouteIndexRenderer.prototype.renderPolyline = function() {
	return new google.maps.Polyline({
		path: this.routeModel.getPolylineCoords(),
		strokeColor: this.routeModel.getPathColor(),
		opacity: 0.1,
		map: this.widget.map.map
	});
};

CMMRM_RouteIndexRenderer.prototype.removeTrailPolylines = function() {
	for (var i=0; i<this.polylines.length; i++) {
		this.polylines[i].setMap(null);
	}
	this.polylines = [];
};

CMMRM_RouteIndexRenderer.prototype.getMarker = function() {
	return this.marker;
};