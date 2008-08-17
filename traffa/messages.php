<?php    
	
	require('../include/core/common.php');
	require_once($hp_includepath . 'message-functions.php');
	require($hp_includepath . 'traffa-functions.php');
	$ui_options['menu_path'] = array('traffa');
	$ui_options['dom_tt_lib'] = true;
	ui_top($ui_options);
	echo '<div style="overflow: hidden; width: 635px;">';

	$faq_category = 'messages';


if(!is_numeric($_GET['message_id']) && isset($_GET['message_id']))
{
	die('DATA');
}


if(!is_numeric($_GET['recipient_id']) && isset($_GET['recipient_id']))
{
	die('DATA');
}



if(!login_checklogin())
{
	header('location: /');
	die();
}
if(strlen($_GET['action']) < 1)
{
	$action = 'inbox';
}
else
{
	$action = $_GET['action'];
}

switch($action)
{
	case 'compose':
		if(!messages_compose($_GET['recipient_id'], $_GET['recipient_username'], $_GET['title'], $_GET['discussion']))
		{
			jscript_alert('Ett fel uppstod, det verkar som om mottagaren inte finns!');
			jscript_go_back();
			die();
		}
		if(isset($_GET['quote']))
		{
			if(!messages_view($_GET['message_id'], $_SESSION['login']['id'], 1))
			{
				jscript_alert('Ett problem uppstod när meddelandet skulle visas. Du kanske inte kan läsa detta meddelande.');
				jscript_location($_SERVER['PHP_SELF']);
			}
		}
		break;
	case 'send':
		$can_send = messages_can_send($_SESSION['login']['id'], $_POST['recipient'], $_POST['title'], $_POST['message'], $_POST['discussion']);
		if(strlen($can_send) < 2)
		{
			messages_send($_SESSION['login']['id'], $_POST['recipient'], $_POST['title'], $_POST['message'], $_POST['discussion']);
			jscript_location($_SERVER['PHP_SELF']);
		}
		else
		{
			jscript_alert(str_replace("\n", ' ', $can_send));
			jscript_go_back();
		}
		break;
	case 'delete':
		if(!messages_delete($_POST, $_SESSION['login']['id']))
		{
			jscript_alert('Ett fel uppstod när meddelandena skulle tas bort!');
		}
		jscript_go_back();
		break;
	case 'read':
		traffa_draw_user_div($_SESSION['login']['id'], $_SESSION);
		if(!messages_view($_GET['message_id'], $_SESSION['login']['id']) || !is_numeric($_GET['message_id']))
		{
			jscript_alert('Ett problem uppstod när meddelandet skulle visas. Du kanske inte kan läsa detta meddelande.');
			jscript_location($_SERVER['PHP_SELF']);
		}
		break;
	case 'conversation':
		$options = array('mode' => 'conversation', 'user' => $_GET['user'], 'order' => $_GET['order'], 'direction' => $_GET['direction'], 'offset' => $_GET['offset']);
		messages_list($_SESSION['login']['id'], $options);
		break;
	case 'list_sent':
		traffa_draw_user_div($_SESSION['login']['id'], $_SESSION);
		$options = array('order' => $_GET['order'], 'mode' => 'sent', 'direction' => $_GET['direction'], 'offset' => $_GET['offset']);
		messages_list($_SESSION['login']['id'], $options);
		break;
	case 'inbox':
		traffa_draw_user_div($_SESSION['login']['id'], $_SESSION);
		$options = array('order' => $_GET['order'], 'mode' => 'recieved', 'direction' => $_GET['direction'], 'offset' => $_GET['offset']);
		messages_list($_SESSION['login']['id'], $options);
		messages_pre_compose();
		break;
	default:
		jscript_alert('Ett okänt action-värde skickades! Dödar scriptet.');
		die();
	break;
}


	echo '</div>';
	ui_bottom();
?>
