<?php
// Hook into core get users AJAX function
add_action( 'wp_ajax_wps_shortcode_options_save', 'wps_shortcode_options_save' ); 
add_action( 'wp_ajax_wps_styles_options_save', 'wps_styles_options_save' ); 
add_action( 'wp_ajax_wps_styles_disable', 'wps_styles_disable' ); 
add_action( 'wp_ajax_wps_styles_enable', 'wps_styles_enable' ); 
add_action( 'wp_ajax_wps_hide_welcome_header_toggle', 'wps_hide_welcome_header_toggle' ); 
add_action( 'wp_ajax_wps_hide_admin_links_toggle', 'wps_hide_admin_links_toggle' ); 
add_action( 'wp_ajax_wps_toggle_main_menu', 'wps_toggle_main_menu' ); 

/* TOGGLE SHOW ON MAIN MENU */
function wps_toggle_main_menu() {

    $item = $_POST['item'];
    $admin_favs = get_option('wps_admin_favs');
    if (!in_array($item, $admin_favs)):
        $admin_favs[] = $item;
    echo "added ".$item;
    else:
        if(($key = array_search($item, $admin_favs)) !== false) {
            unset($admin_favs[$key]);
            echo "removed ".$item;
        }    
    endif;
    var_dump($admin_favs);
    update_option('wps_admin_favs', $admin_favs);
    
    exit;
}

/* TOGGLE SETUP PAGE ADMIN LINKS */
function wps_hide_admin_links_toggle() {

    if (get_option('wps_core_admin_icons')):
        delete_option('wps_core_admin_icons'); // show them on setup page (not on menu)
    else:
        add_option('wps_core_admin_icons', true); // hide them (show on menu)
    endif;
    exit;
}

/* TOGGLE SETUP PAGE WELCOME HEADER */
function wps_hide_welcome_header_toggle() {

    if (get_option('wps_show_welcome_header')):
        delete_option('wps_show_welcome_header');
    else:
        add_option('wps_show_welcome_header', true);
    endif;
    exit;
}

/* SAVE SHORTCODE OPTIONS */
function wps_shortcode_options_save() {
    
    global $current_user;
    
    if ( is_user_logged_in() && current_user_can('manage_options')) {

        $data = $_POST['data'];
        $arr = $data['arr'];
        
        // For multi-select checkboxes, for example:
        //   name = wps_directory_role[]
        //   type = editor (one of the chosen roles, AJAX will sort out how to save)
        //   val = 1 (only ever includes when set to 1)        
        
        // Get values
        $values = array();
    
        // Now recreate before saving
        foreach ($arr as $row):
        
            if (strpos($row[0], '-')):

                $name = explode('-', $row[0]);

                $function = $name[0];
                $option = $name[1];
                $shortcode = $row[0];

                $type = $row[1];
                $form_value = $row[2];

                $v = $form_value ? $form_value : false;
        
                if (strpos($option, '[]') === false):
                    // not a multi-select checkboxes, so just save it
                    $values[$option] = $v ? htmlentities (stripslashes($v), ENT_QUOTES) : '';
                else:
                    // otherwise, need to add to a potentially growing CSV list
                    $option = str_replace('[]', '', $option);
                    if ($v == 'true'):
                        // set to true, so add to list
                        $values[$option] = $values[$option] . $type . ',';
                    endif;
                endif;
        
            endif;

        endforeach;
        
        // store values
        update_option('wps_shortcode_options_'.$function, $values);
        
    }
    
    exit;
    
}

/* SAVE STYLES OPTIONS */
function wps_styles_options_save() {
    
    global $current_user;
    
    if ( is_user_logged_in() && current_user_can('manage_options')) {

        $data = $_POST['data'];
        $arr = $data['arr'];
        
        // Get values
        $values = array();
    
        // Now recreate before saving
        foreach ($arr as $row):
        
            if (strpos($row[0], '-')):

                $name = explode('-', $row[0]);

                $function = $name[0];
                $option = $name[1];

                $type = $row[1];
                $form_value = $row[2];

                $v = $form_value ? $form_value : false;

                $values[$option] = $v ? htmlentities (stripslashes($v), ENT_QUOTES) : '';

            endif;

        endforeach;
        
        update_option('wps_styles_'.$function, $values);
        
    }
    
    exit;
    
}

function wps_styles_enable() {
    update_option('wpspro_use_styles', true);
    exit;
}

function wps_styles_disable() {
    delete_option('wpspro_use_styles');
    exit;
}

?>
