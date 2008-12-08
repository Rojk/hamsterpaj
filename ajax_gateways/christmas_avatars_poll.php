<?php

	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/christmas_avatars_polls.lib.php');
	
	switch(isset($_GET['action']) ? $_GET['action'] : 'home')
	{
		case 'vote':
		if(!isset($_GET['poll_id']))
		{
			die('Must know which poll to vote on.');
		}
		else
		{
			if(!is_numeric($_GET['poll_id']))
			{
				die('Poll_id not numeric. Are you trying to h4xx?');
			}
			else
			{
				if(count($_POST) > 0)
				{
					if(!is_numeric($_POST['avatar_christmas_id']))
					{
						die('UID not numeric. Are you trying to h4xx?');
					}
					else
					{
						echo christmas_avatar_poll_vote(array('contender_id'=>$_POST['avatar_christmas_id'], 'poll_id'=>intval($_GET['poll_id'])));
					}
				}
			}
		}
		break;
	}

	//only admin-functions from here on.
	if(is_privilegied('christmas_avatar_poll'))
	{
		switch(isset($_GET['action']) ? $_GET['action'] : 'home')
		{
			case 'admin_add_vote':
				if(count($_POST) > 0)
				{
					echo christmas_avatar_admin_add_doadd($_POST);
				}
				break;
			case 'admin_edit_poll':
				if(count($_POST) > 0)
				{
					if(isset($_GET['poll_id']) && is_numeric($_GET['poll_id']))
					{
						echo christmas_avatar_admin_edit_doedit(array('data'=>$_POST, 'poll_id'=>intval($_GET['poll_id']) ));
					}
					else
					{
						echo 'Poll_id finns inte eller är av fel datatyp.';
					}
				}
				else
				{
					echo 'Finns inget att uppdatera...';
				}
				break;
		}
	}
?>