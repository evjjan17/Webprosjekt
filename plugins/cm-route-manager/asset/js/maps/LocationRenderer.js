function CMMRM_LocationRenderer(widget, locationModel) {
	
	this.widget = widget;
	this.locationModel = locationModel;
	
	this.marker = this.createMarker();
	
	var that = this;
	
	jQuery(this.locationModel).bind('LocationModel:remove', function() {
		that.remove();
	});
	
	jQuery(this).trigger('LocationRenderer:ready');
	
}

CMMRM_LocationRenderer.prototype.createMarker = function() {
	return new CMMRM_Marker(this.widget.map,
			this.locationModel.getGoogleLatLng(),
			this.getMarkerIconOptions(),
			this.getMarkerLabelOptions()
		);
};


CMMRM_LocationRenderer.prototype.getMarkerIconOptions = function() {
	return {draggable: false, style: 'cursor:pointer;', icon: this.locationModel.getIcon(), iconSize: this.locationModel.getIconSize()};
};


CMMRM_LocationRenderer.prototype.getMarkerLabelOptions = function() {
	return {text: this.getLabelText(), style: 'cursor:pointer;'};
};


CMMRM_LocationRenderer.prototype.getLabelText = function() {
	return this.locationModel.getName();
};


CMMRM_LocationRenderer.prototype.remove = function() {
	this.marker.setMap(null);
};


CMMRM_LocationRenderer.prototype.getMarker = function() {
	return this.marker;
};