<?php

// Add to Getting Started information
add_action('wps_admin_getting_started_hook', 'wps_admin_getting_started_friendships', 4);
function wps_admin_getting_started_friendships() {

    $css = isset($_POST['wps_expand']) && $_POST['wps_expand'] == 'wps_admin_getting_started_friendships' ? 'wps_admin_getting_started_menu_item_remove_icon ' : '';    
  	echo '<div class="'.$css.'wps_admin_getting_started_menu_item" rel="wps_admin_getting_started_friendships" id="wps_admin_getting_started_friendships_div">'.__('Friendships', WPS2_TEXT_DOMAIN).'</div>';

  	$display = isset($_POST['wps_expand']) && $_POST['wps_expand'] == 'wps_admin_getting_started_friendships' ? 'block' : 'none';
  	echo '<div class="wps_admin_getting_started_content" id="wps_admin_getting_started_friendships" style="display:'.$display.'">';

		?>
		<table class="form-table">
		<tr class="form-field">
			<td scope="row" valign="top">
				<label for="wps_friendships_all"><?php _e('Everybody friends', WPS2_TEXT_DOMAIN); ?></label>
			</td>
			<td>
				<input type="checkbox" style="width:10px" name="wps_friendships_all" <?php if (get_option('wps_friendships_all')) echo 'CHECKED'; ?> /> 
				<span class="description"><?php _e('Makes every user friends with everyone else, always. Good for private social networks.', WPS2_TEXT_DOMAIN); ?></span>
			</td>
		</tr> 
		</table>
        <?php
	echo '</div>';

}

/* AJAX */

add_action('wps_admin_setup_form_get_hook', 'wps_admin_friendships_save', 10, 2);
add_action('wps_admin_setup_form_save_hook', 'wps_admin_friendships_save', 10, 2);

function wps_admin_friendships_save($the_post) {

	if (isset($the_post['wps_friendships_all'])):
		update_option('wps_friendships_all', true);
	else:
		delete_option('wps_friendships_all');
	endif;

}

add_action( 'wp_ajax_wps_add_favourite', 'wps_add_favourite' ); 
add_action( 'wp_ajax_wps_remove_favourite', 'wps_remove_favourite' ); 

function wps_add_favourite() {

	global $current_user;
	$the_user = get_user_by('id', $_POST['user_id']);

    $post = array(
    	'post_title'     => $current_user->user_login.' - '.$the_user->user_login,
		'post_name'	=> sanitize_title_with_dashes($member1->user_login.' '.$member2->user_login),      
		'post_status'    => 'publish',
		'post_type'      => 'wps_favourite_friend',
		'post_author'    => $current_user->ID,
		'ping_status'    => 'closed',
		'comment_status' => 'closed',
    );  
    $new_id = wp_insert_post( $post );
    if ($new_id):
		update_post_meta( $new_id, 'wps_favourite_member1', $current_user->ID );
		update_post_meta( $new_id, 'wps_favourite_member2', $_POST['user_id'] );
		update_post_meta( $new_id, 'wps_favourite_friendship_since', date('Y-m-d H:i:s') );
	endif;

	exit;

}

function wps_remove_favourite() {

	global $current_user;

	$friendship = wps_is_a_favourite_friend($current_user->ID, $_POST['user_id']);
	if ($friendship):
		wp_delete_post($friendship['ID'], true);
	endif;

	exit;

}
?>