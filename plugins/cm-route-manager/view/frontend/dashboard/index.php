<?php

use com\cminds\mapsroutesmanager\controller\RouteController;
use com\cminds\mapsroutesmanager\model\Route;
use com\cminds\mapsroutesmanager\model\Labels;


?>

<?php if (Route::canCreate() AND !empty($atts['addbtn'])): ?>
	<p class="cmmrm-route-add"><a href="<?php echo RouteController::getDashboardUrl('add'); ?>"><?php echo Labels::getLocalized('dashboard_add_route_btn'); ?></a></p>
<?php endif; ?>

<?php if (count($routes) > 0): ?>
	<table>
		<thead>
			<tr>
				<th><?php echo Labels::getLocalized('route_name'); ?></th>
				<th style="width:7em"><?php echo Labels::getLocalized('route_status'); ?></th>
				<?php if (!empty($atts['controls'])): ?>
					<th style="width:15em"><?php echo Labels::getLocalized('dashboard_routes_actions'); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody><?php foreach ($routes as $route): ?>
			<tr>
				<td><a href="<?php echo esc_attr($route->getUserEditUrl()); ?>"><?php echo esc_html($route->getTitle()); ?></a></td>
				<td><?php echo Labels::getLocalized('route_status_' . $route->getStatus()); ?></td>
				<?php if (!empty($atts['controls'])): ?>
					<td>
						<ul class="cmmrm-inline-nav">
							<li><a href="<?php echo esc_attr($route->getPermalink()); ?>"><?php echo Labels::getLocalized('dashboard_view'); ?></a></li>
							<li><a href="<?php echo esc_attr($route->getUserEditUrl()); ?>"><?php echo Labels::getLocalized('dashboard_edit'); ?></a></li>
							<li><a href="<?php echo esc_attr($route->getUserDeleteUrl()); ?>" class="cmmrm-delete-confirm"><?php echo Labels::getLocalized('dashboard_delete'); ?></a></li>
						</ul>
					</td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?></tbody>
		</table>
<?php else: ?>
	<p><?php echo Labels::getLocalized('dashboard_no_routes'); ?></p>
<?php endif; ?>