<?php
	require('../include/core/common.php');
	
	if(isset($_POST['recipient']) && isset($_POST['message']) && is_numeric($_POST['recipient']))
	{
		/* I'm not sure about how to do this session-login-thing without login_checklogin()... But i just copy-pasted from traffa/gb-reply.php */
		if(login_checklogin())
		{
			if(userblock_check($_GET['userid'], $_SESSION['login']['id']) == 1)
			{
				die('Fel: Användaren har blockerat dig.');
			}
			
			guestbook_insert(array(
				'sender' => $_SESSION['login']['id'],
				'recipient' => $_POST['recipient'],
				'message' => $_POST['message']
			));
		}
	}
?>