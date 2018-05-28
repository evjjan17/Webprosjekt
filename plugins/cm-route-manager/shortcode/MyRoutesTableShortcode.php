<?php

namespace com\cminds\mapsroutesmanager\shortcode;

use com\cminds\mapsroutesmanager\App;
use com\cminds\mapsroutesmanager\model\Route;
use com\cminds\mapsroutesmanager\controller\DashboardController;

class MyRoutesTableShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'my-routes-table';
	
	
	static function shortcode($atts = array()) {
		
		$atts = shortcode_atts(array(
			'controls' => 1,
			'addbtn' => 1,
		), $atts);
		
		DashboardController::embedAssets();
		
		$query = new \WP_Query(array(
			'author' => get_current_user_id(),
			'post_type' => Route::POST_TYPE,
			'posts_per_page' => 9999,
			'post_status' => array('publish', 'draft', 'pending'),
		));
		$routes = array_filter(array_map(array(App::namespaced('model\Route'), 'getInstance'), $query->posts));
		$out = DashboardController::loadFrontendView('index', compact('routes', 'atts'));
		
		return '<div class="cmmrm-my-routes-shortcode">'. $out .'</div>';
		
	}
	
	
}
