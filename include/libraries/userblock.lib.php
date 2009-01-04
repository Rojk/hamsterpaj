<?php
	
	function userblock_checkblock($object_id)
	{
		// PREVENT SQL QUERY ERROR DUE TO MISSING OBJECT ID
		if (strlen($object_id) == 0)
		{
			return false;
		}
		// PREVENT SQL INJECTIONS VIA INTEGER QUERY OBJECT
		elseif (!is_numeric($object_id))
		{
			return false;
		}
		// PREVENT SQL QUERY ERROR DUE TO NOT LOGGED IN USER
		elseif (!login_checklogin())
		{
			return false;
		}
		$sql = 'SELECT * FROM userblocks WHERE ownerid = ' . $object_id . ' AND blockedid = ' . $_SESSION['userid'] . ' LIMIT 1';
		$result = mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
		$data = mysql_fetch_assoc($result);
		
		if ($data['blockedid'] === $_SESSION['userid'] && !is_privilegied('igotgodmode') && !is_privilegied('ip_ban_admin'))
		{
			return true;
		}
		elseif ($data['blockedid'] === $_SESSION['userid'] && is_privilegied('igotgodmode') && !is_privilegied('ip_ban_admin'))
		{
			echo '<p class="error"><strong>Den h&auml;r anv&auml;ndaren har blockerat dig!</strong><br />' . "\n";
			echo 'Men eftersom du &auml;r 1337 h4xx0r s&aring; kan du se personens presentation i alla fall :)</p>' . "\n";
		}
		else
		{
			return false;
		}
		
	}
	
	// THIS FUNCTION IS NOT USED ANYMORE.
	// SO DON'T USE IT !!1!!11!1one!111!11!ett!1!1!två?!!1!!!1!
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