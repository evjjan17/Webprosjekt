function CMMRM_LocationRendererSingle(widget, locationModel) {
	CMMRM_LocationRenderer.call(this, widget, locationModel);
	
	this.tooltip = this.initializeTooltip();
	this.infoWindow = this.initializeInfoWindow();
}

CMMRM_LocationRendererSingle.prototype = Object.create(CMMRM_LocationRenderer.prototype);
CMMRM_LocationRendererSingle.prototype.contructor = CMMRM_LocationRenderer;


CMMRM_LocationRendererSingle.prototype.getLabelText = function() {
	if (CMMRM_Map_Settings.routeMapLabelType == 'tooltip') {
		return "";
	} else {
		return CMMRM_LocationRenderer.prototype.getLabelText.call(this);
	}
};


CMMRM_LocationRendererSingle.prototype.getMarkerIconOptions = function() {
	var options = CMMRM_LocationRenderer.prototype.getMarkerIconOptions.call(this);
	if (CMMRM_Map_Settings.routeMapLabelType == 'tooltip') {
//		options.title = this.locationModel.getName();
	}
	return options;
};


CMMRM_LocationRendererSingle.prototype.initializeTooltip = function() {
	if (CMMRM_Map_Settings.routeMapLabelType == 'tooltip') {
		var that = this;
		var tooltip = new CMMRM_Tooltip(this.widget, this.locationModel.getGoogleLatLng(), this.locationModel.getName(),
				{backgroundColor: CMMRM_Map_Settings.mapTooltipBgColor});
		tooltip.offsetTop = -20;
		tooltip.offsetLeft = 20;
		
		google.maps.event.addDomListener(this.marker, 'mouseenter', function(ev) {
			tooltip.setMap(that.widget.map.map);
		});
		google.maps.event.addDomListener(this.marker, 'mouseleave', function(ev) {
			tooltip.setMap(null);
		});
		
		return tooltip;
	}
};


CMMRM_LocationRendererSingle.prototype.initializeInfoWindow = function() {
	var that = this;
	if (CMMRM_Map_Settings.routeMapLocationsInfoWindow == '1') {
		var infowindow = new google.maps.InfoWindow({
	          content: '<div class="cmmrm-infowindow">' + this.locationModel.getInfoWindowContent() + '</div>',
	          position: this.locationModel.getGoogleLatLng(),
	          pixelOffset: new google.maps.Size(0, -40)
        });
		google.maps.event.addDomListener(this.marker, 'click', function(ev) {
			infowindow.setZIndex(9000);
			infowindow.open(that.widget.map.map, this.marker);
		});
		return infowindow;
	}
};