<?php

namespace com\cminds\mapsroutesmanager\model;

use com\cminds\mapsroutesmanager\helper\GoogleMapsIcons;

use com\cminds\mapsroutesmanager\shortcode\RouteSnippetShortcode;

use com\cminds\mapsroutesmanager\App;

class Settings extends SettingsAbstract {
	
	const OPTION_PERMALINK_PREFIX = 'cmmrm_permalink_prefix';
	const OPTION_PAGE_TEMPLATE = 'cmmrm_page_template';
	const OPTION_PAGE_TEMPLATE_OTHER = 'cmmrm_page_template_other';
	const OPTION_PAGINATION_LIMIT = 'cmmrm_pagination_limit';
	const OPTION_INDEX_LAYOUT = 'cmmrm_index_layout';
	const OPTION_INDEX_TILE_WIDTH = 'cmmrm_index_tile_width';
	const OPTION_INDEX_ORDERBY = 'cmmrm_index_orderby';
	const OPTION_INDEX_ORDER = 'cmmrm_index_order';
	const OPTION_INDEX_TEXT_TOP = 'cmmrm_index_text_top';
	const OPTION_INDEX_MAP_SHOW = 'cmmrm_index_map_show';
	const OPTION_UNIT_LENGTH = 'cmmrm_unit_length';
	const OPTION_UNIT_TEMPERATURE = 'cmmrm_unit_temperature';
	const OPTION_INDEX_ROUTE_PARAMS = 'cmmrm_index_route_params';
	const OPTION_INDEX_GEOLOCATION_ENABLE = 'cmmrm_index_geolocation_enable';
	const OPTION_INDEX_RATING_FILTER_SHOW = 'cmmrm_index_rating_filter_show';
	const OPTION_INDEX_MAP_MARKER_CLUSTERING_ENABLE = 'cmmrm_index_map_marker_clustering_enable';
	const OPTION_INDEX_SNIPPET_BGCOLOR_FROM_ROUTE = 'cmmrm_index_snippet_bgcolor_from_route';
	const OPTION_INDEX_SEARCH_WHOLE_WORDS = 'cmmrm_index_search_whole_words';
	const OPTION_ROUTE_MAP_MARKER_CLUSTERING_ENABLE = 'cmmrm_route_map_marker_clustering_enable';
	const OPTION_ROUTE_MAP_LABEL_TYPE = 'cmmrm_route_map_label_type';
	const OPTION_ROUTE_MAP_LOCATION_INFO_WINDOW_SHOW = 'cmmrm_route_map_location_info_window_show';
	const OPTION_ROUTE_MAP_LOCATION_INFO_WINDOW_TEMPLATE = 'cmmrm_route_map_location_info_window_template';
	const OPTION_ROUTE_MAP_LOCATION_INFO_WINDOW_IMAGE_MAX_WIDTH = 'cmmrm_route_map_loc_infowindow_img_max_w';
	const OPTION_ROUTE_PAGE_GEOLOCATION_ENABLE = 'cmmrm_route_page_geolocation_enable';
	const OPTION_EDITOR_GEOLOCATION_ENABLE = 'cmmrm_editor_geolocation_enable';
	const OPTION_SINGLE_ROUTE_PARAMS = 'cmmrm_single_route_params';
	const OPTION_SINGLE_ROUTE_RATING_SHOW = 'cmmrm_single_route_rating_show';
	const OPTION_GOOGLE_MAPS_APP_KEY = 'cmmrm_google_maps_app_key';
	const OPTION_GOOGLE_ELEVATION_API_KEY = 'cmmrm_google_elevation_api_key';
	const OPTION_DONT_EMBED_GOOGLE_MAPS_JS_API = 'cmmrm_dont_embed_google_maps_js_api';
	const OPTION_OPENWEATHERMAP_API_KEY = 'cmmrm_openweathermap_api_key';
	const OPTION_COMMENTS_ENABLE = 'cmmrm_comments_enable';
	const OPTION_LOCATION_ICON_ENABLE = 'cmmrm_location_icon_enable';
	const OPTION_MAP_LABEL_BGCOLOR = 'cmmrm_map_label_bgcolor';
	const OPTION_MAP_TOOLTIP_BGCOLOR = 'cmmrm_map_tooltip_bgcolor';
	const OPTION_AUTHOR_LINKS_ENABLE = 'cmmrm_author_links_enable';
	const OPTION_AUTHOR_LINKS_NEW_WINDOW = 'cmmrm_author_links_new_window';
	const OPTION_AUTHOR_AVATAR_SHOW = 'cmmrm_author_avatar_show';
	const OPTION_MAP_TYPE_DEFAULT = 'cmmrm_map_type_default';
	const OPTION_MAP_SCROLL_ZOOM_ENABLE = 'cmmrm_map_scroll_zoom_enable';
	const OPTION_SINGLE_ROUTE_DIRECTIONAL_ARROWS = 'cmmrm_single_route_directional_arrows';
	const OPTION_SINGLE_ROUTE_PARAMS_ABOVE_MAP = 'cmmrm_single_route_params_above_map';
	const OPTION_ROUTE_PARAMS_VALUES_TOP = 'cmmrm_route_params_values_top';
	const OPTION_FANCY_STYLE_ENABLE = 'cmmrm_fancy_style_enable';
	const OPTION_SINGLE_ROUTE_TRAVEL_MODE_SHOW = 'cmmrm_single_route_travel_mode_show';
	const OPTION_FANCY_BGCOLOR = 'cmmrm_fancy_bgcolor';
	const OPTION_FANCY_BORDER = 'cmmrm_fancy_border';
	
	const OPTION_INDEX_ZIP_RADIUS_FILTER_ENABLE = 'cmmrm_index_zip_radius_filter_enable';
	const OPTION_INDEX_ZIP_RADIUS_COUNTRY = 'cmmrm_index_zip_radius_country';
	const OPTION_INDEX_ZIP_RADIUS_MIN = 'cmmrm_index_zip_radius_min';
	const OPTION_INDEX_ZIP_RADIUS_MAX = 'cmmrm_index_zip_radius_max';
	const OPTION_INDEX_ZIP_RADIUS_STEP = 'cmmrm_index_zip_radius_step';
	const OPTION_INDEX_ZIP_RADIUS_DEFAULT = 'cmmrm_index_zip_radius_default';
	const OPTION_INDEX_ZIP_RADIUS_GEOLOCATION = 'cmmrm_index_zip_radius_geolocation';
	
	const OPTION_ROUTE_MODERATION_ENABLE = 'cmmrm_route_moderation_enable';
	const OPTION_ROUTE_MODERATION_EMAILS = 'cmmrm_route_moderation_emails';
	const OPTION_MODERATOR_EMAIL_SUBJECT = 'cmmrm_moderator_email_subject';
	const OPTION_MODERATOR_EMAIL_CONTENT = 'cmmrm_moderator_email_content';
	const OPTION_ROUTE_ACCEPTED_USER_EMAIL_SUBJECT = 'cmmrm_route_accepted_user_email_subject';
	const OPTION_ROUTE_ACCEPTED_USER_EMAIL_CONTENT = 'cmmrm_route_accepted_user_email_content';
	
	const OPTION_IMPORT_CREATE_END_LOCATION = 'cmmrm_import_create_end_location';
	const OPTION_IMPORT_CREATE_START_LOCATION = 'cmmrm_import_create_start_location';
	
	const OPTION_ACCESS_MAP_CREATE_CAP = 'cmmrm_access_map_create_cap';
	const OPTION_ACCESS_MAP_CREATE = 'cmmrm_access_map_create';
	const OPTION_ACCESS_MAP_INDEX_CAP = 'cmmrm_access_map_index_cap';
	const OPTION_ACCESS_MAP_INDEX = 'cmmrm_access_map_index';
	const OPTION_ACCESS_MAP_VIEW_CAP = 'cmmrm_access_map_view_cap';
	const OPTION_ACCESS_MAP_VIEW = 'cmmrm_access_map_view';
	const OPTION_ACCESS_MEDIA_LIBRARY_ROLES = 'cmmrm_access_media_library_roles';
	
	const OPTION_ROUTE_DEFAULT_IMAGE = 'cmmrm_route_default_image';
	const OPTION_ROUTE_INDEX_FEATURED_IMAGE = '_cmmrm_route_index_featured_image';
	const OPTION_EDITOR_DEFAULT_LAT = 'cmmrm_editor_default_lat';
	const OPTION_EDITOR_DEFAULT_LONG = 'cmmrm_editor_default_long';
	const OPTION_EDITOR_DEFAULT_ZOOM = 'cmmrm_editor_default_zoom';
	const OPTION_DEFAULT_TRAVEL_MODE = 'cmmrm_default_travel_mode';
	const OPTION_EDITOR_TRAVEL_MODE_SHOW = 'cmmrm_editor_travel_mode_show';
	const OPTION_EDITOR_RICH_TEXT_ENABLE = 'cmmrm_editor_rich_text_enable';
	const OPTION_LABEL_EDITOR_INSTRUCTION = 'cmmrm_label_editor_instruction';
	const OPTION_CUSTOM_CSS = 'cmmrm_custom_css';
	const OPTION_CUSTOM_ICONS = 'cmmrm_custom_icons';
	
	const ACCESS_GUEST = 'cmmrm_guest';
	const ACCESS_USER = 'cmmrm_user';
	const ACCESS_CAPABILITY = 'cmmrm_capability';
	
	const ORDERBY_TITLE = 'post_title';
	const ORDERBY_CREATED = 'post_date';
	const ORDERBY_VIEWS = 'views';
	
	const ORDER_ASC = 'asc';
	const ORDER_DESC = 'desc';
	
	const UNIT_METERS = 'meters';
	const UNIT_FEET = 'feet';
	const UNIT_TEMP_F = 'temp_f';
	const UNIT_TEMP_C = 'temp_c';
	const FEET_TO_METER = 0.3048;
	const FEET_IN_MILE = 5280;
	
	const DEFAULT_MAP_LABEL_BGCOLOR = '#FFFF00';
	const DEFAULT_INDEX_ORDERBY = self::ORDERBY_CREATED;
	const DEFAULT_INDEX_ORDER = self::ORDER_DESC;
	
	const MAP_TYPE_ROADMAP = 'roadmap';
	const MAP_TYPE_SATELLITE = 'satellite';
	const MAP_TYPE_TERRAIN = 'terrain';
	const MAP_TYPE_HYBRID = 'hybrid';
	
	const INDEX_LAYOUT_LIST = 'list';
	const INDEX_LAYOUT_TILES = 'tiles';
	
	const TRAVEL_MODE_WALKING = 'WALKING';
	const TRAVEL_MODE_DRIVING = 'DRIVING';
	const TRAVEL_MODE_BICYCLING = 'BICYCLING';
	const TRAVEL_MODE_DIRECT = 'DIRECT';
	
	const LABEL_TYPE_SHOW_BELOW = 'below';
	const LABEL_TYPE_TOOLTIP = 'tooltip';
	const LABEL_TYPE_NONE = 'none';
	
	
	public static $categories = array(
		'setup' => 'Setup',
		'general' => 'General',
		'index' => 'Index page',
		'route' => 'Route page',
		'dashboard' => 'Dashboard',
		'moderation' => 'Moderation',
		'access' => 'Access Control',
		'labels' => 'Labels',
	);
	
	public static $subcategories = array(
		'setup' => array(
			'api' => 'API Keys',
			'navigation' => 'Navigation',
		),
		'general' => array(
			'template' => 'Template',
			'appearance' => 'Appearance',
			'units' => 'Units',
		),
		'index' => array(
			'layout' => 'Layout',
			'pagination' => 'Pagination, order, search',
			'filters' => 'Filters',
			'fields' => 'Visible fields',
			'appearance' => 'Appearance',
			'images' => 'Images',
			'zip' => 'ZIP code searching',
		),
		'route' => array(
			'order' => 'Routes order',
			'fields' => 'Visible fields',
			'appearance' => 'Appearance',
			'infowindow' => 'Info window',
		),
		'dashboard' => array(
			'editor' => 'Editor',
			'map' => 'Map default position',
			'import' => 'Importing',
		),
		'moderation' => array(
			'moderation' => 'Moderation',
			'notifications' => 'Notifications',
		),
		'access' => array(
			'access' => '',
		),
		'labels' => array(
			'other' => 'Other',
		),
	);
	
	
	public static function getOptionsConfig() {
		
		return apply_filters('cmmrm_options_config', array(
			
			// General Navigation
			self::OPTION_PERMALINK_PREFIX => array(
				'type' => self::TYPE_STRING,
				'default' => 'maps-routes',
				'category' => 'setup',
				'subcategory' => 'navigation',
				'title' => 'Permalink prefix',
				'desc' => 'Enter the prefix of the index and routes permalinks, eg. <kbd>maps-routes</kbd> '
							. 'will give permalinks such as: <kbd>/<strong>maps-routes</strong>/paris-trip</kbd>.',
			),
			
			// General Template
			self::OPTION_PAGE_TEMPLATE => array(
				'type' => self::TYPE_SELECT,
				'options' => array(__CLASS__, 'getPageTemplatesOptions'),
				'default' => 'page.php',
				'category' => 'general',
				'subcategory' => 'template',
				'title' => 'Page template',
				'desc' => 'Choose the page template of the current theme to use on the index page, routes\' pages and the front-end user\'s dashboard pages.',
			),
			self::OPTION_PAGE_TEMPLATE_OTHER => array(
				'type' => self::TYPE_STRING,
				'category' => 'general',
				'subcategory' => 'template',
				'title' => 'Other page template file',
				'desc' => 'Enter the other name of the page template if your template is not on the list above. '
					. 'This option have priority over the selected page template. Leave blank to reset.',
			),
			
			// General Appearance
			self::OPTION_MAP_TYPE_DEFAULT => array(
				'type' => self::TYPE_RADIO,
				'options' => array(
					self::MAP_TYPE_ROADMAP => 'road map',
					self::MAP_TYPE_TERRAIN => 'terrain',
					self::MAP_TYPE_SATELLITE => 'pure satellite without labels',
					self::MAP_TYPE_HYBRID => 'hybrid: satellite + labels',
				),
				'default' => self::MAP_TYPE_ROADMAP,
				'category' => 'general',
				'subcategory' => 'appearance',
				'title' => 'Default map view',
			),
			self::OPTION_MAP_SCROLL_ZOOM_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'general',
				'subcategory' => 'appearance',
				'title' => 'Zoom map when using mouse wheel',
				'desc' => 'If enabled then scrolling by mouse when on the map will zoom out or zoom in.',
			),
			self::OPTION_CUSTOM_CSS => array(
				'type' => self::TYPE_TEXTAREA,
				'category' => 'general',
				'subcategory' => 'appearance',
				'title' => 'Custom CSS',
				'desc' => 'You can enter a custom CSS which will be embeded on every page that contains a CM Map Routes interface.',
			),
			
			// General Units
			self::OPTION_UNIT_LENGTH => array(
				'type' => self::TYPE_RADIO,
				'options' => array(self::UNIT_METERS => 'meters', self::UNIT_FEET => 'feet'),
				'default' => self::UNIT_METERS,
				'category' => 'general',
				'subcategory' => 'units',
				'title' => 'Length units',
				'desc' => 'Used to display the trail\'s length or the location\'s altitude.',
			),
			
			// Index Pagination
			self::OPTION_PAGINATION_LIMIT => array(
				'type' => self::TYPE_INT,
				'default' => 10,
				'category' => 'index',
				'subcategory' => 'pagination',
				'title' => 'Routes per page',
				'desc' => 'Limit the routes visible on each page.',
			),
			
			// Index Appearance
			self::OPTION_INDEX_TEXT_TOP => array(
				'type' => (App::isPro() ? self::TYPE_RICH_TEXT : self::TYPE_TEXTAREA),
				'category' => 'index',
				'subcategory' => 'appearance',
				'title' => 'Text on top',
				'desc' => 'You can enter text which will be displayed on the top of the index page, below the page title.',
			),
			
			// Index Fields
			self::OPTION_INDEX_ROUTE_PARAMS => array(
				'type' => self::TYPE_MULTICHECKBOX,
				'options' => self::getRouteIndexPageParamsNames(),
				'default' => array_keys(self::getRouteIndexPageParamsNames()),
				'category' => 'index',
				'subcategory' => 'fields',
				'title' => 'Information visible on the index page',
				'desc' => 'Check which route parameters will be displayed on the index page on the route\'s snippet.',
			),
			
			// Index Images
			Settings::OPTION_ROUTE_INDEX_FEATURED_IMAGE => array(
				'type' => Settings::TYPE_RADIO,
				'default' => RouteSnippetShortcode::FEATURED_IMAGE,
				'options' => array(RouteSnippetShortcode::FEATURED_IMAGE => 'First route image', RouteSnippetShortcode::FEATURED_MAP => 'Map thumbnail'),
				'category' => 'index',
				'subcategory' => 'images',
				'title' => 'Route featured image',
				'desc' => 'Choose what kind of featured image to display on the index page.',
			),
			Settings::OPTION_ROUTE_DEFAULT_IMAGE => array(
				'type' => Settings::TYPE_STRING,
				'default' => App::url('asset/img/world-map-small.png'),
				'category' => 'index',
				'subcategory' => 'images',
				'title' => 'Route default image',
				'desc' => 'Enter the URL of the default featured image of the route map.',
			),
			
			// Route Fields
			self::OPTION_SINGLE_ROUTE_PARAMS => array(
				'type' => self::TYPE_MULTICHECKBOX,
				'options' => self::getRouteSinglePageParamsNames(),
				'default' => array_keys(self::getRouteSinglePageParamsNames()),
				'category' => 'route',
				'subcategory' => 'fields',
				'title' => 'Information visible on the route\'s page',
				'desc' => 'Check which route parameters will be displayed on the single route\'s page.',
			),
			
			// Route Appearance
			self::OPTION_COMMENTS_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => false,
				'category' => 'route',
				'subcategory' => 'appearance',
				'title' => 'Enable WP comments',
			),
			
			// Dashboard Map
			self::OPTION_EDITOR_DEFAULT_LAT => array(
				'type' => self::TYPE_STRING,
				'default' => '51',
				'category' => 'dashboard',
				'subcategory' => 'map',
				'title' => 'Editor default location\'s latitude',
				'desc' => 'Enter the latitude of the default location shown in the editor.',
			),
			self::OPTION_EDITOR_DEFAULT_LONG => array(
				'type' => self::TYPE_STRING,
				'default' => 0,
				'category' => 'dashboard',
				'subcategory' => 'map',
				'title' => 'Editor default location\'s longitude',
				'desc' => 'Enter the longitude of the default location shown in the editor.',
			),
			self::OPTION_EDITOR_DEFAULT_ZOOM => array(
				'type' => self::TYPE_SELECT,
				'options' => array_combine(range(0, 18), range(0, 18)),
				'default' => 5,
				'category' => 'dashboard',
				'subcategory' => 'map',
				'title' => 'Editor default zoom',
				'desc' => 'Greater zoom number = closer'
			),
			
			// Dashboard Editor
			self::OPTION_EDITOR_RICH_TEXT_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => false,
				'category' => 'dashboard',
				'subcategory' => 'editor',
				'title' => 'Enable rich text editor',
				'desc' => 'Allow users to use WYSIWYG editor when creating the map description. If disabled then simple textarea will be displayed.',
			),
			
				
			// General API
			self::OPTION_GOOGLE_MAPS_APP_KEY => array(
				'type' => self::TYPE_STRING,
				'category' => 'setup',
				'subcategory' => 'api',
				'title' => 'Google Maps App Key',
				'desc' => 'Enter the Google Maps <strong>Server App Key</strong>.<br /><a target="_blank" '
					. 'href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend&keyType=CLIENT_SIDE&reusekey=true">Get the API key from here</a>.'
					. '<br><br><a href="#" class="button cminds-google-maps-api-check-btn" data-api-key-field-selector="input[name=cmmrm_google_maps_app_key]">Test Configuration</a>',
			),
// 			self::OPTION_GOOGLE_MAPS_JSAPI_DONT_EMBED => array(
// 				'type' => self::TYPE_BOOL,
// 				'category' => 'setup',
// 				'subcategory' => 'api',
// 				'title' => 'Do not embed Google Maps JavaScript API',
// 				'desc' => 'Enable this option if you receiving JavaScript warning ',
// 			),
// 			self::OPTION_GOOGLE_ELEVATION_API_KEY => array(
// 				'type' => self::TYPE_STRING,
// 				'category' => 'general',
// 				'subcategory' => 'api',
// 				'title' => 'Google Elevation Service API key',
// 				'desc' => 'Enter the Google Elevation Service server API Key.',
// 			),
			
			
			
			// Label
			self::OPTION_LABEL_EDITOR_INSTRUCTION => array(
				'type' => self::TYPE_RICH_TEXT,
				'category' => 'labels',
				'subcategory' => 'other',
				'default' => '<iframe src="https://player.vimeo.com/video/161036537" width="500" height="281" frameborder="0" '
					. 'webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'
					. '<ul>'
					. '<li><a href="http://creativeminds.helpscoutdocs.com/article/536-cm-maps-route-manager-cmmrm-first-steps-to-create-a-route">First Steps for route drawing</a></li>'
					. '<li><a href="http://creativeminds.helpscoutdocs.com/article/539-cm-maps-route-manager-cmmrm-drawing-a-route">Drawing and editing routes</a></li>'
					. '<li><a href="http://creativeminds.helpscoutdocs.com/article/538-cm-maps-route-manager-cmmrm-adding-route-locations">Adding locations</a></li>'
					. '</ul>',
				'title' => 'Editor instructions',
			),
			
			
		));
		
	}
	
	
	static function getRouteIndexPageParamsNames() {
		return apply_filters('cmmrm_route_index_params_names', array_merge(Route::getRouteParamsNames(), array(
			'featured_image' => 'Featured image',
			'overview_path' => 'Overview path',
			'publish_date' => 'Publish date',
			'author' => 'Author',
		)));
	}
	
	
	static function getRouteSinglePageParamsNames() {
		return apply_filters('cmmrm_route_single_params_names', array_merge(Route::getRouteParamsNames(), array(
			'altitude' => 'Location altitude',
			'address' => 'Location address',
		)));
	}
	
	
	static function getAccessOptionsWithoutGuest() {
		return static::getAccessOptions(false);
	}
	
	
	static function getAccessOptions($guests = true) {
		if ($guests) {
			$result = array(self::ACCESS_GUEST => 'Everyone including guests');
		} else {
			$result = array();
		}
		return array_merge($result, array(
			self::ACCESS_USER => 'Only logged in users',
		),
		self::getRolesOptions(),
		array(
			self::ACCESS_CAPABILITY => 'Custom capability...',
		));
	}
	
	
	public static function getPageTemplate() {
		if ($template = Settings::getOption(Settings::OPTION_PAGE_TEMPLATE_OTHER)) {
			return $template;
		} else {
			$template = Settings::getOption(Settings::OPTION_PAGE_TEMPLATE);
			$available = Settings::getPageTemplatesOptions();
			if (!empty($template) AND isset($available[$template])) {
				return $template;
			} else {
				return 'page.php';
			}
		}
	}
	
	static function getMapLabelBgcolor() {
		$val = static::getOption(static::OPTION_MAP_LABEL_BGCOLOR);
		if (empty($val)) $val = static::DEFAULT_MAP_LABEL_BGCOLOR;
		return $val;
	}
	
	
	static function getIndexOrderBy() {
		$val = static::getOption(static::OPTION_INDEX_ORDERBY);
		if (empty($val)) $val = static::DEFAULT_INDEX_ORDERBY;
		return $val;
	}
	
	
	static function getIndexOrder() {
		$val = static::getOption(static::OPTION_INDEX_ORDER);
		if (empty($val)) $val = static::DEFAULT_INDEX_ORDER;
		return $val;
	}
	
	
	static function getMarkerIconsUrls() {
		$custom = array_filter(array_map('trim', explode("\n", Settings::getOption(Settings::OPTION_CUSTOM_ICONS))));
		if (!is_array($custom)) $custom = array();
		return array_merge($custom, GoogleMapsIcons::getAll());
	}
	
	
}
