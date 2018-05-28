<?php get_header(); ?>

<div id="content">
	
	<div class="wrapper wrapper-main">

		<div id="main">
		
			<div class="wrapper-content">
			
				<?php while (have_posts()) : the_post(); ?>
	
				<div class="post-intro">
					<h1 class="title-l title-margin"><?php the_title(); ?></h1>
					<?php edit_post_link( __('Edit page', 'campus'), '<p class="postmeta">', '</p>'); ?>
				</div><!-- end .post-intro -->
	
				<div class="divider">&nbsp;</div>
	
				<div class="post-single">
				
					<?php the_content(); ?>
					
					<div class="cleaner">&nbsp;</div>
					
					<?php wp_link_pages(array('before' => '<p class="page-navigation"><strong>'.__('Pages', 'campus').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					
				</div><!-- .post-single -->
				
				<?php endwhile; ?>

				<div id="academia-comments">
					<?php comments_template(); ?>  
				</div><!-- end #academia-comments -->				

			</div><!-- .wrapper-content -->
		
		</div><!-- #main -->
		
		<?php get_sidebar(); ?>
		
		<div class="cleaner">&nbsp;</div>
	</div><!-- .wrapper .wrapper-main -->
	
</div><!-- #content -->

<?php get_footer(); ?>