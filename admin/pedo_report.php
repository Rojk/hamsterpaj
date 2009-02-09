<?
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'profile.lib.php');
	
	//----------------------
	$ui_options['menu_path'] = array('hamsterpaj');

	//$ui_options['javascripts'][] = 'zip_codes.js';
	$ui_options['javascripts'][] = 'settings.js';
	
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'settings.css';
	$ui_options['stylesheets'][] = 'profile_themes/all_themes.php';
	//-------------------
	
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'profile_presentation_change.css';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['stylesheets'][] = 'flags_customize.css';
	
	
	$ui_options['title'] = 'Pedofilvarning - Hamsterpaj.net';
	ui_top($ui_options);
	
	$rounded_corners_tabs_options = array();
	if (is_privilegied('pedo_report_reporter'))
	{
		
		$rounded_corners_tabs_options['tabs'][] = array('href' => '?action=submit', 'label' => 'Anmäl');
		if (is_privilegied('pedo_report_admin'))
		{
			$rounded_corners_tabs_options['tabs'][] = array('href' => '?action=new', 'label' => 'Obehandlade');
			$rounded_corners_tabs_options['tabs'][] = array('href' => '?action=active', 'label' => 'Aktivt väntande');
			$rounded_corners_tabs_options['tabs'][] = array('href' => '?action=passive', 'label' => 'Passivt väntande');
		}
		$out .= rounded_corners_tabs_top($rounded_corners_tabs_options); 
			
		if (isset($_POST['username']) && isset($_POST['anledning']))
		{
			$query = 'SELECT username, id FROM login WHERE username LIKE "' . $_POST['username'] . '" LIMIT 1';
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result))
			{
				if ($row['id'] > 0)
				{
					$pedo_id = $row['id'];
					$query = 'INSERT INTO pedo_report (pedo_id, submit_id, timestamp, reason) VALUES (\'' . $pedo_id . '\', \'' . $_SESSION['login']['id'] . '\', \'' . date('U') .'\', \'' . $_POST['anledning'] . '\');';
					mysql_query($query);
					
					$r_options['color'] = 'orange_deluxe';
					//$out .= rounded_corners_top($r_options, true);
					$out .= 'Du skrev in användarnamnet ' . $_POST['username'] . ' som hittades och hade ID ' . $pedo_id . '<br />';
					$out .= 'Din anledning var:<br />';
					$out .= $_POST['anledning'];
					$out .= '<br /> <br /><a href="/admin/pedo_report.php">Gå tillbaka</a>';
					//$out .= rounded_corners_bottom($r_options, true);
					
				}
				else
				{
					
					$rounded = 'Användarnamnet matchade inte något som fanns i databasen.<br />
					<a href="/admin/pedo_report.php">Gå Tillbaka</a>';
					$out .= $rounded;
					//$out .= rounded_corners($rounded, $r_options);
				}
			}
		}
		else
		{
			$rounded = '<h1 style="margin-top: 0px">Begär utredning av misstänkt pedofil-funktionen</h1>';
			$rounded .= '<p>Nej, jag kunde inte komma på något bättre namn, det måste vara h4xblandningen som Johan (nej, inte den Johan) har proppat i mig.</p>';
			$rounded .= '<p>OnT: här nedanför skriver du in relevant information om en skäligen misstänkt pedofil, och eftersom det bara är Admins och Sysops som har privilegier att rota i de misstänktas filer så vill vi att du inte postar onödiga saker, men ser du något misstänkt så tveka inte att kasta dig över de små knapparna med konstiga krumelurer på.</p>';
			$rounded .= '<form action="/admin/pedo_report.php" method="post">' . "\n";
			$rounded .= 'Användarnamn: stora eller små spelar ingen roll.' . "\n";
			$rounded .= '<input type="text" name="username" />' . "\n";
			$rounded .= 'Anledning: nödvändig, mycket nödvändig.' . "\n";
			$rounded .= '<textarea style="width: 500px;" name="anledning"></textarea>' . "\n";
			$rounded .= '<input type="submit" value="Skicka in begäran" />' . "\n";
			$rounded .= '</form>' . "\n";
			$out .= nl2br($rounded);
			//$out .= rounded_corners(nl2br($rounded));
		}
		
		$out .= rounded_corners_tabs_bottom();
	}
	
	echo $out;
	ui_bottom();
	
?>