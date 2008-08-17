<?php
require('../include/core/common.php');
include($hp_includepath . 'guestbook-functions.php');
include($hp_includepath . 'traffa-functions.php');
require_once(PATHS_INCLUDE . 'libraries/profile.lib.php');

$ui_options['admtoma_category'] = 'guestbooks';
$ui_options['menu_path'] = array('traffa');
$ui_options['stylesheets'][] = 'guestbook.css';
$ui_options['stylesheets'][] = 'user_profile.css';
$ui_options['javascripts'][] = 'guestbook.js';


if (!is_numeric($_GET['view']) && isset($_GET['view']))
{
	die('FISKRENS');
}

if (!is_numeric($_GET['userid']) && isset($_GET['userid']))
{
	die('FISKRENS IGEN');
}

if (!is_numeric($_GET['entry_id']) && isset($_GET['entry_id']))
{
	die('FISKRENS I ÖRAT');
}

if(is_numeric($_GET['view']))
{
	$userid = $_GET['view'];
}
elseif(is_numeric($_GET['userid']))
{
	$userid = $_GET['userid'];
}
else if($_SESSION['login']['id'] > 0 && !isset($userid))
{
	$userid = $_SESSION['login']['id'];
}



	$fetch['login'] = array('id', 'username', 'userlevel', 'regtimestamp', 'regip', 'lastlogon', 'lastip', 'lastaction', 'lastusernamechange', 'lastusername');
	$fetch['userinfo'] = array('contact1', 'contact2', 'gender', 'birthday', 'image', 'forum_signature', 'forum_posts');
	$fetch['traffa'] = array('firstname', 'profile_modules', 'guestbook_entries');
	if (!isset($userid))
	{
		jscript_alert("Du har loggats ut. Logga in och försök igen.");
		//jscript_location($_SERVER['PHP_SELF']);
		die();
	}
	$userinfo = login_load_user_data($userid, $fetch);


	
$profile = profile_fetch(array('user_id' => $userid));
$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';

ui_top($ui_options);	

echo '<div class="profile_' . $profile['profile_theme'] . '">' . "\n";
echo profile_head($profile);
echo '</div>' . "\n";



if(login_checklogin() == 1)
{
	if($_GET['action'] == 'delete')
	{
		if($_POST['all'] == 'true')
		{
			$query = 'UPDATE traffa_guestbooks SET deleted = 1 WHERE recipient = "' . $_SESSION['login']['id'] . '" AND `read` = 1';
			mysql_query($query) or die(report_sql_error($query));
			$query = 'UPDATE traffa SET guestbook_entries = ' . count_unread_gb_entries($userid) . ' WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
			mysql_query($query) or die(report_sql_error($query));
		}
		else
		{
			delete_entry($_GET['entry_id'], $_SESSION['login']['id']);
		}
		jscript_location($_SERVER['PHP_SELF'] . '?offset=' . $_GET['return_offset']);
	}
	elseif($_GET['action'] == 'send_new_message')
	{
		if(userblock_check($_GET['userid'], $_SESSION['login']['id']) == 1)
		{
			jscript_alert('Den användare som du har angivit som mottagare har blockerat dig, och ditt meddelande kan därför inte skickas!');
			echo '<script language="javascript">history.go(-1);</script>';
			die();
		}

		$spamval = spamcheck($_SESSION['login']['id'], $_POST['message'], $_GET['userid']);
		if($spamval == 1 && is_numeric($_GET['userid']))
		{
			new_entry($_GET['userid'], $_SESSION['login']['id'], $_POST['message'], $_POST['is_private']);
			draw_message_form($_GET['userid']);
			list_entries($_GET['userid'], $userinfo['traffa']['guestbook_entries']);
		}
		else
		{
			draw_message_form($_GET['userid'], $_POST['message'], $spamval);
			list_entries($_GET['userid'], $userinfo['traffa']['guestbook_entries'], $_GET['offset']);
		}
	}
	elseif($_GET['action'] == 'history')
	{
		list_entries($_GET['view'], 0, 0, $_GET['remote']);	
	}
	elseif(isset($_GET['view']) && is_numeric($_GET['view']))
	{
		if($_SESSION['login']['id'] != $_GET['view'] && is_numeric($_GET['view']))
		{
			draw_message_form($_GET['view']);
		}
		list_entries($_GET['view'], $userinfo['traffa']['guestbook_entries'], $_GET['offset']);
		if($_GET['view'] == $_SESSION['login']['id'])
		{
			echo '<form action="/traffa/guestbook.php?action=delete" method="post" onsubmit="return confirm(\'Du är på väg att ta bort SAMTLIGA gästboksinlägg. Vill du fortsätta?\');">';
			echo '<input type="hidden" name="all" value="true" />';
			echo '<input type="submit" value="Radera alla inlägg" class="button" />';
			echo '</form>';
			$_SESSION['notices']['unread_gb_entries'] = 0;
		}
	}
	else
	{
		list_entries($_SESSION['login']['id'], $userinfo['traffa']['guestbook_entries'], $_GET['offset']);
		echo '<form action="/traffa/guestbook.php?action=delete" method="post" onsubmit="return confirm(\'Du är på väg att ta bort SAMTLIGA gästboksinlägg. Vill du fortsätta?\');">';
		echo '<input type="hidden" name="all" value="true" />';
		echo '<input type="submit" value="Radera alla inlägg" class="button" />';
		echo '</form>';
		$_SESSION['notices']['unread_gb_entries'] = 0;
	}
}
elseif(isset($_GET['view']) && is_numeric($_GET['view']))
{
	if($_GET['action'] == 'history')
	{
		list_entries($_GET['view'], 0, 0, $_GET['remote']);	
	}
	else
	{
		list_entries($_GET['view'], $userinfo['traffa']['guestbook_entries'], $_GET['offset'], 0);
	}
}
else
{
	echo 'Det verkar som om du nyss loggats ut. Logga in och testa igen ;)';
}
	ui_bottom();
?>
