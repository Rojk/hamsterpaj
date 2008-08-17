<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'traffa-functions.php');
	event_log_log("my_vistors");
	$ui_options['stylesheets'][] = 'my_visitors.css';
	$ui_options['menu_path'] = array('traeffa');
	ui_top($ui_options);
	
	if(!login_checklogin())
	{
		echo '<h1>Ooops! Det verkar som om du är utloggad</h1>' . "\n";
		echo 'Du måste vara inloggad för att kunna se dina senaste besökare, logga in och kom tillbaks hit sen!';
		ui_bottom();
		exit;
	}

	traffa_draw_user_div($_SESSION['login']['id'], $_SESSION);
	
	$query = 'SELECT uel.remote_user_id AS user_id, uel.timestamp, l.lastaction, l.username, l.lastlogon, u.image, u.birthday, u.gender, z.spot ';
	$query .= 'FROM user_event_log AS uel, login AS l, userinfo AS u, zip_codes AS z ';
	$query .= 'WHERE uel.user = "' . $_SESSION['login']['id'] . '" AND l.id = uel.remote_user_id AND u.userid = uel.remote_user_id ';
	$query .= 'AND uel.action = "profile_visit" AND z.zip_code = u.zip_code ';
	$query .= 'ORDER BY uel.timestamp DESC LIMIT 80';
	
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	
	while($data = mysql_fetch_assoc($result))
	{
		/* Only display each user one time, should be done with DISTINCT in query, but I can't ger it to work */
		if(in_array($data['user_id'], $duplicates))
		{
			continue;
		}
		$duplicates[] = $data['user_id'];
		
		$entry = '<div class="visitor">' . "\n";
		if(in_array($data['image'], array(1, 2)))
		{
			$entry .= insert_avatar($data['user_id']);
		}
		$entry .= fix_time($data['timestamp']);
		$entry .= '<div>' . "\n";
		$entry .= '<h3><a href="/traffa/profile.php?id=' . $data['user_id'] . '">' . $data['username'] . '</a></h3>' . "\n";
		$entry .= '<h4>';
		$entry .= ($data['birthday']) != '0000-00-00' ? date_get_age($data['birthday']) . 'år ' : '';
		$entry .= (strlen($data['spot']) > 0) ? 'från ' . $data['spot'] : ''; 
		$entry .= '</h4>' . "\n";
		$entry .= ($data['lastaction'] > time() - 600) ? 'Online nu!' : 'Loggade senast in ' . date('Y-m-d', $data['lastlogon']) . "\n";
		$entry .= '</div>';
		$entry .= '</div>' . "\n";
		switch($data['gender'])
		{
			case 'm':
				$output_boys .= $entry;
				break;
			case 'f':
				$output_girls .= $entry;
				break;
			default:
				$output_unknown .= $entry;
				break;
		}
	}

	echo '<h1>Dina senaste besökare</h1>' . "\n";

	echo '<div class="my_visitors_list"> ' . "\n";
	echo '<h2>Tjejer</h2>' . "\n";
	echo $output_girls;
	echo '</div>' . "\n\n";

	echo '<div class="my_visitors_list"> ' . "\n";
	echo '<h2>Killar</h2>' . "\n";
	echo $output_boys;
	echo '</div>' . "\n\n";
	
	echo '<div class="my_visitors_list"> ' . "\n";
	echo '<h2>Okänt kön</h2>' . "\n";
	echo $output_unknown;
	echo '</div>' . "\n\n";

	echo '<br style="clear: both;" />' . "\n";

	ui_bottom();
?>
