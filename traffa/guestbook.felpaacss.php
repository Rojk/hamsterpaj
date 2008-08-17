<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	$ui_options['menu_path'] = array('traeffa');

	if(isset($_GET['view']))
	{
		$fetch['recipient'] = $_GET['view'];
	}
	elseif(login_checklogin())
	{
		$fetch['recipient'] = $_SESSION['login']['id'];
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

		$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';
		$out .= '<div class="profile_' . $profile['profile_theme'] . '">' . "\n";
		$out .= profile_head($profile);
		$out .= '</div>' . "\n";
			
		$ui_options['stylesheets'][] = 'user_profile.css';
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
	ui_bottom();
?>