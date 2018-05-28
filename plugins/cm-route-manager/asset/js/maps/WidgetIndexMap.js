function CMMRM_WidgetIndexMap(containerId, routes) {
	
	this.containerId = containerId;
	this.container = document.getElementById(containerId);
	this.container.cmmrm = this;
	this.widgetElement = jQuery(this.container).parents('.cmmrm-routes-archive').first();
	
	this.map = new CMMRM_GoogleMap(containerId);
	this.routesModels = [];
	this.routesRenderers = [];
	
	this.addRoutes(routes);
	
	if (CMMRM_Map_Settings.indexGeolocation == '1') {
		this.geolocationMarker = new CMMRM_GeolocationMarker(this.map);
	} else {
		this.geolocationMarker = null;
	}
	
	this.map.map.set('scrollwheel', (CMMRM_Map_Settings.scrollZoom == '1'));
	
	this.bindActions();
	this.markerCluster = this.createMarkerClusterer();
	
}

CMMRM_WidgetIndexMap.prototype.resolve = function(name) {
//	console.log(this);
	var deps = this.getDependencies();
	if (typeof deps[name] != 'undefined') {
		return deps[name];
	} else {
		throw "Missing dependency: " + name;
	}
};




CMMRM_WidgetIndexMap.prototype.getDependencies = function() {
	return {
		LocationRenderer: CMMRM_LocationRenderer,
	};
};



CMMRM_WidgetIndexMap.prototype.addRoutes = function(routes) {
	for (var i=0; i<routes.length; i++) {
		var routeModel = new CMMRM_RouteModel(routes[i], routes[i].waypointsString, []);
		this.routesModels.push(routeModel);
		this.routesRenderers.push(new CMMRM_RouteIndexRenderer(this, routeModel));
	}
	return this;
};


CMMRM_WidgetIndexMap.prototype.getWidgetElement = function() {
	return this.widgetElement;
};


CMMRM_WidgetIndexMap.prototype.bindActions = function() {
	
	var $ = jQuery;
	var that = this;
	var widget = jQuery(this.getWidgetElement());
	
	// Center map button
	$('.cmmrm-map-center-btn', widget).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		that.map.center();
	});
	
	new CMMRM_FullscreenFeature(this);
	
};


CMMRM_WidgetIndexMap.prototype.prepareMapSnippetThumbTrail = function() {
	
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
	
	
};


CMMRM_WidgetIndexMap.prototype.createMarkerClusterer = function() {
	if (CMMRM_Map_Settings.indexMapMarkerClustering == '1') {
		var renderers = this.routesRenderers;
		var markers = [];
		for (var i=0; i<renderers.length; i++) {
			var marker = renderers[i].getMarker();
			if (marker) markers.push(marker);
		}
		return new MarkerClusterer(this.map.map, markers, {
			imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
			maxZoom: 14,
		});
	}
};