<?php

function wps_alerts_per_user() {

    global $wpdb;

  	echo '<div class="wrap">';
        	
	  	echo '<div id="icon-themes" class="icon32"><br /></div>';

	  	echo '<h2>'.__('Alerts per user', WPS2_TEXT_DOMAIN).'</h2>';
    
        $recipient = isset($_GET['recipient']) ? $_GET['recipient'] : '';

		echo '<form action="/wp-admin/edit.php" method="GET">';
            echo '<input type="hidden" name="page" value="wps_alerts_per_user" />';
            echo '<input type="text" name="recipient" placeholder="Email address" value="' . $recipient . '" />';
            echo '<br /><input type="submit" class="button-primary" value="'.__('Search', WPS2_TEXT_DOMAIN).'" />';
        echo '</form>';
    
        $user = get_user_by('email', $recipient);

        if ($user) {

            // Get all alerts for this user
            $paged = (isset($_GET['paged'])) ? $_GET['paged'] : 1;

            $args = array(
                'posts_per_page'   => 10000,
                'paged'            => $paged,
                'orderby'          => 'post_date',
                'order'            => 'DESC',
                'post_type'        => 'wps_alerts',
                'post_status'      => array('publish', 'pending'),
                'meta_query' => array(
                    array(
                        'key' => 'wps_alert_recipient',
                        'value' => $user->user_login,
                        'compare' => '=='
                    )
                )
            );
            $alerts = new WP_Query($args);	

            if($alerts->have_posts()) : 
                while($alerts->have_posts()) :

                    $alerts->the_post();

                    $the_id = $alerts->post->ID;

                    echo '<div style="background-color:#fff; padding: 0 12px 12px 12px; border: 1px solid #000; border-radius: 5px; margin-top: 12px;">';

                        echo '<div style="float:right;padding:30px 12px;font-size:3em;">' . $the_id . '</div>';
                        $color = (get_post_status() == 'publish') ? '#000' : '#f00';
                        echo '<h2 style="color:' . $color . '">' . get_the_title() . '</h2>';
                        $from = get_user_by('ID', get_the_author_meta('ID'));
                        echo '<strong>Author: '.$from->display_name . ' (' . $from->user_login . ')</strong><br />';
                        $status = (get_post_status() == 'publish') ? 'will not send again' : 'pending';
                        if (get_post_meta( $the_id, 'wps_alert_failed_datetime', true )) {
                            echo '<div style="color:#f00">';
                                echo 'Failed to send: ' . get_post_meta( $the_id, 'wps_alert_failed_datetime', true ) . ' (' . $status . ')</br>';
                                echo get_post_meta( $the_id, 'wps_alert_note', true );
                            echo '</div>';
                        } else {
                            echo 'Sent: ' . get_post_meta( $the_id, 'wps_alert_sent_datetime', true ) . ' (' . $status . ')</br>';
                        }
                        echo 'Page slug: ' . get_post_meta($the_id, 'wps_alert_target', true) . '<br />';
                        echo 'Parameters: ' . get_post_meta($the_id, 'wps_alert_parameters', true) . '<br />';

                        echo '<p>' . get_the_content() . '</p>';

                    echo '</div>';  

                endwhile;
            endif;

        }

        wp_reset_postdata();
                    
	echo '</div>';	
    
}

																	/* ************* */
																	/* HOOKS/FILTERS */
																	/* ************* */

// Do action after every alert is added
add_action( 'wps_alert_add_hook', 'wps_alert_add_hook_action', 10, 4 );
function wps_alert_add_hook_action($recipient_id, $new_alert_id, $url, $msg) {
	update_post_meta($new_alert_id, 'wps_alert_url', $url);
	update_post_meta($new_alert_id, 'wps_alert_msg', $msg);
}

// Add unsubscribe to all in wps_usermeta_change
add_filter('wps_usermeta_change_filter', 'wps_activity_subs_usermeta_extend', 10, 3);
function wps_activity_subs_usermeta_extend($tabs, $atts, $user_id) {

	global $current_user;
    $tabs_array = get_option('wps_profile_tabs');

	if (!get_user_meta($user_id, 'wps_activity_subscribe', true))
		update_user_meta($user_id, 'wps_activity_subscribe', 'off');

	// Shortcode parameters
    $values = wps_get_shortcode_options('wps_usermeta_change');
	extract( shortcode_atts( array(
        'activity_subs_subscribe' => wps_get_shortcode_value($values, 'wps_usermeta_change-activity_subs_subscribe', __('Receive email notifications for activity', WPS2_TEXT_DOMAIN)),
        'meta_class' => 'wps_usermeta_change_label',
	), $atts, 'wps_usermeta_change' ) );
    
    $hide_email_notifications_for_activity = get_option('hide_email_notifications_for_activity');

    if (!$hide_email_notifications_for_activity):
        $form_html = '<div id="wps_activity_subs_subscribe" class="wps_usermeta_change_item">';
        $form_html .= '<div class="'.$meta_class.'"><input type="checkbox" name="wps_activity_subscribe" ';
        if (get_user_meta($user_id, 'wps_activity_subscribe', true) != 'off')
            $form_html .= ' CHECKED';
        $form_html .= '/> '.$activity_subs_subscribe.'</div>';
        $form_html .= '</div>';
        $tab_row['tab'] = isset($tabs_array['wps_profile_tab_activity_alerts']) ? $tabs_array['wps_profile_tab_activity_alerts'] : 1;
        $tab_row['html'] = $form_html;   
        $tab_row['mandatory'] = false;     
        array_push($tabs,$tab_row);  
    endif;

	return $tabs;

}

// Extend wps_usermeta_change save
add_action( 'wps_usermeta_change_hook', 'wps_activity_subs_usermeta_extend_save', 10, 4 );
function wps_activity_subs_usermeta_extend_save($user_id, $atts, $the_form, $the_files) {

	global $current_user;

	// Double check logged in
	if (is_user_logged_in()):

		if (isset($_POST['wps_activity_subscribe'])):

			update_user_meta($user_id, 'wps_activity_subscribe', 'on');

		else:

			update_user_meta($user_id, 'wps_activity_subscribe', 'off');

		endif;

	endif;

}

																	/* ********* */
																	/* FUNCTIONS */
																	/* ********* */


function wps_pro_insert_alert($type, $subject, $content, $author_id, $recipient_id, $parameters, $url, $msg, $status, $status_msg) {

	global $current_user;
    $new_alert_id = false;

	if ( is_user_logged_in() && $author_id) {

		if (!$content) $content = '(no content)';
        
        $post = array(
            'post_title'		=> $subject,
            'post_excerpt'		=> $msg,
            'post_content'		=> $content,
            'post_status'   	=> $status,
            'post_type'     	=> 'wps_alerts',
            'post_author'   	=> $author_id,
            'ping_status'   	=> 'closed',
            'comment_status'	=> 'closed',
        );  
        $new_alert_id = wp_insert_post( $post );

        $recipient_user = get_user_by ('id', $recipient_id); // Get user by ID of email recipient
        if ($recipient_user):
            update_post_meta( $new_alert_id, 'wps_alert_recipient', $recipient_user->user_login );	
            update_post_meta( $new_alert_id, 'wps_alert_target', $type );
            update_post_meta( $new_alert_id, 'wps_alert_parameters', $parameters );	

            if ($status == 'publish'):
                update_post_meta( $new_alert_id, 'wps_alert_failed_datetime', current_time('mysql', 1) );
                update_post_meta( $new_alert_id, 'wps_alert_note', $status_msg );
            endif;

            do_action( 'wps_alert_add_hook', $recipient_user->ID, $new_alert_id, $url, $msg );
        endif;

    }
    
	return $new_alert_id;
	
}


																	/* ***** */
																	/* ADMIN */
																	/* ***** */


add_action('wps_admin_getting_started_hook', 'wps_admin_getting_started_alerts', 2.0);
function wps_admin_getting_started_alerts() {

	// Show menu item	
    $css = isset($_POST['wps_expand']) && $_POST['wps_expand'] == 'wps_admin_getting_started_alerts' ? 'wps_admin_getting_started_menu_item_remove_icon ' : '';    
  	echo '<div class="'.$css.'wps_admin_getting_started_menu_item" rel="wps_admin_getting_started_alerts" id="wps_admin_getting_started_alerts_div">'.__('Alerts', WPS2_TEXT_DOMAIN).'</div>';

  	// Show setup/help content
  	$display = isset($_POST['wps_expand']) && $_POST['wps_expand'] == 'wps_admin_getting_started_alerts' ? 'block' : 'none';
  	echo '<div class="wps_admin_getting_started_content" id="wps_admin_getting_started_alerts" style="display:'.$display.';">';
	?>

	<?php echo __('WPS Pro alerts uses the internal WordPress wp_mail() function.', WPS2_TEXT_DOMAIN).' '; ?>
	<?php echo __('If you are experiencing high volumes, depending on your host, you may want to consider using an external mail server.', WPS2_TEXT_DOMAIN).' '; ?>
	<?php echo sprintf(__('There are several WordPress plugins available to support this, such as <a href="%s">Postman SMTP Mailer/Email Log</a>.', WPS2_TEXT_DOMAIN), "https://wordpress.org/plugins/postman-smtp/"); ?>

    <?php echo '<p>'.sprintf(__('To view your schedule in the WordPress cron schedule, we recommend the <a href="%s">WP Crontrol</a> plugin.', WPS2_TEXT_DOMAIN), 'https://wordpress.org/plugins/wp-crontrol/').'</p>'; ?>


		<table class="form-table">

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_disable_alerts"><?php echo __('Disable alerts', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="checkbox" name="wps_disable_alerts" <?php if (get_option('wps_disable_alerts')) echo 'CHECKED '; ?> />
			<span class="description">
				<?php echo __('Stops alerts being sent out via email.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_cron_schedule"><?php echo __('Frequency of Email alert notifications', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<div style="padding-left:55px">
			<input type="text" style="margin-left:-55px;width:50px" name="wps_alerts_cron_schedule" value="<?php echo get_option('wps_alerts_cron_schedule'); ?>" />
			<span class="description">
				<?php 
				echo __('Frequency in seconds, that alerts are sent out via email, defaults to 3600 (every 1 hour).', WPS2_TEXT_DOMAIN).'<br />';
				echo __('Remember, WordPress scheduled tasks are triggered by visits to your site.', WPS2_TEXT_DOMAIN).'<br />';
				echo '<strong>'.__('Do not make too frequent, or your server performance may significantly deteriorate.', WPS2_TEXT_DOMAIN).'</strong><br />';
				echo __('When you save this page, the next cycle will by triggered.', WPS2_TEXT_DOMAIN).'<br />';
				?>
			</span>
			</div>
			</td> 
		</tr> 

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_cron_max"><?php echo __('Emails to send', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<div style="padding-left:55px">
			<input type="text" style="margin-left:-55px;width:50px" name="wps_alerts_cron_max" value="<?php echo get_option('wps_alerts_cron_max'); ?>" />
			<span class="description">
				<?php echo __('Maximum number of emails to send per scheduled cycle (keep as low as possible).', WPS2_TEXT_DOMAIN).'<br />'; ?>
                <?php echo __('Ratio of this number to the above frequency, should be no less than 5:1. Eg: 125 seconds frequency, sending out 25 emails.', WPS2_TEXT_DOMAIN); ?>
			</span>
            </div>
            </td> 
		</tr> 

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_summary_email"><?php echo __('Summary email', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="text" name="wps_alerts_summary_email" value="<?php echo get_option('wps_alerts_summary_email'); ?>" />
			<span class="description">
				<?php echo __('Optional email address to receive summary of scheduled alerts sent.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_cron_email"><?php echo __('Cron report', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="text" name="wps_alerts_cron_email" value="<?php echo get_option('wps_alerts_cron_email'); ?>" />
			<span class="description">
				<?php echo __('Optional email to receive a detailed report from WPS cron activity.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_from_name"><?php echo __('From name', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="text" name="wps_alerts_from_name" value="<?php echo get_option('wps_alerts_from_name'); ?>" />
			<span class="description">
				<?php echo __('Name alert notifications are sent from.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_from_email"><?php echo __('From email', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="text" name="wps_alerts_from_email" value="<?php echo get_option('wps_alerts_from_email'); ?>" />
			<span class="description">
				<?php echo __('Email address alert notifications are sent from.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_check"><?php echo __('Test email', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="checkbox" name="wps_alerts_check" />
			<span class="description">
				<?php echo sprintf(__('Tick to directly send a test email to %s using WordPress.', WPS2_TEXT_DOMAIN), get_bloginfo('admin_email')); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alert_delete"><?php echo __('Alert retention', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="checkbox" name="wps_alert_delete" <?php if (get_option('wps_alert_delete')) echo 'CHECKED '; ?> />
			<span class="description">
				<?php echo sprintf(__('Delete successfully sent <a href="%s">Alerts</a> immediately (removes from entire site).', WPS2_TEXT_DOMAIN), admin_url( 'edit.php?post_type=wps_alerts' )); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alert_resend"><?php echo __('Failed Alerts', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="checkbox" name="wps_alert_resend" <?php if (get_option('wps_alert_resend')) echo 'CHECKED '; ?> />
			<span class="description">
				<?php echo sprintf(__('Re-send failed alerts (as failed alerts grow in number you should <a href="%s">consider removing them</a>). Not applicable to closed accounts.', WPS2_TEXT_DOMAIN), admin_url( 'edit.php?post_type=wps_alerts' )); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_add_alert_check"><?php echo __('Test alert', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="checkbox" name="wps_add_alert_check" />
			<span class="description">
				<?php echo sprintf(__('Tick to add a WP Symposium Pro <a href="%s">Alert</a> to yourself.', WPS2_TEXT_DOMAIN), admin_url( 'edit.php?post_type=wps_alerts' )); ?>
			</span></td> 
		</tr> 	

		<?php
		// Any more?
		do_action('wps_alerts_admin_setup_form_hook');
		?>


	</table>
	<?php

	echo '</div>';
}

add_action( 'wps_admin_setup_form_save_hook', 'wps_alerts_admin_options_save', 10, 1 );
function wps_alerts_admin_options_save ($the_post) {

	if (isset($the_post['wps_disable_alerts'])):
		update_option('wps_disable_alerts', true);
	else:
		delete_option('wps_disable_alerts');
	endif;

	if (isset($the_post['wps_alert_delete'])):
		update_option('wps_alert_delete', true);
	else:
		delete_option('wps_alert_delete');
	endif;    

	if (isset($the_post['wps_alert_resend'])):
		update_option('wps_alert_resend', true);
	else:
		delete_option('wps_alert_resend');
	endif;    

	if ($value = $the_post['wps_alerts_cron_schedule']):
		$value = ($value >= 10) ? $value : 10; // Never less than 10 seconds
		update_option('wps_alerts_cron_schedule', $value);
	else:
		update_option('wps_alerts_cron_schedule', 3600); // Default to once an hour
	endif;

	if ($value = $the_post['wps_alerts_cron_max']):
		$value = ($value > 0) ? $value : 1; // Never less than 1
		update_option('wps_alerts_cron_max', $value);
	else:
		update_option('wps_alerts_cron_max', 25); // Default to 25
	endif;

	if ($value = $the_post['wps_alerts_summary_email']):
		update_option('wps_alerts_summary_email', $value);
	else:
		delete_option('wps_alerts_summary_email');
	endif;

	if ($value = $the_post['wps_alerts_cron_email']):
		update_option('wps_alerts_cron_email', $value);
	else:
		delete_option('wps_alerts_cron_email');
	endif;

	if ($value = $the_post['wps_alerts_from_name']):
		update_option('wps_alerts_from_name', $value);
	else:
		update_option('wps_alerts_from_name', get_bloginfo('name'));
	endif;

	if ($value = $the_post['wps_alerts_from_email']):
		update_option('wps_alerts_from_email', $value);
	else:
		update_option('wps_alerts_from_email', get_bloginfo('admin_email'));
	endif;

	if (isset($the_post['wps_alerts_check'])):
		$name = ($value = get_option('wps_alerts_from_name')) ? $value : get_bloginfo('name');
		$email = ($value = get_option('wps_alerts_from_email')) ? $value : get_bloginfo('admin_email');
		$headers = 'From: '.$name.' <'.$email.'>' . "\r\n";
		$content = __('Wahoo! It worked!', WPS2_TEXT_DOMAIN);
		$filtered_content = apply_filters('wps_alerts_scheduled_job_content_filter', $content, 0);		
		add_filter( 'wp_mail_content_type', 'wps_set_html_content_type' );
		if (wp_mail (get_bloginfo('admin_email'), __('Test email', WPS2_TEXT_DOMAIN), $filtered_content)):
			echo '<div class="updated"><p>'.sprintf(__('Test email sent to %s.', WPS2_TEXT_DOMAIN), get_bloginfo('admin_email')).'</p></div>';
		else:
			echo '<div class="error"><p>'.sprintf(__('Test email failed to send to %s.', WPS2_TEXT_DOMAIN), get_bloginfo('admin_email')).'</p></div>';
		endif;
	endif;

	if (isset($the_post['wps_add_alert_check'])):
        global $current_user;
		$name = ($value = get_option('wps_alerts_from_name')) ? $value : get_bloginfo('name');
		$email = ($value = get_option('wps_alerts_from_email')) ? $value : get_bloginfo('admin_email');
		$headers = 'From: '.$name.' <'.$email.'>' . "\r\n";
		$content = __('Test Alert', WPS2_TEXT_DOMAIN);
        $subject = __('Test Alert', WPS2_TEXT_DOMAIN);
        wps_pro_insert_alert('test_alert', $subject, $content, $current_user->ID, $current_user->ID, '', '', $content, 'pending', 'Test alert.');
		echo '<div class="updated"><p>'.__('Test alert added.', WPS2_TEXT_DOMAIN).'</p></div>';
	endif;

	// Clear existing schedule
	wp_clear_scheduled_hook( 'wps_symposium_pro_alerts_hook' );
	// Re-add as new schedule
	// Schedule the event for right now, then to repeat using the hook 'wps_symposium_pro_alerts_hook'
	wp_schedule_event( time(), 'wps_symposium_pro_alerts_schedule', 'wps_symposium_pro_alerts_hook' );

	// Any more to save?
	do_action( 'wps_alerts_admin_setup_form_save_hook', $the_post );

}



																	/* ******** */
																	/* CRON JOB */
																	/* ******** */

// Hook our function, wps_alerts_scheduled_job(), into the action wps_symposium_pro_alerts_hook
add_action( 'wps_symposium_pro_alerts_hook', 'wps_alerts_scheduled_job' );
function wps_alerts_scheduled_job() {

    /* check if already running */
    $wps_cron_flag = get_option('wps_cron_flag');
    if (!$wps_cron_flag) {
        
        /* Set flag to show as running */
        update_option('wps_cron_flag', time());
        /* set for now, to show as last time ran */
        update_option('wps_cron_flag_last_sent', time());
            
        $already_running = false;
        
    } else {
        
        /* already running, so skip this time */
        $already_running = true;
        
    }

    $name = ($value = get_option('wps_alerts_from_name')) ? $value : get_bloginfo('name');
    $email = ($value = get_option('wps_alerts_from_email')) ? $value : get_bloginfo('admin_email');
    $headers = 'From: '.$name.' <'.$email.'>' . "\r\n";
    $send_report = ($value = get_option('wps_alerts_cron_email')) ? $value : false;
    $time = time();
    
    
    if (!$already_running) {
        
        /* Send email saying started, if enabled */
        if ($value = get_option('wps_alerts_summary_email')) {
            //wp_mail($value, __('Running Cron Job ref:'.$time, WPS2_TEXT_DOMAIN), date('l jS \of F Y h:i:s A'), $headers);
        }
        if ($send_report) {
            //wp_mail($send_report, __('Running Cron Job ref:'.$time, WPS2_TEXT_DOMAIN), date('l jS \of F Y h:i:s A'), $headers);
        }


        // Start detailed cron report (if set with an email address)
        if ($send_report) $report = '<h1>Cron report</h1>';
        if ($send_report) $report .= 'Starting cron at '.date('l jS \of F Y h:i:s A').'<br /><br />';

        $max = ($value = get_option('wps_alerts_cron_max')) ? $value : 25; // Defaults to 25 in one go
        if ($send_report) $report .= sprintf('Sending maxium of %d alerts<br />', $max);

        $args = array (
            'post_type'              => 'wps_alerts',
            'posts_per_page'         => $max,
            'post_status'			 => 'pending',
            'order'                  => 'ASC',
            'orderby'                => 'ID',
        );

        // Inform admin
        $admin_content = sprintf(__('Started scheduled alerts at %s.', WPS2_TEXT_DOMAIN), current_time('mysql', 1)).'<br /><br />';
        if ($send_report):
            $admin_content .= sprintf(__('Sending separate detailed cron report to %s.', WPS2_TEXT_DOMAIN), $send_report).'<br /><br />';
        else:
            $admin_content .= __('NOT sending separate detailed cron report.', WPS2_TEXT_DOMAIN).'<br /><br />';
        endif;

        // Force HTML
        add_filter( 'wp_mail_content_type', 'wps_set_html_content_type' );

        $pending_posts = get_posts( $args );

        $admin_content .= sprintf(__('Pending alerts returned: %d.', WPS2_TEXT_DOMAIN), count($pending_posts)).'<br /><br />';

        $c = 0;

        if ($send_report) $report .= __('Starting to loop through them.', WPS2_TEXT_DOMAIN).'<br />';

        foreach ( $pending_posts as $pending ): 
            if ($c < $max) {

                if (!get_option('wps_disable_alerts')):

                    if ($send_report):
                        $report .= '<br />'.sprintf(__('Processing alert %d, ref:'.$time, WPS2_TEXT_DOMAIN), $pending->ID).'<br />';
                        $report .= '<h2>'.$pending->post_title.'</h2>';
                    endif;

                    /* Alert being processed, if enabled */
                    if ($value = get_option('wps_alerts_summary_email')) {
                        //wp_mail($value, sprintf(__('Processing alert %d, ref:'.$time, WPS2_TEXT_DOMAIN), $pending->ID), sprintf(__('Processing alert %d.', WPS2_TEXT_DOMAIN), $pending->ID), $headers);
                    }
                    if ($send_report) {
                        //wp_mail($send_report, sprintf(__('Processing alert %d.', WPS2_TEXT_DOMAIN), $pending->ID), sprintf(__('Processing alert %d.', WPS2_TEXT_DOMAIN), $pending->ID), $headers);
                    }

                    $try_again = get_option('wps_alert_resend');

                    $user_login = get_post_meta( $pending->ID, 'wps_alert_recipient', true );
                    $user = get_user_by('login', $user_login);

                    if ($user):
                        if (!wps_is_account_closed($user->ID)):

                            // not used! delete? $recipient = get_post_meta( $pending->ID, 'wps_alert_recipient', true );

                            if ($send_report) $report .= sprintf(__('Sending to %s, %s.', WPS2_TEXT_DOMAIN), $user->display_name, $user->user_email).'<br />';

                            $content = wps_formatted_content($pending->post_content);
                            $subject = htmlspecialchars_decode($pending->post_title);
                            $filtered_content = apply_filters('wps_alerts_scheduled_job_content_filter', $content, $pending->ID);
                            if (wp_mail($user->user_email, $subject, $filtered_content, $headers)) {

                                if ($send_report) $report .= __('WordPress reports as sent successfully.', WPS2_TEXT_DOMAIN).'<br />';

                                $admin_content .= '<strong>'.$pending->post_title.'</strong><br />';
                                $admin_content .= sprintf(__('Sent to: %s', WPS2_TEXT_DOMAIN), $user->user_email).'<br /><br />';
                                // Increase sent count
                                $c++;
                                // Update post
                                update_post_meta( $pending->ID, 'wps_alert_sent_datetime', current_time('mysql', 1) );
                                if ($send_report) $report .= __('Alert marked as sent okay.', WPS2_TEXT_DOMAIN).'<br />';
                                // If set, delete alert sent immediately
                                if (get_option('wps_alert_delete')) {
                                    if (wp_delete_post( $pending->ID, true)) {
                                        if ($send_report) $report .= __('As checked in setup, alert deleted automatically.', WPS2_TEXT_DOMAIN).'<br />';
                                    } else {
                                        if ($send_report) $report .= __('As checked in setup, alert deleted automatically - but delete failed.', WPS2_TEXT_DOMAIN).'<br />';                                    
                                    }
                                }
                                $try_again = false;

                            } else {

                                if ($send_report) $report .= __('WordPress reports as FAILED to send.', WPS2_TEXT_DOMAIN).'<br />';

                                update_post_meta( $pending->ID, 'wps_alert_failed_datetime', current_time('mysql', 1) );

                                $admin_content .= '<p style="color:red;font-weight:bold;">'.$pending->post_title.'</p>';
                                if ($user->user_email):
                                    $admin_content .= sprintf(__('Failed to send to: %s', WPS2_TEXT_DOMAIN), $user->user_email).'<br />';
                                else:
                                    $admin_content .= __('Failed to send, name email.', WPS2_TEXT_DOMAIN).'<br />';
                                endif;

                                // Get reason why
                                $msg = __('Mail function failed.', WPS2_TEXT_DOMAIN);
                                global $ts_mail_errors;
                                global $phpmailer;
                                $wps_mail_errors = array();
                                if (isset($phpmailer)) {
                                    $msg .= '<br /><em>'.$phpmailer->ErrorInfo.' ('.$user->ID.'/'.$user_login.')</em>';
                                    $admin_content .= '<em>'.$phpmailer->ErrorInfo.'</em><br />';
                                }
                                if ($try_again) $msg .= ' <strong>'.__('Re-trying...', WPS2_TEXT_DOMAIN).'</strong>';
                                $admin_content .= '<br />';
                                update_post_meta( $pending->ID, 'wps_alert_note', $msg );

                                if ($send_report) $report .= __('Reason why:', WPS2_TEXT_DOMAIN).'<br />';                            
                                if ($send_report) $report .= '<em>'.$phpmailer->ErrorInfo.' ('.$user->ID.'/'.$user_login.')</em><br />';

                            }

                        else:

                            update_post_meta( $pending->ID, 'wps_alert_failed_datetime', current_time('mysql', 1) );
                            update_post_meta( $pending->ID, 'wps_alert_note', __('Closed account', WPS2_TEXT_DOMAIN) );
                            $try_again = false; // Don't try again, as closed account

                            $admin_content .= sprintf(__('Account %d closed.', WPS2_TEXT_DOMAIN), $user->ID).'<br />';

                        endif;

                    else:

                        $admin_content .= sprintf(__('Recipient for post %d: %s not found.', WPS2_TEXT_DOMAIN), $pending->ID, $user_login).'<br />';

                        update_post_meta( $pending->ID, 'wps_alert_failed_datetime', current_time('mysql', 1) );
                        $msg = sprintf(__('Recipient not found, with login of "%s" for alert %d.', WPS2_TEXT_DOMAIN), $user_login, $pending->ID);
                        global $wpdb;
                        $sql = "select meta_value from ".$wpdb->prefix."postmeta where post_ID = %d and meta_key = 'wps_alert_recipient'";
                        $the_user = $wpdb->get_results($wpdb->prepare($sql, $pending->ID));
                        if ($the_user):
                            $msg .= ' ['.$the_user->meta_value.']';
                        else:
                            $msg .= ' ['.__('no meta value', WPS2_TEXT_DOMAIN).']';
                        endif;
                        if ($try_again) $msg .= ' <strong>'.__('Re-trying...', WPS2_TEXT_DOMAIN).'</strong>';
                        update_post_meta( $pending->ID, 'wps_alert_note', $msg );

                    endif;

                    // Set post to published, won't try and be sent again
                    if (!$try_again):
                        $this_post = array(
                            'ID'           	=> $pending->ID,
                            'post_status' 	=> 'publish'
                        );
                        wp_update_post( $this_post );
                    endif;

                else:

                    // Alerts disabled, say so
                    update_post_meta( $pending->ID, 'wps_alert_failed_datetime', current_time('mysql', 1) );
                    update_post_meta( $pending->ID, 'wps_alert_note', __('Alerts disabled via setup', WPS2_TEXT_DOMAIN) );

                    // Set post to published, won't try and be sent again
                    $this_post = array(
                        'ID'           	=> $pending->ID,
                        'post_status' 	=> 'publish'
                    );
                    wp_update_post( $this_post );    

                    if ($send_report) $report .= __('Alerts disabled.', WPS2_TEXT_DOMAIN).'<br /><br />';


                endif;

            } else {

                if ($send_report) $report .= __('Hit maximum number of alerts to send.', WPS2_TEXT_DOMAIN).'<br /><br />';

            }

        endforeach;

        /* Send email saying started, if enabled */
        if ($value = get_option('wps_alerts_summary_email')) {
            //wp_mail($value, __('Ended Cron Job ref:'.$time, WPS2_TEXT_DOMAIN), date('l jS \of F Y h:i:s A'), $headers);
        }
        if ($send_report) {
            //wp_mail($send_report, __('Ended Cron Job ref:'.$time, WPS2_TEXT_DOMAIN), date('l jS \of F Y h:i:s A'), $headers);
        }    

        // Inform admin
        $admin_content .= sprintf(__('Maximum alerts to send: %d.', WPS2_TEXT_DOMAIN), $max).'<br />';
        $admin_content .= sprintf(__('Alerts sent: %d.', WPS2_TEXT_DOMAIN), $c).'<br />';
        if ($value = get_option('wps_alerts_summary_email'))
            wp_mail($value, __('Scheduled Alerts (ref: '.$time.')', WPS2_TEXT_DOMAIN), $admin_content, $headers);

        // Cron report
        if ($send_report) $report .= '<br />'.sprintf(__('Alerts sent: %d.', WPS2_TEXT_DOMAIN), $c).'<br />';
        if ($send_report) $report .= '<br />'.'Ending cron at '.date('l jS \of F Y h:i:s A').'<br /><br />';
        if ($send_report) wp_mail($send_report, __('Cron report (ref: '.$time.')', WPS2_TEXT_DOMAIN), $report, $headers);
        
        /* clear the already running flag so starts again next time */
        delete_option('wps_cron_flag');
        
    } else {
        
        /* already running flag is set */
        
        $admin_content .= sprintf(__('Cron already running from: %s.', WPS2_TEXT_DOMAIN), date("Y-m-d H:i:s",$wps_cron_flag)).'<br />';
        if ($value = get_option('wps_alerts_summary_email')) {
            wp_mail($value, __('Scheduled Alerts (ref: '.$time.')', WPS2_TEXT_DOMAIN), $admin_content, $headers);
        }
        if ($send_report) $report .= '<br />'.sprintf(__('Cron already running from: %s.', WPS2_TEXT_DOMAIN), date("Y-m-d H:i:s",$wps_cron_flag)).'<br />';
        if ($send_report) wp_mail($send_report, __('Cron report (ref: '.$time.')', WPS2_TEXT_DOMAIN), $report, $headers);
        
    }

    remove_filter( 'wp_mail_content_type', 'wps_set_html_content_type' );

    wp_reset_query();


}

/* show status of cron job */
function wps_show_cron_flag_status() {

    $current_screen = get_current_screen();
    if ($current_screen->id == 'edit-wps_alerts') {
    
        if ( (isset($_GET['wps_cron_flag_reset']) ) || (isset($_POST['wps_cron_flag_reset']) && $_POST['wps_cron_flag_reset'] == "1") ) {
            echo '<div class="notice notice-warning">';
                echo '<p>'.__('Cron process reset.', WPS2_TEXT_DOMAIN).'</p>';
            echo '</div>';
            delete_option('wps_cron_flag');
        }
        $wps_cron_flag = get_option('wps_cron_flag');
        if ($wps_cron_flag) {
            echo '<div class="notice notice-warning">';
                echo '<p>'.sprintf(__('WP Symposium Cron current processing. Started at %s.', WPS2_TEXT_DOMAIN), date("Y-m-d H:i:s",$wps_cron_flag)).'</p>';
                $interval = time() - $wps_cron_flag;
                // only show reset button if stuck for over an hour
                if ($interval > 3600) {
                    echo '<form action="#" method="POST"><input type="submit" class="button-primary" value="'.__('Reset', WPS2_TEXT_DOMAIN).'" /><input type="hidden" name="wps_cron_flag_reset" value="1" /></form><br />';
                }
            echo '</div>';
        } else {
            echo '<div class="notice notice-success">';
                echo '<p>'.__('WP Symposium Cron ready to process.', WPS2_TEXT_DOMAIN);
                $last_sent = get_option('wps_cron_flag_last_sent');
                if (true || $last_sent) {
                    echo ' '.sprintf(__('Last processed: %s.', WPS2_TEXT_DOMAIN), date("Y-m-d H:i:s",$last_sent));
                }
                echo '</p>';
            echo '</div>';
        }
        
    }
}
add_action( 'admin_head', 'wps_show_cron_flag_status' );

function wps_set_html_content_type() {
	return 'text/html';
}


// Add to Core options
add_action('wps_admin_getting_started_core_hook', 'wps_admin_getting_started_core_hook_alerts');
function wps_admin_getting_started_core_hook_alerts($the_post) {
	?>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="wps_core_options"><?php _e('Content cleanup', WPS2_TEXT_DOMAIN); ?></label>
		</th>
		<td>
			<input type="checkbox" style="width:10px" name="wps_cleanup" /><span class="description"><?php _e('Run this if you delete users (one off operation, checkbox does not stay checked).', WPS2_TEXT_DOMAIN); ?></span>
		</td>
	</tr> 

	<tr class="form-field">
        <th scope="row" valign="top">
            <label for="activity_sync_avatars"><?php _e('Re-sync avatar meta', WPS2_TEXT_DOMAIN); ?></label>
        </th>
        <td>
            <input name="activity_sync_avatars" id="activity_sync_avatars" type="checkbox" style="width:10px" />
            <span class="description"><?php _e('Initiates re-sync, one-off operation, can take a while... (one off operation, checkbox does not stay checked).', WPS2_TEXT_DOMAIN); ?></span>

            <?php
            if (isset($_POST['activity_sync_avatars'])):
                // re-sync wps_pro_avatar in user meta where avatar uploaded
                set_time_limit ( 3600 ); // 1 hour
                global $wpdb;
                $sql = "SELECT ID FROM ".$wpdb->prefix."users";
                $users = $wpdb->get_col($sql);
                $count=0;
                $avatars_found=0;
                $found = array();
                foreach ($users as $id):
                    $count++;
                    if (file_exists(WP_CONTENT_DIR."/wps-pro-content/members/".$id."/avatar/")):
                        $dir = WP_CONTENT_DIR."/wps-pro-content/members/".$id."/avatar/";
                        $dh  = opendir($dir);
                        while (false !== ($filename = readdir($dh))) {
                            if (strpos($filename, 'wpsfull.jpg') !== false) {
                                update_user_meta( $id, 'wps_pro_avatar', "/wps-pro-content/members/".$id."/avatar/".$filename );
                                $avatars_found++;
                                $found[] = $id;
                            }
                        }    
                    else:
                        delete_user_meta( $id, 'wps_pro_avatar' );
                    endif;
                endforeach;
                echo '<div class="wps_success" style="margin-top:20px">'.sprintf(__('%d users found.', WPS2_TEXT_DOMAIN), $count).'<br />';
                echo sprintf(__('%d users with avatars found, all other users have no avatar uploaded.', WPS2_TEXT_DOMAIN), $avatars_found).'</div>';
            endif;
            ?>

        </td> 
    </tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="wps_filter_recent_comments"><?php _e('Recent Comments Widget', WPS2_TEXT_DOMAIN); ?></label>
		</th>
		<td>
			<input type="checkbox" style="width:10px" name="wps_filter_recent_comments" <?php if (get_option('wps_filter_recent_comments')) echo 'CHECKED '; ?>/><span class="description"><?php _e('Include forum and activity replies in Recent Comments widget (forum security not observed).', WPS2_TEXT_DOMAIN); ?></span>
		</td>
	</tr> 
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="wps_force_https"><?php _e('HTTPS detection', WPS2_TEXT_DOMAIN); ?></label>
		</th>
		<td>
	        <select name="wps_force_https">
             <?php 
                $force_https = get_option('wps_force_https');
                echo '<option value="0"';
                    if ($force_https != "http" && $force_https != "https") echo ' SELECTED';
                    echo'>'.__('Default', WPS2_TEXT_DOMAIN).'</option>';
                echo '<option value="https"';
                    if ($force_https == "https") echo ' SELECTED';
                    echo'>'.__('Force on (https:)', WPS2_TEXT_DOMAIN).'</option>';
                echo '<option value="http"';
                    if ($force_https == "http") echo ' SELECTED';
                    echo '>'.__('Force off (http:)', WPS2_TEXT_DOMAIN).'</option>';
             ?>						
            </select>
		</td>
	</tr> 
	<?php
}

add_action('admin_head', 'wps_admin_getting_started_core_save_hook_alerts', 10, 1);
add_action('wps_admin_getting_started_core_save_hook', 'wps_admin_getting_started_core_save_hook_alerts', 10, 1);
function wps_admin_getting_started_core_save_hook_alerts($the_post) {

    if (isset($the_post['wps_cleanup']) || isset($_GET['wps_cleanup'])):

	    if (is_admin() && current_user_can('manage_options')):

	    	global $wpdb;

	    	$class = isset($_GET['wps_cleanup']) ? 'notice notice-success' : 'wps_success';
	    	$style = isset($_GET['wps_cleanup']) ? 'border: 40px solid #0a0; z-index: 9999; position:absolute !important; width:400px; top:60px; left:60px;' : '';
			echo '<div class="'.$class.'" style="'.$style.'"><p>';

				// Alerts with no recipient
				echo '<strong>'.__('Checking alerts...', WPS2_TEXT_DOMAIN).'</strong><br />';
				$alerts = get_posts( array(
					'post_type' => 'wps_alerts',
					'posts_per_page' => -1,
					'post_status' => 'any'

				) );
				if ( ! empty( $alerts ) ) {
					$alerts_c=0;
					$alerts_n=0;
					foreach ( $alerts as $alert ) {
						$target = get_post_meta($alert->ID, 'wps_alert_recipient', true);
						$alerts_n++;
						if ($target):
							$u = get_user_by('login', $target);
							if (!$u):
								wp_delete_post($alert->ID, true);
								echo sprintf(__('Alert deleted (user ID %d does not exist).', WPS2_TEXT_DOMAIN), $target).'<br />';
								$sql = "DELETE FROM ".$wpdb->prefix."posts WHERE post_type='wps_forum_subs' AND post_title = %s";
								$wpdb->query($wpdb->prepare($sql, $target));
								echo sprintf(__('Forum subscriptions for %s deleted.', WPS2_TEXT_DOMAIN), $target).'<br />';
								$sql = "DELETE FROM ".$wpdb->prefix."posts WHERE post_type='wps_subs' AND post_title = %s";
								$wpdb->query($wpdb->prepare($sql, $target));
								echo sprintf(__('Forum topic subscriptions for %s deleted.', WPS2_TEXT_DOMAIN), $target).'<br />';
								$alerts_c++;
							endif;
						else:
							wp_delete_post($alert->ID, true);
	                        $alerts_c++;
						endif;
					}
					echo __('Alerts checked:', WPS2_TEXT_DOMAIN).' '.$alerts_n.', ';
					echo __('deleted:', WPS2_TEXT_DOMAIN).' '.$alerts_c.'<br />';

				} else {
					echo __('Alerts checked:', WPS2_TEXT_DOMAIN).' 0<br />';
	            }

	            // Subscriptions where user no longer exists
				echo '<br /><strong>'.__('Checking subscriptions...', WPS2_TEXT_DOMAIN).'</strong><br />';	            
	            $sql = "SELECT DISTINCT post_title FROM wp_posts 
	            		WHERE (post_type='wps_subs' OR post_type = 'wps_forum_subs')
	            		AND post_title NOT IN (SELECT user_login FROM wp_users)";
	            $missing = $wpdb->get_col($sql);
	            if ($missing):
	            	$m = 0;
	            	foreach ($missing as $user_login):
	            		$sql = "DELETE FROM ".$wpdb->prefix."posts WHERE (post_type='wps_subs' OR post_type='wps_forum_subs') AND post_title = %s";
	            		$wpdb->query($wpdb->prepare($sql, $user_login));
	            		$m++;
	            	endforeach;
	            	echo __('Subscriptions deleted:', WPS2_TEXT_DOMAIN).' '.$m.'<br />';
	            else:
					echo __('No invalid subscriptions.', WPS2_TEXT_DOMAIN);
	            endif;

	        echo '</p>';

	        if (isset($_GET['wps_cleanup'])):
	        	$url = admin_url( 'edit.php?post_type=wps_alerts' );
	    		echo '<button style="margin:10px 0 10px 0; width:75px;" onclick="javascript:window.location.assign(\''.$url.'\');return false;">'.__('OK', WPS2_TEXT_DOMAIN).'</button>';
	        endif;

	        echo '</div>';

		endif;

	endif;

	if (isset($the_post['wps_filter_recent_comments'])):
		update_option('wps_filter_recent_comments', true);
	else:
		delete_option('wps_filter_recent_comments');
	endif;
    
	if (isset($the_post['wps_force_https'])):
		if ($the_post['wps_force_https'] != '0'):
			update_option('wps_force_https', $the_post['wps_force_https']);
		else:
			delete_option('wps_force_https');
		endif;
	else:
		delete_option('wps_force_https');
	endif;
    
}
?>