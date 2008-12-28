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
		
		$photos_by_year = photoblog_dates_fetch(array('user_id' => $_SESSION['login']['id']));
		
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
				foreach ($photos_by_year as $year => $photos_by_month)
				{
					$select_year .= '<option value="' . $year . '">' . $year . '</option>';

					$select_this_month = '<select style="display: none;" id="photoblog_select_month_' . $year . '">';
					foreach ( $photos_by_month as $month => $photos_by_day )
					{
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
			$out .= '<a href="#" id="photoblog_select_today"><img src="http://images.hamsterpaj.net/famfamfam_icons/house.png" alt="Idag" title="Till dagens datum" /></a>' . "\n";
			$out .= '</div>';
			$out .= '<div id="photoblog_user_header">';
				$out .= '<a href="/fotoblogg/">Min fotoblogg</a><a href="/fotoblogg/ladda_upp">Ladda upp</a><a href="/fotoblogg/ordna">Sortera mina foton</a><a href="/fotoblogg/instaellningar">Inställningar</a>' . "\n";
			$out .= '</div>';
		$out .= '</div>';
		
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
				
				// If this is true, it means that $uri_parts[2] isn't a valid username
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
