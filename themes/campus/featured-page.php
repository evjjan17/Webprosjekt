<?php 
$pagid = campus_wpml_pageid(get_option('page_on_front'));
?>

<?php
if ($pagid > 0)
{

	$academia_loop = new WP_Query( array( 'page_id' => $pagid ) );
					
		if ($academia_loop->have_posts()) {
		//The Loop
		while ( $academia_loop->have_posts() ) : $academia_loop->the_post(); ?>
	
		<div class="post-meta">
			<h1 class="title-l"><?php the_title(); ?></h1>
		</div><!-- end .post-meta -->

		<div class="divider">&nbsp;</div>

		<div class="post-single">
		
			<?php the_content(); ?>
			
			<div class="cleaner">&nbsp;</div>
			
		</div><!-- .post-single -->
	
		<?php endwhile;
	}
} else { ?>

<div class="post-meta">
	<h1 class="title title-l"><?php _e('Thank you for installing Campus Theme','campus'); ?></h1>
</div><!-- end .post-meta -->

<div class="divider">&nbsp;</div>

<div class="post-single">

	<p><?php _e('Please select a static page to be displayed on the homepage.','campus'); ?><br />
	<?php _e('You can do so by going to','campus'); ?> <a href="<?php echo get_admin_url( '', 'options-reading.php' ); ?>"><?php _e('Dashboard > Settings > Reading','campus'); ?></a></p>
	
	<div class="cleaner">&nbsp;</div>
	
</div><!-- .post-single -->

<?php wp_reset_query(); ?>

<?php } // if page is set ?>