<?php

use com\cminds\mapsroutesmanager\App;
use com\cminds\mapsroutesmanager\model\Settings;

use com\cminds\mapsroutesmanager\helper\RouteView;

use com\cminds\mapsroutesmanager\model\Labels;

use com\cminds\mapsroutesmanager\shortcode\RouteSnippetShortcode;

?><div class="cmmrm-routes-archive"<?php echo RouteView::getDisplayParams($displayParams);
	?>>
	
	<?php get_template_part('cmmrm', 'route-index-filter'); ?>
	
	<?php if (!App::isPro() OR Settings::getOption(Settings::OPTION_INDEX_MAP_SHOW)): ?>
		<?php get_template_part('cmmrm', 'route-index-map'); ?>
	<?php endif; ?>
	
	<div class="cmmrm-routes-archive-summary"><?php printf(Labels::getLocalized('routes_index_summary'), count($routes), $totalRoutesNumber); ?></div>
	<div class="cmmrm-routes-archive-<?php echo Settings::getOption(Settings::OPTION_INDEX_LAYOUT); ?>">
		<?php foreach ($routes as $route):
			echo RouteSnippetShortcode::shortcode(array(
				'route' => $route,
				'featured' => Settings::getOption(Settings::OPTION_ROUTE_INDEX_FEATURED_IMAGE),
				'layout' => Settings::getOption(Settings::OPTION_INDEX_LAYOUT),
				'fancy' => Settings::getOption(Settings::OPTION_FANCY_STYLE_ENABLE),
			));
		endforeach; ?>
		<?php if (empty($routes)): ?>
			<p><?php echo Labels::getLocalized('index_no_routes'); ?></p>
		<?php endif; ?>
	</div>
	<?php get_template_part('cmmrm', 'route-index-bottom'); ?>
	<?php get_template_part('cmmrm', 'pagination'); ?>
</div>