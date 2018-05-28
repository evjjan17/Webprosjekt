<?php

use com\cminds\mapsroutesmanager\App;
use com\cminds\mapsroutesmanager\controller\DashboardController;
use com\cminds\mapsroutesmanager\model\Settings;
use com\cminds\mapsroutesmanager\helper\RouteView;
use com\cminds\mapsroutesmanager\model\Labels;
use com\cminds\mapsroutesmanager\model\Location;
use com\cminds\mapsroutesmanager\model\Route;
use com\cminds\mapsroutesmanager\helper\FormHtml;
use com\cminds\mapsroutesmanager\controller\RouteController;

/* @var $route Route */

?>
<div class="cmmrm-route-editor">
	<form action="<?php echo esc_attr($formUrl); ?>" method="post" enctype="multipart/form-data" data-route-id="<?php echo $route->getId(); ?>">
	
		<div class="cmmrm-field">
			<label><?php echo Labels::getLocalized('route_name'); ?>:</label>
			<input type="text" name="name" value="<?php echo esc_attr($route->getTitle()); ?>" required />
		</div>
		
		<div class="cmmrm-field cmmrm-field-description">
			<label><?php echo Labels::getLocalized('route_description'); ?>:</label>
			<?php if (Settings::getOption(Settings::OPTION_EDITOR_RICH_TEXT_ENABLE)): ?>
				<?php wp_editor($route->getContent(), 'cmmrm_route_description', array('textarea_name' => 'description')); ?>
			<?php else :?>
				<textarea name="description"><?php echo esc_html($route->getContent()); ?></textarea>
			<?php endif; ?>
		</div>
		
		<div class="cmmrm-field">
			<label><?php echo Labels::getLocalized('route_status'); ?>:</label>
			<?php echo FormHtml::selectBox('status', apply_filters('cmmrm_editor_allowed_statuses', array(
				'publish' => Labels::getLocalized('route_status_publish'),
				'draft' => Labels::getLocalized('route_status_draft'),
			),  $route), $route->getStatus()); ?>
		</div>
		
		<?php do_action('cmmrm_route_editor_middle', $route); ?>
		
		<div class="cmmrm-field">
			<strong>Route settings</strong>
			<label><input type="checkbox" name="use-minor-length-units" value="1" <?php checked($route->useMinorLengthUnits()); ?> />
				<?php echo Labels::getLocalized('dashboard_use_minor_length_units'); ?></label>
			<?php do_action('cmmrm_route_editor_route_settings', $route); ?>
		</div>
		
		<?php do_action('cmmrm_route_editor_before_map', $route); ?>
		
		<div id="cmmrm-editor-map">
			<a href="" class="cmmrm-editor-instructions-btn"><span class="dashicons dashicons-editor-help"></span><?php echo Labels::getLocalized('instructions_btn') ?></a>
			<div class="cmmrm-editor-instructions">
				<?php echo Settings::getOption(Settings::OPTION_LABEL_EDITOR_INSTRUCTION); ?>
			</div>
			
			<ul class="cmmrm-inline-nav cmmrm-toolbar">
				<li class="rem-separator"><ul class="cmmrm-locations-editor-mode">
					<li class="current"><a href="" data-mode="location" class="dashicons dashicons-location" title="<?php
						echo esc_attr(Labels::getLocalized('editor_add_locations_mode_btn_desc')); ?>"><?php
						echo Labels::getLocalized('editor_add_locations_mode_btn_text'); ?></a></li>
					<li><a href="" data-mode="waypoint" class="dashicons dashicons-admin-customizer" title="<?php
						echo esc_attr(Labels::getLocalized('editor_draw_path_mode_btn_desc')); ?>"><?php
						echo Labels::getLocalized('editor_draw_path_mode_btn_text'); ?></a></li>
				</ul></li>
				<li><?php // echo RouteView::getTravelModeMenu($route->getTravelMode(), $showTitle = false, $labelsAsTooltip = true); ?></li>
				<li class="right"><input type="text" class="cmmrm-find-location" placeholder="<?php echo esc_attr(Labels::getLocalized('dashboard_map_search')); ?>" /></li>
			</ul>
			
			<div id="cmmrm-editor-map-canvas"></div>
			
			<?php do_action('cmmrm_route_editor_after_map', $route); ?>
			
			<?php do_action('cmmrm_route_single_after_map', $route, array()); ?>
			
			<?php if (!App::isPro() OR Settings::getOption(Settings::OPTION_EDITOR_TRAVEL_MODE_SHOW)): ?>
				<?php echo RouteView::getTravelModeMenu($route->getTravelMode()); ?>
			<?php endif; ?>
			<?php echo RouteController::loadFrontendView('route-params', compact('route')); ?>
			
			<input type="hidden" name="travel-mode" value="<?php echo esc_attr($route->getTravelMode()); ?>" />
			<input type="hidden" name="distance" value="<?php echo esc_attr($route->getDistance()); ?>" />
			<input type="hidden" name="duration" value="<?php echo esc_attr($route->getDuration()); ?>" />
			<input type="hidden" name="avg-speed" value="<?php echo esc_attr($route->getAvgSpeed()); ?>" />
			<input type="hidden" name="max-elevation" value="<?php echo esc_attr($route->getMaxElevation()); ?>" />
			<input type="hidden" name="min-elevation" value="<?php echo esc_attr($route->getMinElevation()); ?>" />
			<input type="hidden" name="elevation-gain" value="<?php echo esc_attr($route->getElevationGain()); ?>" />
			<input type="hidden" name="elevation-descent" value="<?php echo esc_attr($route->getElevationDescent()); ?>" />
			<input type="hidden" name="overview-path" value="<?php echo esc_attr($route->getOverviewPath()); ?>" />
			<input type="hidden" name="waypoints-string" value="<?php echo esc_attr($route->getWaypointsString()); ?>" />
			<input type="hidden" name="directions-response" value="" />
			<input type="hidden" name="elevation-response" value="" />
			
		</div>
		
		<div id="cmmrm-editor-locations">
			<ul class="cmmrm-locations-list">
				<li data-id="0" style="display:none">
					<input class="location-id" type="hidden" name="locations[id][]" value="0" />
					<input class="location-name" type="text" name="locations[name][]" value="" placeholder="<?php echo esc_attr(Labels::getLocalized('location_name')); ?>" />
					<input class="location-lat" type="text" name="locations[lat][]" value="" placeholder="<?php echo esc_attr(Labels::getLocalized('location_latitude')); ?>" />
					<input class="location-long" type="text" name="locations[long][]" value="" placeholder="<?php echo esc_attr(Labels::getLocalized('location_longitude')); ?>" />
					<input class="location-address" type="text" name="locations[address][]" value="" title="<?php echo esc_attr(Labels::getLocalized('location_address')); ?>" placeholder="<?php echo esc_attr(Labels::getLocalized('location_address')); ?>" />
					<input class="location-type" type="hidden" name="locations[type][]" value="location" />
					<?php /* <input type="button" class="cmmrm-location-convert" value="<?php echo esc_attr(Labels::getLocalized('dashboard_location_convert_btn')); ?>" /> */ ?>
					<input type="button" class="cmmrm-location-remove" value="<?php echo esc_attr(Labels::getLocalized('dashboard_location_remove_btn')); ?>" />
					<div><textarea class="location-description" name="locations[description][]" placeholder="<?php echo esc_attr(Labels::getLocalized('location_description')); ?>"></textarea></div>
					<?php do_action('cmmrm_route_editor_location_bottom', $route); ?>
				</li>
			</ul>
			
			<?php if (!defined('CMMRM_ROUTE_JS')): define('CMMRM_ROUTE_JS', 1); ?>
				<?php add_action('wp_footer', function() use ($route) { ?>
					<script type="text/javascript">
					jQuery(function($) {
						$('.cmmrm-images').each(CMMRM_Editor_Images_init);
						var routeData = <?php echo json_encode($route->getJSRouteData()); ?>;
						var waypointsString = <?php echo json_encode($route->getWaypointsString()); ?>;
						var locations = <?php echo json_encode($route->getJSLocations()); ?>;
						var editor = new CMMRM_Editor('cmmrm-editor-map-canvas', routeData, waypointsString, locations);
						<?php do_action('cmmrm_editor_wp_footer_js', $route); ?>
					});
					</script>
				<?php }); ?>
			<?php endif; ?>
			
		</div>
		
		<div class="form-summary">
			<input type="hidden" name="<?php echo esc_attr(DashboardController::EDITOR_NONCE); ?>" value="<?php echo esc_attr($nonce); ?>" class="cmmrm-nonce" />
			<input type="submit" name="btn_save" value="<?php echo esc_attr(Labels::getLocalized('dashboard_save_btn')); ?>" class="button button-primary" />
		</div>
	
	</form>
	
</div>