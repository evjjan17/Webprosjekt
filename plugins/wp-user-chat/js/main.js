
window.onload = function(e) {
    jQuery("#chat_user_list").hide();
    genarateUser();
    getChatData();
};

function WPCHT_openChatList(){
	jQuery("#chat_user_list").show();
	jQuery("#chat_bar").hide();
}

function WPCHT_hideChatList(){
	jQuery("#chat_bar").show();
	jQuery("#chat_user_list").hide();
}

setInterval(genarateUser, (1 * 40000));
setInterval(getChatData,(1 * 40000));
function genarateUser()
{
	var getUrl = window.location;
	var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
	jQuery.ajax( {
	    url : baseUrl+"/wp-admin/admin-ajax.php",
	    type : 'GET',
	    cache : false,
	    data : 'action=WPCHT_check_user_status',
	    success : function( result ) {
	    	jQuery("#chat_bar").html(result);
	    }
	});

	jQuery.ajax( {
	    url : baseUrl+"/wp-admin/admin-ajax.php",
	    type : 'GET',
	    cache : false,
	    data : 'action=WPCHT_genarate_user_list',
	    success : function( result ) {
	    	jQuery("#chatUser").html(result);
	    }
	});
}


function WPCHT_loginPlease()
{
	alert('Please Login To Chat');
	return false;
}

function WPCHT_setMood(id)
{
	var mood = document.getElementById("WPCHT_user_mood_"+id).value;
	var getUrl = window.location;
	var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1]; 
	var ajaxurl = baseUrl+'/wp-admin/admin-ajax.php';
	jQuery.post(ajaxurl, {action: 'WPCHT_setMood',userId: id, mood: mood} , function(data){
		jQuery("#mood_msg").fadeIn(5000);
		jQuery("#mood_msg").html('<span style="color: red;"><br />'+data+' feelings activated.');
		jQuery("#mood_msg").fadeOut(5000);

	});
}

