<?php
	require('../include/core/common.php');
	try
	{
		require(PATHS_LIBRARIES . 'photoblog.lib.php');
		require(PATHS_LIBRARIES . 'photoblog_preferences.lib.php');
		require(PATHS_LIBRARIES . 'profile.lib.php');
		require(PATHS_LIBRARIES . 'userblock.lib.php');
		$ui_options['stylesheets'][] = 'photoblog_' . $photoblog_user['color_main'] . '_' . $photoblog_user['color_detail'] . '_.css';
		$ui_options['javascripts'][] = 'jquery-ui-slider.js';
		$ui_options['javascripts'][] = 'jquery-ui-datepicker.js';	
		$ui_options['javascripts'][] = 'photoblog.js';
		$ui_options['ui_modules_hide'] = true;

		// If this is true, it means that $uri_parts[2] isn't a valid username
		if(preg_match('#^/fotoblogg(\/|)$#', $_SERVER['REQUEST_URI']))
		{
			if(login_checklogin() && preg_match('#^/fotoblogg(\/|)$#', $_SERVER['REQUEST_URI']))
			{
				header('Location: /fotoblogg/' . $_SESSION['login']['username']);
			}
			else
			{
				throw new Exception('Du är inte inloggad och kan därför inte se din egen fotoblogg.');
			}
		}
		
		$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
		
		if(in_array($uri_parts[2], array('ordna','instaellningar','ladda_upp')))
		{
			$options['username'] = $_SESSION['login']['username'];
			$photoblog_user = photoblog_fetch_active_user_data($options);
		}
		else if(isset($uri_parts[2]) && preg_match('/^[a-zA-Z0-9-_]+$/', $uri_parts[2]))
		{
			$options['username'] =  $uri_parts[2];
			$photoblog_user = photoblog_fetch_active_user_data($options);
		}
		else if(login_checklogin())
		{
			$options['username'] = $_SESSION['login']['username'];
			$photoblog_user = photoblog_fetch_active_user_data($options);
		}
		else
		{
			throw new Exception('Njet, ogiltigt användarnamn!');
		}
		
		// This line has to be after photoblog_fetch_active_user_data since it use parameters for colors
		$ui_options['stylesheets'][] = 'photoblog_' . $photoblog_user['color_main'] . '_' . $photoblog_user['color_detail'] . '_.css';
		
		$photos_by_year = photoblog_dates_fetch(array('user' => $photoblog_user['id']));
		$month_table = array(
			'01' => 'Januari',
			'02' => 'Februari',
			'03' => 'Mars',
			'04' => 'April',
			'05' => 'Maj',
			'06' => 'Juni',
			'07' => 'Juli',
			'08' => 'Augusti',
			'09' => 'September',
			'10' => 'Oktober',
			'11' => 'November',
			'12' => 'December'
		);
		
		$out .= '<div id="photoblog_header">';
		$out .= '<div id="photoblog_select">';
		$select_year .= '<select id="photoblog_select_year">';
		
		$select_months = array();
		$highest_date = 0;		
		
		foreach ($photos_by_year as $year => $photos_by_month)
		{
			$select_year .= '<option value="' . $year . '">' . $year . '</option>';
			$select_this_month = '<select style="display: none;" id="photoblog_select_month_' . $year . '">';
			foreach ( $photos_by_month as $month => $photos_by_day )
			{
				$highest_date = max((int)($year . $month), $highest_date);
				$select_this_month .= '<option value="' . $month . '">' . $month_table[$month] . '</option>';
			}
			$select_this_month .= '</select>';				
			
			$select_months[] = $select_this_month;
		}
		$select_year .= '</select>';
				
		$out .= $select_year;
		$out .= '<div style="display: inline;" id="photoblog_select_months">';
			$out .= implode('', $select_months);
		$out .= '</div>';
		$out .= '<a href="#" id="photoblog_select_today"><img src="' . IMAGE_URL . 'famfamfam_icons/house.png" alt="Idag" title="Till dagens datum" /></a>' . "\n";
		$out .= '</div>';
			$out .= '<div id="photoblog_user_header">';
				$out .= '<a href="/fotoblogg/">Min fotoblogg</a><a href="/fotoblogg/ladda_upp">Ladda upp</a><a href="/fotoblogg/ordna">Sortera mina foton</a><a href="/fotoblogg/instaellningar">Inställningar</a>' . "\n";
			$out .= '</div>';
		$out .= '</div>';
		
		switch ($uri_parts[2])
		{
			case 'instaellningar':
				require('instaellningar.php');
			break;
			
			case 'ladda_upp':		
				require('ladda_upp.php');
			break;
			
			case 'ordna':
				require('ordna.php');
			break;
				
			default:
				switch ($uri_parts[3])
				{
					case 'album':
						$options['members_only'] = $photoblog_user['members_only'];
						$options['friends_only'] = $photoblog_user['friends_only'];
						$options['action'] = 'visit';
						$options['owner_id'] = $photoblog_user['id'];
						photoblog_access($options);
						require('album.php');
					break;
					
					default:
						$options['members_only'] = $photoblog_user['members_only'];
						$options['friends_only'] = $photoblog_user['friends_only'];
						$options['action'] = 'visit';
						$options['owner_id'] = $photoblog_user['id'];
						photoblog_access($options);
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
