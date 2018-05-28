<?php

namespace com\cminds\mapsroutesmanager\controller;

use com\cminds\mapsroutesmanager\model\Labels;

use com\cminds\mapsroutesmanager\App;

use com\cminds\mapsroutesmanager\model\Route;

use com\cminds\mapsroutesmanager\model\Settings;
use com\cminds\mapsroutesmanager\model\Category;

class RouteController extends Controller {
	
	const PARAM_PAGE = 'page';
	
	static $filters = array(
		'cmmrm_route_index_single' => array('args' => 2),
		'posts_search' => array('args' => 2),
	);
	
	static $actions = array(
		array('name' => 'pre_get_posts', 'args' => 1, 'priority' => PHP_INT_MAX),
		array('name' => 'get_template_part_cmmrm', 'args' => 2),
		'cmmrm_route_index_filter',
		'cmmrm_route_single_before',
		'cmmrm_route_single_map',
		'cmmrm_route_single_details',
		'cmmrm_route_single_locations',
		'wp_enqueue_scripts',
		'cmmrm_load_single_page_scripts',
		'before_delete_post' => array('args' => 1),
	);
	
	static $mapId = null;
	
	
	static function indexView(\WP_Query $query) {
		global $wp_query;
		if (Route::canViewIndex()) {
			$routes = array_map(array(App::namespaced('model\Route'), 'getInstance'), $query->posts);
			$totalRoutesNumber = FrontendController::$query->found_posts;
			$displayParams = Settings::getOption(Settings::OPTION_INDEX_ROUTE_PARAMS);
			$out = self::loadFrontendView('index', compact('routes', 'totalRoutesNumber', 'displayParams'));
		} else {
			$out = Labels::getLocalized('route_index_access_denied');
		}
		$wp_query->reset_postdata();
		return $out;
	}
	
	
	static function singleView(\WP_Query $query) {
		global $id, $post, $withcomments;
		
		$withcomments = true;
		$post = null;
		
		if (!empty($query->posts[0]) AND $route = Route::getInstance($query->posts[0])) {
			if ($route->canView()) {
				
				$post = $query->posts[0];
				$mapId = static::$mapId = 'cmmrm-route-'. mt_rand();
				
				$id = $route->getId();
				$route->incrementViews();
				$displayParams = Settings::getOption(Settings::OPTION_SINGLE_ROUTE_PARAMS);
				
				return self::loadFrontendView('single', compact('route', 'mapId', 'displayParams'));
				
			} else {
				return Labels::getLocalized('route_access_denied');
			}
			
		} else {
			return Labels::getLocalized('route_not_found');
		}
	}
	
	
	static function wp_enqueue_scripts() {
		if (FrontendController::isRoutePostType()) {
			FrontendController::enqueueStyle();
		}
		if (FrontendController::isRouteSinglePage()) {
// 			wp_enqueue_style('thickbox');
			do_action('cmmrm_load_single_page_scripts');
		} else {
			wp_enqueue_script('cmmrm-index-filter');
		}
	}
	
	
	static function cmmrm_load_single_page_scripts() {
// 		wp_enqueue_script('cmmrm-route-map');
		wp_enqueue_script('cmmrm-widget-single-route');
	}
	
	
	static function get_template_part_cmmrm($slug, $name) {
		switch ($name) {
			case 'route-index-filter':
				self::displayIndexTop();
				do_action('cmmrm_route_index_filter');
				break;
			case 'route-index-map':
				self::displayIndexMap();
				break;
			case 'route-single-before':
				do_action('cmmrm_route_single_before');
				break;
			case 'route-single-map':
				do_action('cmmrm_route_single_map');
				break;
			case 'route-single-details':
				do_action('cmmrm_route_single_details');
				break;
			case 'route-single-locations':
				do_action('cmmrm_route_single_locations');
				break;
			case 'pagination':
				static::displayPagination();
		}
	}
	
	
	static function displayIndexTop() {
		$text = trim(Settings::getOption(Settings::OPTION_INDEX_TEXT_TOP));
		$text = wpautop($text);
		$text = do_shortcode($text);
		echo self::loadFrontendView('index-top', compact('text'));
	}
	
	
	static function displayIndexMap() {
		$routes = Route::getIndexMapJSLocations(FrontendController::$query);
// 		if (!empty($routes)) {
// 			wp_enqueue_script('cmmrm-index-map');
			wp_enqueue_script('cmmrm-widget-index-map');
			echo self::loadFrontendView('index-map', compact('routes'));
// 		}
	}
	
	
	static function displayPagination() {
		$query = FrontendController::$query;
		if (FrontendController::isRoutePostType() AND $query->is_archive()) {
			$limit = Route::getPaginationLimit();
			if ($query->found_posts > $limit) {
				$total_pages = $query->max_num_pages;
				$page = $query->get('paged');
				if (empty($page)) $page = 1;
				$base_url = static::getPaginationBaseUrl();
				echo self::loadView('frontend/common/pagination', compact('total_pages', 'page', 'base_url'));
			}
		}
	}
	
	
	
	static function getPaginationBaseUrl() {
		return preg_replace('~/page/[0-9]+/~', '/', $_SERVER['REQUEST_URI']);
	}
	
	
	static function cmmrm_route_index_filter() {
		if ($category = FrontendController::getCategory()) {
			$searchFormUrl = $category->getPermalink();
		} else {
			$searchFormUrl = FrontendController::getUrl();
		}
		if (App::isPro()) {
			echo self::loadFrontendView('index-filter', compact('searchFormUrl'));
		}
	}
	
	
	static function cmmrm_route_single_before() {
		$route = FrontendController::getRoute();
		echo self::loadFrontendView('single-before', compact('route'));
	}
	
	static function cmmrm_route_single_map() {
		$mapId = static::$mapId;
		$route = FrontendController::getRoute();
		$atts = array();
		echo self::loadFrontendView('single-map', compact('route', 'mapId', 'atts'));
	}
	
	static function cmmrm_route_single_details() {
		$route = FrontendController::getRoute();
		echo self::loadFrontendView('single-details', compact('route'));
	}
	
	static function cmmrm_route_single_locations() {
		$route = FrontendController::getRoute();
		if ($route->showLocationsSection()) {
			echo self::loadFrontendView('single-locations', compact('route'));
		}
	}
	
	
	static function cmmrm_route_index_single($output, $route) {
		return self::loadFrontendView('index-single', compact('route'));
	}
	
	
	static function getDashboardUrl($action = 'index', $params = array()) {
		return FrontendController::getUrl(FrontendController::URL_DASHBOARD . '/' . $action, $params);
	}
	
	
	static function pre_get_posts(\WP_Query $query) {
		if (is_admin()) return;
		if ($query->is_main_query() AND FrontendController::isRoutePostType($query)) {
// 			$query->set('post_type', Route::POST_TYPE);
			$query->set('posts_per_page', Route::getPaginationLimit());
			Route::registerQueryOrder($query);
			if (!FrontendController::isDashboard($query)) {
// 				$query->set('post_status', 'publish');
			}
		}
		if ($query->is_main_query() AND $categorySlug = $query->get(Category::TAXONOMY)) {
			$query->set('post_type', Route::POST_TYPE);
		}
		
	}
	
	
	
	static function before_delete_post($postId) {
		if ($route = Route::getInstance($postId) AND $route instanceof Route) {
			
			global $wpdb;
			
			// Delete imported GPX/KML file
			if ($file = $route->getOriginalImportFile()) {
// 				var_dump($file);
				if ($path = $file->getFilePath() AND is_writable($path) AND is_file($path)) {
// 					var_dump($path);
					unlink($path);
				}
			}
			
			// Delete all child posts
			$locationsIds = $route->getLocationsIds();
			$parentIds = $locationsIds;
			$parentIds[] = $postId;
// 			var_dump($parentIds);
			$childPostsIds = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_parent IN (". implode(',', $parentIds) .')');
// 			var_dump($childPostsIds);
			foreach ($childPostsIds as $id) {
				wp_delete_post($id, true);
			}
			
// 			die('end');
			
		}
	}
	
	
	static function posts_search($sql, \WP_Query $query) {
		
		if (!Settings::getOption(Settings::OPTION_INDEX_SEARCH_WHOLE_WORDS)) {
			return $sql;
		}
		
// 		var_dump($sql);
		preg_match_all('~\(\w+posts\.post_\w+ LIKE \'%.+%\'\)~U', $sql, $matches, PREG_SET_ORDER);
// 		var_dump($matches);
		foreach ($matches as $match) {
// 			var_dump($match);
			$new = '';
			$new .= str_replace('%\')', ' %\')', str_replace('LIKE \'%', 'LIKE \'', $match[0])) . ' OR ';
			$new .= str_replace('%\')', '\')', str_replace('LIKE \'%', 'LIKE \'% ', $match[0])) . ' OR ';
			$new .= str_replace('%\')', ' %\')', str_replace('LIKE \'%', 'LIKE \'% ', $match[0])) . ' OR ';
			$new .= str_replace('%\')', '\')', str_replace('LIKE \'%', 'LIKE \'', $match[0])) . PHP_EOL;
			$sql = preg_replace($match[0], $new, $sql);
		}
		
// 		var_dump($sql);
		
// 		exit;
		return $sql;
	}
	
	
}
