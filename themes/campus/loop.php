<ul class="academia-posts">
	
	<?php while (have_posts()) : the_post(); unset($prev); $m++; ?>
	<li <?php post_class('academia-post'); ?>>

		<?php
		get_the_image( array( 'size' => 'thumb-loop-main', 'width' => 260, 'before' => '<div class="post-cover">', 'after' => '</div><!-- end .post-cover -->' ) );
		?>
		
		<div class="post-content">
			<h2 class="title-post title-ms"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'campus' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title(); ?></a></h2>
			<p class="post-excerpt"><?php echo get_the_excerpt(); ?></p>
			<p class="post-meta"><time datetime="<?php echo get_the_date('c'); ?>" pubdate><?php echo get_the_date(); ?></time> / <span class="category"><?php the_category(', '); ?></span></p>
		</div><!-- end .post-content -->

		<div class="cleaner">&nbsp;</div>
		
	</li><!-- end .academia-post -->
	<?php endwhile; ?>
	
</ul><!-- end .academia-posts -->

<?php get_template_part( 'pagination'); ?>