<?php

use com\cminds\mapsroutesmanager\model\Labels;

?>
<?php 

printf('<label><input type="checkbox" name="show-weather-per-location" value="1" %s /> %s</label>',
	checked($route->showWeatherPerLocation(), true, false),
	Labels::getLocalized('dashboard_show_weather_per_location')
);

?>
<label><input type="checkbox" name="directional-arrows" value="1" <?php checked($route->showDirectionalArrows()); ?> />
	<?php echo Labels::getLocalized('dashboard_show_directional_arrows'); ?></label>
<label><input type="checkbox" name="show-locations-section" value="1" <?php checked($route->showLocationsSection()); ?> />
	<?php echo Labels::getLocalized('dashboard_show_locations_section'); ?></label>
	