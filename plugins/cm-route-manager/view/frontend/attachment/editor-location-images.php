<?php

use com\cminds\mapsroutesmanager\model\Settings;
use com\cminds\mapsroutesmanager\model\Location;
use com\cminds\mapsroutesmanager\model\Labels;

?>

<?php if (Settings::getOption(Settings::OPTION_LOCATION_ICON_ENABLE)): ?>
	<div class="cmmrm-location-icon">
		<input class="location-icon" type="hidden" name="locations[icon][]" value="" />
		<input type="button" class="cmmrm-location-choose-icon" value="<?php echo esc_attr(Labels::getLocalized('dashboard_location_icon_choose_btn')); ?>" />
		<input type="button" class="cmmrm-location-remove-icon" value="<?php echo esc_attr(Labels::getLocalized('dashboard_location_icon_remove_btn')); ?>" />
		<label>Icon size:
			<select name="locations[icon_size][]" class="cmmrm-location-icon-size">
				<option name="<?php echo Location::ICON_SIZE_LARGE; ?>"><?php echo Labels::getLocalized('location_icon_size_large'); ?></option>
				<option name="<?php echo Location::ICON_SIZE_NORMAL; ?>"><?php echo Labels::getLocalized('location_icon_size_normal'); ?></option>
				<option name="<?php echo Location::ICON_SIZE_SMALL; ?>"><?php echo Labels::getLocalized('location_icon_size_small'); ?></option>
			</select>
		</label>
	</div>
<?php endif; ?>

<div class="cmmrm-images">
	<input type="hidden" name="locations[images][]" value="" />
	<ul class="cmmrm-images-list">
		<li data-id="0" style="display:none"><a href="about:blank" target="_blank" title="<?php
			echo esc_attr(Labels::getLocalized('dashboard_image_open')); ?>"><img src="about:blank" alt="Image" /></a>
		<span class="cmmrm-image-delete" title="<?php
			echo esc_attr(Labels::getLocalized('dashboard_image_remove')); ?>">&times;</span></li>
	</ul>
	<a href="#" class="button cmmrm-images-add-btn"><?php
			echo Labels::getLocalized('dashboard_image_add'); ?></a>
</div>