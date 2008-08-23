<?php
	require('../include/core/common.php');
	include($hp_includepath . 'guestbook-functions.php');
	
	if(isset($_POST['recipient']) && isset($_POST['message']) && is_numeric($_POST['recipient']))
	{
		/* I'm not sure about how to do this session-login-thing without login_checklogin()... But i just copy-pasted from traffa/gb-reply.php */
		if(login_checklogin())
		{
			if(userblock_check($_GET['userid'], $_SESSION['login']['id']) == 1)
			{
				die('Fel: Användaren har blockerat dig.');
			}
	
			$spamval = spamcheck($_SESSION['login']['id'], $_POST['message']);
			if($spamval == 1)
			{
				new_entry($_POST['recipient'], $_SESSION['login']['id'], $_POST['message']);
				die('Gästboksinlägg skickat!');
			}
			else
			{
				die('Fel: Stoppat av spamskyddet.');
			}
		}
	}
?>