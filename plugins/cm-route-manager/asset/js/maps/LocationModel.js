function CMMRM_LocationModel(data, routeModel) {
	this.data = data;
	this.routeModel = routeModel;
	jQuery(this).trigger('LocationModel:ready');
}

CMMRM_LocationModel.prototype.getId = function() {
	return this.data.id;
};

CMMRM_LocationModel.prototype.getLat = function() {
	return this.data.lat;
};

CMMRM_LocationModel.prototype.getLng = function() {
	return this.data.lng;
};

CMMRM_LocationModel.prototype.getName = function() {
	return this.data.name;
};


CMMRM_LocationModel.prototype.getPosition = function() {
	return [this.getLat(), this.getLng()];
};

CMMRM_LocationModel.prototype.getGoogleLatLng = function() {
	return new google.maps.LatLng(this.getLat(), this.getLng());
};

CMMRM_LocationModel.prototype.setPosition = function(lat, lng) {
	this.data.lat = lat;
	this.data.lng = lng;
	jQuery(this).trigger('LocationModel:setPosition', {lat: lat, lng: lng});
	return this;
};

CMMRM_LocationModel.prototype.remove = function() {
	jQuery(this).trigger('LocationModel:remove');
};

CMMRM_LocationModel.prototype.getRoute = function() {
	return this.routeModel;
};

CMMRM_LocationModel.prototype.getIcon = function() {
	return this.data.icon;
};


CMMRM_LocationModel.prototype.getIconSize = function() {
	return this.data.iconSize;
};


CMMRM_LocationModel.prototype.getAddress = function() {
	return this.data.address;
};

CMMRM_LocationModel.prototype.setAddress = function(address) {
	this.data.address = address;
	jQuery(this).trigger('LocationModel:setAddress', {address: address});
	return this;
};

CMMRM_LocationModel.prototype.getDescription = function() {
	return this.data.description;
};

CMMRM_LocationModel.prototype.getImages = function() {
	if (typeof this.data.images == 'object') {
		return this.data.images;
	} else {
		return [];
	}
};

CMMRM_LocationModel.prototype.getInfoWindowContent = function() {
	return this.data.infoWindowContent;
};