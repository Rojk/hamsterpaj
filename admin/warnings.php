<?
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'profile.lib.php');
	require(PATHS_LIBRARIES . 'warnings.lib.php');
	
	
	//----------------------
	$ui_options['menu_path'] = array('admin', 'warnings');
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'warnings_dot_php.css';
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['title'] = 'Varningar - Hamsterpaj.net';
	
	
	define(THIS_URI, $_SERVER["REQUEST_URI"]);
	//-------------------
	
	
	if (!is_privilegied('warnings_admin'))
	{
		ui_top();
		echo "Eeeemil!";
		ui_bottom();
		exit;
	}
	$one_week_ago = time() - 604800;

		$tab['Varna användare'] = '/admin/warnings.php';
		$tab['Aktiva varningar'] = '/admin/warnings.php?action=active';
		$tab['Välstekta användare'] = '/admin/warnings.php?action=welldone';
		$tab['Varningshistorik'] = '/admin/warnings.php?action=viewhistory';
		
		
		$out .= '<div style="height: 10px"></div>' . "\n";
		$zebra = true;
		
		if (THIS_URI == $tab['Aktiva varningar'])
		{
			$query  = 'SELECT uw.*, l.username AS user_username, l.id AS user_user_id, adm.username AS admin_username, adm.id AS admin_user_id';
			$query .= ' FROM user_warnings AS uw, login AS l, login AS adm';
			$query .= ' WHERE l.id = uw.user_id AND adm.id = uw.set_by';
			$query .= ' AND uw.timestamp > ' . $one_week_ago;
			$query .= ' ORDER BY uw.timestamp DESC';
			
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			$out .= '<h2>Aktiva varningar</h1>' . "\n";
			$out .= '<p>Varningar som har utfärdats inom en vecka tillbaks i tiden</p>' . "\n";
			$out .= warnings_render_table($result);
		}
		
		elseif (THIS_URI == $tab['Välstekta användare'])
		{
			// die('Fulkod :( Fast jag orkar inte laga det nu, det är för varmt! /Joel');
			// Don't remove line above...!
			// Hihi / Joar
			$out .= '<h2>Välstekta användare</h1>' . "\n";
			$out .= '<p>Här ser du användare som har fått 3 eller flera varningar inom den senaste veckan, Hamsterpaj.net:s 
			policy säger att vi ska banna användaren då, men det är egentligen upp till dig.</p>';
			$out .= '<table style="min-width: 500px;">';
			$out .= '<tr><td><strong>Användare:</strong></td><td>&nbsp;</td></tr>';
			$query = 'SELECT * FROM user_warnings WHERE timestamp > ' . $one_week_ago . ' ORDER BY timestamp DESC';
			$result = mysql_query($query) or $out .= mysql_error();
			while ($row = mysql_fetch_assoc($result))
			{
				$user[$row['user_id']][] = $row['timestamp'];
			}
			foreach ($user AS $user => $warnings)
			{
				if (count($warnings) >= 3)
				{
					$count_warnings = count($warnings);
					
					$query = 'SELECT username FROM login WHERE id LIKE ' . $user . ' LIMIT 1';
					$result = mysql_query($query);
					while ($row = mysql_fetch_assoc($result))
					{
						$username = $row['username'];
					}
					$out .= '<tr><td><strong><a href="/traffa/user_facts.php?user_id=' . $user . '">' . $username . '</a></strong></td>
					<td style="text-align: right;"><a style="border-bottom: thin dotted;" href="/admin/warnings.php?action=viewhistory&user_id=' . $user. '">Varningshistorik för användaren.</a></td></tr>';
				}
			}
			$out .= '</table>';
		}
		
		elseif (substr(THIS_URI, 0, 38) == $tab['Varningshistorik'])
		{
			$override = true;
			if (isset($_GET["username"]) || isset($_GET['user_id']) && is_numeric($_GET['user_id']))
			{
				$query  = 'SELECT 
				uw.*, 
				l.username AS user_username, 
				l.id AS user_user_id, 
				adm.username AS admin_username, 
				adm.id AS admin_user_id';
				
				$query .= ' FROM 
				user_warnings AS uw, 
				login AS l, 
				login AS adm';
				
				$query2 = 'SELECT regtimestamp FROM login';
				
				if (isset($_GET['username']))
				{
					$query .= ' WHERE 
					l.username = "' . $_GET["username"] . '" AND
					l.id = uw.user_id AND 
					adm.id = uw.set_by';
					$username_or_user_id = $_GET['username'];
					
					$query2 .= ' WHERE
					username = "' . $_GET['username'] . '"';
				}
				else
				{
					$query .= ' WHERE 
					l.id = "' . $_GET["user_id"] . '" AND
					l.id = uw.user_id AND 
					adm.id = uw.set_by';
					$username_or_user_id = $_GET['user_id'];
					
					$query2 .= ' WHERE
					id = ' . $_GET['user_id'];
				}
				
				$query2 .= ' LIMIT 1';
								
				$query .= ' ORDER BY uw.timestamp DESC';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				$result2 = mysql_query($query2) or report_sql_error($query2, __FILE__, __LINE__);
				
				$data2 = mysql_fetch_assoc($result2);
				$user_regtimestamp = $data2['regtimestamp'];
				$time_at_hp = floor((time() - $user_regtimestamp) / 60 / 60 / 24);
				
				$out .= '<h2>Varningshistorik för ' . $username_or_user_id . '.</h2>' . "\n";
				$out .= '<p>Här ser du alla varningar som ' . $username_or_user_id . ' har fått under sina ' . $time_at_hp . ' dagar på Hamsterpaj :)</p>';
				$out .= warnings_render_table($result);
				
			}
			else
			{
				
				$out .= '<h2>Varningshistorik</h2>' . "\n";
				$out .= '<p>Här visas ALLA varningar sedan tidernas begynnelse, de varningar som är aktiva visas dessutom i orange :)</p>' . "\n";
				
				$query  = 'SELECT uw.*, l.username AS user_username, l.id AS user_user_id, adm.username AS admin_username, adm.id AS admin_user_id';
				$query .= ' FROM user_warnings AS uw, login AS l, login AS adm';
				$query .= ' WHERE l.id = uw.user_id AND adm.id = uw.set_by';
				$query .= ' ORDER BY uw.timestamp DESC';
				
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				$out .= '<div style="background: #ccc; height: 1px; margin: 4px 0px 4px 0px;" ></div>
				<form action="/admin/warnings.php" method="get">
				<input type="hidden" name="action" value="viewhistory" />
				<label for="username">Titta på en specifik användare</label>';
				$out .= (strlen($_GET['username']) > 0) ? '<input type="text" name="username" value="' . $_GET['username'] . '" />' : '<input type="text" name="username" />';
				$out .= '<input type="submit" value="Jihad!" />
				</form>
				<div style="background: #ccc; height: 1px; margin: 4px 0px 4px 0px;" ></div>';
				$out .= warnings_render_table($result);
				
			}
		}
		
		elseif (THIS_URI == $tab['Varna användare'] && isset($_POST['username']) && isset($_POST['reason']))
		{
			if (strlen($_POST['username']) > 0 && strlen($_POST['reason']) > 0)
			{
				$query = 'SELECT username, id FROM login WHERE username LIKE "' . $_POST['username'] . '" LIMIT 1';
				$result = mysql_query($query) or die(mysql_error());
				$row = mysql_fetch_assoc($result);
				if ($row['id'] > 0)
				{
					$user_id = $row['id'];
					$query = 'INSERT INTO user_warnings (user_id, set_by, timestamp, reason) 
					VALUES (\'' . $user_id . '\', \'' . $_SESSION['login']['id'] . '\', \'' . time() .'\', \'' . $_POST['reason'] . '\');';
					$query2 = 'UPDATE userinfo SET last_warning = ' . time() . ' WHERE userid = ' . $user_id . ' LIMIT 1';

					$query3 = 'INSERT INTO user_abuse (timestamp, admin, freetext)
					VALUES (' . time() . ', ' . $_SESSION['login']['id'] . ', "Varnad: ' . $_POST['reason'] . '")';

					mysql_query($query) or die(mysql_error() . '<br />' . $query);
					mysql_query($query2) or report_sql_error($query2, __FILE__, __LINE__);
					mysql_query($query3) or report_sql_error($query3, __FILE__, __LINE__);
					
					log_admin_event('user warned', $row['username'], $_SESSION['login']['id'], $user_id, $user_id);
						
					$message  = 'Hej, du har blivit varnad med anledningen:' . "\n";
					$message .= '-----' . "\n";
					$message .= '%REASON%' . "\n";
					$message .= '-----' . "\n\n";
					$message .= 'Varningen håller i en vecka. dvs tills ' . date("d/m H:i", time() + 604800) . ' så mitt råd är att du tar det lugnt och inte besvärar någon i onödan :)' . "\n\n";
					$message .= 'Med vänliga hälsningar Hamsterpaj Crew.';
					$guestbook_message = array(
						'sender' => 2348,
						'recipient' => intval($user_id),
						'message' => mysql_real_escape_string(str_replace(
							array('%REASON%',      '%ADMIN%'),
							array($_POST['reason'], $_SESSION['login']['username']),
							$message
						))
					);
					
					//preint_r($guestbook_message);
					
					guestbook_insert($guestbook_message);
					
					$out .= '<h2>Användaren hittades!</h1>' . "\n";
					$out .= 'Användarnamnet <strong>' . $_POST['username'] . '</strong> hittades och hade ID <strong>' . $user_id . '</strong> :)<br />' . "\n";
					$out .= 'Tidpunkt: ' . time() . '<br />';
					$out .= 'Anledning var:<br />' . "\n";
					$out .= $_POST['reason'] . "<br />\n";
					$out .= '<strong>Användaren har nu tilldelats en varning!</strong>';
					$out .= '<br /> <br /><a href="/admin/warnings.php">Gå tillbaka</a>' . "\n";
					
				}
				else
				{
					$out .= '<h2>Användaren hittades inte!</h1>' . "\n";
					$out .= 'Användarnamnet matchade inte något som fanns i databasen.<br />
					<a href="/admin/warnings.php">Gå Tillbaka</a>' . "\n";
				}
			}
			else
			{
				$out .= '<h2>Dummer!</h1>' . "\n";
				$out .= '<p>Du fyllde inte i alla fält.</p>
				<a href="/admin/warnings.php">Gå Tillbaka</a>' . "\n";
			}
				
		}
		else
		{
			$out .= '<h2>Varna användare</h1>' . "\n";
			$out .= '<p>Här kan du varna fulingar eller <span style="text-decoration: line-throught:">fiender</span>.</p>' . "\n";
			$out .= '<form action="/admin/warnings.php" method="post">' . "\n";
			$out .= '<strong>Användarnamn</strong> - <em>stora eller små bokstäver spelar ingen roll.</em><br />' . "\n";
			$out .= (isset($_GET["username"])) ? '<input type="text" readonly name="username" value="' . $_GET["username"] . '" /> Användarnamn hämtat via $_GET-parameter. Klicka <a href="/admin/warnings.php">här</a> för att skriva in fritext.<br />' . "\n" : '<input type="text" name="username" /><br />' . "\n";
			$out .= '<br />';
			$out .= '<strong>Anledning</strong> - <em>nödvändig, mycket nödvändig.</em><br />' . "\n";
			$out .= '<textarea style="width: 500px;" name="reason"></textarea><br />' . "\n";
			$out .= '<input type="submit" value="Skicka in begäran" /><br />' . "\n";
			$out .= '</form>' . "\n";
		}
		
		
		foreach ($tab AS $label => $href)
		{
			if ($override == true && $label == 'Varningshistorik')
			{
				$rounded_corners_tabs_options['tabs'][] = array('href' => $href, 'label' => $label, 'current' => true);
			}
			elseif ($label == 'Varna användare' && stristr(THIS_URI, $href . '?username='))
			{
				$rounded_corners_tabs_options['tabs'][] = array('href' => $href, 'label' => $label, 'current' => true);
			}
			else
			{
				$rounded_corners_tabs_options['tabs'][] = (THIS_URI == $href) ? 
				array('href' => $href, 'label' => $label, 'current' => true) : 
				array('href' => $href, 'label' => $label);
			}
		}
		
		$out_first .= rounded_corners_tabs_top($rounded_corners_tabs_options, true);
		
		$out_last .= rounded_corners_tabs_bottom($rounded_corners_tabs_options, true);
//	$out .= substr(THIS_URI, 0, 38).'<br />';
//	$out .= (substr(THIS_URI, 0, 38) == $tab['Varningshistorik']) ? 'true' : 'false';
	
	
	ui_top($ui_options);
	echo $out_first;
	echo $out;
	echo $out_last;
	ui_bottom();
	
?>
