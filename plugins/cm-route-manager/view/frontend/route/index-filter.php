<?php

use com\cminds\mapsroutesmanager\model\Labels;

?><div class="cmmrm-route-index-filter">

	<?php do_action('cmmrm_route_index_filter_top'); ?>

	<form action="<?php echo esc_attr($searchFormUrl); ?>" class="cmmrm-route-index-search-form">
	
		<?php do_action('cmmrm_route_index_search_form_top'); ?>
	
		<label class="cmmrm-field-search"><input type="text" name="s" value="<?php
			echo esc_attr(isset($_GET['s']) ? $_GET['s'] : ''); ?>" placeholder="<?php echo Labels::getLocalized('search_placeholder'); ?>" /></label>
		<input type="submit" value="<?php echo Labels::getLocalized('search_btn'); ?>" />
		
		<?php do_action('cmmrm_route_index_search_form_bottom'); ?>
	
	</form>
	
	<?php do_action('cmmrm_categories_filter'); ?>
	
</div>

<div class="clerfix"></div>