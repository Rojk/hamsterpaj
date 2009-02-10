<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'profile.lib.php');
	require(PATHS_LIBRARIES . 'userblock.lib.php');
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

	if(isset($_POST['guestbook_remove_all']) && login_checklogin())
	{
		//delete all guestbooks entries
		$query = 'UPDATE traffa_guestbooks SET deleted = 1 WHERE recipient = '.$_SESSION['login']['id'];
		$res = mysql_query($query) or report_sql_error($query, __FILE__, __LINE);
		unset($_SESSION['notices']['unread_gb_entries']);

		jscript_alert('Nu har du raderat '.mysql_affected_rows().' gästboksinlägg!');
		jscript_location('/traffa/guestbook.php');
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
		
		if (userblock_checkblock($fetch['recipient']))
		{
			ui_top();
			echo '<p class="error">IXΘYΣ! Du har blivit blockad, var snel hest så slipper du sånt ;)<br /><em>Visste du förresten att IXΘYΣ betyder Fisk på grekiska?</em></p>';
			ui_bottom();
			exit;
		}

		// Joar är stolt över detta, ge honom en klapp på ryggen.
		$title_start = $profile['username'];
		$title_end = ' gästbok - Hamsterpaj.net';
		$title_end = (strtolower(substr($profile['username'], -1)) != "s") ? 's'.$title_end : $title_end;
		$ui_options['title'] = $title_start.$title_end;

		$ui_options['stylesheets'][] = 'user_profile.css';
		if (strlen($profile['profile_theme']) > 0)
		{
			$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';
		}
		$out .= profile_mini_page($profile);

		$ui_options['javascripts'][] = 'user_profile.js';
		$ui_options['header_extra'] .= '<link href="/rss/'.$profile['username'].'" rel="alternate" type="application/rss+xml" title="'.rtrim($profile['username'], 's').'s'.' gästbok som RSS-flöde" />';
		
		$out .= guestbook_p12_shield($profile['gb_anti_p12']);
		
		if(!isset($_GET['history']) && login_checklogin() && $fetch['recipient'] != $_SESSION['login']['id'] && !is_array($fetch['recipient']))
		{
			$out .= guestbook_form(array('recipient' => $fetch['recipient'], 'username' => $profile['username']));
		}

		if(!is_array($fetch['recipient']) && $fetch['recipient'] == $_SESSION['login']['id'])
		{
			if($_SESSION['login']['id'] == $fetch['recipient'])
			{
				$out .= '<form style="display:inline;" action="/traffa/guestbook.php" method="post" onsubmit="return confirm(\'Är du säker på att du vill radera ALLA dina gästboksinlägg?\n\nDU KAN INTE ÅNGRA DETTA\');">
			<input type="hidden" name="guestbook_remove_all">
			<input type="submit" id="guestbook_remove_all" class="button_130" value="Radera alla inlägg" />
			</form>' . "\n";
			}
			$out .= '<button id="guestbook_zero_unread" class="button_150">Markera alla som lästa</button>' . "\n";
			$out .= '<div style="padding-top: 4px;" height><a href="/rss/'.$profile['username'].'" style="float:right;" title="RSS-flöde"><img src="'.IMAGE_URL.'rss-23x23.png" alt="RSS-flöde" /></a></div><span style="clear:both;">&nbsp;</span>';
		}
		if(isset($_GET['history']))
		{
			$fetch['recipient'] = array($fetch['recipient'], $_GET['history']);
			$fetch['sender'] = $fetch['recipient'];
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
		$fetch['offset'] = (($page - 1) * 30);
		$fetch['limit'] = 30;
		$entries = guestbook_fetch($fetch);
		$out .= guestbook_list($entries);

		//Create Pagination links
		if(isset($_GET['page']) && is_numeric($_GET['page']))
		{
			$page = intval($_GET['page']);
			if($page > 1)
			{
				$out .= ' <a href="' . $_SERVER['PHP_SELF'];
				if (!empty($_GET['history']))
				{
					$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&history=' . $_GET['history'] . '&page' : '?page';
					$out .= '=' . ($page - 1) . '">&laquo; Föregående</a> |';
				}
				else
				{
					$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&page' : '?page';
					$out .= '=' . ($page - 1) . '">&laquo; Föregående</a> |';
				}
			}

			if($page > 0)
			{
				$out .= ' ' . $page . ' | <a href="' . $_SERVER['PHP_SELF'];
				if (!empty($_GET['history']))
				{
					$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&history=' . $_GET['history'] . '&page' : '?page';
					$out .= '=' . ($page + 1) . '">Nästa &raquo;</a>';
				}
				else
				{
					$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&page' : '?page';
					$out .= '=' . ($page + 1) . '">Nästa &raquo;</a>';
				}
			}
		}
		else
		{
			$out .= ' <a href="' . $_SERVER['PHP_SELF'];
			if (!empty($_GET['history']))
			{
				$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&history=' . $_GET['history'] . '&page' : '?page';
				$out .= '=2">Nästa &raquo;</a>';
			}
			else
			{
				$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&page' : '?page';
				$out .= '=2">Nästa &raquo;</a>';
			}
		}

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
	ui_bottom();
?>
