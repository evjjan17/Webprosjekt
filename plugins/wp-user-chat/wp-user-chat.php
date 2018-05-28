<?php
/*
Plugin Name: WP User Chat
Donate link: http://www.wpsuperiors.com/donate/
Plugin URI: http://www.wpsuperiors.com/shop/
Description: This plugin gives you freedom to chat with users each other. It supported many to many interaction.
Version: 1.0.1
Author: WPSuperiors
Author URI: http://www.wpsuperiors.com
*/
register_activation_hook( __FILE__, 'WPCHT_activate' );
register_deactivation_hook( __FILE__, 'WPCHT_deactivate' );

require_once(dirname(__file__).'/functions.php');

require_once(dirname(__file__).'/display-front.php');

require_once(dirname(__file__).'/all-ajax.php');

require_once(dirname(__file__).'/chat-data.php');

require_once(dirname(__file__).'/avatar.php');


add_action('wp_login','WPCHT_last_login_time');

add_action('wp_logout','WPCHT_last_logout_time');

add_action( 'get_footer', 'WPCHT_display_front' );

add_action('activated_plugin','WPCHT_save_error');

add_action('init','WPCHT_load');

add_filter('logout_redirect', 'WPCHT_login_redirect', 10, 3 );

add_action('after_setup_theme', 'WPCHT_remove_admin_bar');

add_action('wp_ajax_WPCHT_getChatData', 'WPCHT_getChatData');

add_action('wp_ajax_WPCHT_startChatSession', 'WPCHT_startChatSession');

add_action('wp_ajax_WPCHT_sendChat', 'WPCHT_sendChat');

add_action('wp_ajax_WPCHT_closeChat', 'WPCHT_closeChat');

add_action('wp_ajax_WPCHT_getReceiverId', 'WPCHT_getReceiverId');

add_action( 'show_user_profile', 'WPCHT_avatar_field' );

add_action( 'edit_user_profile', 'WPCHT_avatar_field' );

add_action( 'personal_options_update', 'WPCHT_save_avatar_field' );

add_action( 'edit_user_profile_update', 'WPCHT_save_avatar_field' );

add_action('admin_head-users.php','WPCHT_hide_avatars_username_column');

add_filter( 'manage_users_columns', 'WPCHT_add_avatar_column' );

add_filter( 'manage_users_custom_column', 'WPCHT_show_avatar_column_data', 10, 3 );

add_action('wp_ajax_WPCHT_genarate_user_list','WPCHT_genarate_user_list');

add_action('wp_ajax_nopriv_WPCHT_genarate_user_list','WPCHT_genarate_user_list');

add_action('wp_ajax_WPCHT_check_user_status','WPCHT_check_user_status');

add_action('wp_ajax_nopriv_WPCHT_check_user_status','WPCHT_check_user_status');

add_action('wp_ajax_WPCHT_get_user_details','WPCHT_get_user_details');

add_action('wp_ajax_nopriv_WPCHT_get_user_details','WPCHT_get_user_details');

add_action('wp_ajax_WPCHT_setMood', 'WPCHT_setMood');

wp_register_sidebar_widget(
    'user_widget',
    'User Details',
    'WPCHT_user_widget_display',
    array( 
        'description' => 'View the loggedin user details'
    )
);

