<?php

namespace com\cminds\mapsroutesmanager\model;

use com\cminds\mapsroutesmanager\helper\Polyline;

use com\cminds\mapsroutesmanager\helper\GpxHelper;

use com\cminds\mapsroutesmanager\helper\KmlHelper;

use com\cminds\mapsroutesmanager\helper\PolylineEncoder;

use com\cminds\mapsroutesmanager\controller\RouteController;

use com\cminds\mapsroutesmanager\controller\DashboardController;

use com\cminds\mapsroutesmanager\model\Category;
use com\cminds\mapsroutesmanager\App;
use com\cminds\mapsroutesmanager\helper\RemoteConnection;

class Route extends PostType {
	
	const POST_TYPE = 'cmmrm_route';
	
	const META_RATE = '_cmmrm_route_rate';
	const META_RATE_USER_ID = '_cmmrm_route_rate_user_id';
	const META_RATE_TIME = '_cmmrm_route_rate_time';
	const META_VIEWS = '_cmmrm_views';
	
	const META_DISTANCE = '_cmmrm_distance'; // in meters
	const META_DURATION = '_cmmrm_duration';
	const META_AVG_SPEED = '_cmmrm_avg_speed';
	const META_MAX_ELEVATION = '_cmmrm_max_elevation';
	const META_MIN_ELEVATION = '_cmmrm_min_elevation';
	const META_ELEVATION_GAIN = '_cmmrm_elevation_gain';
	const META_ELEVATION_DESCENT = '_cmmrm_elevation_descent';
	const META_DIRECTIONS_RESPONSE = '_cmmrm_directions_response';
	const META_ELEVATION_RESPONSE = '_cmmrm_elevation_response';
	const META_TRAVEL_MODE = '_cmmrm_travel_mode';
	const META_USE_MINOR_LENGTH_UNITS = '_cmmrm_use_minor_length_units';
	const META_SHOW_DIRECTIONAL_ARROWS = 'cmmrm_use_directional_arrows';
	const META_SHOW_LOCATIONS_SECTION = 'cmmrm_show_locations_section';
	const META_SHOW_WEATHER_PER_LOCATION = '_cmmrm_show_weather_per_location';
	const META_PATH_COLOR = '_cmmrm_path_color';
	const META_OVERVIEW_PATH = '_cmmrm_overview_path';
	const META_WAYPOINTS = '_cmmrm_waypoints';
	const META_WAYPOINTS_STRING = '_cmmrm_waypoints_string';
	const META_MODERATOR_ACCEPTED = '_cmmrm_moderator_accepted';
	
	const WAYPOINTS_LIMIT = 512;
	
	const DEFAULT_TRAVEL_MODE = 'DIRECT';
	
	const TRANSIENT_GEOLOCATION_BY_ADDR_CACHE = 'cmmrm_geoloc_by_addr_cache';
	
	static $travelModes = array(
		Settings::TRAVEL_MODE_WALKING,
		Settings::TRAVEL_MODE_BICYCLING,
		Settings::TRAVEL_MODE_DRIVING,
		Settings::TRAVEL_MODE_DIRECT,
	);
	
	
	static protected $postTypeOptions = array(
		'label' => 'Route',
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_admin_bar' => true,
		'show_in_menu' => App::PREFIX,
		'hierarchical' => false,
		'supports' => array('title', 'editor'),
		'has_archive' => true,
	);
	
	
	
	static protected function getPostTypeLabels() {
		$singular = ucfirst(Labels::getLocalized('route'));
		$plural = ucfirst(Labels::getLocalized('routes'));
		return array(
			'name' => $plural,
            'singular_name' => $singular,
            'add_new' => sprintf(__('Add %s', App::SLUG), $singular),
            'add_new_item' => sprintf(__('Add New %s', App::SLUG), $singular),
            'edit_item' => sprintf(__('Edit %s', App::SLUG), $singular),
            'new_item' => sprintf(__('New %s', App::SLUG), $singular),
            'all_items' => $plural,
            'view_item' => sprintf(__('View %s', App::SLUG), $singular),
            'search_items' => sprintf(__('Search %s', App::SLUG), $plural),
            'not_found' => sprintf(__('No %s found', App::SLUG), $plural),
            'not_found_in_trash' => sprintf(__('No %s found in Trash', App::SLUG), $plural),
            'menu_name' => App::getPluginName()
		);
	}
	
	
	static function init() {
		static::$postTypeOptions['rewrite'] = array('slug' => Settings::getOption(Settings::OPTION_PERMALINK_PREFIX));
		if (App::isPro()) {
			static::$postTypeOptions['taxonomies'] = apply_filters('cmmrm_route_post_type_taxonomies', array(Category::TAXONOMY, RouteTag::TAXONOMY));
		}
		parent::init();
	}
	
	
	
	/**
	 * Get instance
	 * 
	 * @param WP_Post|int $post Post object or ID
	 * @return com\cminds\mapsroutesmanager\model\Route
	 */
	static function getInstance($post) {
		return parent::getInstance($post);
	}
	
	
	
	function getEditUrl() {
		return admin_url(sprintf('post.php?action=edit&post=%d',
			$this->getId()
		));
	}
	
	
	function getCategories($fields = TaxonomyTerm::FIELDS_MODEL, $params = array()) {
		return Category::getPostTerms($this->getId(), $fields, $params);
	}
	
	
	
	function getTags($fields = TaxonomyTerm::FIELDS_MODEL, $params = array()) {
		return RouteTag::getPostTerms($this->getId(), $fields, $params);
	}
	
	
	function setCategories($categoriesIds) {
		return wp_set_post_terms($this->getId(), $categoriesIds, Category::TAXONOMY, $append = false);
	}
	
	
	function setCategoriesByNames(array $categoriesNames) {
		$existingCategories = \get_terms(Category::TAXONOMY, array('name' => $categoriesNames, 'fields' => Category::FIELDS_ID_NAME, 'hide_empty' => 0));
		$existingCategoriesIds = array_keys($existingCategories);
		$notExisting = array_diff($categoriesNames, $existingCategories);
		foreach ($notExisting as $name) {
			$term = \wp_create_term($name, Category::TAXONOMY);
			if (!\is_wp_error($term)) {
				$existingCategoriesIds[] = (is_array($term) ? $term['term_id'] : (is_numeric($term) ? $term : 0));
			}
		}
		$existingCategoriesIds = array_filter($existingCategoriesIds);
		return \wp_set_post_terms($this->getId(), $existingCategoriesIds, Category::TAXONOMY, $append = false);
	}
	
	
	function addDefaultCategory() {
		$term = get_term('General', Category::TAXONOMY);
		if (empty($term)) {
			$terms = get_terms(array(Category::TAXONOMY), array('hide_empty' => false));
			if (!empty($terms)) {
				$term = reset($terms);
			}
		}
		if (!empty($term)) {
			wp_set_post_terms($this->getId(), $term->term_id, Category::TAXONOMY);
		}
	}
	
	
	function getUserEditUrl() {
		return RouteController::getDashboardUrl('edit', array('id' => $this->getId()));
	}
	
	
	function getUserDeleteUrl() {
		return RouteController::getDashboardUrl('delete', array(
			'id' => $this->getId(),
			'nonce' => wp_create_nonce(DashboardController::DELETE_NONCE),
		));
	}
	
	
	function getImages() {
		if ($id = $this->getId()) {
			return array_values(array_filter(Attachment::getForPost($id), function($image) { return ($image->isImage() OR $image->isVideo()); }));
		} else {
			return array();
		}
	}
	
	
	function getMapThumbUrl($size) {
		$color = '0x' . preg_replace('~[^0-9A-F]~i', '', $this->getPathColor());
		$pathParams = array('weight' => 3, 'color' => $color, 'enc' => $this->getOverviewPath());
		foreach ($pathParams as $name => &$val) {
			$val = $name .':'. $val;
		}
		$pathParams = implode('|', $pathParams);
		return add_query_arg(urlencode_deep(array(
			'size' => $size,
			'maptype' => 'roadmap',
			'key' => Settings::getOption(Settings::OPTION_GOOGLE_MAPS_APP_KEY),
			'path' => $pathParams,
		)), 'https://maps.googleapis.com/maps/api/staticmap');
	}
	
	
	function getImagesIds() {
		if ($id = $this->getId()) {
			return get_posts(array(
				'posts_per_page' => -1,
				'post_type' => Attachment::POST_TYPE,
				'post_status' => 'any',
				'post_parent' => $id,
				'fields' => 'ids',
				'orderby' => 'menu_order',
				'order' => 'asc',
			));
		} else {
			return array();
		}
	}
	
	
	function setImages($images) {
		global $wpdb;
		
		if (!is_array($images)) {
			$images = array_filter(explode(',', $images));
		}
		
		$currentIds = $this->getImagesIds();
		$postedImagesIds = array_filter(array_map('intval', array_map('trim', $images)));
		
		$toAdd = array_diff($postedImagesIds, $currentIds);
		$toDelete = array_diff($currentIds, $postedImagesIds);
		
		if ($originalImportedFile = $this->getOriginalImportFile()) {
			$toDelete = array_diff($toDelete, array($originalImportedFile->getId()));
		}
		
		if (!empty($toAdd)) $wpdb->query("UPDATE $wpdb->posts SET post_parent = ". intval($this->getId()) ." WHERE ID IN (" . implode(',', $toAdd) . ")");
		if (!empty($toDelete)) $wpdb->query("UPDATE $wpdb->posts SET post_parent = 0 WHERE ID IN (" . implode(',', $toDelete) . ")");
		
		// Change the sorting order
		foreach ($images as $i => $id) {
			$wpdb->query("UPDATE $wpdb->posts SET menu_order = ". intval($i+1) ." WHERE ID = ". intval($id) ." LIMIT 1");
		}
		
	}
	
	
	function getLocationsIds() {
		if ($id = $this->getId()) {
			return get_posts(array(
				'fields' => 'ids',
				'post_type' => Location::POST_TYPE,
				'post_parent' => $id,
				'post_status' => 'any',
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'asc',
			));
		} else return array();
	}
	
	
	function getLocations() {
		if ($id = $this->getId()) {
			return array_map(array(App::namespaced('model\Location'), 'getInstance'), get_posts(array(
				'post_type' => Location::POST_TYPE,
				'post_parent' => $id,
				'post_status' => 'any',
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'asc',
				'meta_key' => Location::META_LOCATION_TYPE,
				'meta_value' => Location::TYPE_LOCATION,
			)));
		} else return array();
	}
	
	
	function getJSRouteData() {
		return array(
			'id' => $this->getId(),
			'title' => $this->getTitle(),
			'travelMode' => $this->getTravelMode(),
			'overviewPath' => $this->getOverviewPath(), //'eykhHqtb|AeOqQgG[qV?k^W}NpFaCaA_GqOwCwUzPgpBzGo}ClCgTqC{VuAo^`K{b@aJgSCw`@~Ra~@rEaPaHybAFsf@nQeo@jM{|@`G_K~Fub@|Rcp@jJ}W~Eaa@xV{bAbt@m|@dTun@dMgbA`Uu}@~Fyh@kOweC~Bo_@zKm]bUwYlGyTnFuzAnBmYlVcu@dg@awAtMgbAp]}|DtNq_Bt\_pB`PohAhKyN~a@oB|NeL`KinAxBmiBs@{|@xLqh@fr@cr@|Wg]xGew@fEkM|SePljAqy@hMe[n`@oG`]wIj`@iDnO}HpImh@AubAz`@_vAxGkTlFcGzQqAzC{BhXrIhZqH`NyJrJOlNsDjGhGrGlQ~XlUtOtQpRkQtT_b@`OyLlRmWl`@q|@rN}UrHfFp]_Cll@jArIiExMgAdNqMhHAdO}BnEeCmAaLvD}@zJaHtW`ExQx@`UW~MyQpEsAfEzIjFgC~h@gYbLoItRwKxKoMdIcF|LoT~HiHrNiEfPiDrEbCzJxB`KmAfCsCpBx@bB`HBvBC_EcCwFxIgHkD_JbAue@~N}{BtC{YzOkZrDsK}@uCqCmIi@_AoI}@mOyKiN}WyUmO_j@mYoNmMiZf^_Sp]uThWwJd^wXff@oPfi@sQxj@mL~PxAjX~DnFnGfLjQjWjSv`@`Khe@nIzXzJlPfe@iYpCcI~McKvHqQ~LwEf\gHbT`GxHiDlGFDbKBoJiEiBmHvEcUuDmZ`EqO~EiFfNaLdOkGtGqc@dX}NhMcg@vV{DmH_Ee@cNtSyS]cSVyJeCmLeBuIbIsFF`B`M}IdDuSdB{W|QyIo@kDlD{TiC}f@`DgQv@iDcF}B?yk@xqA}i@dq@ia@dj@_L{Oq\uVsOuZid@tJo_@tNq\aGwM~BeHe@kF`QkPxh@kHpUsJl^g@hYmArbAsNvYcSCyg@bNq[pCkNnEoFpIoM~YiwAv`A_Hf^_Dbb@wKhV_n@dh@c\fl@cAhdC}GnnBkKd\q^xD}QtFwBaDx@gHFe@lAgJ_BdI[BMb@SpKiXjlBwX~cBsOncBc\zxDyRphAyg@hvA}Pbi@_Cn`@Jj`@eFdu@q^|l@mJ|^aBzf@rLbuAfA`l@{_@llBaMb`A{Y`n@w\d^yRp\uSd~@qDf`@mQ`f@_UvaAyDlN{QxgAgOl^h@bFrGngBcRhv@iHzh@nGnSlC~RyKxb@dHd_@w@pZmBnYa@h^]x]tExCzRrNtDfNhg@b^zInLrAtD_EvDgMzv@b@hV`DpRvFfSgAjIbApKhFxJ^|@',
// 			'waypoints' => $this->getWaypoints(),
			'pathColor' => $this->getPathColor(),
			'showDirectionalArrows' => ($this->showDirectionalArrows() ? true : false),
// 			'locations' => $this->getJSLocations(),
			'distance' => $this->getDistance(),
			'duration' => $this->getDuration(),
			'avgSpeed' => $this->getAvgSpeed(),
			'minElevation' => $this->getMinElevation(),
			'maxElevation' => $this->getMaxElevation(),
			'elevationGain' => $this->getElevationGain(),
			'elevationDescent' => $this->getElevationDescent(),
		);
	}
	
	
	function getJSLocations() {
		return array_map(function(Location $location) {
			return array(
				'id' => $location->getId(),
				'name' => $location->getTitle(),
				'lat' => $location->getLat(),
				'lng' => $location->getLong(),
				'description' => $location->getContent(),
				'type' => $location->getLocationType(),
				'address' => $location->getAddress(),
				'icon' => $location->getIcon(),
				'iconSize' => $location->getIconSize(),
				'images' => array_map(function(Attachment $image) {
					return array(
						'id' => $image->getId(),
						'url' => $image->getImageUrl(Attachment::IMAGE_SIZE_FULL),
						'thumb' => $image->getImageUrl(Attachment::IMAGE_SIZE_THUMB)
					);
				}, $location->getImages()),
				'infoWindowContent' => (App::isPro() ? $location->getInfoWindowContent() : ''),
			);
		}, $this->getLocations());
	}
	
	
	static function getIndexMapJSLocations(\WP_Query $query) {
		global $wpdb;
		
		$locQuery = new \WP_Query(array_merge($query->query, array(
			'post_type' => Route::POST_TYPE,
			'fields' => 'ids',
			'posts_per_page' => -1,
		)));
		$postsIds = $locQuery->get_posts();
		
		if (empty($postsIds)) {
			return array();
		}
		
		$sql = $wpdb->prepare("SELECT
				r.*,
				r.post_title AS name,
				lm_lat.meta_value AS lat,
				lm_lon.meta_value AS `long`,
				rm_pc.meta_value AS `pathColor`,
				rm_op.meta_value AS `overviewPath`,
				rm_ws.meta_value AS `waypointsString`
			FROM $wpdb->posts r
			LEFT JOIN $wpdb->posts l ON l.post_parent = r.ID AND l.post_type = %s AND l.menu_order = 1
			LEFT JOIN $wpdb->postmeta lm_lat ON lm_lat.post_id = l.ID AND lm_lat.meta_key = %s
			LEFT JOIN $wpdb->postmeta lm_lon ON lm_lon.post_id = l.ID AND lm_lon.meta_key = %s
			LEFT JOIN $wpdb->postmeta rm_pc ON rm_pc.post_id = r.ID AND rm_pc.meta_key = %s
			LEFT JOIN $wpdb->postmeta rm_op ON rm_op.post_id = r.ID AND rm_op.meta_key = %s
			LEFT JOIN $wpdb->postmeta rm_ws ON rm_ws.post_id = r.ID AND rm_ws.meta_key = %s
			WHERE r.ID IN (" . implode(',', $postsIds) . ")
			",
			Location::POST_TYPE,
			Location::META_LAT,
			Location::META_LONG,
			Route::META_PATH_COLOR,
			Route::META_OVERVIEW_PATH,
			Route::META_WAYPOINTS_STRING
		);
		
// 		var_dump($sql);
		
		$routes = $wpdb->get_results($sql, ARRAY_A);
		
		foreach ($routes as $i => &$row) {
			/* @var $route Route */
			$route = new Route($row);
			$routes[$i]['permalink'] = $route->getPermalink();
			$routes[$i]['type'] = Location::TYPE_LOCATION;
			$startingPoint = $route->getStartingPointCoords();
			if ($startingPoint) {
				$row['lat'] = $startingPoint[0];
				$row['long'] = $startingPoint[1];
			}
			
		}
// 		var_dump($routes);
		return $routes;
		
	}
	
	
	function getStartingPointCoords() {
		$overviewPath = $this->getOverviewPath();
		if ($overviewPath) {
			$encoder = new PolylineEncoder();
			$points = $encoder->decodePolylineToArray($overviewPath);
			if (is_array($points) AND count($points) > 0) {
				return reset($points);
			}
		}
		if ($location = $this->getFirstLocation()) {
			return array(
				$location->getLat(),
				$location->getLong(),
			);
		}
	}
	
	
	function getFirstLocation() {
		$locations = $this->getLocations();
		return reset($locations);
	}
	
	
	function canEdit($userId = null) {
		if (is_null($userId)) $userId = get_current_user_id();
		return (user_can($userId, 'manage_options') OR ($userId == $this->getAuthorId() AND self::canCreate($userId)));
	}
	
	
	static function canCreate($userId = null) {
		$access = Settings::getOption(Settings::OPTION_ACCESS_MAP_CREATE);
		if (empty($access)) $access = Settings::ACCESS_USER;
		return self::checkAccess(
			$access,
			$capability = Settings::getOption(Settings::OPTION_ACCESS_MAP_CREATE_CAP),
			$userId
		);
	}
	
	
	function canView($userId = null) {
		$access = Settings::getOption(Settings::OPTION_ACCESS_MAP_VIEW);
		if (empty($access)) $access = Settings::ACCESS_GUEST;
		return self::checkAccess(
			$access,
			$capability = Settings::getOption(Settings::OPTION_ACCESS_MAP_VIEW_CAP),
			$userId
		);
	}
	
	
	static function canViewIndex($userId = null) {
		$access = Settings::getOption(Settings::OPTION_ACCESS_MAP_INDEX);
		if (empty($access)) $access = Settings::ACCESS_GUEST;
		return self::checkAccess(
			$access,
			$capability = Settings::getOption(Settings::OPTION_ACCESS_MAP_INDEX_CAP),
			$userId
		);
	}
	
	
	function canDelete($userId = null) {
		return $this->canEdit($userId);
	}
	
	
	function getRate() {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT SUM(meta_value)/COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
			$this->getId(),
			self::META_RATE
		));
	}
	
	
	function canRate() {
		return is_user_logged_in();
	}
	
	
	function didUserRate() {
		global $wpdb;
		$userId = get_current_user_id();
		if (empty($userId)) return null;
		$sql = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s AND meta_value = %d",
			$this->getId(),
			self::META_RATE_USER_ID .'%',
			$userId
		);
		$count = $wpdb->get_var($sql);
		return ($count > 0);
	}
	
	
	function rate($rate) {
		$id = add_post_meta($this->getId(), self::META_RATE, $rate, $unique= false);
		if ($id) {
			add_post_meta($this->getId(), self::META_RATE_TIME .'_'. $id, time());
			add_post_meta($this->getId(), self::META_RATE_USER_ID .'_'. $id, get_current_user_id());
			return $id;
		}
	}
	
	
	function getVotesNumber() {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
			$this->getId(),
			self::META_RATE
		));
	}
	
	
	function getRelatedRoutes($limit = 5) {
		return array_map(array(get_called_class(), 'getInstance'), get_posts(array(
			'posts_per_page' => $limit,
			'post_type' => static::POST_TYPE,
			'post_status' => 'publish',
			'orderby' => 'id',
			'order' => 'desc',
// 			'suppress_filters' => true,
// 			'category' => implode(',', $this->getCategories(Category::FIELDS_ID_SLUG)),
// 			'post__not_in' => get_option( 'sticky_posts' ),
			'exclude' => $this->getId(),
			'tax_query' => array(
				array(
					'taxonomy' => Category::TAXONOMY,
					'field' => 'id',
					'terms' => $this->getCategories(Category::FIELDS_IDS),
					'include_children' => false,
				),
				array(
					'taxonomy' => Tag::TAXONOMY,
					'field' => 'id',
					'terms' => $this->getTags(Tag::FIELDS_IDS),
				),
				'relation' => 'OR',
			),
		)));
	}
	
	
	function updateLocationsAltitudes() {
		$locations = $this->getLocations();
		if (!empty($locations)) {
			$result = Location::downloadEvelations(array_map(function(Location $location) {
				return array($location->getLat(), $location->getLong());
			}, $locations));
			foreach ($locations as $i => $location) {
				if (isset($result['results'][$i]) AND $location->getAltitude() != $result['results'][$i]['elevation']) {
					$location->setAltitude($result['results'][$i]['elevation']);
				}
			}
		}
	}
	
	
	
	function determineElevationParams() {
		
		$path = $this->getOverviewPath();
		$encoder = new PolylineEncoder();
		$coords = $encoder->decodePolylineToArray($path);
		
		$elevationResult = Location::downloadEvelations($coords);
		if (empty($elevationResult) OR !is_array($elevationResult)) {
			return $this;
		}
		
		$maxElevation = null;
		$minElevation = null;
		$gain = 0;
		$descent = 0;
		$lastElevation = null;
		foreach ($elevationResult['results'] as $row) {
			if (is_null($maxElevation) OR $row['elevation'] > $maxElevation) {
				$maxElevation = $row['elevation'];
			}
			if (is_null($minElevation) OR $row['elevation'] < $minElevation) {
				$minElevation = $row['elevation'];
			}
			if (!is_null($lastElevation)) {
				$gain += ((($row['elevation'] - $lastElevation) > 0) ? ($row['elevation'] - $lastElevation) : 0);
				$descent += ((($lastElevation - $row['elevation']) > 0) ? ($lastElevation - $row['elevation']) : 0);
			}
			$lastElevation = $row['elevation'];
		}
		
		if (!is_null($maxElevation)) {
			$this->setMaxElevation($maxElevation);
		}
		if (!is_null($minElevation)) {
			$this->setMinElevation($minElevation);
		}
		$this->setElevationGain($gain);
		$this->setElevationDescent($descent);
		
		return $this;
		
	}
	
	
	
	static function checkAccess($access, $capability, $userId = null) {
		if (is_null($userId)) $userId = get_current_user_id();
		
		if (user_can($userId, 'manage_options')) {
			return true;
		}
		
		switch ($access) {
			case Settings::ACCESS_GUEST:
				return true;
				break;
			case Settings::ACCESS_USER:
				return !empty($userId);
				break;
			case Settings::ACCESS_CAPABILITY:
				return (!empty($userId) AND user_can($userId, $capability));
			default:
				if (!empty($userId) AND $user = get_userdata($userId)) {
					return in_array($access, $user->roles);
				}
				break;
		}
		return false;
	}
	
	
	function getDistance() {
		return intval(get_post_meta($this->getId(), self::META_DISTANCE, $single = true));
	}
	
	
	function getFormattedDistance() {
		
		$dist = $this->getDistance();
		$useMinor = $this->useMinorLengthUnits();
		
		if (Settings::UNIT_FEET == Settings::getOption(Settings::OPTION_UNIT_LENGTH)) {
			$num = $dist/Settings::FEET_TO_METER;
			if (!$useMinor AND $num > Settings::FEET_IN_MILE) {
				return number_format(round($num/Settings::FEET_IN_MILE)) .' miles';
			} else {
				return number_format(floor($num)) .' ft';
			}
		} else {
			if (!$useMinor AND $dist > 2000) {
				return round($dist/1000) .' km';
			} else {
				return $dist .' m';
			}
		}
		
	}
	
	
	static function formatLength($dist) {
		if (Settings::UNIT_FEET == Settings::getOption(Settings::OPTION_UNIT_LENGTH)) {
			$num = $dist/Settings::FEET_TO_METER;
			if ($num > Settings::FEET_IN_MILE) {
				return number_format(round($num/Settings::FEET_IN_MILE)) .' miles';
			} else {
				return number_format(floor($num)) .' ft';
			}
		} else {
			if ($dist > 2000) {
				return round($dist/1000) .' km';
			} else {
				return $dist .' m';
			}
		}
	}
	
	
	static function formatElevation($dist) {
		if (Settings::UNIT_FEET == Settings::getOption(Settings::OPTION_UNIT_LENGTH)) {
			$num = round($dist/Settings::FEET_TO_METER);
			return number_format($num) .' ft';
		} else {
			return $dist .' m';
		}
	}
	
	
	static function formatSpeed($meterPerSec) {
		if (Settings::UNIT_FEET == Settings::getOption(Settings::OPTION_UNIT_LENGTH)) {
			return round($meterPerSec/Settings::FEET_TO_METER/Settings::FEET_IN_MILE*3600) . ' mph';
		} else {
			return round($meterPerSec * 3.6) . ' km/h';
		}
	}
	
	
	static function formatTime($sec) {
		$num = $sec;
		$label = round($num) .' s';
		if ($num > 60) {
			$num /= 60;
			$label = round($num) .' min';
		}
		if ($num > 60) {
			$label = floor($num/60) .' h '. ($num%60) .' min ';
		}
		return $label;
	}
	
	function setDistance($distMeters) {
		return update_post_meta($this->getId(), self::META_DISTANCE, $distMeters);
	}
	
	
// 	function determineDistance() {
		
// 		$dist = 0;
// 		$path = $this->getOverviewPath();
// 		$encoder = new PolylineEncoder();
// 		$points = $encoder->decodePolylineToArray($path);
// 		if (is_array($points) AND count($points) > 0) {
// 			$last = null;
// 			foreach ($points as $point) {
// 				if ($last) {
// 					$dist += Route::calculateDistance($point[0], $point[1], $last[0], $last[1]);
// 				}
// 				$last = $point;
// 			}
// 		}
	
// 		return $this->setDistance($dist);
// 	}
	
	
	function getDuration() {
		return intval(get_post_meta($this->getId(), self::META_DURATION, $single = true));
	}
	
	function setDuration($durationSec) {
		return update_post_meta($this->getId(), self::META_DURATION, $durationSec);
	}
	
	function determineDuration() {
		$sec = $this->getDistance() / $this->getAvgSpeed();
		return $this->setDuration($sec);
	}
	
	
	function getWaypoints() {
		return get_post_meta($this->getId(), self::META_WAYPOINTS, $single = true) ?: array();
	}
	
	
	function setWaypoints(array $waypoints) {
		return update_post_meta($this->getId(), self::META_WAYPOINTS, $waypoints);
	}
	
	
	function getWaypointsString() {
		return get_post_meta($this->getId(), self::META_WAYPOINTS_STRING, $single = true) ?: '';
	}
	
	
	function setWaypointsString($val) {
		return update_post_meta($this->getId(), self::META_WAYPOINTS_STRING, addslashes($val));
	}
	
	function getOverviewPath() {
		return (string)get_post_meta($this->getId(), self::META_OVERVIEW_PATH, $single = true);
	}
	
	
	
	function setOverviewPath($path) {
		return update_post_meta($this->getId(), self::META_OVERVIEW_PATH, addslashes($path));
	}
	
	
	function recalculateOverviewPath() {
		global $wpdb;
		$result = '';
		$points = $wpdb->get_results($wpdb->prepare("SELECT lat.meta_value AS latitude, lon.meta_value AS longitude
			FROM $wpdb->posts loc
			JOIN $wpdb->postmeta lat ON loc.ID = lat.post_id AND lat.meta_key = %s
			JOIN $wpdb->postmeta lon ON loc.ID = lon.post_id AND lon.meta_key = %s
			WHERE loc.post_parent = %d
				AND loc.post_type = %s
			ORDER BY loc.menu_order ASC
		", Location::META_LAT, Location::META_LONG, $this->getId(), Location::POST_TYPE), ARRAY_N);
		if (!empty($points)) {
			
// 			$result = Polyline::encodePoints($points);
			$polyline = new PolylineEncoder();
			$r = $polyline->encode($points);
			if (!empty($r->points)) {
				$result = $r->points;
			}
			
			if (strlen($result) > 0) {
				$this->setOverviewPath(stripslashes($result));
			}
		}
	}
	
	
	/**
	 * Get average speed in meters per second.
	 * 
	 * @return number
	 */
	function getAvgSpeed() {
		return get_post_meta($this->getId(), self::META_AVG_SPEED, $single = true);
	}
	
	/**
	 * Set average speed in meters per second.
	 * 
	 * @param float $speed AVG speed in meters per second.
	 */
	function setAvgSpeed($meterPerSec) {
		return update_post_meta($this->getId(), self::META_AVG_SPEED, $meterPerSec);
	}
	
	function determineAvgSpeed() {
		switch ($this->getTravelMode()) {
			case 'BICYCLING':
				$speed = 12; // km/h
				break;
			case 'DRIVING':
				$speed = 70; // km/h
				break;
			default:
				$speed = 4; // km/h
		}
		return $this->setAvgSpeed($speed * 1000/3600);
	}
	
	
	function getMaxElevation() {
		return intval(get_post_meta($this->getId(), self::META_MAX_ELEVATION, $single = true));
	}
	
	function setMaxElevation($maxElevation) {
		return update_post_meta($this->getId(), self::META_MAX_ELEVATION, $maxElevation);
	}
	
	function determineMaxElevation() {
		global $wpdb;
		$max = $wpdb->get_var($wpdb->prepare("SELECT MAX(al.meta_value)
			FROM $wpdb->posts loc
			JOIN $wpdb->postmeta al ON loc.ID = al.post_id AND al.meta_key = %s
			WHERE loc.post_parent = %d
				AND loc.post_type = %s
			", Location::META_ALTITUDE, $this->getId(), Location::POST_TYPE));
		return $this->setMaxElevation($max);
	}
	
	function getMinElevation() {
		return intval(get_post_meta($this->getId(), self::META_MIN_ELEVATION, $single = true));
	}
	
	function determineMinElevation() {
		global $wpdb;
		$min = $wpdb->get_var($wpdb->prepare("SELECT MIN(al.meta_value)
			FROM $wpdb->posts loc
			JOIN $wpdb->postmeta al ON loc.ID = al.post_id AND al.meta_key = %s
			WHERE loc.post_parent = %d
			AND loc.post_type = %s
			", Location::META_ALTITUDE, $this->getId(), Location::POST_TYPE));
			return $this->setMinElevation($min);
	}
	
	function setMinElevation($minElevation) {
		return update_post_meta($this->getId(), self::META_MIN_ELEVATION, $minElevation);
	}
	
	function getElevationGain() {
		return intval(get_post_meta($this->getId(), self::META_ELEVATION_GAIN, $single = true));
	}
	
	function setElevationGain($elevationGain) {
		return update_post_meta($this->getId(), self::META_ELEVATION_GAIN, $elevationGain);
	}
	
	function determineElevationGain() {
		global $wpdb;
		$altitude = $wpdb->get_results($wpdb->prepare("SELECT al.meta_value AS altitude
			FROM $wpdb->posts loc
			JOIN $wpdb->postmeta al ON loc.ID = al.post_id AND al.meta_key = %s
			WHERE loc.post_parent = %d
			AND loc.post_type = %s
			ORDER BY loc.menu_order ASC
			", Location::META_ALTITUDE, $this->getId(), Location::POST_TYPE), ARRAY_N);
		$gain = 0;
		$last = null;
		foreach ($altitude as $alt) {
			if (!is_null($last)) {
				$gain += ((($alt[0] - $last[0]) > 0) ? ($alt[0] - $last[0]) : 0);
			}
			$last = $alt;
		}
		return $this->setElevationGain($gain);
	}
	
	
	function determineElevationDescent() {
		global $wpdb;
		$descent = $wpdb->get_results($wpdb->prepare("SELECT al.meta_value AS altitude
			FROM $wpdb->posts loc
			JOIN $wpdb->postmeta al ON loc.ID = al.post_id AND al.meta_key = %s
			WHERE loc.post_parent = %d
			AND loc.post_type = %s
			ORDER BY loc.menu_order ASC
			", Location::META_ALTITUDE, $this->getId(), Location::POST_TYPE), ARRAY_N);
		$val = 0;
		$last = null;
		foreach ($descent as $desc) {
			if (!is_null($last)) {
				$val += ((($last[0] - $desc[0]) > 0) ? ($last[0] - $desc[0]) : 0);
			}
			$last = $desc;
		}
		return $this->setElevationDescent($val);
	}
	
	function getElevationDescent() {
		return intval(get_post_meta($this->getId(), self::META_ELEVATION_DESCENT, $single = true));
	}
	
	function setElevationDescent($elevationDescent) {
		return update_post_meta($this->getId(), self::META_ELEVATION_DESCENT, $elevationDescent);
	}
	
	
	function setDirectionResponse($response) {
		$val = array('json' => $response, 'time' => time());
		return add_post_meta($this->getId(), self::META_DIRECTIONS_RESPONSE, $val, $unique = false);
	}
	
	
	function setElevationResponse($response) {
		$val = array('json' => $response, 'time' => time());
		return add_post_meta($this->getId(), self::META_ELEVATION_RESPONSE, $val, $unique = false);
	}
	
	function getTravelMode() {
		$val = get_post_meta($this->getId(), self::META_TRAVEL_MODE, $single = true);
		if (empty($val)) $val = Settings::getOption(Settings::OPTION_DEFAULT_TRAVEL_MODE);
		if (empty($val)) $val = Settings::TRAVEL_MODE_DIRECT;
		return $val;
	}
	
	function setTravelMode($mode) {
		return update_post_meta($this->getId(), self::META_TRAVEL_MODE, $mode);
	}
	
	
	function useMinorLengthUnits() {
		return (1 == $this->getPostMeta(self::META_USE_MINOR_LENGTH_UNITS));
	}
	
	
	function setMinorLengthUnits($use) {
		return $this->setPostMeta(self::META_USE_MINOR_LENGTH_UNITS, intval($use));
	}
	
	
	function showDirectionalArrows() {
		$val = $this->getPostMeta(self::META_SHOW_DIRECTIONAL_ARROWS);
		if ($val === '' OR is_null($val)) {
			$val = Settings::getOption(Settings::OPTION_SINGLE_ROUTE_DIRECTIONAL_ARROWS);
		}
		return $val;
	}
	
	
	function setShowDirectionalArrows($use) {
		return $this->setPostMeta(self::META_SHOW_DIRECTIONAL_ARROWS, !empty($use) ? 1 : 0);
	}
	
	
	function showLocationsSection() {
		if (!App::isPro()) return true;
		$val = $this->getPostMeta(self::META_SHOW_LOCATIONS_SECTION);
// 		if ($val === '' OR is_null($val)) {
// 			$val = false;
// 		}
		return $val;
	}
	
	
	function setShowLocationsSection($val) {
		return $this->setPostMeta(self::META_SHOW_LOCATIONS_SECTION, !empty($val) ? 1 : 0);
	}

	function getPathColor() {
		$val = $this->getPostMeta(self::META_PATH_COLOR);
		return ((!is_null($val) AND strlen($val) > 0) ? $val : '#3377FF');
	}
	
	
	function setPathColor($value) {
		return $this->setPostMeta(self::META_PATH_COLOR, $value);
	}
	
	
	function showWeatherPerLocation() {
		return (1 == $this->getPostMeta(self::META_SHOW_WEATHER_PER_LOCATION));
	}
	
	
	function setWeatherPerLocation($val) {
		return $this->setPostMeta(self::META_SHOW_WEATHER_PER_LOCATION, intval($val));
	}
	
	
	static function getPaginationLimit() {
		return Settings::getOption(Settings::OPTION_PAGINATION_LIMIT);
	}
	
	
	function getPostMetaKey($name) {
		return $name;
	}
	
	static function getRouteParamsNames() {
		return array(
			self::META_DISTANCE => 'Distance',
			self::META_DURATION => 'Duration',
			self::META_MAX_ELEVATION => 'Max elevation',
			self::META_MIN_ELEVATION => 'Min elevation',
			self::META_ELEVATION_GAIN => 'Climb',
			self::META_ELEVATION_DESCENT => 'Descent',
			self::META_AVG_SPEED => 'AVG Speed',
		);
	}
	
	
	static function registerQueryOrder(\WP_Query $query, $orderby = null, $order = null) {
		$orderby = Settings::getIndexOrderBy();
		$order = Settings::getIndexOrder();
		switch ($orderby) {
			case Settings::ORDERBY_VIEWS:
				$query->set('meta_key', self::META_VIEWS);
				$orderby = 'meta_value_num';
				break;
		}
		$query->set('orderby', $orderby);
		$query->set('order', $order);
	}
	
	function setViews($val) {
		update_post_meta($this->getId(), self::META_VIEWS, $val);
		return $this;
	}
	
	
	function getViews() {
		return get_post_meta($this->getId(), self::META_VIEWS, $single = true);
	}
	
	
	function incrementViews() {
		$this->setViews($this->getViews() + 1);
	}
	

	function save() {
		$id = $this->getId();
		$result = parent::save();
		if (!$id) {
			$this->setViews(0);
		}
		return $result;
	}
	
	
	/**
	 * 
	 * @return \com\cminds\mapsroutesmanager\model\Attachment
	 */
	function getOriginalImportFile() {
		global $wpdb;
		if (App::isPro()) {
			$attachId = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = %s AND post_mime_type IN (%s, %s)",
				$this->getId(), Attachment::POST_TYPE, KmlHelper::MIME_TYPE, GpxHelper::MIME_TYPE));
			if ($attachId) {
				return Attachment::getInstance($attachId);
			}
		}
	}
	
	
	
	function acceptByModerator() {
		$this->setStatus('publish')->save();
		do_action('cmmrm_route_accepted_by_moderator', $this);
		return $this->setPostMeta(static::META_MODERATOR_ACCEPTED, 1);
	}
	
	
	function trashByModerator() {
		do_action('cmmrm_route_trashed_by_moderator', $this);
		wp_trash_post($routeId);
	}
	
	
	function isAcceptedByModerator() {
		return ($this->getPostMeta(static::META_MODERATOR_ACCEPTED) == 1);
	}
	

	static function calculateDistance2d($p1Lat, $p1Long, $p2Lat, $p2Long) {
	
		$R = 6371000; // metres
		$k = deg2rad($p1Lat);
		$l = deg2rad($p2Lat);
		$m = deg2rad($p2Lat - $p1Lat);
		$n = deg2rad($p2Long - $p1Long);
	
		$a = sin($m/2) * sin($m/2) +
		cos($k) * cos($l) *
		sin($n/2) * sin($n/2);
		$c = 2 * atan2(sqrt($a), sqrt(1-$a));
	
		return $R * $c;
	
	}
	
	
	static function calculateDistance($lat1, $lon1, $lat2, $lon2, $el1 = 0, $el2 = 0) {
		
		$R = 6371; // Radius of the earth
		
		$latDistance = deg2rad($lat2 - $lat1);
		$lonDistance = deg2rad($lon2 - $lon1);
		$a = sin($latDistance / 2) * sin($latDistance / 2)
		+ cos(deg2rad($lat1)) * cos(deg2rad($lat2))
		* sin($lonDistance / 2) * sin($lonDistance / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$distance = $R * $c * 1000; // convert to meters
		
		$height = $el1 - $el2;
		
		$distance = pow($distance, 2) + pow($height, 2);
		
		return sqrt($distance);
		
	}
	
	
	static function getShortcodeTokensFuncMap() {
		return array(
			'[name]' => 'getTitle',
			'[description]' => 'getContent',
			'[author]' => 'getAuthorDisplayName',
			'[permalink]' => 'getPermalink',
		);
	}
	

	static function findLocationByAddress($address) {
	
		if (empty($address)) return array();
	
		$cache = get_transient(static::TRANSIENT_GEOLOCATION_BY_ADDR_CACHE);
		if (is_array($cache) AND isset($cache[$address])) {
			return $cache[$address];
		}
	
		$url = 'http://maps.googleapis.com/maps/api/geocode/json';
	
		$url = add_query_arg(urlencode_deep(array(
			'address' => $address,
		)), $url);
// 		var_dump($url);exit;
	
		$result = RemoteConnection::getRemoteJson($url);
		if (is_array($result) AND !empty($result['results']) AND !empty($result['status']) AND $result['status'] == 'OK') {
			$coords = array($result['results'][0]['geometry']['location']['lat'], $result['results'][0]['geometry']['location']['lng']);
			$cache[$address] = $coords;
			set_transient(static::TRANSIENT_GEOLOCATION_BY_ADDR_CACHE, $cache);
			return $coords;
		}
	
	}
	
}
