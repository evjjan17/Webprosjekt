<?php get_header(); ?>

<div id="content">
	
	<div class="wrapper wrapper-main">

		<div id="main">
		
			<div class="wrapper-content">
			
				<?php if (is_home() && $paged < 2) { ?>

				<div class="academia-home-full">

				<?php get_template_part('slideshow-home'); } ?>
				
				<?php if (is_active_sidebar('home-full-1') && is_home() && $paged < 2) { ?>
				
				<?php
				if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Homepage: Top') ) : ?> <?php endif;
				?>
				
				<?php } ?>
				
				<?php if (is_home() && $paged < 2) { ?>
				</div><!-- .academia-home-full -->
				<?php } ?>

				<?php if (get_option('page_on_front') == $post->ID) {
				
					get_template_part('featured-page');
				
				} // if displaying a static page
				else {
				
					?>
					
					<div class="post-meta">
						<h1 class="title-m title-margin"><?php _e('Recent Posts','campus'); ?></h1>
					</div><!-- end .post-meta -->
					
					<?php
					get_template_part('loop','index');
				
				} // if displaying posts
				
				?>
				
			</div><!-- .wrapper-content -->
		
		</div><!-- #main -->
		
		<?php get_sidebar(); ?>
		
		<div class="cleaner">&nbsp;</div>
	</div><!-- .wrapper .wrapper-main -->

</div><!-- #content -->
	
<?php get_footer(); ?>