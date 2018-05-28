<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 */

?>
<!--FOOTER SIDEBAR-->
 <div id="footer">
   		<?php if ( is_active_sidebar( 'foot_sidebar' ) ) { ?>
    		<div class="widgets">
   			 	<div class="row">
     				<?php if ( is_active_sidebar('dynamic_sidebar') || !dynamic_sidebar('foot_sidebar') ) : ?><?php endif; ?>
            	</div>
   			</div>
   		<?php } ?> 
 
	<!--COPYRIGHT TEXT-->
    <div id="copyright">
    	<div class="row">
            <div class="copytext">
           		<?php
					$advance_footertext = esc_attr(get_theme_mod('advance_footertext'));
					$advance_footertext = html_entity_decode(get_theme_mod ('advance_footertext'));
					echo $advance_footertext;
				?>
           		<a target="_blank" href="<?php echo esc_url( 'http://www.imonthemes.com/'); ?>"><?php printf( __( 'Theme by %s', 'advance' ), 'Imon Themes' ); ?></a>
             </div>
            
            	<?php $advance_social2_checkbox = get_theme_mod('advance_social2_checkbox',0);?>
				<?php if( isset($advance_social2_checkbox) && $advance_social2_checkbox == 1 ):?>
					<?php get_template_part('parts/social','loop'); ?>
      			<?php endif?>
 		</div>
    	<a href="#" class="scrollup" > <i class=" fa fa-angle-up fa-2x"></i></a>
    </div>

</div>





