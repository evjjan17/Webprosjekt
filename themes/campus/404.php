<?php get_header(); ?>

<div id="content">
	
	<div class="wrapper wrapper-main">

		<div id="main">
		
			<div class="wrapper-content">
			
				<div class="post-intro">
					<h1 class="title-l title-margin"><?php _e('Page not found', 'campus'); ?></h1>
				</div><!-- end .post-intro -->
	
				<div class="divider">&nbsp;</div>
	
				<div class="post-single">
		
					<p><?php _e( 'Apologies, but the requested page cannot be found. Perhaps searching will help find a related page.', 'campus' ); ?></p>
					
					<div class="cleaner">&nbsp;</div>
					
					<div class="divider divider-notop">&nbsp;</div>
					
					<h3 class="title-s"><?php _e( 'Browse Categories', 'campus' ); ?></h3>
					<ul>
						<?php wp_list_categories('title_li=&hierarchical=0&show_count=1'); ?>	
					</ul>
				
					<h3 class="title-s"><?php _e( 'Monthly Archives', 'campus' ); ?></h3>
					<ul>
						<?php wp_get_archives('type=monthly&show_post_count=1'); ?>	
					</ul>
					<div class="cleaner">&nbsp;</div>
					
				</div><!-- .post-single -->			
				
			</div><!-- .wrapper-content -->
		
		</div><!-- #main -->
		
		<?php get_sidebar(); ?>
		
		<div class="cleaner">&nbsp;</div>
	</div><!-- .wrapper .wrapper-main -->

</div><!-- #content -->

<?php get_footer(); ?>