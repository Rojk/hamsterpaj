<?php
	require('../include/core/common.php');
	include($hp_includepath . 'guestbook-functions.php');
	
	if (!is_numeric($_GET['answereid']) && isset($_GET['answereid']))
	{
		die('FISKRENS');
	}

	if (!is_numeric($_GET['userid']) && isset($_GET['userid']))
	{
		die('FISKRENS IGEN');
	}
	
	
	echo '<html><head><title>Svara</title>';
	echo '<link href="/stylesheets/ui.css.php" rel="stylesheet" type="text/css">';
	echo '<link href="/stylesheets/shared.css" rel="stylesheet" type="text/css">';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	echo '</head>';
	if($_GET['action'] == 'reply')
	{
		echo '<body onload="document.forms[0].message.focus()">';
	}
	else
	{
		echo '<body>';
	}
	echo '<div id="main" style="padding: 5px; width: 215px; height: 170px; margin-top: 10px;">';
	
	if(login_checklogin())
	{
		if($_GET['action'] == 'reply')
		{
			draw_reply_form(htmlspecialchars($_GET['username']), $_GET['userid'], $_GET['answereid']);
		}
		elseif($_GET['action'] == 'send_reply')
		{
			if(userblock_check($_GET['userid'], $_SESSION['login']['id']) == 1)
			{
				jscript_alert('Den användare som du har angivit som mottagare har blockerat dig, och ditt meddelande kan därför inte skickas!');
				echo '<script language="javascript">history.go(-1);</script>';
				die();
			}
/*

				if(644314 == $_SESSION['login']['id'])
					log_to_file('henrik', LOGLEVEL_DEBUG, __FILE__, __LINE__, $_POST['message']);

*/
			$spamval = spamcheck($_SESSION['login']['id'], $_POST['message']);
			if($spamval == 1)
			{
				echo '<script language="javascript">setTimeout(\'window.close();\',500);</script>';
				new_entry($_GET['userid'], $_SESSION['login']['id'], $_POST['message'], $_POST['is_private'], $_GET['answereid']);
				echo '<h1>Inlägget skickat!</h1>';
			}
			else
			{
				echo '<script language="javascript">alert("' . $spamval . '");</script>';
				draw_reply_form(htmlspecialchars($_GET['username']), $_GET['userid'], $_POST['message']);
			}
		}
	}
	else
	{
		die('Du tycks ha loggats ut :(');
	}
	
	echo '</div></body></html>';
?>
