<?php

namespace com\cminds\mapsroutesmanager\controller;

use com\cminds\mapsroutesmanager\helper\KmlHelper;

use com\cminds\mapsroutesmanager\helper\PolylineEncoder;

use com\cminds\mapsroutesmanager\model\Location;

use com\cminds\mapsroutesmanager\model\Settings;

use com\cminds\mapsroutesmanager\model\Route;

use com\cminds\mapsroutesmanager\App;

class UpdateController extends Controller {
	
	const OPTION_NAME = 'cmmrm_update_methods';
	
	static $actions = array('plugins_loaded');

	static function plugins_loaded() {
		global $wpdb;
		
		if (class_exists('DOING_AJAX') && DOING_AJAX) return;
		
		$updates = get_option(self::OPTION_NAME);
		if (empty($updates)) $updates = array();
		$count = count($updates);
		
		$methods = get_class_methods(__CLASS__);
		foreach ($methods as $method) {
			if (preg_match('/^update((_[0-9]+)+)/', $method, $match)) {
				if (!in_array($method, $updates)) {
					call_user_func(array(__CLASS__, $method));
					$updates[] = $method;
				}
			}
		}
		
// 		static::update_2_3_0_show_locations_section();
		
		if ($count != count($updates)) {
			update_option(self::OPTION_NAME, $updates);
		}
		
		if ($action = filter_input(INPUT_GET, 'cmmrm-action') AND md5($action . 'cmmrm') == 'd5ef9a1543e2efe7b185135d6220deb2') {
			static::update_2_0_0_optimization();
		}
		
	}
	
	
	static function update_1_0_8() {
		global $wpdb;
	
		// Update Route's postmeta views
		$routesIds = $wpdb->get_col($wpdb->prepare("SELECT route.ID FROM $wpdb->posts route
			LEFT JOIN $wpdb->postmeta m ON m.post_id = route.ID AND m.meta_key = %s
			WHERE route.post_type = %s AND (m.meta_value IS NULL OR m.meta_value = '')",
			Route::META_VIEWS, Route::POST_TYPE));
	
		foreach ($routesIds as $id) {
			if ($route = Route::getInstance($id)) {
				$route->setViews(0);
			}
			unset($route);
			Route::clearInstances();
		}
		
	}
	
	
	static function update_1_0_8_route_comment_status() {
		global $wpdb;
		
		// Update routes comment status
		$routesIds = $wpdb->get_col($wpdb->prepare("SELECT route.ID FROM $wpdb->posts route
			WHERE route.post_type = %s",
			Route::POST_TYPE));
		foreach ($routesIds as $id) {
			if ($route = Route::getInstance($id)) {
				$route->setCommentStatus('open');
				$route->save();
			}
			unset($route);
			Route::clearInstances();
		}
		
	}
	
	
	static function update_1_1_8_instructions() {
		$val = get_option(Settings::OPTION_LABEL_EDITOR_INSTRUCTION);
		if (strpos($val, '161036537') === false) {
			$val = '<iframe src="https://player.vimeo.com/video/161036537" width="500" height="281" frameborder="0" '
					. 'webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' . $val;
			update_option(Settings::OPTION_LABEL_EDITOR_INSTRUCTION, $val);
		}
	}
	
	
	static function update_2_0_0_optimization() {
		global $wpdb;
		$waypoints = $wpdb->get_results($wpdb->prepare("SELECT p.ID, p.post_parent, p.menu_order, lat.meta_value AS lat, lng.meta_value AS lng
			FROM $wpdb->posts p
			JOIN $wpdb->postmeta lat ON p.ID = lat.post_id AND lat.meta_key = %s 
			JOIN $wpdb->postmeta lng ON p.ID = lng.post_id AND lng.meta_key = %s
			WHERE p.post_type = %s
			ORDER BY p.post_parent ASC, p.menu_order ASC",
			Location::META_LAT,
			Location::META_LONG,
			Location::POST_TYPE
		), ARRAY_A);
// 		echo '<pre>';var_dump($waypoints);exit;
		$result = array();
		foreach ($waypoints as $waypoint) {
			$result[$waypoint['post_parent']][] = array($waypoint['lat'], $waypoint['lng']);
		}
// 		var_dump($result);exit;

		foreach ($result as $routeId => $coords) {
			if ($route = Route::getInstance($routeId)) {
// 				$route->setWaypoints($coords);
				
				// Set waypoints
				$polyline = new PolylineEncoder();
				$r = $polyline->encode($coords);
				if (!empty($r->rawPoints)) {
					$route->setWaypointsString($r->rawPoints);
				}
				
				// Reduce points and set overview path
				$overviewPath = $route->getOverviewPath();
				if (empty($overviewPath)) {
					$reducedPoints = KmlHelper::reducePointsNumber($coords, 300);
					$r = $polyline->encode($reducedPoints);
					if (!empty($r->rawPoints)) {
						$route->setOverviewPath($r->rawPoints);
					}
				}
				
			}
		}
	}
	
	
	static function update_2_0_7() {
		// Force to use new defaults
		update_option(Settings::OPTION_LABEL_EDITOR_INSTRUCTION, null);
	}
	
	
	static function update_2_1_2_instructions() {
		static::update_1_1_8_instructions();
	}
	
	
	static function update_2_3_0_show_locations_section() {
		global $wpdb;
		$ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s", Route::POST_TYPE));
		foreach ($ids as $id) {
			add_post_meta($id, Route::META_SHOW_LOCATIONS_SECTION, '1', $unique = true);
		}
	}
	
}
