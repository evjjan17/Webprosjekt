<?php

use com\cminds\mapsroutesmanager\model\Labels;

?>
<div class="cmmrm-field">
	<label><input type="color" name="path-color" value="<?php echo esc_attr($route->getPathColor()); ?>" />
		<?php echo Labels::getLocalized('dashboard_path_color'); ?></label>
</div>
