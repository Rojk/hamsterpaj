<?php
	try
	{
		require('../include/core/common.php');
		require_once(PATHS_INCLUDE . 'libraries/groups_active.lib.php');
	
		$action = $_POST['action'];
		switch($action)
		{
			case 'new_post':
				$auth = group_check_auth($_SESSION['login']['id'], $_POST['groupid'], 1);
		    	if($auth)
		    	{
					$_POST['group_message'] = trim($_POST['group_message']);
					if (strlen($_POST['group_message']) > 0)
					{
						group_send_new_message($_POST['groupid'], $_SESSION['login']['id'], $_POST['group_message']);
					}
					else
					{
						throw new Exception('Nånting måste du skriva! (Nånting, charmigt Heggan!)');
					}
				}
			break;
			
			default:
				throw new Exception('No input!');
			break;
		}
	}
	catch (Exception $error)
	{
		echo $error->getMessage();
	}
?>