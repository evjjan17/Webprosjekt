function CMMRM_WidgetSingleRoute(containerId, routeData, waypointsString, locations) {
	
	this.containerId = containerId;
	this.container = document.getElementById(containerId);
	this.container.cmmrm = this;
	this.widgetElement = jQuery(this.container).parents('.cmmrm-route-single').first();
	
	this.map = new CMMRM_GoogleMap(containerId);
	this.routeModel = new CMMRM_RouteModel(routeData, waypointsString, locations);
	
	var that = this;
	that.routeRenderer = new (that.resolve('RouteRenderer'))(that, that.routeModel);
	
	this.map.map.set('scrollwheel', (CMMRM_Map_Settings.scrollZoom == '1'));
	
	this.initGeolocation();
	this.addBlocks();
	this.bindActions();
	this.initWeather();
	
	this.markerCluster = this.createLocationClusterer();
	
	// Hide some features for big routes
	var waypointsCoords = this.routeModel.getWaypointsCoords();
	if (waypointsCoords.length >= CMMRM_Map_Settings.editorWaypointsLimit) {
		jQuery('.cmmrm-route-travel-mode a[data-mode!=DIRECT]', this.widgetElement).hide();
	}
	
}


CMMRM_WidgetSingleRoute.prototype.resolve = function(name) {
//	console.log(this);
	var deps = this.getDependencies();
	if (typeof deps[name] != 'undefined') {
		return deps[name];
	} else {
		throw "Missing dependency: " + name;
	}
};




CMMRM_WidgetSingleRoute.prototype.getDependencies = function() {
	return {
		LocationRenderer: CMMRM_LocationRendererSingle,
		RouteRenderer: CMMRM_RouteRenderer,
		WaypointRenderer: CMMRM_WaypointRenderer
	};
};


CMMRM_WidgetSingleRoute.prototype.initGeolocation = function() {
	if (CMMRM_Map_Settings.routeGeolocation == '1') {
		this.geolocationMarker = new CMMRM_GeolocationMarker(this.map);
	} else {
		this.geolocationMarker = null;
	}
};


CMMRM_WidgetSingleRoute.prototype.getWidgetElement = function() {
	return this.widgetElement;
};


CMMRM_WidgetSingleRoute.prototype.addBlocks = function() {
	this.elevationGraph = new CMMRM_ElevationGraph(this, this.routeModel);
	this.blockRouteParams = new CMMRM_BlockRouteParams(this, this.routeRenderer, this.elevationGraph);
	this.blockDirections = new CMMRM_BlockDirections(this, this.routeRenderer);
};

CMMRM_WidgetSingleRoute.prototype.bindActions = function() {
	
	var $ = jQuery;
	var that = this;
	var widget = jQuery(this.getWidgetElement());
	
	// Center map button
	$('.cmmrm-map-center-btn', widget).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		that.map.center();
	});
	
	// Change travel mode
	$('.cmmrm-route-travel-mode a', widget).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var obj = $(this);
		obj.parents('.cmmrm-route-travel-mode').find('.current').removeClass('current');
		obj.addClass('current');
		
		that.routeModel.setTravelMode(obj.data('mode'));
		
	});
	
	// Display directions steps
	$('.cmmrm-directions-steps-btn', widget).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var wrapper = widget.find('.cmmrm-route-map-canvas-outer');
		var name = 'data-show-steps';
		wrapper.attr(name, '1' == wrapper.attr(name) ? '0' : '1');
	});
	
	new CMMRM_FullscreenFeature(this);
	
};


CMMRM_WidgetSingleRoute.prototype.initWeather = function() {
	if (CMMRM_Map_Settings.openweathermapAppKey) {
		var locations = this.routeModel.getLocations();
		for (var i=0; i<locations.length; i++) {
			new CMMRM_BlockLocationWeather(this, locations[i]);
		}
	}
};


CMMRM_WidgetSingleRoute.prototype.createLocationClusterer = function() {
	if (CMMRM_Map_Settings.routeMapMarkerClustering == '1') {
		var locationRenderers = this.routeRenderer.getLocationRenderers();
		var markers = [];
		for (var i=0; i<locationRenderers.length; i++) {
			markers.push(locationRenderers[i].getMarker());
		}
		return new MarkerClusterer(this.map.map, markers, {
			imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
			maxZoom: 14,
		});
	}
};
