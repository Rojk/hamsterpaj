<?php
	require('../include/core/common.php');
	
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['stylesheets'][] = 'irl.css';
	$ui_options['stylesheets'][] = 'forms.css';

	function irl_attending($options)
	{
		if(!is_numeric($options['id']))
		{
			throw new Exception('Id is not numeric');
		}
		$query = 'SELECT DISTINCT l.id, l.username, i.*, u.birthday, z.spot, u.gender ';
		$query .= 'FROM login AS l, userinfo AS u, irl_attendings AS i, zip_codes AS z ';
		$query .= 'WHERE i.user_id = l.id AND z.zip_code = u.zip_code AND i.user_id = u.userid AND i.irl_id = ' . $options['id'] . ' AND i.attending = "' . $options['attending'] . '" ';
		$query .= 'ORDER BY i.irl_title DESC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		$output .= $options['attending'] == 'yes' ? '<h2 style="margin-top: 0;">De som kommer(' . mysql_num_rows($result) . ')</h2>' : '<h2 style="margin-top: 0;">De som kanske kommer(' . mysql_num_rows($result) . ')</h2>' . "\n";
		$output .= '<table class="irl_attendings">' . "\n";
		$output .= '<tr>' . "\n";
		$output .= '<th>Användare</th>' . "\n";
		$output .= '<th>Titel</th>' . "\n";
		$output .= '<th>Har sängplatser för</th>' . "\n";
		$output .= '<th>Behöver sängplats</th>' . "\n";
		$output .= '<th>Bor</th>' . "\n";
		$output .= '</tr>' . "\n";
		while($data = mysql_fetch_assoc($result))
		{
			$output .= '<tr>' . "\n";
			$output .= '<td class="irl_attendings_username"><a href="http://www.hamsterpaj.net/traffa/profile.php?user_id=' . $data['user_id'] . '">' . $data['username'] . '</a>' . "\n";
			$genders = array('m' => 'kille', 'f' => 'tjej');
			$output .= $data['gender'];		
			$output .= ($data['birthday'] != '0000-00-00') ? '<span class="age">' . date_get_age($data['birthday']) . '</span> ' : '';
			$output .= '</td>' . "\n";
			$output .= '<td>' . $data['irl_title'] . '</td>' . "\n";
			$output .= '<td>' . $data['has_beds'] . '</td>' . "\n";
			$output .= '<td>' . (($data['needs_beds'] == '1') ? 'Ja' : 'Nej') . '</td>' . "\n";
			$output .= '<td>' .  $data['spot'] . '</td>' . "\n";
			$output .= '</tr>' . "\n";
		}
		$output .= '</table>' . "\n";
		return $output;
	}
	
	function irl_attending_form()
	{
		if(!login_checklogin())
		{
			$output = 'Du måste vara inloggad för att kunna lägga till dig i listan';
			return $output;
		}
		
		$output .= '<form name="attending_form" method="post" action="?action=update_attending&irl=' . $_GET['irl'] . '">' . "\n";
		$output .= '<label for="attending">Kommer</label>' . "\n";
		$output .= '<select name="attending">
								<option value="yes">Ja</option>
								<option value="maybe">Kanske</option>
								<option value="no">Nej</option>
								</select>' . "\n";
		$output .= '<label for="has_beds">Har sängplatser för</label>' . "\n";
		$output .= '<select name="has_beds">
								<option value="0">0</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								</select>' . "\n";
		$output .= '<label for="needs_beds">Behöver sängplats</label>' . "\n";
		$output .= '<select name="needs_beds">
								<option value="1">Ja</option>
								<option value="0">Nej</option>
								</select>' . "\n";
		$output .= '<input type="submit" value="Spara" class="button_60" />' . "\n";
		$output .= '</form>' . "\n";
		return $output;
	}
	
	function irl_header($irl_data)
	{
		$ui_options['title'] = 'Hamsterpaj IRL - ' . $irl_data['name'] . '';
		$output .= '<h1>Hamsterpaj IRL - ' . $irl_data['name'] . '</h1>' . "\n";
		return $output;
	}
	
	function irl_update_attending($content)
	{
		if(!is_numeric($_GET['irl']))
		{
			throw new Exception('ID is not numeric');
		}
		if(!login_checklogin())
		{
			throw new Exception('You must be loged in');
		}
		$query = 'SELECT user_id FROM irl_attendings WHERE user_id = ' . $_SESSION['login']['id'] . ' AND irl_id = ' . $_GET['irl'] . ' LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		if(mysql_num_rows($result) == 1)
		{
			$query = 'UPDATE irl_attendings SET attending = "' . $content['attending'] . '", has_beds = "' . $content['has_beds'] . '", needs_beds = "' . $content['needs_beds'] . '"';
			$query .= ' WHERE user_id = "' . $_SESSION['login']['id'] . '" AND irl_id = ' . $_GET['irl'] . ' LIMIT 1';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		else
		{
			$query = 'INSERT INTO irl_attendings (irl_id, user_id, attending, has_beds, needs_beds) ';
			$query .= 'VALUES(' . $_GET['irl'] . ', "' . $_SESSION['login']['id'] . '", "' . $content['attending'] . '", "' . $content['has_beds'] . '", "' . $content['needs_beds'] . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
	}
	
	function irl_fetch_information()
	{
		if(!is_numeric($_GET['irl']))
		{
			throw new Exception('ID is not numeric');
		}
		$query = 'SELECT name, description, information, location, forumthread_url';
		$query .= ' FROM irl';
		$query .= ' WHERE id = ' . $_GET['irl'] . '';
		$query .= ' LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
		return $data;
	}
	
	try
	{
		switch($_GET['action'])
		{
			case "show_attendings":
				$irl_data = irl_fetch_information();
				$out .= irl_header($irl_data);
			
				$rounded_corners_tabs_options['return'] = TRUE;
				$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=show_information&irl=' . $_GET['irl'] . '', 'label' => 'Information');
				$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=show_attendings&irl=' . $_GET['irl'] . '', 'label' => 'Deltagare', 'current' => TRUE);
				$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . $irl_data['forumthread_url'], 'label' => 'Forumtråd');
				$out .= rounded_corners_tabs_top($rounded_corners_tabs_options); 
					if(!isset($_GET['irl']))
					{
						$out .= 'Inget IRL valt';
						break;
					}
					$out .= (isset($_GET['updated']) ?  '<div class="form_notice_success">Din IRL information har uppdateras. Klad Hest.</div>' : '');
					$out .= irl_attending(array('attending' => 'yes', 'id' => $_GET['irl']));
					$out .= irl_attending(array('attending' => 'maybe', 'id' => $_GET['irl']));
					$out .= irl_attending_form();
				$out .= rounded_corners_tabs_bottom(array('return' => TRUE)); 
			break;
			case "show":
			case "show_information":
				$irl_data = irl_fetch_information();
				$out .= irl_header($irl_data);
			
				$rounded_corners_tabs_options['return'] = TRUE;
				$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=show_information&irl=' . $_GET['irl'] . '', 'label' => 'Information', 'current' => TRUE);
				$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=show_attendings&irl=' . $_GET['irl'] . '', 'label' => 'Deltagare');
				$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . $irl_data['forumthread_url'], 'label' => 'Forumtråd');
				$out .= rounded_corners_tabs_top($rounded_corners_tabs_options);
				$out .= nl2br($irl_data['information']);
				$out .= rounded_corners_tabs_bottom(array('return' => TRUE)); 
			break;
			case "update_attending":
				irl_update_attending($_POST);
				header('Location: /traffa/irl.php?action=show_attendings&irl=' . $_GET['irl'] . '&updated');
			break;
			default:
				$out .= 'Här ska det vara en lista över IRL' . "\n";
		}
	}
	catch (Exception $error)
	{
		$options['type'] = 'error';
    	$options['title'] = 'Nu blev det fel här';
   		$options['message'] = $error -> getMessage();
    	$options['collapse_link'] = 'Visa felsökningsinformation';
   		$options['collapse_information'] = preint_r($error, true);
    	$out .= ui_server_message($options);
	}
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
