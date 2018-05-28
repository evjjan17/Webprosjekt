<?php

function WPCHT_avatar_field( $user ) { ?>
    <h3>Avatar</h3>
     
    <table class="form-table">
        <tr>
            <th><label for="WPCHT_user_avatar">Avatar URL</label></th>
            <td>
                <input class="regular-text code" type="text" name="WPCHT_user_avatar" id="WPCHT_user_avatar" value="<?php echo esc_attr( get_the_author_meta( 'WPCHT_user_avatar', $user->ID ) ); ?>" />
                <?php if(get_the_author_meta( 'WPCHT_user_avatar', $user->ID ) != ''){?>
                    <img src="<?php echo esc_attr( get_the_author_meta( 'WPCHT_user_avatar', $user->ID ) ); ?>" width="50px;" height="50px;" />
                <?php } else {?>
                    <img src="<?php echo plugin_dir_url(__FILE__) ;?>images/default.png" width="50px;" height="50px;" />
                <?php } ?>
                <P>Upload image(Recommended: 50x50) into Media, and copy the image url. Paste the url into the above box.</P>
            </td>
        </tr>
    </table>
    <?php 
}


function WPCHT_save_avatar_field( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
        update_usermeta( $user_id, 'WPCHT_user_avatar', $_POST['WPCHT_user_avatar'] );
}

function WPCHT_hide_avatars_username_column() {
        echo "<style>.users td.username img.avatar{display:none !important;}</style>";
    
}

function WPCHT_add_avatar_column( $column ) {
    $column['avatar'] = 'Avatar';
    return $column;
}


function WPCHT_show_avatar_column_data( $val, $column_name, $user_id ) {
    $user = get_userdata( $user_id );
    if(get_the_author_meta( 'WPCHT_user_avatar', $user_id ) != ''){
        $img_url = esc_attr( get_the_author_meta( 'WPCHT_user_avatar', $user_id ) );
    }
    else
    {
        $img_url =  plugin_dir_url(__FILE__).'images/default.png';

    }

    switch ($column_name) {
        case 'avatar' :
            return  "<img src=".$img_url." width='50px' height='50px' />";
            break;
        default:
    }
    return $return;
}
