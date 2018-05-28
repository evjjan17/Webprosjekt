<?php
function WPCHT_genarate_user_list()
{
	$allUsers = get_users();

	$i=0; $j=0;
	?>
		<ul>
				<?php

					foreach($allUsers as $user)
					{

						$user_id 	= $user->ID;
						$user_name 	= $user->user_login;
						$allowed_users = array("$user_name");
						$is_active	= get_user_meta($user_id, 'is_active', true);
						
						if($is_active == 1)
						{
							if($user_id != get_current_user_id())
							{
								global $current_user;
      							get_currentuserinfo();
							?>
								<li class="line on" onclick="javascript:chatWith(<?php echo $user_id;?>,'<?php echo trim($current_user->display_name);?>','<?php echo trim($user->display_name);?>')">
									<?php
										if(get_the_author_meta( 'WPCHT_user_avatar', $user_id ) != ''){?>
										<img src="<?php echo esc_attr( get_the_author_meta( 'WPCHT_user_avatar', $user_id ) ); ?>" width="20px;" height="20px;" />
									<?php } else{?>
										<img src="<?php echo plugin_dir_url(__FILE__); ?>images/default.png" width="20px;" height="20px;" />
									<?php } ?>
									<?php echo $user->display_name;?><?php if(get_user_meta($user_id,  'WPCHT_user_mood', true )!=''){echo ", ".get_user_meta($user_id,  'WPCHT_user_mood', true );}?>
									<div class='showme'>
										<img class='callout' src='<?php echo plugin_dir_url(__FILE__);?>images/callout_black.gif' />
	        							<strong>Online</strong>
	        						</div>
									<div class='greenCircle'></div>
								</li>

							<?php
							}
							$i++;
						}
						else
						{
							?>
							<li class="line off">
								<?php
										if(get_the_author_meta( 'WPCHT_user_avatar', $user_id ) != ''){?>
										<img src="<?php echo esc_attr( get_the_author_meta( 'WPCHT_user_avatar', $user_id ) ); ?>" width="20px;" height="20px;" />
									<?php } else{?>
										<img src="<?php echo plugin_dir_url(__FILE__);?>images/default.png" width="20px;" height="20px;" />
									<?php } ?>
								<?php echo $user->display_name; ?>
								<div class='showme'>
									<img class='callout' src='<?php echo plugin_dir_url(__FILE__); ?>images/callout_black.gif' />
        							<strong>Offline</strong>
        						</div>
        						<div class='redCircle'></div>
        					</li>

							<?php
							echo '';
							$j++;
						}


					}
				?>
		</ul>
	<?php
	exit;
}

function WPCHT_check_user_status() 
{
	$allUsers = get_users();

	$i=0; $j=0;
	foreach($allUsers as $user)
	{
		$user_id 	= $user->ID;
		$is_active	= get_user_meta($user_id, 'is_active', true);
		if($is_active == 1)
		{
			$i++;
		}
		else
		{
			$j++;
		}
	}
	$i = $i-1;
	?>
		<div class="chat_bar dashed">Chat: Online(<?php echo $i;?>) Offline(<?php echo $j;?>)</div>
	<?php
	exit;				
}

function WPCHT_get_user_details() 
{
	$user_info = get_userdata($_POST['user_id']);
	echo "<pre>"; print_r($user_info);
	exit;				
}

function WPCHT_setMood()
{
	$userId = $_POST['userId'];
	$mood = $_POST['mood'];
	if(update_user_meta($userId, 'WPCHT_user_mood', $mood))
	{
		echo $mood;
		exit;
	}
	else
	{
		echo "No";
		exit;
	}
	
}

?>