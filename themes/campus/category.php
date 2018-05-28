<?php get_header(); ?>

<div id="content">
	
	<div class="wrapper wrapper-main">

		<div id="main">
		
			<div class="wrapper-content">
			
				<div class="post-intro">
					<h1 class="title-l title-margin"><?php single_cat_title(); ?></h1>
				</div><!-- end .post-intro -->
	
				<?php if (category_description()) { ?>
				<div class="post-single">
				
					<?php echo category_description(); ?>
					
					<div class="cleaner">&nbsp;</div>
					
				</div><!-- .post-single -->
		
				<?php } ?>

				<?php get_template_part('loop'); ?>			
				
			</div><!-- .wrapper-content -->
		
		</div><!-- #main -->
		
		<?php get_sidebar(); ?>
		
		<div class="cleaner">&nbsp;</div>
	</div><!-- .wrapper .wrapper-main -->

</div><!-- #content -->

<?php get_footer(); ?>