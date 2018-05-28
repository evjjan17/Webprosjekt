function CMMRM_BlockRouteParams(widget, routeRenderer, elevationGraph) {
	
	this.widget = widget;
	this.routeRenderer = routeRenderer;
	this.elevationGraph = elevationGraph;
	this.paramsContainer = jQuery(this.widget.getWidgetElement()).find('.cmmrm-route-params');
	this.distance = widget.routeModel.getRouteParam('distance');
	this.duration = widget.routeModel.getRouteParam('duration');
	this.firstElevationRequest = true;
	this.firstTrailRequest = true;
	
	var that = this;
	jQuery(this.routeRenderer).bind('RouteRenderer:trailRequestSuccess', function(ev, data) {
		if (that.firstTrailRequest) {
			that.firstTrailRequest = false;
			return;
		}
		var distance = data.request.getDistance();
		var duration = data.request.getDuration();
		that.updateDistance(distance);
		that.updateDuration(duration);
		that.updateAvgSpeed(duration > 0 ? distance/duration : 0);
	});
	
	jQuery(this.elevationGraph).bind('ElevationGraph:successResponse', function(ev, data) {
		if (that.firstElevationRequest) {
			that.firstElevationRequest = false;
			return;
		}
		that.updateMaxElevation(this.getMaxElevation());
		that.updateMinElevation(this.getMinElevation());
		that.updateElevationGain(this.getElevationGain());
		that.updateElevationDescent(this.getElevationDescent());
	});
	
}


CMMRM_BlockRouteParams.prototype.updateParam = function(name, value) {
	
};

CMMRM_BlockRouteParams.prototype.updateDuration = function(duration, dontUpdateSpeed) {
	this.paramsContainer.find('.cmmrm-route-duration span').text(this.getDurationLabel(duration));
	this.duration = duration;
	if (typeof dontUpdateSpeed == 'undefined' || dontUpdateSpeed == false) {
		this.updateAvgSpeed(duration > 0 ? this.distance/duration : 0);
	}
};

CMMRM_BlockRouteParams.prototype.updateDistance = function(dist) {
	this.distance = dist;
	var elem = this.paramsContainer.find('.cmmrm-route-distance span');
	elem.text(this.getDistanceLabel(dist));
	this.updateAvgSpeed(this.duration > 0 ? dist/this.duration : 0);
};


CMMRM_BlockRouteParams.prototype.updateAvgSpeed = function(meterPerSec, withDurationUpdate) {
	this.avgSpeed = meterPerSec;
	var elem = this.paramsContainer.find('.cmmrm-route-avg-speed span');
	elem.text(this.getSpeedLabel(meterPerSec));
	if (typeof withDurationUpdate != 'undefined' && withDurationUpdate) {
		this.updateDuration(this.distance / meterPerSec);
	}
};





CMMRM_BlockRouteParams.prototype.getSpeedLabel = function(meterPerSec) {
	if ('feet' == CMMRM_Map_Settings.lengthUnits) {
		return '' + Math.round(meterPerSec/CMMRM_Map_Settings.feetToMeter/CMMRM_Map_Settings.feetInMile*3600) + ' mph';
	} else {
		return '' + Math.round(meterPerSec * 3.6) + ' km/h';
	}
};


CMMRM_BlockRouteParams.prototype.getDurationLabel = function(durationSec) {
		
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

//CMMRM_BlockRouteParams.prototype.updateDuration = function(duration) {
//	this.paramsContainer.find('.cmmrm-route-duration span').text(this.getDurationLabel(duration));
//	this.updateAvgSpeed(duration > 0 ? this.distance/duration : 0);
//};

CMMRM_BlockRouteParams.prototype.getDistanceLabel = function(distanceMeters, useMinorUnits) {
	
	if (typeof useMinorUnits == 'undefined') {
		useMinorUnits = (1 == jQuery('.cmmrm-route-params').data('useMinorLengthUnits'));
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


CMMRM_BlockRouteParams.prototype.updateMaxElevation = function(maxElevation) {
	this.paramsContainer.find('.cmmrm-max-elevation span').text(this.getElevationLabel(maxElevation));
};

CMMRM_BlockRouteParams.prototype.updateMinElevation = function(minElevation) {
	this.paramsContainer.find('.cmmrm-min-elevation span').text(this.getElevationLabel(minElevation));
};


CMMRM_BlockRouteParams.prototype.updateElevationGain = function(elevationGain) {
	this.paramsContainer.find('.cmmrm-elevation-gain span').text(this.getElevationLabel(elevationGain));
};

CMMRM_BlockRouteParams.prototype.updateElevationDescent = function(elevationDescent) {
	this.paramsContainer.find('.cmmrm-elevation-descent span').text(this.getElevationLabel(elevationDescent));
};

CMMRM_BlockRouteParams.prototype.getElevationLabel = function(elev) {
	if ('feet' == CMMRM_Map_Settings.lengthUnits) {
		var num = elev/CMMRM_Map_Settings.feetToMeter;
		return Math.floor(num) + ' ft';
	} else {
		return '' + Math.round(elev) + ' m';
	}
};
