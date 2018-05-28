	<footer>
	
		<?php if (is_active_sidebar('footer-col-1') || is_active_sidebar('footer-col-2') || is_active_sidebar('footer-col-3')) { ?>
		
		<div class="wrapper wrapper-footer">
		
			<div class="academia-column academia-column-1">
				
				<?php
				if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer: Column 1') ) : ?> <?php endif;
				?>
				
				<div class="cleaner">&nbsp;</div>
			</div><!-- .academia-column .academia-column-1 -->

			<div class="academia-column academia-column-2">
				
				<?php
				if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer: Column 2') ) : ?> <?php endif;
				?>
				
				<div class="cleaner">&nbsp;</div>
			</div><!-- .academia-column .academia-column-1 -->

			<div class="academia-column academia-column-3">
				
				<?php
				if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer: Column 3') ) : ?> <?php endif;
				?>
				
				<div class="cleaner">&nbsp;</div>
			</div><!-- .academia-column .academia-column-1 -->

			<div class="cleaner">&nbsp;</div>
		
		</div><!-- end .wrapper .wrapper-footer -->
		
		<?php } ?>

		<div class="wrapper wrapper-copy">
	
			<?php if (is_front_page()) { ?><p class="academia-credit"><?php _e('Designed by', 'campus'); ?> <a href="http://www.academiathemes.com" target="_blank">AcademiaThemes</a></p><?php } ?>
			<?php $copyright_default = __('Copyright &copy; ','campus') . date("Y",time()) . ' ' . get_bloginfo('name') . '. ' . __('All Rights Reserved', 'campus'); ?>
			<p class="copy"><?php echo esc_attr(get_theme_mod( 'academia_copyright_text', $copyright_default )); ?></p>
	
		</div><!-- .wrapper .wrapper-copy -->

	</footer>

</div><!-- end #container -->

<?php 
wp_reset_query();
wp_footer(); 
?>
</body>
</html>