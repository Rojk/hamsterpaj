<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');

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
		$out .= profile_head($profile);
		
		$ui_options['stylesheets'][] = 'user_profile.css';
		$ui_options['javascripts'][] = 'user_profile.js';		


		if(isset($_GET['history']))
		{
			echo 'history on';
			$fetch['recipient'] = array($fetch['recipient'], $_GET['history']);
			$fetch['sender'] = $fetch['recipient'];
		}
		else
		{
			echo 'nohistory';
		}
		$entries = guestbook_fetch($fetch);
		$out .= guestbook_list($entries);
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