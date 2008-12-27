<?php
	require('../include/core/common.php');
	try
	{
		require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
		require(PATHS_INCLUDE . 'libraries/photoblog_preferences.lib.php');
		require(PATHS_INCLUDE . 'libraries/profile.lib.php');
		
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
				
			default:
				if ( isset($uri_parts[2]) && preg_match('/^[a-zA-Z0-9-_]+$/', $uri_parts[2]) && strtolower($uri_parts[2]) != 'borttagen' )
				{
					$username = $uri_parts[2];
					$sql = 'SELECT id FROM login WHERE username = "' . $username . '" LIMIT 1';
					$result = mysql_query($sql);
					$data = mysql_fetch_assoc($result);
					$user_id = $data['id'];
					
					$sql = 'SELECT user_id FROM photoblog_preferences WHERE user_id = ';
					$sql = 'SELECT pp.*, l.id, l.username';
					$sql .= ' FROM login AS l, photoblog_preferences AS pp';
					$sql .= ' WHERE pp.user_id = l.id AND l.username = "' . $uri_parts[2] . '"';
					$sql .= ' LIMIT 1';
					$result = mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
					$data = mysql_fetch_assoc($result);
					if ( strlen($data['id']) == 0 )
					{
						throw new Exception('Användaren verkar inte finnas i databasen *sadface*<br /><a href="/fotoblogg/">Tillbaka</a>');
					}
					else
					{
						preint_r($data);
					}
				}
				// If this is true, it means that $uri_parts[2] is'nt a valid username
				elseif ( strlen($uri_parts[2]) > 0 )
				{
					throw new Exception('Användarnamnet var fail :P, ett användarnamn kan bara använda atillzetaATILLZETAnolltillniobindestreckochunderstreck.');
				}
				elseif ( login_checklogin() )
				{
					header('Location: /fotoblogg/' . $_SESSION['login']['username']);
				}
				else
				{
					echo strlen($uri_parts[2]);
					var_dump($uri_parts[2]);
					$username = 'iphone';
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
