<?php
function WPCHT_getChatData()
{
	
	global $wpdb;
	$userId = get_current_user_id();
	$results = $wpdb->get_results( 'SELECT * FROM wp_WPCHT_chat  WHERE receiver_id = '.$userId.' AND recd = 0 ORDER BY id ASC');
	global $current_user;
    get_currentuserinfo();

	$items = '';

	$chatBoxes = array();

	foreach($results as $res)
	{
		

		if (!isset($_SESSION['openChatBoxes'][$res->sender_id]) && isset($_SESSION['chatHistory'][$res->sender_id])) {
			$items = $_SESSION['chatHistory'][$res->sender_id];
		}

		$results['message'] = sanitize($results['message']);
		$receiver = $res->receiver_id;

		$sender_info = get_userdata($res->sender_id);

		$items .= <<<EOD
					   {
			"s": "{$current_user->display_name}",
			"f": "{$sender_info->display_name}",
			"f_id": "{$sender_info->ID}",
			"m": "{$res->message}"
	   },
EOD;

	if (!isset($_SESSION['chatHistory'][$res->sender_id])) {
		$_SESSION['chatHistory'][$res->sender_id] = '';
	}

	$_SESSION['chatHistory'][$results['sender_id']] .= <<<EOD
						   {
			"s": "{$current_user->display_name}",
			"f": "{$sender_info->display_name}",
			"f_id": "{$sender_info->ID}",
			"m": "{$res->message}"
	   },
EOD;
		
		unset($_SESSION['tsChatBoxes'][$res->sender_id]);
		$_SESSION['openChatBoxes'][$res->sender_id] = $results['sent'];
	}


	$wpdb->update( 'wp_WPCHT_chat', array( 'recd' => 1), array( 'receiver_id' => $userId ), array( '%d' ), array( '%d' ) );
	if ($items != '') {
		$items = substr($items, 0, -1);
	}
header('Content-type: application/json');
?>
{
		"items": [
			<?php echo $items;?>
        ]
}

<?php
			exit(0);
}

function chatBoxSession($chatbox) {
	
	$items = '';
	
	if (isset($_SESSION['chatHistory'][$chatbox])) {
		$items = $_SESSION['chatHistory'][$chatbox];
	}

	return $items;
}


function WPCHT_startChatSession()
{
	$items = '';
	if (!empty($_SESSION['openChatBoxes'])) {
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
			$items .= chatBoxSession($chatbox);
		}
	}


	if ($items != '') {
		$items = substr($items, 0, -1);
	}

header('Content-type: application/json');
?>
{

		"userId": "<?php echo get_current_user_id();?>",
		"items": [
			<?php echo $items;?>
        ]
}

<?php


	exit(0);
}



function WPCHT_sendChat()
{
	
	global $wpdb;
	$from = get_current_user_id();
	$to = absint($_POST['to']);
	$messagesan = sanitize_text_field($_POST['message']);
	
	$_SESSION['openChatBoxes'][absint($_POST['to'])] = date('Y-m-d H:i:s', time());
	
	if (!isset($_SESSION['chatHistory'][absint($_POST['to'])])) {
		$_SESSION['chatHistory'][absint($_POST['to'])] = '';
	}

	$_SESSION['chatHistory'][absint($_POST['to'])] .= <<<EOD
					   {
			"s": "{$to}",
			"f": "{$from}",
			"m": "{$messagesan}"
	   },
EOD;


	unset($_SESSION['tsChatBoxes'][absint($_POST['to'])]);
	
	$wpdb->insert(
		    'wp_WPCHT_chat',
		    array(
		        'sender_id' => $from,
		        'receiver_id' => $to,
		        'message' => $messagesan,
		    ),
		    array(
		    	'%d',
		    	'%d',
		        '%s',
		    )
		);
	
	echo "1";
	exit;
}


function WPCHT_closeChat()
{

	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
	
	echo "1";
	exit(0);
}

function sanitize($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br>",$text);
	return $text;
}


function WPCHT_getReceiverId()
{
	$display_name = sanitize_text_field($_POST['name']);
	global $wpdb;

    if ( ! $user = $wpdb->get_row( $wpdb->prepare(
        "SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $display_name
    ) ) )
        return false;

    echo $user->ID;
    exit;
}