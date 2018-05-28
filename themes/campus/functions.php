<?php			

if ( ! isset( $content_width ) ) $content_width = 630;

/* Add javascripts and CSS used by the theme 
================================== */

function campus_scripts_styles() {

	// Loads our main stylesheet
	wp_enqueue_style( 'campus-campus-style', get_stylesheet_uri(), array(), '2016-11-23' );
	
	if (! is_admin()) {

		wp_enqueue_script(
			'superfish',
			get_template_directory_uri() . '/js/superfish.js',
			array('jquery'),
			null
		);
		
		wp_enqueue_script(
			'init',
			get_template_directory_uri() . '/js/init.js',
			array('jquery'),
			null
		);

		wp_enqueue_script(
			'flexslider',
			get_template_directory_uri() . '/js/jquery.flexslider.js',
			array('jquery'),
			null
		);

		if ( is_front_page() || is_singular() && wp_attachment_is_image() ) {
			wp_enqueue_script(
				'init-slider',
				get_template_directory_uri() . '/js/init-slider.js',
				array('jquery','flexslider'),
				null
			);
		}

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

		// Loads our default Google Webfont
		wp_enqueue_style( 'campus-webfonts', '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,400italic,600italic,700italic', array() );

	}

}
add_action('wp_enqueue_scripts', 'campus_scripts_styles');

// This theme styles the visual editor to resemble the theme style.
add_editor_style( array( 'css/editor-style.css' ) );

/**
 * Sets up theme defaults and registers the various WordPress features that Campus supports.
 *
 * @return void
 */

function campus_setup() {

	/* Register Thumbnails Size 
	================================== */
	
	add_image_size( 'thumb-academia-slideshow', 630, 350, true );
	add_image_size( 'thumb-loop-main', 260, 0, false );
	
	/* 	Register Custom Menu 
	==================================== */
	
	register_nav_menu('primary', 'Main Menu');
	register_nav_menu('secondary', 'Secondary Menu');
	
	/* Add support for Localization
	==================================== */
	
	load_theme_textdomain( 'campus', get_template_directory() . '/languages' );
	
	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable($locale_file) )
		require_once($locale_file);
	
	/* Add support for Custom Background 
	==================================== */
	
	add_theme_support( 'custom-background' );
	
	/* Add support for post and comment RSS feed links in <head>
	==================================== */
	
	add_theme_support( 'automatic-feed-links' ); 

	/* Add support for WP 4.1 title tag
	==================================== */
	
	add_theme_support( 'title-tag' );

	if ( ! function_exists( '_wp_render_title_tag' ) ) :
	    function campus_render_title() {
	?>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php
	    }
	    add_action( 'wp_head', 'campus_render_title' );
	endif;

}

add_action( 'after_setup_theme', 'campus_setup' );

/**
 * Registers one widget area.
 *
 * @return void
 */

function campus_campus_widgets_init() {

	register_sidebar( array(
		'name'          => __( 'Homepage: Top', 'campus' ),
		'id'            => 'home-full-1',
		'description'   => __( 'Appears on the homepage of the site, before the posts loop.', 'campus' ),
		'before_widget' => '<div class="widget %2$s" id="%1$s">',
		'after_widget'  => '<div class="cleaner">&nbsp;</div></div>',
		'before_title'  => '<p class="title-widget title-m">',
		'after_title'   => '</p>',
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar', 'campus' ),
		'id'            => 'sidebar',
		'description'   => __( 'Appears in the narrow column of the site.', 'campus' ),
		'before_widget' => '<div class="widget %2$s" id="%1$s">',
		'after_widget'  => '<div class="cleaner">&nbsp;</div></div>',
		'before_title'  => '<p class="title-widget title-s">',
		'after_title'   => '</p>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer: Column 1', 'campus' ),
		'id'            => 'footer-col-1',
		'description'   => __( 'Appears in the footer of the site.', 'campus' ),
		'before_widget' => '<div class="widget %2$s" id="%1$s">',
		'after_widget'  => '<div class="cleaner">&nbsp;</div></div>',
		'before_title'  => '<p class="title-widget title-s">',
		'after_title'   => '</p>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer: Column 2', 'campus' ),
		'id'            => 'footer-col-2',
		'description'   => __( 'Appears in the footer of the site.', 'campus' ),
		'before_widget' => '<div class="widget %2$s" id="%1$s">',
		'after_widget'  => '<div class="cleaner">&nbsp;</div></div>',
		'before_title'  => '<p class="title-widget title-s">',
		'after_title'   => '</p>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer: Column 3', 'campus' ),
		'id'            => 'footer-col-3',
		'description'   => __( 'Appears in the footer of the site.', 'campus' ),
		'before_widget' => '<div class="widget %2$s" id="%1$s">',
		'after_widget'  => '<div class="cleaner">&nbsp;</div></div>',
		'before_title'  => '<p class="title-widget title-s">',
		'after_title'   => '</p>',
	) );

}

add_action( 'widgets_init', 'campus_campus_widgets_init' );

/* Enable Excerpts for Static Pages
==================================== */

add_action( 'init', 'campus_excerpts_for_pages' );

function campus_excerpts_for_pages() {
	add_post_type_support( 'page', 'excerpt' );
}

/* Custom Excerpt Length
==================================== */

function campus_new_excerpt_length($length) {
	return 40;
}
add_filter('excerpt_length', 'campus_new_excerpt_length');

/* Replace invalid ellipsis from excerpts
==================================== */

function campus_excerpt($text)
{
   return str_replace(' [...]', '...', $text); // if there is a space before ellipsis
   return str_replace('[...]', '...', $text);
}
add_filter('the_excerpt', 'campus_excerpt');

/* Reset [gallery] shortcode styles						
==================================== */

add_filter('gallery_style', create_function('$a', 'return "<div class=\'gallery\'>";'));

/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @since Campus 1.1
 *
 * @global int $paged WordPress archive pagination page count.
 * @global int $page  WordPress paginated post page count.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function campus_campus_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() ) {
		return $title;
	}

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title = "$title $sep " . sprintf( __( 'Page %s', 'campus' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'campus_campus_wp_title', 10, 2 );

/* Comments Custom Template						
==================================== */

function campus_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
			?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<div id="comment-<?php comment_ID(); ?>">
				
					<div class="comment-author vcard">
						<?php echo get_avatar( $comment, 50 ); ?>

						<div class="reply">
							<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						</div><!-- .reply -->

					</div><!-- .comment-author .vcard -->
	
					<div class="comment-body">
	
						<?php printf( __( '%s', 'campus' ), sprintf( '<cite class="comment-author-name">%s</cite>', get_comment_author_link() ) ); ?>
						<span class="comment-timestamp"><?php printf( __('%s at %s', 'campus'), get_comment_date(), get_comment_time()); ?></span><?php edit_comment_link( __( 'Edit', 'campus' ), ' <span class="comment-bullet">&#8226;</span> ' ); ?>
	
						<div class="comment-content">
						<?php if ( $comment->comment_approved == '0' ) : ?>
						<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'campus' ); ?></p>
						<?php endif; ?>
	
						<?php comment_text(); ?>
						</div><!-- .comment-content -->

					</div><!-- .comment-body -->
	
					<div class="cleaner">&nbsp;</div>
				
				</div><!-- #comment-<?php comment_ID(); ?> -->
		
			</li><!-- #li-comment-<?php comment_ID(); ?> -->
		
			<?php
		break;

		case 'pingback'  :
		case 'trackback' :
			?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<p><?php _e( 'Pingback:', 'campus' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'campus' ), ' ' ); ?></p>
			</li>
			<?php
		break;
	
	endswitch;
}

/* Add theme customizer to <head> 
================================== */

function campus_customizer_head() {

	/*
	This block refers to the functionality of the Appearance > Customize screen.
	*/
	
	$academia_font_main = esc_attr(get_theme_mod( 'academia_font_main' ));
	$academia_color_header = esc_attr(get_theme_mod( 'academia_color_header' ));
	$academia_color_header2 = esc_attr(get_theme_mod( 'academia_color_header2' ));
	$academia_color_menu_bg = esc_attr(get_theme_mod( 'academia_color_menu_bg' ));
	$academia_color_body = esc_attr(get_theme_mod( 'academia_color_body' ));
	$academia_color_link = esc_attr(get_theme_mod( 'academia_color_link' ));
	$academia_color_link_hover = esc_attr(get_theme_mod( 'academia_color_link_hover' ));
	
	if( $academia_font_main != '' || $academia_color_header != '' || $academia_color_body != '' || $academia_color_link != '' || $academia_color_link_hover != '') {
		echo '<style type="text/css">';
		if (($academia_font_main != '') && ($academia_font_main != 'default')) {
			echo 'body { font-family: '.$academia_font_main.'; } ';
		}
		if ($academia_color_header != '') {
			echo 'header { background-color: '.$academia_color_header.'; } ';
		}
		if ($academia_color_header2 != '') {
			echo '.wrapper-header { background-color: '.$academia_color_header2.'; } ';
		}
		if ($academia_color_menu_bg != '') {
			echo '#header-menu { background-color: '.$academia_color_menu_bg.'; } ';
		}
		if ($academia_color_body != '') {
			echo 'body, .post-single { color: '.$academia_color_body.'; } ';
		}
		if ($academia_color_link != '') {
			echo 'a, .featured-pages h2 a { color: '.$academia_color_link.'; } ';
		}
		if ($academia_color_link_hover != '') {
			echo 'a:hover, .featured-pages h2 a:hover { color: '.$academia_color_link_hover.'; } ';
		}

		echo '</style>';
	}

}
add_action('wp_head', 'campus_customizer_head');

/* Include WordPress Theme Customizer
================================== */

require_once('academia-admin/academia-customizer.php');

/* Include Additional Options and Components
================================== */

if ( !function_exists( 'get_the_image' ) ) {
	require_once('academia-admin/components/get-the-image.php');
}
require_once('academia-admin/components/wpml.php'); // enables support for WPML plug-in
require_once('academia-admin/post-options.php');