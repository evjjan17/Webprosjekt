<?php

use com\cminds\mapsroutesmanager\model\Labels;

?><div class="cmmrm-route-index-map">

	<div class="cmmrm-toolbar">
		<a class="cmmrm-map-fullscreen-btn dashicons dashicons-editor-expand" href="#" title="<?php echo esc_attr(Labels::getLocalized('show_fullscreen_title')); ?>"></a>
	</div>
	
	<div class="cmmrm-route-map-canvas-outer">
		<div id="cmmrm-route-index-map-canvas" class="cmmrm-route-map-canvas" style="<?php
			if (!empty($atts['mapwidth'])) echo 'width:'. intval($atts['mapwidth']) .'px;';
			if (!empty($atts['mapheight'])) echo 'height:'. intval($atts['mapheight']) .'px;';
		?>"></div>
	</div>
	
	
	<script type="text/javascript">
	jQuery(function($) {
		new CMMRM_WidgetIndexMap('cmmrm-route-index-map-canvas', <?php echo json_encode($routes); ?>);
	});
	</script>
	
</div>