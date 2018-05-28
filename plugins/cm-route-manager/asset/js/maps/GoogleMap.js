if (typeof Number.prototype.toRadians == 'undefined') {
	Number.prototype.toRadians = function() {
	   return this * Math.PI / 180;
	};
}

if (typeof Number.prototype.toDegrees == 'undefined') {
	Number.prototype.toDegrees = function() {
		   return this * 180 /  Math.PI;
	};
}


function CMMRM_GoogleMap(containerId) {
	
	this.containerId = containerId;
	this.container = document.getElementById(containerId);
	this.map = new google.maps.Map(this.container);
	this.bounds = new google.maps.LatLngBounds();
	this.suspendAddWaypoints = false;
	
	this.map.setMapTypeId(CMMRM_Map_Settings.mapType);
//	this.map.setZoom(13);
//	this.map.setCenter(new google.maps.LatLng(37.4419, -122.1419));
	
}


CMMRM_GoogleMap.prototype.getContainerId = function() {
	return this.containerId;
};

CMMRM_GoogleMap.prototype.getContainer = function() {
	return this.container;
};

CMMRM_GoogleMap.prototype.setMapType = function(type) {
	this.map.setMapTypeId(type);
	return this;
};

CMMRM_GoogleMap.prototype.extendBounds = function(coords) {
	for (var i=0; i<coords.length; i++) {
		var pos = coords[i];
		if (Object.prototype.toString.call(pos) == '[object Array]') {
			pos = new google.maps.LatLng(pos[0], pos[1]);
		}
		this.bounds.extend(pos);
	}
	return this;
};

CMMRM_GoogleMap.prototype.center = function() {
	this.map.fitBounds(this.bounds);
	return this;
};


CMMRM_GoogleMap.prototype.calculateDistance = function(p1, p2) {
	
	var R = 6371000; // metres
	var k = p1.lat().toRadians();
	var l = p2.lat().toRadians();
	var m = (p2.lat() - p1.lat()).toRadians();
	var n = (p2.lng() - p1.lng()).toRadians();
	
	var a = Math.sin(m/2) * Math.sin(m/2) +
    	Math.cos(k) * Math.cos(l) *
    	Math.sin(n/2) * Math.sin(n/2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	
	return R * c;
	
};


CMMRM_GoogleMap.prototype.calculateDistanceArray = function(coords) {
	var dist = 0;
	var last = null;
	for (var i=0; i<coords.length; i++) {
		var current = coords[i];
		if (last) {
			dist += CMMRM_GoogleMap.prototype.calculateDistance(last, current);
		}
		last = current;
	}
	return dist;
};


CMMRM_GoogleMap.prototype.calculateMidpoint = function(p1, p2) {
	
	var lat1 = p1.lat().toRadians();
	var lon1 = p1.lng().toRadians();
	var lat2 = p2.lat().toRadians();
	var lon2 = p2.lng().toRadians();
	
	var bx = Math.cos(lat2) * Math.cos(lon2 - lon1);
	var by = Math.cos(lat2) * Math.sin(lon2 - lon1);
	var lat3 = Math.atan2(Math.sin(lat1) + Math.sin(lat2), Math.sqrt((Math.cos(lat1) + bx) * (Math.cos(lat1) + bx) + by*by));
	var lon3 = lon1 + Math.atan2(by, Math.cos(lat1) + Bx);
	
	return new google.maps.LatLng(lat3.toDegrees(), lon3.toDegrees());
	
};


CMMRM_GoogleMap.prototype.calculateMidpoints = function(p1, p2, maxDist) {
	var dist = this.calculateDistance(p1, p2);
	if (dist <= maxDist) return [];
	var num = dist / maxDist;
	
	
	
};

CMMRM_GoogleMap.prototype.findAddress = function(pos, successCallback) {
	var geocoder = new google.maps.Geocoder;
	geocoder.geocode({'location': pos}, function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			
			var findPostalCode = function(results) {
				for (var j=0; j<results.length; j++) {
					var address = results[j];
					var components = address.address_components;
//					console.log(components);
					for (var i=0; i<components.length; i++) {
						var component = components[i];
						if (component.types[0] == "postal_code"){
					        return component.short_name;
					    }
					}
				}
				return "";
			};
			
			if (results.length > 0) {
				var address = results[0];
				successCallback({
					results: results,
					postal_code: findPostalCode(results),
					formatted_address: address.formatted_address,
				});
			}
		}
	});
};


