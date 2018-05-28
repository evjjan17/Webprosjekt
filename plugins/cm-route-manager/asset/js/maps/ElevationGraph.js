function CMMRM_ElevationGraph(widget, routeModel) {
	
	this.widget = widget;
	this.routeModel = routeModel;
	this.results = null;
	
	this.maxElevation = 0;
	this.minElevation = 99999;
	this.elevationGain = 0;
	this.elevationDescent = 0;
	
	this.graph = null;
	this.graphData = null;
	
	if (this.routeModel.getTravelMode() == 'DIRECT') {
		this.calculateElevationAlongPath(this.routeModel.getWaypointsCoords());
		this.initPolylineMouseEventListeners();
	}
	
	var that = this;
	var timeout = null;
	jQuery(this.routeModel).bind('RouteModel:setPolylineString', function() {
		var route = this;
		clearTimeout(timeout);
		timeout = setTimeout(function() {
			that.calculateElevationAlongPath(route.getPolylineCoords());
		}, 500);
	});
	
	jQuery(widget.routeRenderer).bind('RouteRenderer:trailRequestSuccess', function() {
		that.initPolylineMouseEventListeners();
	});
	
}


CMMRM_ElevationGraph.prototype.calculateElevationAlongPath = function(path) {
	
	if (path.length < 2) return;
	
	var elevator = new google.maps.ElevationService;
	var that = this;
//	var dist = 0;
//	dist = this.widget.map.calculateDistance(path[0], path[path.length-1]);
	var samples = 450; //Math.min(450, Math.max(2, Math.floor(dist/5)));
//	console.log('dist = '+ dist + ' samples = '+ samples);
	path = this.reduceCoordsNumber(path, samples);
	
	elevator.getElevationAlongPath({
		'path': path,
		'samples': samples,
	  }, function(results, status) {
//		  console.log(results);
		  that.results = results;
		  that.status = status;
		  that.processElevationResults(results, status);
	  });
};


CMMRM_ElevationGraph.prototype.reduceCoordsNumber = function(path, max) {
//	console.log('path.lengt = ', path.length);
//	console.log('max = ', max);
	if (path.length < max) return path;
	var result = [];
	var i = 0;
	var step = path.length/max;
	while (i < path.length) {
		result.push(path[Math.floor(i)]);
		i += step;
	}
//	console.log('result.len = ', result.length);
	return result;
};



CMMRM_ElevationGraph.prototype.processElevationResults = function(results, status) {
	if (status !== google.maps.ElevationStatus.OK) {
		console.error('[CMMRM_ElevationGraph] Elevation service failed due to: ' + status);
		return;
	}
	
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
//		console.log('elev '+ elevation +' --- '+(elevation-prev));
		if (typeof prev == 'number') {
			if (elevation-prev > 0) {
				this.elevationGain += (elevation-prev);
			} else {
				this.elevationDescent += (prev-elevation);
			}
		}
		prev = elevation;
	}
	
	if (this.minElevation == 99999) {
		this.minElevation = 0;
	}
	
	this.showElevationGraph(results);
	jQuery(this).trigger('ElevationGraph:successResponse', {results: results});
	
};


CMMRM_ElevationGraph.prototype.showElevationGraph = function(elevations) {
//	console.log('showElevationGraph');
	var graphDiv = this.getGraphCanvasContainer();
//	console.log(this.widget);
	if (graphDiv.length == 0 || typeof google == 'undefined' || typeof google.visualization == 'undefined' || typeof google.visualization.ColumnChart == 'undefined') {
		console.error('[CMMRM_ElevationGraph] Missing library: google.visualization.ColumnChart');
		return;
	}
	var graph = new google.visualization.ColumnChart(graphDiv[0]);
	this.graph = graph;
	var data = new google.visualization.DataTable();
	this.graphData = data;
	var unit = ('feet' == CMMRM_Map_Settings.lengthUnits ? 'ft' : 'm');
//	data.addColumn('number', 'Sample');
	data.addColumn('number', 'Elevation');
	for (var i = 0; i < elevations.length; i++) {
		var num = elevations[i].elevation / (unit == 'ft' ? CMMRM_Map_Settings.feetToMeter : 1);
		data.addRow([num]);
	}
	graph.draw(data, {
	    height: 150,
	    legend: 'none',
	    titleY: 'Elevation ('+ unit +')',
	    crosshair: null,
	  });
	
	var marker = new google.maps.Marker({
//		position: new google.maps.LatLng(location.lat, location.long),
//		map: this.map,
		icon: 'https://maps.gstatic.com/mapfiles/dd-via.png',
		draggable: false,
	});
	
	var googleMapObj = this.widget.map.map;
	google.visualization.events.addListener(graph, 'onmouseover', function(ev) {
		if (typeof elevations[ev.row] != 'undefined') {
			marker.setMap(googleMapObj);
			marker.setPosition(elevations[ev.row].location);
		}
	});
	
	graphDiv.mouseout(function() {
		marker.setMap(null);
	});
	
};


CMMRM_ElevationGraph.prototype.getGraphCanvasContainer = function() {
	return jQuery(this.widget.getWidgetElement()).find('.cmmrm-elevation-graph-canvas');
};


CMMRM_ElevationGraph.prototype.getGraphWrapper = function() {
	return jQuery(this.widget.getWidgetElement()).find('.cmmrm-elevation-graph');
};


CMMRM_ElevationGraph.prototype.removeElevationGraph = function() {
	this.getGraphCanvasContainer().html('');
};


CMMRM_ElevationGraph.prototype.getMaxElevation = function() {
	return this.maxElevation;
};

CMMRM_ElevationGraph.prototype.getMinElevation = function() {
	return this.minElevation;
};

CMMRM_ElevationGraph.prototype.getElevationGain = function() {
	return this.elevationGain;
};

CMMRM_ElevationGraph.prototype.getElevationDescent = function() {
	return this.elevationDescent;
};


CMMRM_ElevationGraph.prototype.initPolylineMouseEventListeners = function() {
	
	// @TODO maybe in future
	return;
	
	var renderer = this.widget.routeRenderer;
	var polylines = renderer.polylines;
	var that = this;
	for (var i=0; i<polylines.length; i++) {
		google.maps.event.addListener(polylines[i], "mouseover", function(ev) {
			var polyline = polylines[i];
			
			// Find closest elevation result to the current location
			var closestResultIndex = that.findClosestResult(ev.latLng);
			if (closestResultIndex != null) {
				that.showCrosshair(closestResultIndex);
			}
			
		});
	}
};


CMMRM_ElevationGraph.prototype.findClosestResult = function(coords) {
	var minDistance = -1;
	var closestResultIndex = null;
	for (var i=0; i<this.results.length; i++) {
		var result = this.results[i];
		var d = CMMRM_GoogleMap.prototype.calculateDistance(coords, result.location);
		if (minDistance == -1 || d < minDistance) {
			minDistance = d;
			closestResultIndex = i;
		}
	}
	return closestResultIndex;
};


CMMRM_ElevationGraph.prototype.showCrosshair = function(columnIndex) {
	
	var cli = this.graph.getChartLayoutInterface();
    var chartArea = cli.getChartAreaBoundingBox();
    // "Zombies" is element #5.
    var wrapper = this.getGraphWrapper();
    
    var xDiv = jQuery('.cmmrm-elevation-graph-crosshair-x', wrapper);
    var x = Math.floor(cli.getXLocation(columnIndex)) - 10
    xDiv.css('left', x + "px");
    xDiv.show();
    
    var yDiv = jQuery('.cmmrm-elevation-graph-crosshair-y', wrapper);
    var y = Math.floor(cli.getYLocation(dataTable.getValue(columnIndex, 1))) - 50;
    yDiv.css('top', y + "px");
    yDiv.show();
    
	
};