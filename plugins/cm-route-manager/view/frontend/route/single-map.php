<?php

use com\cminds\mapsroutesmanager\model\Settings;
use com\cminds\mapsroutesmanager\App;
use com\cminds\mapsroutesmanager\helper\RouteView;
use com\cminds\mapsroutesmanager\model\Route;
use com\cminds\mapsroutesmanager\controller\RouteController;

add_action('wp_footer', array(App::namespaced('controller\\DashboardController'), 'loadGoogleChart'), PHP_INT_MAX);

/* @var $route Route */


?>


<div class="cmmrm-route-map-canvas-outer" style="display:<?php echo (!isset($atts['map']) OR $atts['map'] == 1) ? 'block' : 'none'; ?>">
	<div class="cmmrm-directions-steps-wrapper"><ul>
		<li class="cmmrm-template">
			<span class="cmmrm-step-distance">Distance</span>
			<span class="cmmrm-step-instructions">Instructions</span>
		</li>
	</ul></div>
	<div id="<?php echo $mapId; ?>" class="cmmrm-route-map-canvas" style="<?php
		if (!empty($atts['mapwidth'])) echo 'width:'. intval($atts['mapwidth']) .'px;';
		if (!empty($atts['mapheight'])) echo 'height:'. intval($atts['mapheight']) .'px;';
	?>"></div>
</div>

<?php do_action('cmmrm_route_single_after_map', $route, $atts); ?>

<?php if (!isset($atts['map']) OR $atts['map'] == 1): ?>
	<?php if (isset($atts['showtravelmode']) AND is_numeric($atts['showtravelmode'])): ?>
		<?php if (!empty($atts['showtravelmode'])): ?>
			<?php echo RouteView::getTravelModeMenu($route->getTravelMode()); ?>
		<?php endif; ?>
	<?php elseif ((!App::isPro() OR Settings::getOption(Settings::OPTION_SINGLE_ROUTE_TRAVEL_MODE_SHOW))): ?>
		<?php echo RouteView::getTravelModeMenu($route->getTravelMode()); ?>
	<?php endif; ?>
<?php endif; ?>

<?php if (!isset($atts['params']) OR $atts['params'] == 1): ?>
	<?php if (!Settings::getOption(Settings::OPTION_SINGLE_ROUTE_PARAMS_ABOVE_MAP)): ?>
		<?php echo RouteController::loadFrontendView('route-params', compact('route')); ?>
	<?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
jQuery(function() {
	var mapId = <?php echo json_encode($mapId); ?>;
	var routeData = <?php echo json_encode($route->getJSRouteData()); ?>;
	var waypointsString = <?php echo json_encode($route->getWaypointsString()); ?>;
	var locations = <?php echo json_encode($route->getJSLocations()); ?>;
	var widget = new CMMRM_WidgetSingleRoute(mapId, routeData, waypointsString, locations);
	<?php if (isset($zoom) AND is_numeric($zoom) AND $zoom > 0): ?>
		setTimeout(function() {
			widget.map.map.setZoom(<?php echo $zoom; ?>);
		}, 500);
	<?php endif; ?>
});
</script>

