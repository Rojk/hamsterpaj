<?php
	require('../include/core/common.php');
	try
	{
		require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
		require(PATHS_INCLUDE . 'libraries/photoblog_preferences.lib.php');
		require(PATHS_INCLUDE . 'libraries/profile.lib.php');
		
		if (!is_privilegied('igotgodmode'))
		{
			throw new Exception('Den här delen är inte uppe för allmänheten än ;)');
		}
		
		$ui_options['stylesheets'][] = 'photoblog.css.php';
		$ui_options['javascripts'][] = 'jquery-ui-slider.js';
		$ui_options['javascripts'][] = 'jquery-ui-datepicker.js';	
		$ui_options['javascripts'][] = 'photoblog.js';
		$ui_options['ui_modules_hide'] = true;
		
		$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
		
		$out .= '<div id="photoblog_menu">
		<ul>
		<li>
			<a href="/fotoblogg/">
			<img src="http://images.hamsterpaj.net/photoblog/menu_my_diary.png" alt="Min dagbok" />
			</a>
		</li>
		<!--<li>
			<a href="/fotoblogg/Lef/">Lefs dagbok</a>
		</li>-->
		<li>
			<a href="/fotoblogg/ladda_upp/">
			<img src="http://images.hamsterpaj.net/photoblog/menu_upload.png" alt="Ladda upp" />
			</a>
		</li>
		<li>
			<a href="/fotoblogg/instaellningar/">
			<img src="http://images.hamsterpaj.net/photoblog/menu_settings.png" alt="Inställningar" />
			</a>
		</li>
		</ul>
		</div>' . "\n";
		
		switch ($uri_parts[2])
		{
			case 'instaellningar':
				if ( login_checklogin() )
				{
					require('instaellningar.php');
				}
				else
				{
					throw new Exception('Inga inställningar för dig!<br />Logga in så kanske det går bättre ;)');
				}
			break;
			
			case 'ladda_upp':		
				require('ladda_upp.php');
			break;
			
			case 'ordna':
				require('ordna.php');
			break;
				
			default:
				
				if ( isset($username) && preg_match('/^[a-zA-Z0-9-_]+$/', $username) && strtolower($username) != 'borttagen' )
				{
					$sql = 'SELECT id FROM login WHERE username = "' . $username . '" LIMIT 1';
					$result = mysql_query($sql);
					$data = mysql_fetch_assoc($result);
					$user_id = $data['id'];
					
					$sql = 'SELECT user_id FROM photoblog_preferences WHERE user_id = ' . $user_id . ' LIMIT 1';
					$result = mysql_query($sql);
					if (mysql_num_rows($result) == 0)
					{
						
						$sql = 'INSERT INTO photoblog_preferences SET ';
						$sql .= ' user_id = ' . $user_id . ',';
						$sql .= ' color_main = "' . $photoblog_preferences_default_values['color_main'] . '",';
						$sql .= ' color_detail = "' . $photoblog_preferences_default_values['color_detail'] . '",';
						$sql .= ' hamster_guard_on = ' . $photoblog_preferences_default_values['hamster_guard_on'];
						if (!mysql_query($sql))
						{
							report_sql_error($sql);
						}
					}
					
					$sql = 'SELECT pp.*, l.id, l.username';
					$sql .= ' FROM login AS l, photoblog_preferences AS pp';
					$sql .= ' WHERE pp.user_id = l.id AND l.username = "' . $username . '"';
					$sql .= ' LIMIT 1';
					$result = mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
					$data = mysql_fetch_assoc($result);
					if ( mysql_num_rows($result) == 1 )
					{
						return $data;
					}
					else
					{
						throw new Exception('Användaren verkar inte finnas i databasen *sadface*<br /><a href="/fotoblogg/">Tillbaka</a>');
					}
				}
				// If this is true, it means that $uri_parts[2] is'nt a valid username
				
				if ( $_SERVER['REQUEST_URI'] == '/fotoblogg/')
				{
					if ( login_checklogin() )
					{
						header('Location: /fotoblogg/' . $_SESSION['login']['username']);
					}
					else
					{
						throw new Exception('Du är inte inloggad och kan därför inte se din egen fotoblogg.');
					}
				}
				elseif ( strlen($uri_parts[2]) > 0 )
				{
					$active_user_data = photoblog_fetch_active_user_data($uri_parts[2]);
					preint_r($active_user_data);
					$username = $active_user_data['username'];
					$ui_options['photoblog_current_view_username'] = $active_user_data['username'];
					$ui_options['photoblog_current_view_user_id'] = $active_user_data['user_id'];
				}
				switch ($uri_parts[3])
				{
					case 'album':
						require('album.php');
					break;
					
					default:
						require('blogg.php');
					break;
				}
			break;
		}
	}
	catch (Exception $error)
	{
		$options['type'] = 'error';
    $options['title'] = 'Felmeddelande!';
    $options['message'] = $error->getMessage();
    $options['collapse_information'] = '<p>File: ' . $error->getFile() . '<br />Line: ' . $error->getLine() . '</p>';
    $out = ui_server_message($options);
	}
	ui_top($ui_options);
	echo $out;
	ui_bottom($ui_options);
?>
