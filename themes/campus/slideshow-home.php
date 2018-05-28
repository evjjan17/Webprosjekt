<?php 

$loop = new WP_Query( 
	array( 
		'post__not_in' => get_option( 'sticky_posts' ),
		'posts_per_page' => 5,
		'meta_key' => 'academia_post_display_home',
		'meta_value' => 'on'				
		) );
$default_image = get_template_directory_uri() . '/images/x.gif';

if ( $loop->have_posts() ) { ?>

<div id="academia-gallery" class="flexslider">
	<ul class="academia-slides">

		<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
		<li class="academia-gallery-slide">
		
			<?php get_the_image( array( 'size' => 'thumb-academia-slideshow', 'width' => 630, 'height' => 350, 'default_image' => $default_image, 'before' => '<div class="post-cover">', 'after' => '</div>' ) ); ?>

			<div class="slide-meta">
				<h2 class="title-post title-ms"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			</div><!-- end .slide-meta -->

		</li><!-- end .academia-gallery-slide -->
		<?php endwhile; ?>

	</ul><!-- .academia-slides -->
</div><!-- end #academia-gallery .flexslider -->

<?php  // if there are posts
} ?>