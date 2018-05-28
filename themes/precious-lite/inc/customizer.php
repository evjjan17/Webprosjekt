<?php
/**
 * Precious Lite Theme Customizer
 *
 * @package Precious Lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function precious_lite_customize_register( $wp_customize ) {

function precious_lite_format_for_editor( $text, $default_editor = null ) {
    if ( $text ) {
        $text = htmlspecialchars( $text, ENT_NOQUOTES, get_option( 'blog_charset' ) );
    }
 
    /**
     * Filter the text after it is formatted for the editor.
     *
     * @since 4.3.0
     *
     * @param string $text The formatted text.
     */
    return apply_filters( 'precious_lite_format_for_editor', $text, $default_editor );
}

//Add a class for titles
    class Precious_Info extends WP_Customize_Control {
        public $type = 'info';
        public $label = '';
        public function render_content() {
        ?>
			<h3 style="text-decoration: underline; color: #DA4141; text-transform: uppercase;"><?php echo esc_html( $this->label ); ?></h3>
        <?php
        }
    }
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';	
	
	$wp_customize->add_section('opacity',array(
			'title'	=> __('Background Opacity (PRO Version)','precious-lite'),
			'description'	=> __('<strong>Background opacity available in</strong>','precious-lite'). '<a href="'.esc_url(precious_lite_pro_theme_url).'">'.__('PRO Version','precious-lite').'</a>',
			'priority'	=> 2
	));
	
	$wp_customize->add_setting('bg_opacity',array(
			'sanitize_callback'	=> 'sanitize_text_field',
			'type'	=> 'info-control',
			'capability'	=> 'edit_theme_options'
	));
	
	$wp_customize->add_control(
		new Precious_Info(
			$wp_customize,
			'bg_opacity',
			array(
				'setting'	=> 'bg_opacity',
				'section'	=> 'opacity'
			)
		)
	);
	
	$wp_customize->add_setting('color_scheme', array(
		'default' => '#d6181a',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','precious-lite'),
			'description'	=> __('<strong>More color options in</strong>','precious-lite'). '<a href="'.esc_url(precious_lite_pro_theme_url).'" target="_blank">PRO version</a>',
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	
	$wp_customize->add_section('social_section',array(
		'title'	=> __('Social Links','precious-lite'),
		'description'	=> __('Add your social links here. <br><strong>More social links in</strong>','precious-lite'). '<a href="'.esc_url(precious_lite_pro_theme_url).'" target="_blank">PRO version</a>.',
		'priority'		=> null
	));

	
	$wp_customize->add_section('slider_section',array(
		'title'	=> __('Slider Settings','precious-lite'),
		'description'	=> __('Add slider images here. <br><strong>More slider settings available in</strong>','precious-lite'). '<a href="'.esc_url(precious_lite_pro_theme_url).'" target="_blank">PRO version</a>.',
		'priority'		=> null
	));
	
	// Slide Image 1
	$wp_customize->add_setting('slide_image1',array(
		'default'	=> get_template_directory_uri().'/images/slides/slider1.jpg',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	
	$wp_customize->add_control(
    new WP_Customize_Image_Control(
        $wp_customize,
        'slide_image1',
        array(
            'label' => __('Slide Image 1 (1440x700)','precious-lite'),
            'section' => 'slider_section',
            'settings' => 'slide_image1'
        )
    )
);

	$wp_customize->add_setting('slide_title1',array(
		'default'	=> __('Responsive Design','precious-lite'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	
	$wp_customize->add_control('slide_title1',array(
		'label'	=> __('Slide Title 1','precious-lite'),
		'section'	=> 'slider_section',
		'type'	=> 'text'
	));
	
	$wp_customize->add_setting('slide_desc1',array(
		'default'	=> __('This is description for slider one.','precious-lite'),
		'sanitize_callback'	=> 'precious_lite_format_for_editor',
	));
	
	$wp_customize->add_control('slide_desc1',array(
				'label' => __('Slide Description 1','precious-lite'),
				'section' => 'slider_section',
				'setting'	=> 'slide_desc1',
				'type'	=> 'textarea'
		)
	);
	
	$wp_customize->add_setting('slide_link1',array(
		'default'	=> '#link1',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	
	$wp_customize->add_control('slide_link1',array(
		'label'	=> __('Slide Link 1','precious-lite'),
		'section'	=> 'slider_section',
		'type'		=> 'text'
	));
	
	// Slide Image 2
	$wp_customize->add_setting('slide_image2',array(
		'default'	=> get_template_directory_uri().'/images/slides/slider2.jpg',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	
	$wp_customize->add_control(
    new WP_Customize_Image_Control(
        $wp_customize,
        'slide_image2',
        array(
            'label' => __('Slide Image 2 (1440x700)','precious-lite'),
            'section' => 'slider_section',
            'settings' => 'slide_image2'
        )
    )
);

	$wp_customize->add_setting('slide_title2',array(
		'default'	=> __('Flexible Design','precious-lite'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	
	$wp_customize->add_control('slide_title2',array(
		'label'	=> __('Slide Title 2','precious-lite'),
		'section'	=> 'slider_section',
		'type'	=> 'text'
	));
	
	$wp_customize->add_setting('slide_desc2',array(
		'default'	=> __('This is description for slide two','precious-lite'),
		'sanitize_callback'	=> 'precious_lite_format_for_editor',
	));
	
	$wp_customize->add_control('slide_desc2',array(
				'label' => __('Slide Description 2','precious-lite'),
				'section' => 'slider_section',
				'setting'	=> 'slide_desc2',
				'type'		=> 'textarea'
		)
	);
	
	$wp_customize->add_setting('slide_link2',array(
		'default'	=> '#link2',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	
	$wp_customize->add_control('slide_link2',array(
		'label'	=> __('Slide Link 2','precious-lite'),
		'section'	=> 'slider_section',
		'type'		=> 'text'
	));
	
	// Slide Image 3
	$wp_customize->add_setting('slide_image3',array(
		'default'	=> get_template_directory_uri().'/images/slides/slider3.jpg',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	
	$wp_customize->add_control(
    new WP_Customize_Image_Control(
        $wp_customize,
        'slide_image3',
        array(
            'label' => __('Slide Image 3 (1440x700)','precious-lite'),
            'section' => 'slider_section',
            'settings' => 'slide_image3'
        )
    )
);

	$wp_customize->add_setting('slide_title3',array(
		'default'	=> __('Awesome Features','precious-lite'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	
	$wp_customize->add_control('slide_title3',array(
		'label'	=> __('Slide Title 3','precious-lite'),
		'section'	=> 'slider_section',
		'type'	=> 'text'
	));
	
	$wp_customize->add_setting('slide_desc3',array(
		'default'	=> __('This is description for slide three','precious-lite'),
		'sanitize_callback'	=> 'precious_lite_format_for_editor',
	));
	
	$wp_customize->add_control('slide_desc3',array(
				'label' => __('Slide Description 3','precious-lite'),
				'section' => 'slider_section',
				'setting'	=> 'slide_desc3',
				'type'		=> 'textarea'
		)
	);
	
	$wp_customize->add_setting('slide_link3',array(
		'default'	=> '#link3',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	
	$wp_customize->add_control('slide_link3',array(
		'label'	=> __('Slide Link 3','precious-lite'),
		'section'	=> 'slider_section',
		'type'		=> 'text'
	));
	
	$wp_customize->add_section('footer_section',array(
		'title'	=> __('Footer Text','precious-lite'),
		'description'	=> __('Add some text for footer like copyright etc.','precious-lite'),
		'priority'	=> null
	));
	
	$wp_customize->add_setting('footer_copy',array(
		'default'	=> __('Precious Lite 2015 | All Rights Reserved.','precious-lite'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	
	$wp_customize->add_control('footer_copy',array(
		'label'	=> __('Copyright Text','precious-lite'),
		'section'	=> 'footer_section',
		'type'		=> 'text'
	));
	
	$wp_customize->add_setting('Precious_options[credit-info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Precious_Info( $wp_customize, 'cred_section', array(
        'section' => 'footer_section',
		'label'	=> __('To remove credit link upgrade to pro','precious-lite'),
        'settings' => 'Precious_options[credit-info]',
        ) )
    );
	
	$wp_customize->add_section(
        'theme_layout_sec',
        array(
            'title' => __('Layout Settings (PRO Version)', 'precious-lite'),
            'priority' => null,
            'description' => __('<strong>Layout Settings available in</strong>','precious-lite'). '<a href="'.esc_url(precious_lite_pro_theme_url).'" target="_blank">'.__('PRO Version','precious-lite').'</a>.',
        )
    );  
    $wp_customize->add_setting('Precious_options[layout-info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Precious_Info( $wp_customize, 'layout_section', array(
        'section' => 'theme_layout_sec',
        'settings' => 'Precious_options[layout-info]',
        'priority' => null
        ) )
    );
	
	$wp_customize->add_section(
        'theme_font_sec',
        array(
            'title' => __('Fonts Settings (PRO Version)', 'precious-lite'),
            'priority' => null,
            'description' => __('<strong>Font Settings available in</strong>','precious-lite'). '<a href="'.esc_url(precious_lite_pro_theme_url).'" target="_blank">'.__('PRO Version','precious-lite').'</a>.',
        )
    );  
    $wp_customize->add_setting('Precious_options[font-info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Precious_Info( $wp_customize, 'font_section', array(
        'section' => 'theme_font_sec',
        'settings' => 'Precious_options[font-info]',
        'priority' => null
        ) )
    );
	
    $wp_customize->add_section(
        'precious_lite_theme_doc',
        array(
            'title' => __('Documentation &amp; Support', 'precious-lite'),
            'priority' => null,
            'description' => __('For documentation and support check this link :','precious-lite'). '<a href="'.esc_url(precious_lite_theme_doc).'" target="_blank">Precious Lite Documentation</a>',
        )
    );  
    $wp_customize->add_setting('Precious_options[info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Precious_Info( $wp_customize, 'doc_section', array(
        'section' => 'theme_doc_sec',
        'settings' => 'Precious_options[info]',
        'priority' => 10
        ) )
    );
	
	
}
add_action( 'customize_register', 'precious_lite_customize_register' );

//Integer
function precious_lite_sanitize_integer( $input ) {
    if( is_numeric( $input ) ) {
        return intval( $input );
    }
}	

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function precious_lite_customize_preview_js() {
	wp_enqueue_script( 'precious_lite_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'precious_lite_customize_preview_js' );

function precious_lite_css(){
		?>
        <style>
				.social_icons h5,
				.social_icons a,
				a, 
				.tm_client strong,
				#footer a,
				#footer ul li:hover a, 
				#footer ul li.current_page_item a,
				.postmeta a:hover,
				#sidebar ul li a:hover,
				.blog-post h3.entry-title,
				.woocommerce ul.products li.product .price{
					color:<?php echo esc_html(get_theme_mod('color_scheme','#d6181a')); ?>;
				}
				a.read-more, a.blog-more,
				.pagination ul li .current, 
				.pagination ul li a:hover,
				#commentform input#submit,
				input.search-submit{
					background-color:<?php echo esc_html(get_theme_mod('color_scheme','#d6181a')); ?>;
				}
		</style>
	<?php }
add_action('wp_head','precious_lite_css');

function precious_lite_custom_customize_enqueue() {
	wp_enqueue_script( 'precious-lite-custom-customize', get_template_directory_uri() . '/js/custom.customize.js', array( 'jquery', 'customize-controls' ), false, true );
}
add_action( 'customize_controls_enqueue_scripts', 'precious_lite_custom_customize_enqueue' );