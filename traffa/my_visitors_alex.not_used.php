<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'traffa-functions.php');
	event_log_log("my_visitors");
	//$ui_options['stylesheets'][] = 'my_visitors_new.css';
	$ui_options['menu_path'] = array('traeffa');
	ui_top($ui_options);
	
	if(!login_checklogin())
	{
		echo '<h1>Ooops! Det verkar som om du är utloggad</h1>' . "\n";
		echo 'Du måste vara inloggad för att kunna se dina senaste besökare, logga in och kom tillbaks hit sen!';
		ui_bottom();
		exit;
	}

	//traffa_draw_user_div($_SESSION['login']['id'], $_SESSION);

	$query = 'SELECT uel.remote_user_id AS user_id, uel.timestamp, l.lastaction, l.username, l.lastlogon, u.image, u.birthday, u.gender, z.spot ';
	$query .= 'FROM user_event_log AS uel, login AS l, userinfo AS u, zip_codes AS z ';
	$query .= 'WHERE uel.user = "' . $_SESSION['login']['id'] . '" AND l.username NOT LIKE "borttagen" AND l.id = uel.remote_user_id AND u.userid = uel.remote_user_id ';
	$query .= 'AND uel.action = "profile_visit" AND z.zip_code = u.zip_code ';
	$query .= 'ORDER BY uel.timestamp DESC LIMIT 80';
	
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	
	echo '
		<div style="padding: 3px; font-size: 18px;">
			<div style="float: left; width: 43%;">Medlem</div>
			<div style="float: left; width: 23%;">Från</div>
			<div style="float: left; width: 33%; text-align: right;">Besökstid</div>
			<br style="clear: both;"/>
		</div>';
		$x=0;
	while($data = mysql_fetch_assoc($result))
	{
		/* Only display each user one time, should be done with DISTINCT in query, but I can't ger it to work */
		if(in_array($data['user_id'], $duplicates))
		{
			continue;
		}
		$duplicates[] = $data['user_id'];
			$x++;
	
		echo '
		<div style="padding: 3px; '.(($x%2) ? 'background: #fffaef;' : '') .'">
			<div style="float: left; width: 3%;"><img src="'. IMAGE_URL .'/famfamfam_icons/' . (($data['gender'] == 'f') ?  'user_female.png' : (($data['gender'] == 'm') ? 'user.png' : 'user_gray.png')) . '"/></div>
			<div style="float: left; width: 40%;">
				<div style="float: left; width: 90%;">
					<a href="/traffa/profile.php?id=' . $data['user_id'] . '"'. (in_array($data['image'], array(1, 2)) ? 'onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'<img src=' . IMAGE_URL . 'images/users/thumb/' . $data['user_id'] . '.jpg />\', \'trail\', true));"' : '') . '>' . $data['username'] . '</a>' . ($data['birthday'] != '0000-00-00'? ', ' . date_get_age($data['birthday']) . ' år' : '') . (($data['lastaction'] > time() - 600) ? '<span style="color: green;"> (Online)</span>' : '') . '
				</div>
				<div style="float: left; width: 10%; text-align: center;">
					'. (in_array($data['image'], array(1, 2)) ? '<a href="javascript:;" style="text-decoration: none;" onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'<img src=' . IMAGE_URL . 'images/users/thumb/' . $data['user_id'] . '.jpg />\', \'trail\', true));" onclick="window.open(\'http://www.hamsterpaj.net/avatar.php?id='.$data['user_id'].'\',\''.time().'\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=320, height=427\')"><img src="'. IMAGE_URL .'/famfamfam_icons/pictures.png"></a>' : '') .'
				</div>
				<br style="clear: both;"/>
			</div>
			<div style="float: left; width: 23%;">'. ((strlen($data['spot']) > 0) ? $data['spot'] : 'Okänd ort') .'</div>
			<div style="float: left; width: 33%; text-align: right;">'. fix_time($data['timestamp']) .'</div>
			<br style="clear: both;"/>
		</div>
		';

	}


	ui_bottom();
?>
