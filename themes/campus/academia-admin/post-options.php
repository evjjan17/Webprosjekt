<?php
 
/*----------------------------------*/
/* Custom Posts Options				*/
/*----------------------------------*/

add_action('admin_menu', 'campus_options_box');

function campus_options_box() {
	
	add_meta_box('campus_post_template', 'Post Options', 'campus_post_options', 'post', 'side', 'high');

}

add_action('save_post', 'custom_add_save');

function custom_add_save($postID){
	
	// called after a post or page is saved
	if($parent_id = wp_is_post_revision($postID))
	{
		$postID = $parent_id;
	}
	
	if (isset($_POST['save']) || isset($_POST['publish'])) {
		
		update_custom_meta($postID, esc_attr($_POST['academia_post_display_home']), 'academia_post_display_home');
		
	}
}

function update_custom_meta($postID, $newvalue, $field_name) {
	// To create new meta
	if(!get_post_meta($postID, $field_name)){
		add_post_meta($postID, $field_name, $newvalue);
	}else{
		// or to update existing meta
		update_post_meta($postID, $field_name, $newvalue);
	}
	
}

// Regular Posts Options
function campus_post_options() {
	global $post;
	?>
	<fieldset>
		<div>
			<p>
				<input class="checkbox" type="checkbox" id="academia_post_display_home" name="academia_post_display_home" value="on" <?php checked( get_post_meta($post->ID, 'academia_post_display_home', true), 'on' ); ?> />
 				<label for="academia_post_display_home"><?php _e('Feature this Post in the Homepage Slideshow','campus'); ?></label><br />
			</p>
  		</div>
	</fieldset>
	<?php
}