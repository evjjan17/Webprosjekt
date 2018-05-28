<?php get_header(); ?>

<div id="content">
	
	<div class="wrapper wrapper-main">

		<div id="main">
		
			<div class="wrapper-content">
			
				<?php $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author')); ?>
				<div class="post-intro">
					<h1 class="title-l title-margin"><?php _e('Posts by', 'campus');?> <span><?php echo $curauth->display_name; ?></span></h1>
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