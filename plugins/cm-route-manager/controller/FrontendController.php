<?php

namespace com\cminds\mapsroutesmanager\controller;

use com\cminds\mapsroutesmanager\helper\RouteView;

use com\cminds\mapsroutesmanager\model\RouteTag;

use com\cminds\mapsroutesmanager\model\Category;

use com\cminds\mapsroutesmanager\model\Labels;

use com\cminds\mapsroutesmanager\App;

use com\cminds\mapsroutesmanager\model\Settings;

use com\cminds\mapsroutesmanager\model\Route;
use com\cminds\mapsroutesmanager\model\User;

class FrontendController extends DummyPageController {
	
	const URL_DASHBOARD = 'routes';
	const QUERY_DASHBOARD_PAGE = 'cmmrm_dashboard_page';
	const DASHBOARD_ADD = 'add';
	const DASHBOARD_IMPORT = 'import';
	const DASHBOARD_EDIT = 'edit';
	const DASHBOARD_DELETE = 'delete';
	
	const PARAM_FILTER_AUTHOR = 'route_author';
	
	static $actions = array(
		'init',
		'pre_get_posts' => array('args' => 1),
		'admin_head',
	);
	static $filters = array(
		'query_vars',
		'body_class',
		'cmmrm_get_index_filter_url' => array('args' => 2),
	);
	
	static function init() {
		
// 		flush_rewrite_rules(true);
		$slug = Settings::getOption(Settings::OPTION_PERMALINK_PREFIX);
		add_rewrite_rule( $slug . '/'. static::URL_DASHBOARD .'/(\w+)', add_query_arg(array(
			static::QUERY_DASHBOARD_PAGE => '$matches[1]'
		), 'index.php'), 'top' );
	}
	
	
	static function template_include($template) {
		global $wp_query;
// 		var_dump($wp_query->request);exit;
		
		$template = parent::template_include($template);
		if (static::isDummyPageRequired() AND FrontendController::isDashboard() AND 'edit' == static::getDashboardPage()) {
			// Custom template for editor page
// 			$template = App::path('view/frontend/dashboard/editor-template.php');
		}
		return $template;
	}
	
	
	static function query_vars($vars) {
		$vars[] = static::QUERY_DASHBOARD_PAGE;
		return $vars;
	}
	
	
	
	static function isDummyPageRequired(\WP_Query $query = null) {
		return (static::isThePage($query));
	}
	
	
	static function isThePage(\WP_Query $query = null) {
		if (empty($query)) $query = static::$query;
		return (static::isRoutePostType($query) OR static::isDashboard($query));
	}
	
	
	static function getDummyPostTitle() {
// 		debug_print_backtrace();exit;
		$title = Labels::getLocalized('route_index_title');
		if (static::isDashboard()) {
			switch (static::getDashboardPage()) {
				case static::DASHBOARD_ADD:
					$title = Labels::getLocalized('dashboard_add_route_title');
					break;
				case static::DASHBOARD_IMPORT:
					$title = Labels::getLocalized('dashboard_import_route_title');
					break;
				case static::DASHBOARD_EDIT:
					$title = Labels::getLocalized('dashboard_edit_route_title');
// 					if ($route = self::getRoute()) {
// 						$title .= ' | ' . $route->getTitle();
// 					}
					break;
				default:
					$title = Labels::getLocalized('dashboard_my_routes_title');
			}
		}
		else if (static::$query AND static::$query->is_404()) {
			$title = Labels::getLocalized('route_not_found');
		}
		else if (static::$query AND static::$query->is_single()) {
			$title = static::$query->post->post_title;
		}
		else if ($category = static::getCategory()) {
			$title = $category->getName();
		}
		else if ($author = filter_input(INPUT_GET, static::PARAM_FILTER_AUTHOR) AND $user = get_user_by('slug', $author)) {
			$title = Labels::getLocalized('route_index_for_author_title') .' '. $user->display_name;
		}
		
		if ($tag = static::getTag()) {
			if (empty($category)) $title = '';
			else if (!empty($title)) $title .= ', ';
			$title .= 'Tag: ' . $tag->getName();
		}
		
		return $title;
		
	}
	
	
	static function getCategory($query = null) {
		if (empty($query)) $query = static::$query;
		if (!empty($query->query['cmmrm_category']) AND $category = Category::getInstance($query->query['cmmrm_category'])) {
			return $category;
		}
	}
	
	
	static function getTag($query = null) {
		if (empty($query)) $query = static::$query;
		if (!empty($query->query['tag']) AND $tag = RouteTag::getInstance($query->query['tag'])) {
			return $tag;
		}
	}
	
	
	static function the_content($content) {
		
		global $withcomments, $post;
		
		if (static::isDummyPageRequired()) {
			
			$withcomments = true;
			$post = null;
			
			if (static::isDashboard()) {
				if (Route::canCreate()) {
					$method = array(App::namespaced('controller\DashboardController'), static::getDashboardPage() . 'View');
					if (method_exists($method[0], $method[1]) AND is_callable($method)) {
						return call_user_func($method, static::$query);
					} else {
						return Labels::getLocalized('dashboard_unknown_action_msg');
					}
				} else {
					return Labels::getLocalized('dashboard_access_denied_msg');
				}
			}
			else if (static::$query AND static::$query->is_404()) {
				return Labels::getLocalized('page_not_found');
			}
			else if (static::$query AND static::$query->is_single()) {
				return RouteController::singleView(static::$query);
			}
			else {
				return RouteController::indexView(static::$query);
			}
		}
		return $content;
	}
	
	
	static function isRoutePostType(\WP_Query $query = null) {
		if (empty($query)) $query = static::$query;
		return (!empty($query) AND ($query->get('post_type') == Route::POST_TYPE OR $query->get(Category::TAXONOMY)));
	}
	
	
	static function getRoute(\WP_Query $query = null) {
		$route = null;
		if (empty($query)) $query = static::$query;
		if (self::isDashboard($query) AND isset($_GET['id'])) {
			$route = Route::getInstance($_GET['id']);
		}
		else if (self::isRoutePostType($query) AND $query->is_single() AND !empty($query->posts[0])) {
			$route = Route::getInstance($query->posts[0]);
		} else {
			global $route;
		}
		return $route;
	}
	
	
	static function isRouteSinglePage(\WP_Query $query = null) {
		if (empty($query)) $query = static::$query;
		return (self::isRoutePostType($query) AND $query->is_single());
	}
	
	
	static function isDashboard(\WP_Query $query = null) {
		if (empty($query)) $query = static::$query;
		$page = self::getDashboardPage($query);
		return (!empty($page));
	}
	
	
	static function getDashboardPage(\WP_Query $query = null) {
		if (empty($query)) $query = static::$query;
		if (!empty($query)) return $query->get(static::QUERY_DASHBOARD_PAGE);
	}
	
	
	static function getUrl($action = '', $params = array()) {
		$slug = Settings::getOption(Settings::OPTION_PERMALINK_PREFIX);
		$url = home_url($slug . '/'. $action);
		return add_query_arg(urlencode_deep($params), trailingslashit($url));
	}
	
	
	static function wp_title($title, $sep = '', $seplocation = 'right') {
		if (static::isDummyPageRequired()) {
			$title = static::getDummyPostTitle();
			if (!FrontendController::isDashboard() AND (static::$query AND static::$query->is_single()) OR static::getCategory() OR static::getTag()) {
				$title .= ' | ' . Labels::getLocalized('single_route_title_part');
			}
			$title .= ' | ' . get_option('blogname');
		}
		return $title;
	}
	
	
	static function enqueueStyle() {
		wp_enqueue_style('cmmrm-frontend');
		add_action('wp_footer', array(__CLASS__, 'displayCustomCSS'));
	}
	
	
	static function displayCustomCSS() {
		$bgcolor = implode(',',
			array_map('hexdec',
				str_split(
					str_replace('#', '',
						Settings::getMapLabelBgcolor()
					), 2
				)
			)
		);
		echo '<style type="text/css">
			.cmmrm-map-label {background-color: rgba(' . $bgcolor . ', 0.9) !important;}
			.cmmrm-routes-archive-tiles .cmmrm-shortcode-route-snippet {width: '. intval(Settings::getOption(Settings::OPTION_INDEX_TILE_WIDTH)) .'px;}
			.cmmrm-routes-archive-tiles .cmmrm-shortcode-route-snippet .cmmrm-route-featured-image-large {height: '. RouteView::getTileImageMaxHeight() .'px;}
			*[data-fancy="1"] .cmmrm-route-params, .cmmrm-shortcode-route-snippet[data-fancy="1"][data-layout="tiles"] {background-color: '. Settings::getOption(Settings::OPTION_FANCY_BGCOLOR) .';}
			*[data-fancy="1"][data-layout="tiles"] .cmmrm-route-params {background-color: transparent !important;}
			.cmmrm-infowindow img {max-width: '. Settings::getOption(Settings::OPTION_ROUTE_MAP_LOCATION_INFO_WINDOW_IMAGE_MAX_WIDTH) .'px;}
			' . Settings::getOption(Settings::OPTION_CUSTOM_CSS) . '
		</style>';
	}
	
	
	
	static function cmmrm_get_index_filter_url($url, $includeCategory) {
		return static::getFilterUrl($includeCategory);
	}
	
	
	static function getFilterUrl($includeCategory = false) {
		
		if (!FrontendController::$query) {
			return FrontendController::getUrl();
		}
		
		if ($includeCategory AND $slug = FrontendController::$query->get(Category::TAXONOMY) AND $category = Category::getInstance($slug)) {
			$url = $category->getPermalink();
		} else {
			$url = FrontendController::getUrl();
		}
		
// 		if ($slug = FrontendController::$query->get(Difficulty::TAXONOMY)) {
// 			$url = add_query_arg(Difficulty::TAXONOMY, urlencode($slug), $url);
// 		}
// 		if ($slug = FrontendController::$query->get(RouteType::TAXONOMY)) {
// 			$url = add_query_arg(RouteType::TAXONOMY, urlencode($slug), $url);
// 		}
		
		return apply_filters('cmmrm_get_filter_url', $url, FrontendController::$query, $includeCategory);
		
	}
	
	
	static function body_class($class) {
		global $wp_query;
		
		$isRoute = static::isRoutePostType();
		$isDashboard = static::isDashboard();
		
		if ($isRoute) {
			if (static::isRouteSinglePage()) {
				$class[] = 'cmmrm-single';
			} else {
				$class[] = 'cmmrm-archive';
			}
		}
		
		if ($isDashboard) {
			$class[] = 'cmmrm-dashboard';
			if ($page = static::getDashboardPage()) {
				$class[] = 'cmmrm-dashboard-' . $page;
			}
		}
		
		if ($isRoute OR $isDashboard) {
			// Divi theme fix:
			$class[] = 'et_right_sidebar';
		}
		
		return $class;
	}
	
	
	static function pre_get_posts(\WP_Query $query) {
		if ($author = filter_input(INPUT_GET, static::PARAM_FILTER_AUTHOR) AND $query->get('post_type') == Route::POST_TYPE) {
			$query->set('author_name', $author);
		}
	}
	
	
	static function admin_head() {
		$roles = Settings::getOption(Settings::OPTION_ACCESS_MEDIA_LIBRARY_ROLES);
		if (User::hasRole($roles)) return;
		echo '<script type="text/javascript">
			document.addEventListener("DOMContentLoaded", function() {
				if (top && top.document && top.document.body && top.document.body.className.indexOf("cmmrm-dashboard") > -1) {
					document.getElementById("tab-library").style.display = "none";
				}
			});
		</script>';
	}
	
	
}
