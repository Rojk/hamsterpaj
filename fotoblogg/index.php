<?php
	error_reporting(E_ALL);
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
				require('instaellningar.php');
			break;
			
			case 'ladda_upp':		
				require('ladda_upp.php');
			break;
				
			default:
				if ( isset($uri_parts[2]) && preg_match('/^[a-zA-Z0-9-_]+$/', $uri_parts[2]) )
				{
					$username = $uri_parts[2];	
				}
				elseif ( login_checklogin() )
				{
					jscript_location('/fotoblogg/' . $_SESSION['login']['username']);
				}
				else
				{
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
