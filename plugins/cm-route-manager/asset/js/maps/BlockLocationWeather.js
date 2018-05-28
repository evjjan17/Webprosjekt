function CMMRM_BlockLocationWeather(widget, locationModel) {
	
	this.widget = widget;
	this.locationModel = locationModel;
	this.widgetContainer = jQuery(this.widget.getWidgetElement());
	this.weatherContainer = this.widgetContainer.find('.cmmrm-location-details[data-id='+ locationModel.getId() +'] .cmmrm-weather');
	
	if (this.weatherContainer.length == 0) return;
	
	var that = this;
	
	var units = ('temp_f' == CMMRM_Map_Settings.temperatureUnits ? 'imperial' : 'metric');
	var url = '//api.openweathermap.org/data/2.5/weather?APPID='
				+ encodeURIComponent(CMMRM_Map_Settings.openweathermapAppKey)
				+ '&lat='+ encodeURIComponent(locationModel.getLat())
				+ '&lon=' + encodeURIComponent(locationModel.getLng())
				+ '&units=' + encodeURIComponent(units);
	this.pushRequest(url, function(response) {
//		console.log(response);
		if (200 == response.cod) {
			var iconUrl = 'http://openweathermap.org/img/w/'+ response.weather[0].icon +'.png';
			var tempUnit = ('temp_f' == CMMRM_Map_Settings.temperatureUnits ? 'F' : 'C');
			that.weatherContainer.attr('href', 'http://openweathermap.org/city/' + response.id);
			that.weatherContainer.append(jQuery('<img/>', {src: iconUrl}));
			that.weatherContainer.append(jQuery('<div/>', {"class" : "cmmrmr-weather-temperature"}).html(Math.round(response.main.temp) + "&deg;"+ tempUnit));
			that.weatherContainer.append(jQuery('<div/>', {"class" : "cmmrmr-weather-pressure"}).html(Math.round(response.main.pressure) + " hPa"));
		}
	});
	
}


CMMRM_BlockLocationWeather.prototype.pushRequest = function(url, callback) {
	var callbackName = 'cmmrm_callback_' + Math.floor(Math.random()*99999999);
	window[callbackName] = callback;
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = url + '&callback=' + callbackName;
	document.getElementsByTagName('body')[0].appendChild(script);
};