<?php
	require('../include/core/common.php');

	if(!is_privilegied('use_ghosting_tools') && !isset($_SESSION['ghost']))
	{
		die('Move along...');
	}
	
	$allow_ghost = array('borttagen', strtolower($_SESSION['login']['username']), '-johan', 'ace', 'heggan', 'joel', 'soode');
	
	if(isset($_GET['username'], $_GET['reason']) && !in_array(strtolower($_GET['username']), $allow_ghost))
	{
		$query = 'SELECT id, username FROM login WHERE username LIKE "' . $_GET['username'] . '"';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) == 1)
		{
			trace('ghosted_users', $_GET['username'] . ' (userid ) ghosted by ' . $_SESSION['login']['username'] . ' (userid ' . $_SESSION['login']['id'] . '). Reason: ' . $_GET['reason']);
			
			$_SESSION = array();
			session_destroy();
			session_start();
			session_regenerate_id();
			
			var_dump(login_dologin(array(
				'username' => $_GET['username'],
				'method' => 'ghost'
			));
		}
		else
		{
			$output .= 'Användaren hittades inte.' . "\n";
		}
	}
	else
	{
		$output .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="get">' . "\n";
		
		$output .= '<h2>Varför ghostar du personen?</h2>' . "\n";
		$output .= '<p>' . "\n";
		$output .= '<label for="reason_fld">Alla ghostningar <strong>måste</strong> ha en anledning:</label><br />' . "\n";
		$output .= '<textarea name="reason"></textarea><br />' . "\n";
		$output .= '</p>' . "\n";
		
		$output .= '<h2>Vem vill du ghosta?</h2>' . "\n";
		$output .= '<p>' . "\n";
		$output .= '<label for="username_fld">Användare att ghosta:</label><br />';
		$output .= '<input type="text" name="username" id="username_fld" />' . "\n";
		$output .= '</p>' . "\n";
		
		$output .= '<h2>Tryck på den stora röda knappen...</h2>' . "\n";
		$output .= '<input type="submit" value="Ghosta" style="width: 400px; height: 200px; background: #ff5555; margin: 30px auto 30px auto" />' . "\n";
		
		$output .= '</form>' . "\n";
	}
	
	$ui_options['title'] = 'Ghostning';
	$ui_options['menu_path'] = array('fra', 'user_ghost');
	ui_top($ui_options);
	
	echo '<h1>Ghostning</h1>' . "\n";
	
	echo rounded_corners_top();
	echo 'OBS! För att få ghosta måste du ha godkännande av Ace och ghostningen måste följa våran <a href="#">policy för ghostning</a>. <strong>Alla ghostningar loggas</strong>.' . "\n";
	echo rounded_corners_bottom();
	
	echo $output;
	
	ui_bottom();
?>