<?php

use com\cminds\mapsroutesmanager\controller\RouteController;
use com\cminds\mapsroutesmanager\helper\RouteView;

?>
<div class="cmmrm-map-shortcode cmmrm-routes-archive"<?php echo RouteView::getDisplayParams($displayParams);
	?> style="<?php if (!empty($atts['width'])) echo 'width:'. intval($atts['width']) .'px;'; ?>">
	<?php echo RouteController::loadFrontendView('index-map', compact('routes', 'atts')); ?>
</div>