<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	require(PATHS_INCLUDE . 'libraries/userblock.lib.php');
	$ui_options['menu_path'] = array('traeffa');

	if(isset($_GET['view']))
	{
		$fetch['recipient'] = $_GET['view'];
	}
	elseif(login_checklogin())
	{
		$fetch['recipient'] = $_SESSION['login']['id'];
	}

	if($fetch['recipient'] == $_SESSION['login']['id'])
	{
		unset($_SESSION['unread_gb_entries']);
	}

	if($fetch['recipient'] > 0)
	{
		$params['user_id'] = $fetch['recipient'];
		$profile = profile_fetch($params);
		
		/* ...and check for errors. */
		if(strlen($profile['error_message']) > 0)
		{
			$ui_options['title'] .= 'Presentationsfel - Hamsterpaj.net';
			ui_top($ui_options);
			echo '<h1>Presentationsfel</h1>';
			echo '<p>' . $profile['error_message'] . '</p>';
			ui_bottom();
			exit; //Important!
		}
		
		$is_blocked = retrieve_userblock();
	
		if ($is_blocked['is_blocked'] == true)
		{
			if ($is_blocked['ov_is_blocked'] == false)
			{	
				echo 'fisk';
			}
			else
			{
				$ui_options['title'] = 'Blockerad - Hamsterpaj.net';
				ui_top($ui_options);
				$out = '<h1 style="margin-top: 0px">Den här användaren har blockerat dig!</h1>
				<p>Den användaren du försöker besöka har blockerat dig. <br />Ett tips är att du uppför dig bättre i fortsättningen.</p><br />';
				echo rounded_corners($out, array('color' => 'orange_deluxe'));
				ui_bottom();
				exit;
			}
		}

		$ui_options['stylesheets'][] = 'user_profile.css';
		$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';
		$out .= profile_mini_page($profile);
		
		$ui_options['javascripts'][] = 'user_profile.js';		
		
		
		if(isset($_GET['history']))
		{
			$fetch['recipient'] = array($fetch['recipient'], $_GET['history']);
			$fetch['sender'] = $fetch['recipient'];
		}

		if(!isset($_GET['history']) && login_checklogin() && $fetch['recipient'] != $_SESSION['login']['id'] && !is_array($fetch['recipient']))
		{
			$out .= guestbook_form(array('recipient' => $fetch['recipient']));
		}

		//Get pagenumber
		$page = 1;
		if(isset($_GET['page']) && is_numeric($_GET['page']))
		{
			$page = intval($_GET['page']);
			if($page < 1 || $page > 999)
			{
				$page = 1;
			}
		}
		$offset = (($page - 1) * 30);		

		$fetch['limit'] = 30;
		$fetch['offset'] = $offset;
		$entries = guestbook_fetch($fetch);
		$out .= guestbook_list($entries);
		
		if(login_checklogin())
		{
			foreach($entries AS $entry)
			{
				if($entry['recipient'] == $_SESSION['login']['id'] && $entry['read'] == 0)
				{
					$update_read[] = $entry['id'];
				}
			}
			if(count($update_read) > 0)
			{
				$query = 'UPDATE traffa_guestbooks SET `read` = 1 WHERE id IN("' . implode('", "', $update_read) . '")';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				$_SESSION['notices']['unread_gb_entries'] -= count($update_read);
			}
			$_SESSION['notices']['unread_gb_entries'] = 0;
		}
		

	}
	else
	{
		$out .= '<h1>Oops, du verkar ha loggats ut</h1>' . "\n";
		$out .= '<p>Skapa ett konto eller logga in om du vill komma åt gästboken</p>' . "\n";
	}
	
	ui_top($ui_options);
	echo $out;
	echo '<img src="http://images.hamsterpaj.net/rojk/heart.gif" id="rojk_love" />' . "\n";
	ui_bottom();
?>