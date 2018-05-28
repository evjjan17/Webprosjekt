<?php
function WPCHT_display_front()
{
	?>
	<div class='chat_list ' id="chat_user_list" onclick="WPCHT_hideChatList()">
		<div class="dashed">Chat Friend List</div>
		<div class="chat_user" id="chatUser"></div>
	</div>
	<?php if(is_user_logged_in()){?>
		<div id="chat_bar" onclick="WPCHT_openChatList()"></div>
	<?php } else{ ?>
		<div title="This is a tooltip" id="chat_bar" onclick="WPCHT_loginPlease()"></div>
	<?php
	}
}


function WPCHT_user_widget_display($args) {
?>
	<aside class="widget">
		<h2 class="widget-title">User Area</h2>
		<?php if(is_user_logged_in()){
				global $current_user;
      			get_currentuserinfo();
		?>
		<ul>
			<?php if(esc_attr( get_the_author_meta( 'WPCHT_user_avatar', $current_user->ID ) )){?>
				<li><img src="<?php echo esc_attr( get_the_author_meta( 'WPCHT_user_avatar', $current_user->ID ) ); ?>" width="50px;" height="50px;" /></li>
			<?php } ?>
			<li><?php echo 'Hi,'.$current_user->display_name;?></li>
			<li>
				Right Now, You Feels...
				<select class="search-field" name="WPCHT_user_mood" id="WPCHT_user_mood_<?php echo $current_user->ID;?>">
					<option value='Blank' <?php if(get_user_meta($current_user->ID,  'WPCHT_user_mood', true )=='Blank' || get_user_meta($current_user->ID,  'WPCHT_user_mood', true )==''){echo 'selected';} ?>>Blank</option>
					<option value='Happy' <?php if(get_user_meta($current_user->ID,  'WPCHT_user_mood', true )=='Happy'){echo 'selected';} ?>>Happy</option>
					<option value='Angry' <?php if(get_user_meta($current_user->ID,  'WPCHT_user_mood', true )=='Angry'){echo 'selected';} ?>>Angry</option>
					<option value='Blessed' <?php if(get_user_meta($current_user->ID,  'WPCHT_user_mood', true )=='Blessed'){echo 'selected';} ?>>Blessed</option>
					<option value='Confused' <?php if(get_user_meta($current_user->ID,  'WPCHT_user_mood', true )=='Confused'){echo 'selected';} ?>>Confused</option>
					<option value='Sad' <?php if(get_user_meta($current_user->ID,  'WPCHT_user_mood', true )=='Sad'){echo 'selected';} ?>>Sad</option>
				</select>
				<img onclick='WPCHT_setMood(<?php echo $current_user->ID;?>);' src='<?php echo plugin_dir_url(__FILE__);?>images/right.png' width="20px;" height="20px;" />
				<div id="mood_msg"></div>
			</li>

		</ul>
		<a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a>
		<?php } else {
		?>
		<a href="<?php echo wp_login_url( home_url() ); ?>" title="Login">Login</a>
		<?php } ?>
	</aside>
<?php
}

?>