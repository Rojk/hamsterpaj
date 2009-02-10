<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('admin', 'flaggor');
	
	if(!is_privilegied('user_flag_admin'))
	{
		header('location: /');
		die();
	}
	$output .= '<h1>Moderera flaggor</h1> <br style="clear: both;" />';
	$output .= rounded_corners_top();
	$output .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=view">' . "\n";
	$output .= '<br /><h2>Användarnamn</h2><input type="text" name="username" />' . "\n";
	$output .= '<input type="submit" value="Visa" class="button_60" />';
	$output .= '</form>' . "\n";
	$output .= '<br style="clear: both;" />';
	
	switch(isset($_GET['action']) ? $_GET['action'] : 'home')
	{			
	case 'view':	
		
		if(isset($_POST['username']) && strtolower($_POST['username']) != 'borttagen')
		{
			$username = str_replace('_', '\\_', $_POST['username']);
			$query = 'SELECT l.username, l.id AS userid, ufl.* FROM login AS l, user_flags AS uf, user_flags_list AS ufl WHERE l.username LIKE "' .
			$username . '" AND uf.user = l.id AND ufl.id = uf.flag';
			$result = mysql_query($query) or die(report_sql_error($query));

			if(mysql_num_rows($result) > 0)
			{
				while($data = mysql_fetch_assoc($result))
				{
					$username = $data['username'];
					$userid = $data['userid'];
					$flags[] = $data;
				}

				if(count($flags) > 0)
				{
					$output .= '<h2>Användare: ' . $username . '</h2>' . "\n";
					foreach($flags AS $data)
					{
						$output .= '<img src="' . IMAGE_URL . 'user_flags/' . 
						$data['handle'] . '.png" alt="' . $data['title'] . '" title="' . $data['title'] . '" id="' . $data['id'] . '" />' . "\n";
					}
				}
				$query = 'SELECT id, title, category FROM user_flags_list ORDER BY category DESC';
				$result = mysql_query($query) or die(report_sql_error($query));
				
				while($data = mysql_fetch_assoc($result))
				{
					$flags[] = $data;
				}				
				
				$output .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=edit">' . "\n";
				$output .= '<select name="user_flag">';	
				foreach($flags AS $data)
				{
					$output .= '<option value="' . $data['id'] . '">' . $data['title'] . '</option>';
				}
				$output .= '</select>';
				$output .= '<input type="hidden" name="userid" value="' . $userid . '" />' . "\n";
				$output .= '<input type="submit" name="add" value="Lägg till" class="button_60" />';
				$output .= ' <input type="submit" name="remove" value="Ta bort" class="button_60" />';
				$output .= '</form>' . "\n";
				$output .= '<br style="clear: both;" />';
			}
			else
			{
				$output .= 'Användarnamnet finns inte, försök igen.' . "\n";
			}
		}
	break;
	
	case 'edit':
		
		$user_flag = $_POST['user_flag'];
		$userid = $_POST['userid'];
				
		if(isset($_POST['add']) && is_numeric($user_flag) && is_numeric($userid))
		{
			$query = 'INSERT INTO user_flags (user , flag) VALUES (' . $userid . ',' . $user_flag . ')';
			$result = mysql_query($query) or die(report_sql_error($query));
			$output .= 'Flaggan är inlagd!';
		}
		elseif(isset($_POST['remove']) && is_numeric($user_flag) && is_numeric($userid))
		{
			$query = 'DELETE FROM user_flags WHERE user =' . $userid . ' AND flag =' . $user_flag . ' LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query));
			$output .= 'Flaggan är borttagen!';
		}		
		else
		{
			$output .= 'Nåt fel hände, försök igen.' . "\n";
		}
		
		break;
		
	}
	$output .= rounded_corners_bottom();
	
	ui_top($ui_options);
	echo $output;
	ui_bottom();
?>	