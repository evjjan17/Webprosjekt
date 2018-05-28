function CMMRM_RouteRenderer(widget, routeModel) {
	
	this.widget = widget;
	this.routeModel = routeModel;
	this.polylineCache = {};
	this.polylines = [];
	
	this.renderPolylines();
	this.locationRenderers = this.renderLocations();
	
	var that = this;
	
	jQuery(this.routeModel).bind('RouteModel:setTravelMode', function() {
		that.renderPolylines();
	});
	
	jQuery(this.routeModel).bind('RouteModel:setWaypointsString', function() {
		that.renderPolylines();
	});
	
	setTimeout(function() {
		that.widget.map.extendBounds(that.routeModel.getBounds()).center();
	}, 500);
	
	jQuery(this).trigger('RouteRenderer:ready');
	
}


CMMRM_RouteRenderer.prototype.renderLocations = function() {
	var locations = this.routeModel.getLocations();
	var renderers = [];
//	var markers = [];
	for (var i=0; i<locations.length; i++) {
		var renderer = new (this.widget.resolve('LocationRenderer'))(this.widget, locations[i]);
		renderers.push(renderer);
//		markers.push(renderer.getMarker());
	}
	
	return renderers;
	
};



CMMRM_RouteRenderer.prototype.renderPolylines = function() {
	var that = this;
//	console.log('renderPolylines')
	var waypointsCoords = this.routeModel.getWaypointsCoords();
	if (waypointsCoords.length == 0) return;
	var request = new CMMRM_RequestTrail(this.routeModel.getTravelMode(), waypointsCoords);
	request.run(this, function(response, status) {
		if (status !== google.maps.DirectionsStatus.OK) {
			var errorMsg = request.getDirectionErrorMessage(status);
			window.CMMRM.Utils.toast(errorMsg, null, Math.ceil(errorMsg.length/10));
			console.log(status);
			console.log(response);
		} else {
//			console.log(response);
			that.removeTrailPolylines();
			that.polylines = request.createTrailPolylines(that.widget.map.map, that.routeModel.getPathColor(), that.routeModel.showDirectionalArrows());
			that.routeModel.setPolylineString(response.routes[0].overview_polyline);
			jQuery(that).trigger('RouteRenderer:trailRequestSuccess', {request: request});
		}
	});
};


CMMRM_RouteRenderer.prototype.removeTrailPolylines = function() {
	for (var i=0; i<this.polylines.length; i++) {
		this.polylines[i].setMap(null);
	}
	this.polylines = [];
};


CMMRM_RouteRenderer.prototype.getLocationRenderers = function() {
	return this.locationRenderers;
};

