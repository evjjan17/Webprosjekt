<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Precious Lite
 */
?>

        <div class="copyright-wrapper">
        	<div class="inner">
                <div class="footer-menu">
                        <?php wp_nav_menu(array('theme_location' => 'footer')); ?>
               </div><!-- footer-menu -->
                <div class="copyright">
                    	<p><?php echo esc_attr(get_theme_mod('footer_copy',__('Precious Lite 2015 | All Rights Reserved.','precious-lite'))); ?> <?php echo precious_lite_credit_link(); ?></p>               
                </div><!-- copyright --><div class="clear"></div>         
            </div><!-- inner -->
        </div>
    </div>
<a href="#" class="scrollToTop"></a>  
<?php wp_footer(); ?>

</body>
</html>