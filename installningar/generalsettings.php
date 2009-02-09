<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'profile.lib.php');
	$ui_options['menu_path'] = array('installningar');
	require(PATHS_INCLUDE . 'traffa-definitions.php');

	$ui_options['javascripts'][] = 'settings.js';

	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'settings.css';
	$ui_options['stylesheets'][] = 'profile_themes/all_themes.php';
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';

	$ui_options['title'] = 'Ändra dina inställningar på hamsterpaj.net';

	if(login_checklogin() != 1)
	{
		jscript_alert('Du måste vara inloggad för att komma åt denna sidan!');
		jscript_location('/');
		die();
	}

	if($_GET['action'] == 'perform_changes')
	{
		switch($_GET['type'])
		{
		case 'forum_preferences':
			$newdata['preferences']['forum_enable_smilies'] = ($_POST['forum_enable_smilies'] == 0) ? 0 : 1;
			$newdata['preferences']['forum_subscribe_on_create'] = ($_POST['forum_subscribe_on_create'] == 0) ? 0 : 1;
			$newdata['preferences']['forum_subscribe_on_post'] = ($_POST['forum_subscribe_on_post'] == 1) ? 1 : 0;
		break;
		case 'guestbook_preferences':
			$newdata['preferences']['gb_anti_p12'] = ($_POST['gb_anti_p12'] == 0) ? 'off' : 'on';
		break;
		case 'hetluft_settings':
			$newdata['preferences']['enable_hetluft'] = ($_POST['enable_hetluft'] == 1) ? 1 : 0;
			$newdata['userinfo']['occupation'] = (is_numeric($_POST['occupation'])) ? $_POST['occupation'] : 0;

			/* Find lifestyles */
			unset($_SESSION['lifestyles']);
			foreach(array_keys($lifestyles) AS $lifestyle)
			{
				if($_POST['lifestyle_' . $lifestyle] == 'true' && count($new_lifestyles) <= 3)
				{
					$new_lifestyles[] = $lifestyle;
					$_SESSION['lifestyles'][] = $lifestyle;
				}

				$query = 'DELETE FROM user_lifestyles WHERE user = "' . $_SESSION['login']['id'] . '"';
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

				foreach($new_lifestyles AS $lifestyle)
				{
					$query = 'INSERT INTO user_lifestyles (user, lifestyle) VALUES("' . $_SESSION['login']['id'] . '", "' . $lifestyle . '")';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
			}

		break;
		case 'optional_info':
			$newdata['userinfo']['gender'] = NULL;
			if($_POST['gender'] == 'm' || $_POST['gender'] == 'f')
			{
				$newdata['userinfo']['gender'] = $_POST['gender'];
			}
			$newdata['userinfo']['birthday'] = NULL;
			if(is_numeric($_POST['birth_year'])  && is_numeric($_POST['birth_month']) && is_numeric($_POST['birth_day']))
			{
				$newdata['userinfo']['birthday'] = (($_SESSION['login']['id'] == 827889) ? '2000-04-15' : $_POST['birth_year']) . '-' . $_POST['birth_month'] . '-' . $_POST['birth_day'];
			}
			$zip_code = str_replace(' ', '', $_POST['zip_code']);
			if(is_numeric($zip_code))
			{
				$query = 'SELECT x_rt90, y_rt90 FROM zip_codes WHERE zip_code = "' . $zip_code . '" LIMIT 1';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				$data = mysql_fetch_assoc($result);
				if(!empty($data))
				{
					$newdata['userinfo']['zip_code'] = is_numeric($zip_code) ? $zip_code : 0;
					$_SESSION['userinfo']['x_rt90'] = $data['x_rt90'];
					$_SESSION['userinfo']['y_rt90'] = $data['y_rt90'];
				}
				else
				{
					jscript_alert('Men det postnummret fanns ju inte i Sveariket. Fast vi ändrar allt annat åt dig ändå =)');
				}
			}

			$newdata['userinfo']['cell_phone'] = $_POST['cell_phone'];
			
			$newdata['traffa']['firstname'] = htmlspecialchars($_POST['firstname']);
			$newdata['userinfo']['gbrss'] = $_POST['gbrss'];
			$newdata['preferences']['gb_anti_p12'] = ($_POST['gb_anti_p12'] == 0) ? 'off' : 'on';
		break;
		case 'password':
			if(hamsterpaj_password(utf8_decode($_POST['password_old'])) != $_SESSION['login']['password'])
			{
				jscript_alert('Det där går inte, du måste skriva in ditt nuvarande lösenord, annars funkar inte skiten. Seså, gör om gör rätt!');
				jscript_go_back();
				exit;
			}
			if($_POST['password_new'] != $_POST['password_verify'])
			{
				jscript_alert('"Nytt lösenord" och "Upprepa nytt lösenord" måste ju vara samma, annars funkar det ju inte :(');
				jscript_go_back();
				exit;
			}
			
			$newdata['login']['password'] = hamsterpaj_password(utf8_decode($_POST['password_new']));
		break;
		}
		login_save_user_data($_SESSION['login']['id'], $newdata);
		session_merge($newdata);
		jscript_alert('Ändrat, fixat och donat :)');
		jscript_location($_SERVER['PHP_SELF']);
	}

	if($_POST['action'] == 'profile_theme')
	{
		$query = 'UPDATE userinfo SET profile_theme = "' . $_POST['theme'] . '" WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query);
		$_SESSION['userinfo']['profile_theme'] = $_POST['theme'];
	}



/* Frivillig information */
	$out .= '<a name="optional_info"></a>';

	$out .= rounded_corners_tabs_top($void, true);
	$out .= '<h2 style="margin-top: 0px;">Frivillig information</h2>' . "\n";
	$out .= '<form action="' . $_SERVER['PHP_SELF'] . '?action=perform_changes&type=optional_info" method="post" name="optional_info">';
	$out .= '<strong>Är du flicka eller pojke?</strong><br />' . "\n";
	$out .= '<input type="radio" name="gender" value="m" ';
	if($_SESSION['userinfo']['gender'] == 'm')
	{
		$out .= 'checked="1" ';
	}
	$out .= '/> Pojke&nbsp;&nbsp;';
	$out .= '<input type="radio" name="gender" value="f" ';
	if($_SESSION['userinfo']['gender'] == 'f')
	{
		$out .= 'checked="1" ';
	}
	$out .= '/> Flicka&nbsp;&nbsp;';
	$out .= '<input type="radio" name="gender" value="unknown" ';
	if($_SESSION['userinfo']['gender'] != 'm' && $_SESSION['userinfo']['gender'] != 'f')
	{
		$out .= 'checked="1" ';
	}
	$out .= '/> Hemligt<br /><br />';
	$out .= '<strong>När fyller du år?</strong><br />';
	$userbirthday = explode('-', $_SESSION['userinfo']['birthday']);
	$out .= '<select name="birth_year">';
	$out .= '<option value="unknown">-Årtal-</option>' ."\n";
	for($i = 2000; $i > 1930; $i--)
	{
		$out .= '<option value="' . $i . '"';
		$out .= ($i == $userbirthday[0]) ? ' selected="selected"' : null;
		$out .= '>' . $i . '</option>' . "\n";
	}
	$out .= '</select>' . "\n\n";
	$out .= '<select name="birth_month">';
	$out .= '<option value="01">-Månad-</option>';
	$months = array(1 => 'Januari', 2 => 'Februari', 3 => 'Mars', 4 => 'April', 5 => 'Maj', 6 => 'Juni', 7 => 'Juli', 8 => 'Augusti', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'December');
	foreach($months AS $key => $label)
	{
		if ($key < 10)
		{
			$key = "0" . $key;
		}
		$out .= '<option value="' . $key . '"';
		$out .= ($key == $userbirthday[1]) ? ' selected="selected"' : null;
		$out .= '>' . $label . '</option>' . "\n";
	}
	$out .= '</select>' . "\n\n";
	$out .= '<select name="birth_day">';
	$out .= '<option value="01">-Dag-</option>';
	for($i = 1;$i <= 31; $i++)
	{
		$out .= '<option value="';
		$out .= ($i < 10) ? '0' : null;
		$out .= $i;
		$out .= '"';
		$out .= ($i == $userbirthday[2]) ? ' selected="selected"' : null;
		$out .= '>';
		$out .= $i . '</option>' . "\n";
	}
	$out .= '</select>' . "\n";
	$out .= '<br /><br />' . "\n\n";
?>

<?php
	$out .= '<strong>Postnummer</strong><br />' . "\n";
	$out .= '<input autocomplete="off" type="text" name="zip_code" id="settings_zip_code" value="' . $_SESSION['userinfo']['zip_code'] . '" maxlength="6" style="width: 50px;" /><br />' . "\n";
	$out .= '<div id="zip_code_label"> </div>' . "\n";
	$out .= 'Vet du inte ditt postnummer? Sök på din adress på <a href="http://www.hitta.se" target="_blank">hitta.se</a>, dom har postnummer';
	$out .= '<br /><br />';


	$out .= '<strong>Förnamn</strong><br />' . "\n";
	$out .= '<input type="text" name="firstname" class="textbox" value="' . $_SESSION['traffa']['firstname'] . '" /><br /><br />' . "\n";
	$out .= '<strong>P12-sköld i gästboken</strong><br />'."\n";
	$out .= 'Vill du ha en "sköld" i din gästbok som visar på att du inte är intresserad av att ge bort din msn eller cama med okända? <input type="checkbox" name="gb_anti_p12" value="1" ' . ($_SESSION['preferences']['gb_anti_p12'] == 'on' ? 'checked="checked" ' : '') . ' />' . "\n";
	$out .= '<br />';
	$out .= '<strong>RSS-flöde i gästboken</strong><br />'."\n";
	$out .= 'Ska andra kunna prenumerera på din gästbok via RSS-flöde? <input type="radio" name="gbrss" value="1" '.($_SESSION['userinfo']['gbrss'] ? 'checked="checked" ' : '').' /> Ja <input type="radio" name="gbrss" value="0" '.(!$_SESSION['userinfo']['gbrss'] ? 'checked="checked" ' : '').'/> Nej'."\n";
	$out .= '<br />';
	$out .= '<br />';
	$out .= '<input type="submit" value="Spara frivillig information &raquo;" class="button_150" id="optional_info_submit" />';
	$out .= '</form>' . "\n";
	$out .= rounded_corners_tabs_bottom($void, true);


/* Lösenordsbyte */
	$out .= '<a name="change_password"></a>';
	$out .= rounded_corners_tabs_top($void, true);
	$out .= '<h2 style="margin-top: 0px;">Byt lösenord</h2>' . "\n";
	$out .= '<form action="' . $_SERVER['PHP_SELF'] . '?action=perform_changes&type=password" method="post">' . "\n";
	$out .= '<table><tr style="font-weight: bold;"><td>Nuvarande lösenord</td><td>Nytt lösenord</td><td>Upprepa nytt lösenord</td></tr>' . "\n";
	$out .= '<tr>' . "\n";
	$out .= '<td><input type="password" name="password_old" class="textbox" /></td>' . "\n";
	$out .= '<td><input type="password" name="password_new" class="textbox" /></td>' . "\n";
	$out .= '<td><input type="password" name="password_verify" class="textbox" /></td>' . "\n";
	$out .= '</tr></table><br />' . "\n";
	$out .= '<input type="submit" class="button_80" value="Byt lösenord &raquo;" />' . "\n";
	$out .= '</form>' . "\n";
	$out .= rounded_corners_tabs_bottom($void, true);

	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
