<aside>
	
	<div class="aside-wrapper">
	
		<?php if (is_page()) { get_template_part('related-pages'); } ?>
	
		<?php
		if ( !dynamic_sidebar('Sidebar') ) : ?> <?php endif;
		?>
	
		<div class="cleaner">&nbsp;</div>
	
	</div><!-- .aside-wrapper -->

</aside>