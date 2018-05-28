<?php			

/**
 * Adds the Customize page to the WordPress admin area
 */
function campus_customizer_menu() {
	add_theme_page( __('Customize','campus'), __('Customize','campus'), 'edit_theme_options', 'customize.php' );
}
add_action( 'admin_menu', 'campus_customizer_menu' );

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */

function campus_customizer( $wp_customize ) {

	// Define array of web safe fonts
	$academia_fonts = array(
		'default' => __('Default','campus'),
		'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
		'Georgia, serif' => 'Georgia, serif',
		'Impact, Charcoal, sans-serif' => 'Impact, Charcoal, sans-serif',
		'"Source Sans Pro", Arial, Helvetica, sans-serif' => 'Source Sans Pro, Arial, Helvetica, sans-serif',
		'"Palatino Linotype", "Book Antiqua", Palatino, serif' => 'Palatino Linotype, Book Antique, Palatino, serif',
		'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva, sans-serif',
	);

	$wp_customize->add_section(
		'academia_section_general',
		array(
			'title' => __('General Settings','campus'),
			'description' => __('This controls various general theme settings.','campus'),
			'priority' => 5,
		)
	);

	$wp_customize->add_section(
		'academia_section_fonts',
		array(
			'title' => __('Fonts & Color Settings','campus'),
			'description' => __('Customize theme fonts and color of elements.','campus'),
			'priority' => 35,
		)
	);


	$wp_customize->add_setting( 
		'academia_logo_upload',
		array(
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	
	$wp_customize->add_control(
		new WP_Customize_Upload_Control(
			$wp_customize,
			'file-upload',
			array(
				'label' => __('Logo File Upload','campus'),
				'section' => 'academia_section_general',
				'settings' => 'academia_logo_upload'
			)
		)
	);

	$copyright_default = __('Copyright &copy; ','campus') . date("Y",time()) . ' ' . get_bloginfo('name') . '. ' . __('All Rights Reserved', 'campus');
	
	$wp_customize->add_setting(
		'academia_copyright_text',
		array(
			'default' => $copyright_default,
			'sanitize_callback' => 'sanitize_text_input',
		)
	);

	$wp_customize->add_control(
		'academia_copyright_text',
		array(
			'label' => __('Copyright text in Footer','campus'),
			'section' => 'academia_section_general',
			'type' => 'text',
		)
	);

	$wp_customize->add_setting(
		'academia_color_header',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'academia_color_header',
			array(
				'label' => __('Header background color','campus'),
				'section' => 'academia_section_fonts',
				'settings' => 'academia_color_header',
				'priority' => 1
			)
		)
	);

	$wp_customize->add_setting(
		'academia_color_header2',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'academia_color_header2',
			array(
				'label' => __('Header wrapper background color','campus'),
				'section' => 'academia_section_fonts',
				'settings' => 'academia_color_header2',
				'priority' => 2
			)
		)
	);

	$wp_customize->add_setting(
		'academia_color_menu_bg',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'academia_color_menu_bg',
			array(
				'label' => __('Menu background color','campus'),
				'section' => 'academia_section_fonts',
				'settings' => 'academia_color_menu_bg',
				'priority' => 3
			)
		)
	);

	$wp_customize->add_setting(
		'academia_font_main',
		array(
			'default' => 'default',
			'sanitize_callback' => 'sanitize_font',
		)
	);
	
	$wp_customize->add_control(
		'academia_font_main',
		array(
			'type' => 'select',
			'label' => __('Choose the main body font','campus'),
			'section' => 'academia_section_fonts',
			'choices' => $academia_fonts,
			'priority' => 4
		)
	);

	$wp_customize->add_setting(
		'academia_color_body',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'academia_color_body',
			array(
				'label' => __('Main body text color','campus'),
				'section' => 'academia_section_fonts',
				'settings' => 'academia_color_body',
				'priority' => 4
			)
		)
	);

	$wp_customize->add_setting(
		'academia_color_link',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'academia_color_link',
			array(
				'label' => __('Main body anchor(link) color','campus'),
				'section' => 'academia_section_fonts',
				'settings' => 'academia_color_link',
				'priority' => 5
			)
		)
	);

	$wp_customize->add_setting(
		'academia_color_link_hover',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'academia_color_link_hover',
			array(
				'label' => __('Main body anchor(link) :hover color','campus'),
				'section' => 'academia_section_fonts',
				'settings' => 'academia_color_link_hover',
				'priority' => 6
			)
		)
	);

}
add_action( 'customize_register', 'campus_customizer' );