function CMMRM_GeolocationMarker(map) {
	
	this.map = map;
	this.userPositionMarker = null;
	
	var that = this;
	this.geolocationWatchPosition(function(pos) {
		that.showUserPositionMarker(pos.coords.latitude, pos.coords.longitude);
	}, null, true);
	
}


CMMRM_GeolocationMarker.prototype.geolocationWatchPosition = function(callback, errorCallback, highAccuracy) {
	if ("geolocation" in navigator) {
		if (typeof highAccuracy != 'boolean') highAccuracy = true;
		var geo_options = {
		  enableHighAccuracy: highAccuracy, 
		  maximumAge        : 1000 * 60 * 1, // 1 minute
		  timeout           : 1000 * 60 * 10 // 10 minutes
		};
		errorCallback = function(err) {
			console.log(err);
			window.CMMRM.Utils.toast('Geolocation error: [' + err.code + '] ' + err.message, null, Math.ceil(err.message.length/5));
		};
		return navigator.geolocation.watchPosition(callback, errorCallback, geo_options);
	}
};


CMMRM_GeolocationMarker.prototype.showUserPositionMarker = function(lat, long) {
	var pos = new google.maps.LatLng(lat, long);
	if (!this.userPositionMarker) {
		this.userPositionMarker = new CMMRM_Marker(this.map, pos,
		   {draggable: false, style: 'padding-top:20px;padding-right:20px;', icon: CMMRM_Map_Settings.geolocationIcon},
		   {text: '', style: ''}
		 );
	} else {
		this.userPositionMarker.setPosition(pos);
	}
};
