<?php get_header(); ?>

<div id="content">
	
	<div class="wrapper wrapper-main">

		<div id="main">
		
			<div class="wrapper-content">
			
				<div class="post-intro">
					<h1 class="title-l title-margin"><?php /* tag archive */ if( is_tag() ) { ?><?php _e('Post Tagged with:', 'campus'); ?> "<?php single_tag_title(); ?>"
					<?php /* daily archive */ } elseif (is_day()) { ?><?php _e('Archive for', 'campus'); ?> <?php the_time('F jS, Y'); ?>
					<?php /* search archive */ } elseif (is_month()) { ?><?php _e('Archive for', 'campus'); ?> <?php the_time('F, Y'); ?>
					<?php /* yearly archive */ } elseif (is_year()) { ?><?php _e('Archive for', 'campus'); ?> <?php the_time('Y'); ?>
					<?php } ?></h1>
				</div><!-- end .post-intro -->
	
				<div class="divider">&nbsp;</div>
	
				<?php get_template_part('loop'); ?>			
				
			</div><!-- .wrapper-content -->
		
		</div><!-- #main -->
		
		<?php get_sidebar(); ?>
		
		<div class="cleaner">&nbsp;</div>
	</div><!-- .wrapper .wrapper-main -->

</div><!-- #content -->

<?php get_footer(); ?>