<?php
function traffaFetchProfile($userid)
{
	$sql = 'SELECT * FROM traffa WHERE userid = "' . $userid . '" LIMIT 1';
	$result = mysql_query($sql) or die('Query failed: ' . mysql_error());		
	return mysql_fetch_assoc($result);
}
	
function traffaUpdateProfile($userid, $firstname, $height, $haircolor, $personality, $single, $cam, $bgimage, $presentation, $imgframe)
{
	$sql = 'UPDATE traffa SET firstname = "' . $firstname . '", height = "' . $height . '", haircolor ="' . $haircolor . '",
	personality = "' . $personality . '", single = "' . $single . '", cam = "' . $cam . '", bgimage = ' . $bgimage . ', presentation = "' . $presentation . '", imgframe = "' . $imgframe . '" WHERE userid = "' . $userid . '" LIMIT 1';

	mysql_query($sql) or die('Query failed: ' . mysql_error($traffa_dbconn));

	to_logfile('notice', __FILE__, __LINE__, 'Deprecated function traffaUpdateProfile() was called', $_SERVER['REQUEST_URI']);
	return true;
}

function traffa_draw_user_div($userid, $info = null)
{
	/* If you already have information from the database, please pass it on in the $info
	   parameter. The information should be formatted like this:
	   $info['login']['username'] = 'Foo';
	   $info['userinfo']['birthday'] = '1970-01-01';
	   Note that it isn't necessary to have complete info, the script will fetch all info
	   not sent.
	   Please note that this function returns all the data used, you may use it further in your script.
	   Return will look like this:
	   $return['status'] = 'success'/'fail';
	   $return['reason'] = null or error explanation.
	   $return['login']['lastaction'] = 118371272;
	*/
	
	/* $info is unset further down and the snyggve-info isn't fetched in this function. */
	if($info['snyggve'] == 1)
	{
		$snyggve = 1;
	}
	
	if($userid < 1)
	{
		to_logfile('error', __FILE__, __LINE__, 'traffa_draw_user_div() was called without userid', print_r(debug_backtrace(), true));
	}
	
	
	$fetch['login'] = array('username', 'lastaction', 'lastrealaction', 'lastlogon', 'regtimestamp', 'userlevel');
	$fetch['userinfo'] = array('contact1', 'contact2', 'gender', 'birthday', 'zip_code', 'image', 'current_action', 'cell_phone', 'user_status');
	$fetch['traffa'] = array('firstname', 'color_theme');
	$fetch['preferences']= array('activate_current_action');
	
	/* Remove already known fields from the fetch array */
	foreach($info AS $table)
	{
		foreach($table AS $row)
		{
			unset($fetch[$table][$row]);
		}
	}

		$userinfo = login_load_user_data($userid, $fetch);

	if(!is_array($userinfo))
	{
		to_logfile('error', 'userinfo was not an array, as expected.', __FILE__, __LINE__);
		die('<p class="error">Ett internt fel orsakades. Visningen av sidan har stoppats och hamsterpajs utvecklingsavdelning har underrättats.</p>');
	}
	
	/* Inject already known fields to the userinfo array */
	foreach($info AS $table)
	{
		foreach($table AS $row)
		{
			$userinfo[$table][$row] = $info[$table][$row];
		}
	}
	unset($info);

	switch($userinfo['traffa']['color_theme'])
	{
		case '1':
			$profile_colors['light'] = '#edf4fd';
			$profile_colors['background'] = '#c9ddf9';
			$profile_colors['dark'] = '#7ba0cf';
			$profile_colors['border'] = '#3f5879';
			break;
		case '2':
			$profile_colors['light'] = '#f1edfd';
			$profile_colors['background'] = '#d1c9f9';
			$profile_colors['dark'] = '#897bcf';
			$profile_colors['border'] = '#493f79';
			break;
		case '3':
			$profile_colors['light'] = '#faedfd';
			$profile_colors['background'] = '#efc9f9';
			$profile_colors['dark'] = '#bb7bcf';
			$profile_colors['border'] = '#6b3f79';
			break;
		case '4':
			$profile_colors['light'] = '#fdedf7';
			$profile_colors['background'] = '#f9c9e7';
			$profile_colors['dark'] = '#cf7bb0';
			$profile_colors['border'] = '#793f64';
			break;
		case '5':
			$profile_colors['light'] = '#fdeeed';
			$profile_colors['background'] = '#f9c9c9';
			$profile_colors['dark'] = '#cf7b7d';
			$profile_colors['border'] = '#793f40';
			break;
		case '6':
			$profile_colors['light'] = '#fdf7ed';
			$profile_colors['background'] = '#f9e6c9';
			$profile_colors['dark'] = '#cfaa7b';
			$profile_colors['border'] = '#79603f';
			break;
		case '7':
			$profile_colors['light'] = '#fafded';
			$profile_colors['background'] = '#f0f9c9';
			$profile_colors['dark'] = '#c1cf7b';
			$profile_colors['border'] = '#6f793f';
			break;
		case '8':
			$profile_colors['light'] = '#f0fded';
			$profile_colors['background'] = '#d4f9c9';
			$profile_colors['dark'] = '#8dcf7b';
			$profile_colors['border'] = '#4b793f';
			break;
		case '9':
			$profile_colors['light'] = '#edfdf4';
			$profile_colors['background'] = '#c9f9dc';
			$profile_colors['dark'] = '#7bcf9a';
			$profile_colors['border'] = '#3f7954';
			break;
		case '10':
			$profile_colors['light'] = '#edfdfd';
			$profile_colors['background'] = '#c9f9f8';
			$profile_colors['dark'] = '#7bcfcd';
			$profile_colors['border'] = '#3f7978';
			break;
		default:
			$profile_colors['light'] = '#edf4fd';
			$profile_colors['background'] = '#c9ddf9';
			$profile_colors['dark'] = '#7ba0cf';
			$profile_colors['border'] = '#3f5879';
			break;
	}

	if($userinfo['userinfo']['image'] == 1 || $userinfo['userinfo']['image'] == 2)
	{
		$image_code = ui_avatar($userid, array('style' => 'float: left; margin: 5px; border: 1px solid #3f657a;'));
	}
	elseif($userid == $_SESSION['login']['id'])
	{
		$image_code = '<div class="avatar" style="float: left;"><img src="http://images.hamsterpaj.net/images/noimage.png" /><input type="button" class="button" value="Fixa bild" onclick="window.location=\'/avatar-settings.php\';" /></div>' . "\n";
	}
	else
	{
		$image_code = '<img src="http://images.hamsterpaj.net/images/noimage.png" class="avatar" style="float: left; margin: 5px;" />';
	}
		
	$gender = '';
	if($userinfo['userinfo']['gender'] == 'm')
	{
		$gender = 'pojke ';
	}
	elseif($userinfo['userinfo']['gender'] == 'f')
	{
		$gender = 'flicka ';
	}

	$location = (strlen($userinfo['userinfo']['geo_location']) > 0) ? 'från ' . $userinfo['userinfo']['geo_location'] : '';
	if($_SESSION['userinfo']['x_rt90'] > 0 && $userinfo['userinfo']['x_rt90'] > 0 && $userinfo['userinfo']['zip_code'] != $_SESSION['userinfo']['zip_code'])
	{
		$location .= ', ' . rt90_readable(rt90_distance($_SESSION['userinfo']['x_rt90'], $_SESSION['userinfo']['y_rt90'], $userinfo['userinfo']['x_rt90'], $userinfo['userinfo']['y_rt90']));
	}
	if($userinfo['userinfo']['x_rt90'] > 0)
	{
		/* Note RT90 Y and X values are flipped, due to a "bug" at hitta.se */
		/* Reference: daniel.eklund@hitta.se */
		$hittapunktse_url = 'http://www.hitta.se/LargeMap.aspx?ShowSatellite=false&pointX=' . $userinfo['userinfo']['y_rt90'];
		$hittapunktse_url .= '&pointY=' . $userinfo['userinfo']['x_rt90'] . '&z=4&name=' . $userinfo['login']['username'];
		$hittapunktse_url .= '&cy=' . $userinfo['userinfo']['x_rt90'] . '&cx' . $userinfo['userinfo']['y_rt90'];

		$hittapunktse_url = 'http://www.hitta.se/LargeMap.aspx?ShowSatellite=false&pointX=' . $userinfo['userinfo']['y_rt90'];
		$hittapunktse_url .= '&pointY=' . $userinfo['userinfo']['x_rt90'] . '&cx=' . $userinfo['userinfo']['y_rt90'];
		$hittapunktse_url .= '&cy=' . $userinfo['userinfo']['x_rt90'] . '&z=6&name=' . $userinfo['login']['username'];

		$location_button = '<input type="button" value="Visa på karta" class="button_90" onclick="';
		$location_button .= "window.open('$hittapunktse_url', 'user_map_$userid', 'location=false, width=750, height=500');";
		$location_button .= '" style="float: right;"/>' . "\n";
	}
	if($userid == 20702)
	{
		$location = 'från Internet ';
	}
	
	$age = '';
	if($userinfo['userinfo']['birthday'] != '0000-00-00')
	{
		$age = date_get_age($userinfo['userinfo']['birthday']) . ' år ';
	}
	
	$status = login_onlinestatus($userinfo['login']['lastaction'], $userinfo['login']['lastrealaction']);
//	$onlinestatus = '<span style="color: grey;">Offline</span>';
//	$onlinestatus = '<span style="font-size: 14px;">Loggade senast in ' . fix_time($userinfo['login']['lastlogon']) . '</span>' . "\n";
	switch($status['handle'])
	{
		case 'offline':
			if($userinfo['login']['lastrealaction'] == 0)
			{
				$onlinestatus = '<span style="font-size: 14px;">Sågs senast ' . fix_time($userinfo['login']['lastlogon']) . '</span>' . "\n";
			}
			else
			{
				$onlinestatus = '<span style="font-size: 14px;">Sågs senast ' . fix_time($userinfo['login']['lastrealaction']) . '</span>' . "\n";				
			}
			break;
		case 'online':
			$hours = floor(($userinfo['login']['lastaction'] - $userinfo['login']['lastlogon'])/3600);
			$minutes = floor((($userinfo['login']['lastaction'] - $userinfo['login']['lastlogon']) - $hours * 3600) /60);
			$onlinestatus = '<span style="color: green; font-size: 14px;">Online </span><span style="font-size: 14px;"> sedan ';
			$onlinestatus .= ($hours > 0) ? $hours . ' timmar och ' : null;
			$onlinestatus .= $minutes . ' minuter</span>';
			break;
		case 'idle':
			$onlinestatus = $status['label'] . ' sedan ' . date('H:i', $userinfo['login']['lastrealaction']);
			break;
	}

	$contact1 = (strlen($userinfo['userinfo']['contact1']) > 0) ? parseContact($userinfo['userinfo']['contact1']) : array('label' => 'Kontaktsätt', 'link' => '-');
	$contact2 = (strlen($userinfo['userinfo']['contact2']) > 0) ? parseContact($userinfo['userinfo']['contact2']) : array('label' => 'Kontaktsätt', 'link' => '-');

	$firstname = (strlen($userinfo['traffa']['firstname']) > 0) ? $userinfo['traffa']['firstname'] : '-';

	$right_now = (strlen($userinfo['userinfo']['current_action']) > 0) ? $userinfo['userinfo']['current_action'] : 'Okänt';

	if($userinfo['login']['lastlogon'] > strtotime(date('Y-m-d')))
	{
		$lastlogon = 'Idag ' . date('H:i', $userinfo['login']['lastlogon']);
	}
	elseif($userinfo['login']['lastlogon'] > strtotime(date('Y-m-d')) - 86400)
	{
		$lastlogon = 'Igår ' . date('H:i', $userinfo['login']['lastlogon']);
	}
	elseif($userinfo['login']['lastlogon'] > strtotime(date('Y-m-d')) - 518400)
	{
		$weekdays = array('Söndags', 'Måndags', 'Tisdags', 'Onsdags', 'Torsdags', 'Fredags', 'Lördags');
		$lastlogon = 'I ' . $weekdays[date('w', $userinfo['login']['lastlogon'])] . ' klockan ' . date('H:i', $userinfo['login']['lastlogon']);
	}
	else
	{
		$lastlogon = date('Y-m-d H:i', $userinfo['login']['lastlogon']);
	}

	echo '<div style="height: 120px; margin: -3px; margin-bottom: 3px; background: ' . $profile_colors['background'] . '; border-bottom: 1px solid ' . $profile_colors['border'] . '; padding: 3px;">' . "\n";
	if(strlen($userinfo['userinfo']['user_status']) > 0)
	{
		echo '<h3>' . $userinfo['userinfo']['user_status'] . '</h3>' . "\n";
	}
	echo $image_code . "\n";
	echo '<div style="width: 545px; float: left;">' . "\n";
	echo '<div style="background: white;">' . "\n";
	echo '<h2 style="margin: 0px; font-size: 19px;">' . $userinfo['login']['username'] . ' - ' . $gender . $age .  $location . $location_button . '</h2>' . "\n";
	echo '<h3>' . $onlinestatus . '</h3>' . "\n";
	echo '</div>' . "\n";

	echo '<div style="width: 400px;">' . "\n"; 
	echo '<div style="float: left; clear: right; width: 50%;"><span style="font-weight: bold;">Förnamn</span><br />' . $firstname . '</div>';
	echo '<div style="float: left; clear: right; width: 50%;"><span style="font-weight: bold;">Blev medlem: </span><br/> ' . date('Y-m-d', $userinfo['login']['regtimestamp']) . '</div><br />' . "\n";
	echo '<div style="float: left; clear: both; width: 50%;"><span style="font-weight: bold;">' . $contact1['label'] . '</span><br />' . $contact1['link'] . '</div>' . "\n";
	echo '<div style="float: left; clear: right; width: 50%;"><span style="font-weight: bold;">' . $contact2['label'] . '</span><br />' . $contact2['link'] . '</div>' . "\n";
	echo '</div>' . "\n";

	echo '</div>' . "\n";
	echo '</div>' . "\n";	

	echo '<div style="margin: -3px; border-bottom: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['dark'] . '; height: 25px; font-weight: bold; color: white;">' . "\n";

	$links[] = array('width' => 95, 'label' => 'Presentation', 'uri' => '/traffa/profile.php?id=' . $userid);
	$links[] = array('width' => 85, 'label' => 'Gästbok', 'uri' => '/traffa/guestbook.php?view=' . $userid);
	if ($_SESSION['login']['id'] == $userid)
	{
		$links[] = array('width' => 150, 'label' => 'Mina meddelanden', 'uri' => '/traffa/messages.php');
		$links[] = array('width' => 100, 'label' => 'Mina besök', 'uri' => '/traffa/my_visitors_joel.php');
	}
	else
	{
		$links[] = array('width' => 185, 'label' => 'Skicka privatmeddelande', 'uri' => '/traffa/messages.php?action=compose&amp;recipient_username=' . $userinfo['login']['username'] . '&amp;recipient_id=' . $userid);
	}

	foreach($links AS $current)
	{
		echo '<div style="float: left; width: ' . $current['width'] . 'px; line-height: 25px; border-right: 1px solid #3f657a; height: 25px; text-align: center;"><a href="' . $current['uri'] . '" style="color: white;">' . $current['label'] . '</a></div>' . "\n";
	}

	if($userid != $_SESSION['login']['id'] && login_checklogin())
	{
		echo '<input type="button" class="button_80" style="float: right; margin-right: 5px; margin-top: 3px;" value="Kompis" onclick="window.open(\'/traffa/friends_popup.php?user_id=' . $userid . '\', \'cpinternetexplorer\', \'location=no, width=200, height=100\');" />' . "\n";
		echo '<input type="button" class="button_100" style="float: right; margin-right: 5px; margin-top: 3px;" value="Rapportera" onclick="abuse_report(\'user\', ' . $userid . ');" />' . "\n";
	}
	
	if($_SERVER['REQUEST_URI'] == '/traffa/my_visitors.php')
	{
		treasure_item(9);
	}
	
	echo '</div>' . "\n";
	
}
	
	function traffaDrawUserDiv($id, $username, $gender, $birthday, $lastaction, $geo_location, $ip)
	{
		return false; 
	}

?>
