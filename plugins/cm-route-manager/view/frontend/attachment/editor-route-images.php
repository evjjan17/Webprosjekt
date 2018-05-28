<?php

use com\cminds\mapsroutesmanager\model\Attachment;

use com\cminds\mapsroutesmanager\model\Labels;

?>
<div class="cmmrm-field cmmrm-images">
	<label><?php echo Labels::getLocalized('route_images'); ?>:</label>
	<ul class="cmmrm-images-list"><?php
	
	$template = '<li data-id="%s"%s><a href="%s" target="_blank" title="%s"><img src="%s" alt="Image" /></a>'
		. '<span class="cmmrm-image-delete" title="%s">&times;</span></li>';
	printf($template, 0, ' style="display:none"', 'about:blank', esc_attr(Labels::getLocalized('dashboard_image_open')),
		'about:blank', esc_attr(Labels::getLocalized('dashboard_image_remove')));
	
	$imagesIds = array();
	foreach ($route->getImages() as $image):
		if (!$image->isImage() AND !$image->isVideo()) continue;
		$imagesIds[] = $image->getId();
		printf($template,
			$image->getId(),
			'',
			esc_attr($image->getImageUrl(Attachment::IMAGE_SIZE_FULL)),
			esc_attr(Labels::getLocalized('dashboard_image_open')),
			esc_attr($image->getImageUrl(Attachment::IMAGE_SIZE_THUMB)),
			esc_attr(Labels::getLocalized('dashboard_image_remove'))
		);
	endforeach; ?></ul>
	<div class="cmmrm-field-desc"<?php if (empty($imagesIds)) echo ' style="display:none;"'; ?>><?php echo Labels::getLocalized('dashboard_images_description'); ?></div>
	<div class="cmmrm-images-add">
		<input type="hidden" name="images" value="<?php echo esc_attr(implode(',', $imagesIds)); ?>" />
		<a href="#" class="cmmrm-images-add-btn"><?php echo Labels::getLocalized('dashboard_image_add'); ?></a>
	</div>
</div>