<?php

namespace com\cminds\mapsroutesmanager\helper;

use com\cminds\mapsroutesmanager\App;

class GoogleMapsIcons {
	
	static function getAll() {
		$icons = array();
		include App::path('asset/google-maps-icons.php');
		return $icons;
	}
		
}
