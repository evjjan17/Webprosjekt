<?php

namespace com\cminds\mapsroutesmanager\helper;
use com\cminds\mapsroutesmanager\model\Route;

use com\cminds\mapsroutesmanager\App;

class GpxHelper {
	
	const MIME_TYPE = 'application/gpx+xml';
	
	
	static function initGeoPHP() {
		if (!class_exists('\\geoPHP')) {
			require_once App::path('lib/geoPHP/geoPHP.php');
		}
	}
	
	
	static function convertToKml($gpxSource) {
		self::initGeoPHP();
		$geom = \geoPHP::load($gpxSource, 'gpx');
		if ($kmlSource = $geom->out('kml')) {
			return $kmlSource;
		}
	}
	
	
	static function getSimpleXml($gpxSource) {
		$gpxSource = str_replace('xmlns=', 'ns=', $gpxSource);
		/* @var $xml SimpleXMLElement */
		return \simplexml_load_string($gpxSource);
	}
	
	
	static function getName(\SimpleXMLElement $xml, $fileName) {
		if ($name = (string)$xml->trk->name) {
			return $name;
		} else {
			return $fileName;
		}
	}
	
	
	static function export(Route $route) {
		self::initGeoPHP();
		return self::convertFromKml(KmlHelper::export($route));
	}
	
	
	static function convertFromKml($kmlSource) {
		if ($kmlSource) {
			self::initGeoPHP();
			$geom = \geoPHP::load($kmlSource, 'kml');
			if ($gpxSource = $geom->out('gpx')) {
				return $gpxSource;
			}
		}
	}
	
	
}
