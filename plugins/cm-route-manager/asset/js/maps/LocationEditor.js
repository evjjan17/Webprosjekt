function CMMRM_LocationEditor(widget, locationModel) {
	
	this.widget = widget;
	this.locationModel = locationModel;
	
	this.formItem = this.createFormItem();
	this.updateFormFields();
	
	var that = this;
	
	jQuery(this.locationModel).bind('LocationModel:remove', function() {
		that.remove();
	});
	
	this.initAddressHandlers();
	this.initImagesEditor();
	
	jQuery(this).trigger('LocationEditor:ready');
	
}

CMMRM_LocationEditor.prototype.createFormItem = function() {
	
	var that = this;	
	var locationsList = jQuery('#cmmrm-editor-locations .cmmrm-locations-list');
	
	var item = locationsList.find('li[data-id=0]').first().clone();
	locationsList.append(item);
	item.show();
	item.find('.cmmrm-location-remove').click(function() {
		that.locationModel.remove();
	});
	
	return item;
	
};



CMMRM_LocationEditor.prototype.updateFormFields = function() {
//	console.log(this.locationModel.getLat());console.log(this.locationModel.getLng());
	var id = this.locationModel.getId();
	this.formItem.attr('data-id', id);
	this.formItem.find('.location-id').val(id);
	this.formItem.find('.location-name').val(this.locationModel.getName());
	this.formItem.find('.location-lat').val(this.locationModel.getLat());
	this.formItem.find('.location-long').val(this.locationModel.getLng());
	this.formItem.find('.location-description').val(this.locationModel.getDescription());
	this.formItem.find('.location-address').val(this.locationModel.getAddress());
};


CMMRM_LocationEditor.prototype.initAddressHandlers = function() {
	var that = this;
//	this.updateAddress();
	jQuery(this.locationModel).bind('LocationModel:setPosition', function(ev, data) {
//		console.log('setpos')
//		console.log(data);
		that.updateFormFields();
		that.updateAddress();
		
	});
	jQuery(this.locationModel).bind('LocationModel:setAddress', function(ev, data) {
		jQuery('.location-address', that.formItem).val(data.address);
	});
};


CMMRM_LocationEditor.prototype.updateAddress = function() {
	var that = this;
	this.widget.map.findAddress(new google.maps.LatLng(this.locationModel.getLat(), this.locationModel.getLng()), function(result) {
		that.locationModel.setAddress(result.formatted_address);
	});
};


CMMRM_LocationEditor.prototype.remove = function() {
	this.formItem.remove();
};


CMMRM_LocationEditor.prototype.initImagesEditor = function() {
	
	this.formItem.find('.cmmrm-images').each(CMMRM_Editor_Images_init);
	
	var images = this.locationModel.getImages();
	if (images.length > 0) {
		var imageFileInput = this.formItem.find('input[type=hidden][name*=images]');
		var imageFileList = this.formItem.find('.cmmrm-images-list');
		for (var i=0; i<images.length; i++) {
			var image = images[i];
			CMMRM_Editor_Images_add(imageFileInput, imageFileList, image.id, image.thumb, image.url);
		}
	}
	
	if (typeof CMMRM_Location_Icon_init == 'function') {
		CMMRM_Location_Icon_init(this.formItem, this.locationModel.getIcon(), this.locationModel.getIconSize());
	}
	
};