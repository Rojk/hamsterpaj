<?php
function refuse_image($userid, $validator) 
	{
		if($userid == 17505 || $userid == 573633 || $userid == 625747 || $userid == 68767)
		{
			die('Man kan inte ta bort denna bild...');
			exit;
		}

		global $hp_path;
		$query = 'UPDATE userinfo SET image = "3", image_validator = "' . $validator . '" ';
		$query.= ' WHERE userid = "' . $userid . '" LIMIT 1';
		mysql_query($query) or die();
		if(unlink(PATHS_IMAGES . 'users/full/' . $userid . '.jpg') && unlink(PATHS_IMAGES . 'users/thumb/' . $userid . '.jpg'))
		{
			messages_send(2348, $userid, '', $_POST['message'], 0, 7);
    } 
		else 
		{
        	     echo '<script language="javascript">alert("Ett fel uppstod när ' . $userid . '.jpg skulle tas bort!");</script>';
		}
		admin_report_event($_SESSION['login']['username'], 'Refused avatar', $userid);


		log_admin_event('avatar validated', 'denied', $validator, $userid, 0); //image id not available here
		admin_action_count($_SESSION['login']['id'], 'avatar_denied');

	}

?>
