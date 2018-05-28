<?php

use com\cminds\mapsroutesmanager\model\Settings;

use com\cminds\mapsroutesmanager\controller\RouteController;
use com\cminds\mapsroutesmanager\model\Route;
use com\cminds\mapsroutesmanager\model\Labels;

use com\cminds\mapsroutesmanager\helper\RouteView;

/* @var $route Route */

?><div class="cmmrm-route-map-before">

	<?php do_action('cmmrm_route_map_before_top', $route); ?>
	
	<?php if (Settings::getOption(Settings::OPTION_AUTHOR_AVATAR_SHOW)): ?>
		<div class="cmmrm-author cmmrm-author-avatar"><?php echo apply_filters('cmmrm_display_author',
				sprintf('<img src="%s" alt="%s" title="%s" />',
					esc_attr(get_avatar_url($route->getAuthorId())), esc_attr($route->getAuthorDisplayName()), esc_attr($route->getAuthorDisplayName())),
				$route->getAuthorId(), $route); ?></div>
	<?php endif; ?>

	<ul class="cmmrm-route-properties">
		<li class="cmmrm-author">
			<strong><?php echo Labels::getLocalized('route_author'); ?>:</strong>
			<span><?php echo apply_filters('cmmrm_display_author', $route->getAuthorDisplayName(), $route->getAuthorId(), $route); ?></span>
		</li>
		<?php $created = $route->formatCreatedDate(); ?>
		<li class="cmmrm-date cmmrm-publish-date"><strong><?php echo Labels::getLocalized('route_created'); ?>:</strong> <span><?php echo $created; ?></span></li>
		<?php if ($updated = $route->formatModifiedDate() AND $updated != $created): ?>
			<li class="cmmrm-date cmmrm-update-date"><strong><?php echo Labels::getLocalized('route_updated'); ?>:</strong> <span><?php echo $updated; ?></span></li>
		<?php endif; ?>
	</ul>
	
	<?php if ($categories = $route->getCategories()) RouteView::displayTermsInlineNav(Labels::getLocalized('categories'), 'categories', $categories); ?>
	<?php if ($tags = $route->getTags()) RouteView::displayTermsInlineNav(Labels::getLocalized('tags'), 'tags', $tags); ?>
	
	<?php do_action('cmmrm_single_route_properties', $route); ?>
	
	<?php if (!isset($atts['params']) OR $atts['params'] == 1): ?>
		<?php if (Settings::getOption(Settings::OPTION_SINGLE_ROUTE_PARAMS_ABOVE_MAP)): ?>
			<?php echo RouteController::loadFrontendView('route-params', compact('route')); ?>
		<?php endif; ?>
	<?php endif; ?>
	
	<ul class="cmmrm-inline-nav cmmrm-toolbar">
		<li><a href="<?php echo esc_attr(RouteView::getRefererUrl()); ?>" title="<?php echo esc_attr(Labels::getLocalized('route_backlink'));
			?>" class="dashicons dashicons-controls-back"></a></li>
		<?php if ($route->getTravelMode() !== 'DIRECT'): ?>
			<li><a class="dashicons dashicons-list-view cmmrm-directions-steps-btn" href="#" title="Show directions steps"></a></li>
		<?php endif; ?>
		<?php do_action('cmmrm_route_single_toolbar_middle', $route); ?>
		<li style="float:right"><a class="cmmrm-map-fullscreen-btn dashicons dashicons-editor-expand" href="#" title="<?php echo esc_attr(Labels::getLocalized('show_fullscreen_title')); ?>"></a></li>
		<li style="float:right"><a class="cmmrm-map-center-btn dashicons dashicons-update" href="#" title="<?php echo esc_attr(Labels::getLocalized('show_all_locations')); ?>"></a></li>
	</ul>

</div>