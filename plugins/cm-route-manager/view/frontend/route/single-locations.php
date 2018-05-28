<?php

use com\cminds\mapsroutesmanager\model\Location;

use com\cminds\mapsroutesmanager\model\Labels;
use com\cminds\mapsroutesmanager\model\Route;

use com\cminds\mapsroutesmanager\helper\RouteView;

/* @var $route Route */

$i = 0;

?><div class="cmmrm-route-locations">
	<?php foreach ($route->getLocations() as $location): ?>
		<?php /* @var $location Location */ ?>
		<?php if (Location::TYPE_LOCATION == $location->getLocationType()): ?>
			<?php $i++; ?>
			<div class="cmmrm-location-details" data-id="<?php echo $location->getId();
				?>" data-lat="<?php echo $location->getLat(); ?>"  data-long="<?php echo $location->getLong(); ?>">
				<a class="cmmrm-map-center-btn" href="#" title="<?php echo esc_attr(Labels::getLocalized('show_all_locations')); ?>"><?php
					echo Labels::getLocalized('map_reset_btn'); ?></a>
				
				<h3><?php echo esc_html($i . '. '. $location->getTitle()); ?></h3>
				<div class="cmmrm-altitude"><strong><?php echo Labels::getLocalized('location_altitude'); ?>:</strong> <span><?php echo $location->formatAltitude(); ?></span></div>
				<?php if ($address = $location->getAddress()): ?>
					<div class="cmmrm-address">
						<strong><?php echo Labels::getLocalized('location_address'); ?>:</strong>
						<span><?php echo esc_html($address); ?></span>
					</div>
				<?php endif; ?>
				
				<?php do_action('cmmrm_single_location_before_images', $location, $i); ?>
				
				<?php if ($images = $location->getImages()):
					RouteView::displayImages($images, 'location', $location->getId());
				endif; ?>
				
				<div class="cmmrm-description"><?php echo $location->getContent(); ?></div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>