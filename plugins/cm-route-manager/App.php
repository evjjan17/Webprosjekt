<?php

namespace com\cminds\mapsroutesmanager;

use com\cminds\mapsroutesmanager\core\Core;
use com\cminds\mapsroutesmanager\controller\SettingsController;
use com\cminds\mapsroutesmanager\model\Settings;

require_once dirname(__FILE__) . '/core/Core.php';

class App extends Core {
	
	const PREFIX = 'cmmrm';
	const SLUG = 'cm-maps-routes-manager';
	const PLUGIN_NAME = 'CM Maps Routes Manager';
	const PLUGIN_WEBSITE = 'https://www.cminds.com/';
	
	
	
	static function bootstrap($pluginFile) {
		parent::bootstrap($pluginFile);
	}
	
	
	static protected function getClassToBootstrap() {
		$classToBootstrap = array_merge(
			parent::getClassToBootstrap(),
			static::getClassNames('controller'),
			static::getClassNames('model')
		);
		if (static::isLicenseOk()) {
			$classToBootstrap = array_merge($classToBootstrap, static::getClassNames('shortcode'), static::getClassNames('widget'));
		}
		return $classToBootstrap;
	}
	
	
	static function init() {
		parent::init();
		
		wp_register_script('cmmrm-utils', static::url('asset/js/utils.js'), array('jquery'), App::getVersion(), true);
		wp_register_script('cmmrm-google-api-check', static::url('asset/js/google-maps-api-check.js'), array('jquery'), App::getVersion(), true);
		wp_register_script('cmmrm-editor-images', App::url('asset/js/editor-images.js'), array('jquery', 'thickbox'), App::getVersion(), true);
		
		wp_register_script('cmmrm-google-jsapi', 'https://www.google.com/jsapi', null, App::getVersion(), false);
		wp_register_script('cmmrm-google-marker-clusterer', static::url('asset/js/maps/markerclusterer.js'), null, App::getVersion(), false);
		if (Settings::getOption(Settings::OPTION_DONT_EMBED_GOOGLE_MAPS_JS_API)) {
			// However embed a dummy script to keep dependencies:
			wp_register_script('cmmrm-google-maps', static::url('asset/js/google-maps-dummy.js'), array('cmmrm-google-jsapi'), App::getVersion(), false);
		} else {
			// Embed Google Maps API with the API key:
			$key = Settings::getOption(Settings::OPTION_GOOGLE_MAPS_APP_KEY);
			wp_register_script('cmmrm-google-maps', 'https://maps.googleapis.com/maps/api/js?key='. urlencode($key) .'&libraries=places,geometry', array('cmmrm-google-jsapi'), App::getVersion(), false);
		}
		
		wp_register_script('cmmrm-map-marker', static::url('asset/js/maps/Marker.js'), array('cmmrm-google-maps'), App::getVersion(), true);
		wp_register_script('cmmrm-map-tooltip', static::url('asset/js/maps/Tooltip.js'), array('cmmrm-google-maps'), App::getVersion(), true);
		
		wp_register_script('cmmrm-widget-single-route', static::url('asset/js/maps/WidgetSingleRoute.js'), array('jquery', 'cmmrm-map',
			'cmmrm-geolocation-marker', 'cmmrm-fullscreen-feature', 'cmmrm-block-route-params', 'cmmrm-block-directions',
			'cmmrm-block-location-weather', 'cmmrm-route-gallery', 'cmmrm-single-location-renderer'), App::getVersion());
		wp_register_script('cmmrm-widget-index-map', static::url('asset/js/maps/WidgetIndexMap.js'), array('jquery', 'cmmrm-map',
			'cmmrm-geolocation-marker', 'cmmrm-fullscreen-feature', 'cmmrm-route-index-renderer'), App::getVersion());
		wp_register_script('cmmrm-map', static::url('asset/js/maps/GoogleMap.js'), array('jquery', 'cmmrm-google-maps', 'cmmrm-route-renderer',
			'cmmrm-location-renderer', 'cmmrm-waypoint-renderer', 'cmmrm-utils', 'cmmrm-map-marker', 'cmmrm-elevation-graph', 'cmmrm-map-tooltip'), App::getVersion());
		wp_register_script('cmmrm-route-model', static::url('asset/js/maps/RouteModel.js'), array('jquery'), App::getVersion());
		wp_register_script('cmmrm-location-model', static::url('asset/js/maps/LocationModel.js'), array('jquery'), App::getVersion());
		wp_register_script('cmmrm-waypoint-model', static::url('asset/js/maps/WaypointModel.js'), array('jquery'), App::getVersion());
		wp_register_script('cmmrm-route-renderer', static::url('asset/js/maps/RouteRenderer.js'), array('jquery', 'cmmrm-route-model',
			'cmmrm-request-trail', 'cmmrm-google-marker-clusterer'), App::getVersion());
		wp_register_script('cmmrm-route-index-renderer', static::url('asset/js/maps/RouteIndexRenderer.js'), array('jquery', 'cmmrm-route-model'), App::getVersion());
		wp_register_script('cmmrm-location-renderer', static::url('asset/js/maps/LocationRenderer.js'), array('jquery', 'cmmrm-location-model'), App::getVersion());
		wp_register_script('cmmrm-editor-location-renderer', static::url('asset/js/maps/LocationRendererEditor.js'), array('jquery', 'cmmrm-location-renderer'), App::getVersion());
		wp_register_script('cmmrm-single-location-renderer', static::url('asset/js/maps/LocationRendererSingle.js'), array('jquery', 'cmmrm-location-renderer'), App::getVersion());
		wp_register_script('cmmrm-waypoint-renderer', static::url('asset/js/maps/WaypointRenderer.js'), array('jquery', 'cmmrm-waypoint-model'), App::getVersion());
		wp_register_script('cmmrm-geolocation-marker', static::url('asset/js/maps/GeolocationMarker.js'), array('jquery', 'cmmrm-map-marker'), App::getVersion());
		wp_register_script('cmmrm-elevation-graph', static::url('asset/js/maps/ElevationGraph.js'), array('jquery', 'cmmrm-map-marker'), App::getVersion());
		wp_register_script('cmmrm-fullscreen-feature', static::url('asset/js/maps/FullscreenFeature.js'), array('jquery'), App::getVersion());
		wp_register_script('cmmrm-request-trail', static::url('asset/js/maps/RequestTrail.js'), array('jquery'), App::getVersion());
		wp_register_script('cmmrm-block-route-params', static::url('asset/js/maps/BlockRouteParams.js'), array('jquery'), App::getVersion());
		wp_register_script('cmmrm-block-directions', static::url('asset/js/maps/BlockDirections.js'), array('jquery'), App::getVersion());
		wp_register_script('cmmrm-route-editor', static::url('asset/js/maps/Editor.js'), array('jquery', 'cmmrm-widget-single-route',
			'cmmrm-editor-images', 'cmmrm-location-editor', 'cmmrm-editor-location-renderer'), App::getVersion());
		wp_register_script('cmmrm-location-editor', static::url('asset/js/maps/LocationEditor.js'), array(), App::getVersion());
		wp_register_script('cmmrm-block-location-weather', static::url('asset/js/maps/BlockLocationWeather.js'), array(), App::getVersion());
		
		wp_register_script('cmmrm-backend', static::url('asset/js/backend.js'), array('jquery', 'cmmrm-google-api-check'), App::getVersion());
		wp_register_style('cmmrm-font-awesome', static::url('asset/vendor/font-awesome-4.4.0/css/font-awesome.min.css'), null, App::getVersion());
		wp_register_style('cmmrm-settings', static::url('asset/css/settings.css'), null, App::getVersion());
		wp_register_style('cmmrm-backend', static::url('asset/css/backend.css'), null, App::getVersion());
		wp_register_style('cmmrm-frontend', static::url('asset/css/frontend.css'), array('cmmrm-font-awesome', 'dashicons'), App::getVersion());
		wp_register_style('cmmrm-editor', static::url('asset/css/editor.css'), array('cmmrm-frontend'), App::getVersion());
		
		wp_register_script('cmmrm-route-gallery', static::url('asset/js/route-gallery.js'), array('jquery'), App::getVersion(), true);
		wp_register_script('cmmrm-index-filter', static::url('asset/js/index-filter.js'), array('jquery', 'cmmrm-route-rating'), App::getVersion());
		
		// Old:
		wp_register_script('cmmrm-markerwithlabel', static::url('asset/js/markerwithlabel.js'), array('cmmrm-google-maps'), App::getVersion(), true);
		wp_register_script('cmmrm-map-abstract', static::url('asset/js/map-abstract.js'), array('jquery', 'cmmrm-google-maps',
			'cmmrm-map-marker', 'cmmrm-route-gallery', 'cmmrm-markerwithlabel', 'cmmrm-utils'), App::getVersion(), true);
		wp_register_script('cmmrm-index-map', static::url('asset/js/index-map.js'), array('cmmrm-map-abstract', 'cmmrm-route-rating'), App::getVersion(), true);
		wp_register_script('cmmrm-route-map', static::url('asset/js/route-map.js'), array('cmmrm-map-abstract', 'cmmrm-route-rating'), App::getVersion(), true);
		wp_register_script('cmmrm-route-rating', static::url('asset/js/route-rating.js'), array('jquery'), App::getVersion(), true);
		wp_register_script('cmmrm-editor', static::url('asset/js/editor.js'), array('cmmrm-map-abstract', 'cmmrm-editor-images'), App::getVersion());
		
		wp_localize_script('cmmrm-map', 'CMMRM_Map_Settings', array(
			'lengthUnits' => Settings::getOption(Settings::OPTION_UNIT_LENGTH),
			'feetToMeter' => Settings::FEET_TO_METER,
			'temperatureUnits' => Settings::getOption(Settings::OPTION_UNIT_TEMPERATURE),
			'feetInMile' => Settings::FEET_IN_MILE,
			'openweathermapAppKey' => Settings::getOption(Settings::OPTION_OPENWEATHERMAP_API_KEY),
			'googleMapAppKey' => Settings::getOption(Settings::OPTION_GOOGLE_MAPS_APP_KEY),
			'mapType' => Settings::getOption(Settings::OPTION_MAP_TYPE_DEFAULT),
			'indexGeolocation' => Settings::getOption(Settings::OPTION_INDEX_GEOLOCATION_ENABLE) ? 1 : 0,
			'routeGeolocation' => Settings::getOption(Settings::OPTION_ROUTE_PAGE_GEOLOCATION_ENABLE) ? 1 : 0,
			'editorGeolocation' => Settings::getOption(Settings::OPTION_EDITOR_GEOLOCATION_ENABLE) ? 1 : 0,
			'geolocationIcon' => static::url('asset/img/geolocation.png'),
			'scrollZoom' => Settings::getOption(Settings::OPTION_MAP_SCROLL_ZOOM_ENABLE) ? 1 : 0,
			'editorWaypointsLimit' => 200,
			'indexMapMarkerClustering' => Settings::getOption(Settings::OPTION_INDEX_MAP_MARKER_CLUSTERING_ENABLE) ? 1 : 0,
			'routeMapMarkerClustering' => Settings::getOption(Settings::OPTION_ROUTE_MAP_MARKER_CLUSTERING_ENABLE) ? 1 : 0,
			'routeMapLabelType' => Settings::getOption(Settings::OPTION_ROUTE_MAP_LABEL_TYPE),
			'routeMapLocationsInfoWindow' => Settings::getOption(Settings::OPTION_ROUTE_MAP_LOCATION_INFO_WINDOW_SHOW) ? 1 : 0,
			'mapTooltipBgColor' => Settings::getOption(Settings::OPTION_MAP_TOOLTIP_BGCOLOR),
			'zipFilterCountry' => Settings::getOption(Settings::OPTION_INDEX_ZIP_RADIUS_COUNTRY),
			'zipFilterGeolocation' => intval(Settings::getOption(Settings::OPTION_INDEX_ZIP_RADIUS_GEOLOCATION)),
		));
		
		wp_localize_script('cmmrm-route-rating', 'CMMRM_Route_Rating', array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('route_rating'),
		));
		
		wp_localize_script('cmmrm-editor-images', 'CMMRM_Editor_Images_Settings', array(
			'icons' => Settings::getMarkerIconsUrls(),
		));
		
		
	}
	

	static function admin_menu() {
		parent::admin_menu();
		$name = static::getPluginName(true);
		$page = add_menu_page($name, $name, 'manage_options', static::PREFIX, create_function('$q', 'return;'), 'dashicons-location-alt', 1234);
	}
	
	
	static function getLicenseAdditionalNames() {
		return array(static::getPluginName(false), static::getPluginName(true));
	}
	
	
	static function activatePlugin() {
		parent::activatePlugin();
		if (App::isPro()) {
			SettingsController::fixPathesInSettings();
		}
	}
	
}
