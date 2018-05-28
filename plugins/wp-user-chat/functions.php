<?php
function WPCHT_save_error()
{
    file_put_contents(dirname(__file__).'/error_activation.txt', ob_get_contents());
}

function WPCHT_activate()
{
	
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   $sql="
       CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."WPCHT_chat`
     (
          id bigint(20) NOT NULL auto_increment,
          sender_id mediumint(9) NOT NULL default 0,
          receiver_id mediumint(9) NOT NULL default 0,
          message varchar(255),
          sent datetime NOT NULL default CURRENT_TIMESTAMP,
          recd mediumint(9) NOT NULL default 0,
          PRIMARY KEY  (`id`)
     );";
    dbDelta($sql);
 
	
}

function WPCHT_deactivate()
{
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}WPCHT_chat" );
}

function WPCHT_load()
{
	

	wp_register_script('WPCHT-script1', plugins_url( '/js/chat.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('WPCHT-script1');

    wp_register_script('WPCHT-script2',plugins_url( '/js/main.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('WPCHT-script2');

    wp_register_style('WPCHT-css1',plugins_url( '/css/style.css', __FILE__ ));
    wp_enqueue_style('WPCHT-css1');

    wp_register_style('WPCHT-css2',plugins_url( '/css/chat.css', __FILE__ ));
    wp_enqueue_style('WPCHT-css2');

	
}

function WPCHT_last_login_time($login)
{
    global $user_ID;
    $user = get_userdatabylogin($login);
    update_usermeta( $user->ID, 'last_login_time', date('Y-m-d H:i:s') );
    update_usermeta( $user->ID, 'last_logout_time', '0000-00-00 00:00:00' );
    update_usermeta($user->ID,'is_active',1);
    session_start();
    $_SESSION['active']=1;
}

function WPCHT_last_logout_time()
{
  	$current_user = wp_get_current_user(); 
  	update_usermeta($current_user->ID,'last_logout_time',date('Y-m-d H:i:s'));
  	update_usermeta($current_user->ID,'is_active',0);
   
}


function WPCHT_login_redirect( $url, $request, $user ){
    if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
        if( $user->has_cap( 'administrator' ) ) {
            $url = admin_url();
        } else {
            $url = home_url();
        }
    }
    return $url;
}
add_filter('login_redirect', 'WPCHT_login_redirect', 10, 3 );



function WPCHT_logout_redirect( $url, $request, $user ){
    if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
        if( $user->has_cap( 'administrator' ) ) {
            $url = admin_url();
        } else {
            $url = home_url();
        }
    }
    return $url;
}


function WPCHT_remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  show_admin_bar(false);
	}
}

