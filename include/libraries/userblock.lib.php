<?php
	// NEW Standards, always use ?user_id= when retrieving or sending an user id.
	
	function userblock_checkblock($object_id)
	{
		$sql = 'SELECT * FROM userblocks WHERE ownerid = ' . $object_id . ' AND blockedid = ' . $_SESSION['userid'] . ' LIMIT 1';
		$result = mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
		$data = mysql_fetch_assoc($result);
		
		if ($data['blockedid'] === $_SESSION['userid'])
		{
			return true;
		}
		else
		{
			return false;
		}
			
	}
	
	function retrieve_userblock()
	{
		if(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
		{
			$pres_id = $_GET['user_id'];
		}
		// "id" is often used, sadly..
		elseif(isset($_GET['id']) && is_numeric($_GET['id']))
		{
			$pres_id = $_GET['id'];
		}
		// "view" is used in the guestbook.
		elseif(isset($_GET['view']) && is_numeric($_GET['view']))
		{
			$pres_id = $_GET['view'];
		}
		/*
		$_POST['recipient'] is used in /ajax_gateways/guestbook.json.php 
		and this function is used there to prevent users from
		bypassing the userblock-system by sending 
		guestbook-messages from a forum-post
		*/
		elseif(isset($_POST['recipient']))
		{
			$pres_id = $_POST['recipient'];
		}
		
		$user_id = $_SESSION['login']['id'];
		$userlevel = $_SESSION['login']['userlevel'];
		//$pres_id = 3;
		//$user_id = 32;
		
		$query = 'SELECT ownerid, blockedid FROM userblocks WHERE ownerid LIKE "' . $pres_id . '" AND blockedid LIKE "' . $user_id . '" LIMIT 1';
		$result = mysql_query($query);
		while ($row = mysql_fetch_assoc($result))
		{
			if ($userlevel < 3)
			{
				if ($row['ownerid'] != $row['blockedid'])
				{
					if ($row['blockedid'] > 0 )
					{
						$is_blocked = true;
					}
				}
			}
			elseif ($userlevel >= 3)
			{
				if ($row['ownerid'] != $row['blockedid'])
				{
					if ($row['blockedid'] > 0 )
					{
						$ov_is_blocked = true;
					}
				}
			}
		}
		if ($is_blocked)
		{
			$return['is_blocked'] = true;
		}
		elseif ($ov_is_blocked)
		{
			$return['ov_is_blocked'] = true;
		}
		else
		{
			$return['is_blocked'] = true;
		}
		
		return $return;
	}
?>