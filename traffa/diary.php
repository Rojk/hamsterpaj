<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	require(PATHS_INCLUDE . 'libraries/userblock.lib.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');
	require(PATHS_INCLUDE . 'libraries/comments.lib.php');
	
	$ui_options['title'] = 'DATOR';
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'diary.css';
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['javascripts'][] = 'comments.js';
	$ui_options['javascripts'][] = 'diary.js';
	
	if(isset($_GET['user']) && is_numeric($_GET['user']))
	{
		$user_id = $_GET['user'];
	}
	// New standards, always use ?user_id= when retrieving an user id.
	elseif(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
	{
		$user_id = $_GET['user_id'];
	}
	elseif(login_checklogin())
	{
		$user_id = $_SESSION['login']['id'];
	}
	else
	{
		ui_top();
		echo '<p>I think I know I mean "Yes," but it\'s all wrong.</p>';
		ui_bottom();
		exit;
	}

	if($_GET['action'] == 'remove' & ($user_id == USER_ID || is_privilegied('remove_diary_post')))
	{
		$query = 'UPDATE blog SET is_removed = 1 WHERE id = "' . $_GET['id'] . '" LIMIT 1';
		mysql_query($query)  or report_sql_error($query,__FILE__,__LINE__);
		jscript_alert('Dagboksinlägg borttaget');
		jscript_location('?user_id=' . $user_id . '');
	}
	
	if(login_checklogin() && isset($_POST['title']))
	{
		$insertquery = 'INSERT INTO blog (user, date, title, text) VALUES("' . $_SESSION['login']['id'] . '", "' . date('Y-m-d') . '", "' . $_POST['title'] . '", "' . $_POST['text'] . '")';
		$updatequery = 'UPDATE blog SET title = "' . $_POST['title'] . '", text = "' . $_POST['text'] . '" WHERE user = "' . $_SESSION['login']['id'] . '" AND date = "' . date('Y-m-d') . '" LIMIT 1';
		
		if(mysql_query($insertquery))
		{
			$ualquery = 'INSERT INTO user_action_log (timestamp, user, action, url, label)';
			$ualquery .= ' VALUES("' . time() . '", "' . $_SESSION['login']['id'] . '", "diary", "/traffa/diary.php?user=' . $_SESSION['login']['id'] . '&entry=' . mysql_insert_id() . '", "' . $_POST['title'] . '")';
			
			mysql_query($ualquery) or report_sql_error($ualquery, __FILE__, __LINE__);
		}
		else
		{
			mysql_query($updatequery);
		}

	}
	
	$params['user_id'] = $user_id;
	$profile = profile_fetch($params);
	$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';

	$output .= profile_mini_page($profile);
	
	/* Fetch and render an entry */
	if(isset($_GET['entry']))
	{
		$query = 'SELECT * FROM blog WHERE id = "' . $_GET['entry'] . '" AND is_removed = 0';
	}
	else
	{
		$query = 'SELECT * FROM blog WHERE user = "' . $user_id . '" AND is_removed = 0 ORDER BY id DESC LIMIT 1';		
	}
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	if(mysql_num_rows($result) == 1)
	{
		$data = mysql_fetch_assoc($result);
		$highlight_month = $data['date'];
		
		$entry .= '<h1>' . $data['date'] . ': ' . $data['title'] . '</h1>' . "\n";
		// Lef-91 adds photos from the same date
		$photos = photos_fetch(array('date' =>  $data['date'], 'user' => $user_id));
		if(isset($photos))
		{
			$entry .= rounded_corners_top(array(), true);
			$entry .= '<h2 style="margin-top: 0;">Foton från samma datum</h2>' . "\n";
			$entry .= photos_list_mini($photos);
			$entry .= '<div style="clear: both;"></div>' . "\n";
			$entry .= rounded_corners_bottom(array(), true);
		}
		
		$entry .= '<p>' . nl2br($data['text']) . '</p>' . "\n";
		
		if($user_id == USER_ID || is_privilegied('remove_diary_post'))
		{
			$entry .= '<a href="?user_id=' . $user_id . '&action=remove&id=' . $data['id'] . '">Ta bort dagboksinlägget</a>' . "\n";
		}
		
		// Comments
		$entry .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
		$entry .= comments_input_draw($data['id'], 'blog');
		$entry .= '<div style="clear: both;"></div>' . "\n";
		$entry .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
		$entry .= comments_list($data['id'], 'blog');
	}
	
	$short_months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Maj', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sept', 10 => 'Okt', 11 => 'Nov', 12 => 'Dec');

	$query = 'SELECT id, date, title FROM blog WHERE user = "' . $user_id . '" AND is_removed = 0 ORDER BY id DESC';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$entries[$data['date']] = $data;
		
		$month = $short_months[date('n', strtotime($data['date']))] . date(' -y', strtotime($data['date']));
		if(!in_array($month, $months))
		{
			$months[date('Y-m', strtotime($data['date']))] = $month;
		}
	}
	
	$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
	$output .= '<select id="diary_date_selector">' . "\n";
	$i = 0;
	foreach($months AS $ymd => $month)
	{
		if($ymd == substr($highlight_month, 0, -3))
		{
			$selected = ' selected="selected"';
			$scroll_target = $i;
		}
		else
		{
			$selected = '';
		}
		$output .= '<option value="' . $i . '"' . $selected . '>' . $month . '</option>' . "\n";
		$i++;
		
	}
	$output .= '</select>' . "\n";
	
	$output .= '<div id="diary_date_scroller">' . "\n";
	foreach($months AS $month => $crap)
	{
		$output .= '<ul class="diary_date_list">' . "\n";
		for($i = 1; $i <= 31; $i++)
		{
			$tmp = ($i < 10) ? '0' . $i : $i;
			$output .= '<li>' . "\n";
			$output .= (isset($entries[$month . '-' . $tmp])) ? '<a href="?user=' . $user_id . '&entry=' . $entries[$month . '-' . $tmp]['id'] . '">' . $i . '</a>' : $i;
			$output .= '</li>' . "\n";
		}
		$output .= '</ul>' . "\n";
	}
	$output .= '</div>' . "\n";
	
	$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
	

	if($_SESSION['login']['id'] == $user_id)
	{
		$query = 'SELECT * FROM blog WHERE date = "' . date('Y-m-d') . '" AND user = "' . $_SESSION['login']['id'] . '" AND is_removed = 0 LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
		}
		else
		{
			$data = array();
		}
	
		$compose .= '<form action="/traffa/diary.php" method="post" class="diary_form">' . "\n";
		$compose .= '<h1>Skriv i dagens dagbok</h1>' . "\n";
		$compose .= '<input type="text" name="title" value="' . addslashes($data['title']) . '" class="textbox" />' . "\n";
		$compose .= '<textarea name="text">' . $data['text']. '</textarea>' . "\n";
		$compose .= '<input type="submit" value="Spara" class="button_50" />' . "\n";
		$compose .= '</form>' . "\n";
	}

	// Slakta Joar om det här är fel.
	if (strlen($profile['error_message']) > 0)
	{
		$ui_options['title'] .= 'Presentationsfel - Hamsterpaj.net';
		ui_top($ui_options);
		echo '<h1>Presentationsfel</h1>';
		echo '<p>' . $profile['error_message'] . '</p>';
		ui_bottom();
		exit; //Important!
	}
	else
	{
	$ui_options['title'] = $profile['username'] . 's dagbok, Hamsterpaj.net';
	}
	
	ui_top($ui_options);
	echo $output;
	echo $entry;
	echo $compose;
	if(isset($scroll_target))
	{
		echo '<script>$(\'#diary_date_scroller\').animate({scrollTop: ' . ($scroll_target * 22) . '}, 250);</script>' . "\n";
	}
	ui_bottom();
?>