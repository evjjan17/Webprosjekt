<?php 

use com\cminds\mapsroutesmanager\model\Labels;

?><div class="cmmrm-filter cmmrm-custom-taxonomy-filter cmmrm-custom-taxonomy-filter-<?php echo $taxonomy; ?>">
	<select>
		<option value="<?php echo esc_attr(remove_query_arg($taxonomy, $baseUrl)); ?>"><?php
			printf(Labels::getLocalized('filter_option_show_all_custom_taxonomy_terms'), $tax['name_plural']); ?></option>
		<?php foreach ($terms as $term):
			$url = add_query_arg($taxonomy, $term->slug, $baseUrl);
			printf('<option value="%s"%s>%s</option>', esc_attr($url), selected($term->slug, $current, false), $term->name);
		endforeach; ?>
	</select>
</div>