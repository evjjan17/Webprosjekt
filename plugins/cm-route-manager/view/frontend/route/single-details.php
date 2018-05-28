<?php

use com\cminds\mapsroutesmanager\helper\RouteView;

use com\cminds\mapsroutesmanager\model\Attachment;

?><div class="cmmrm-route-details" data-id="<?php echo $route->getId(); ?>">
	<?php if ($images = $route->getImages()):
		RouteView::displayImages($images, 'route', $route->getId());
	endif; ?>
	<div class="cmmrm-description"><?php echo nl2br($route->getContent()); ?></div>
</div>