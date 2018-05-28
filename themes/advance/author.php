<?php 
/**
 *
 * Displays The Author page template
 *
 * @package advance
 * 
 */
?>

<?php get_header(); ?>
	 <!-- head select -->   
	
        <?php get_template_part('headers/part','headsingle'); ?>
        <div id="sub_banner">
 <h1>
	<?php
		/**
		 * Filter the Advance author bio avatar size.
		 *
		 * @since Advance
		 *
		 * @param int $size The avatar height and width size in pixels.
		 */
		$author_bio_avatar_size = apply_filters( 'advance_author_bio_avatar_size', 42 );

		echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
	?>
   <?php _e('Posts by ', 'advance');?><?php echo get_the_author(); ?>
        
    <!--AUTHOR BIO END-->
</h1>
<div class='h-line'></div>

</div>
   <div id="content">
  		<div class="row">
    		<div class="large-9 columns <?php if ( !is_active_sidebar( 'sidebar' ) ){ ?> nosid <?php }?>">	
  					<?php if ( have_posts() ) : ?>
			
						<?php /* Start the Loop */ ?>
							<?php while ( have_posts() ) : the_post(); ?>
								<?php
								/*
							 	* Include the Post-Format-specific template for the content.
								 * If you want to override this in a child theme, then include a file
					 		 	* called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 			*/
								get_template_part( 'content', get_post_format() );
								?>

						 <?php endwhile; ?>

			 		<?php get_template_part('pagination'); ?>  

				<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>
			
		<?php endif; ?>
		</div><!--POST END--> 
 
 			<div class=" wow fadeIn large-3 small-12 columns"> 
   
   				<?php get_sidebar();?>

			</div><!--sidebar END--> 
	 </div><!--row END-->
</div><!--content END-->
<?php get_footer(); ?>
