
	 <!-- head select -->

        <?php get_template_part('headers/part','headsingle'); ?>

	<!-- / head select -->

		<div id="content" >
			<div class="row">
				<div class="large-9 columns <?php if ( !is_active_sidebar( 'sidebar' ) ){ ?> nosid <?php }?> ">
						<!--Content-->
   				<?php if(have_posts()): ?><?php while(have_posts()): ?><?php the_post(); ?>
            <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">  </div>
							<div  class=" content_blog blog_style_b1" role="main">
 								<?php if ( is_user_logged_in() || is_admin() ) { ?>
									<?php
										edit_post_link(
										sprintf(
										/* translators: %s: Name of current post */
										__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'advance' ),
											get_the_title()
										),
										'<span class="edit-link">',
										'</span>'
										);
										?>
									<?php } ?>
									<div class="title_area">
										<h1 class="wow fadeInup post_title"><?php the_title(); ?></h1>
									</div>
                    <div class="post_info post_info_2">
                      <span class="post_author"> <i class="fa fa-calendar"></i>
                        <a class="post_date"><?php the_time( get_option('date_format') ); ?></a></span>
                      	<span class="post_info_delimiter"></span>
												<span class="post_author"><i class="fa fa-user"></i>
                        	<a class="post_author" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
														<?php the_author(); ?>
													</a>
												</span>
												<span class="post_info_delimiter"></span>
                          <?php if( has_category() ) { ?>
														<span class="post_categories">
														<span class="cats_label"><i class="fa fa-th-list"></i></span>
														<a class="cat_link"><?php $categories = get_the_category();
                                                      $separator = ' ';
 														 													$output = '';
															if ( ! empty( $categories ) ) {
    																foreach( $categories as $category ) {
        														$output .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr(sprintf( __( 'View all posts in %s', 						                 'advance' ), $category->name ) ) . '">' . esc_html( $category->name ) . '</a>' . $separator;
    																}
   														 		echo trim( $output, $separator );
															} ?>
														</a>
													</span>

												<?php } ?>
                          <div class="post_comments">
                            <a><i class="fa fa-comments"></i><span class="comments_number">
															<?php comments_popup_link( __('0 Comment', 'advance'), __('1 Comment', 'advance'), __('% Comments', 'advance'), '', __('Off' , 'advance')); ?>
                              </span><span class="icon-comment"></span></a>
                          </div>
												</div>


				<div class="post_content wow fadeIn">
							<p><?php the_content(); ?></p>
					<div class="post_wrap_n">
						<?php wp_link_pages( array(
								'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'advance' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
								'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'advance' ) . ' </span>%',
								'separator'   => '<span class="screen-reader-text">, </span>',
						) );?>
          </div>
				</div>

            <?php if( has_tag() ) { ?>
						<div class="post_info post_info_4 clearboth">
                        	<span class="post_tags">
								<span class="tags_label"><i class="fa fa-tag fa-lg"></i></span><?php } ?>
								<?php if( has_tag() ) { ?><a class="tag_link"><?php the_tags('','  '); ?></a>

							</span>
						</div>
					<?php } ?>

       		  <!--NEXT AND PREVIOUS POSTS START-->
       		  <div class="wp-pagenavi">
                    <?php previous_post_link( '<div class="alignleft">%link</div>', '&laquo; %title' ); ?>
                    <?php next_post_link( '<div class="alignright">%link</div>', '%title &raquo; ' ); ?>

                </div>
               <!--NEXT AND PREVIOUS POSTS END-->
  <?php endwhile ?>

    <!--POST END-->

    <!--COMMENT START-->
    	<a class="comments_template">
			<?php if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
        </a>
     <!--COMMENT END-->
	 		<?php endif ?>

</div>

    </div>

  <div class=" wow fadeIn large-3 small-12 columns"><!--SIDEBAR START-->

	<?php get_sidebar();?>

</div><!--SIDEBAR END-->

</div>
</div>
