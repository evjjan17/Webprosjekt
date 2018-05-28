<?php

use com\cminds\mapsroutesmanager\model\Settings;

use com\cminds\mapsroutesmanager\helper\RouteView;

use com\cminds\mapsroutesmanager\controller\RouteController;

use com\cminds\mapsroutesmanager\model\Route;


/* @var $route Route */

?><div class="cmmrm-shortcode-route-snippet flex-container" <?php echo RouteView::getDisplayParams($displayParams);
		?> data-layout="<?php echo $atts['layout'];
		?>" data-fancy="<?php echo $atts['fancy'];
		?>" data-fancy-border="<?php echo (Settings::getOption(Settings::OPTION_FANCY_BORDER) ? '1': '0'); ?>"<?php
		
		$pathColor = $route->getPathColor();
		if (Settings::getOption(Settings::OPTION_INDEX_SNIPPET_BGCOLOR_FROM_ROUTE) AND strlen($pathColor) > 0) {
			echo ' style="background-color:'. esc_attr($pathColor) .'"';
		}
		
		?>>
	<div class="cmmrm-route-snippet flex-item-stretch" data-route-id="<?php echo $route->getId(); ?>">
		<div class="cmmrm-route-featured-image"><?php echo RouteView::getFeaturedImageThumb($route, $atts); ?></div>
		<h2><a href="<?php echo esc_attr($route->getPermalink()); ?>"><?php echo esc_html($route->getTitle()); ?></a></h2>
		<?php if (!isset($atts['params']) OR $atts['params'] == 1): ?>
			<?php echo RouteController::loadFrontendView('route-params', compact('route')); ?>
		<?php endif; ?>
		
		<?php echo RouteView::getFeaturedImageLarge($route, $atts); ?>
		<div class="cmmrm-route-rating"><?php echo RouteView::displayRating($route); ?></div>
		<div class="cmmrm-date"><?php echo $route->formatCreatedDate(); ?></div>
		<div class="cmmrm-author"><?php echo apply_filters('cmmrm_display_author', $route->getAuthorDisplayName(), $route->getAuthorId(), $route); ?></div>
		<?php do_action('cmmrm_route_snippet_bottom', $route, $atts); ?>
		
		<div class="clear"></div>
	</div>
	<?php do_action('route_snippet_end', $route, $atts); ?>
</div>