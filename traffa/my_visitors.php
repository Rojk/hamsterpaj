<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'traffa-functions.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	require(PATHS_INCLUDE . 'libraries/userblock.lib.php');

	$show_user_username = 'dina';
	$show_user_id = $_SESSION['login']['id'];
	$paths_prefix = '/traffa/my_visitors.php?';
	
	if(isset($_GET['userid']) && is_numeric($_GET['userid']) && $_GET['userid'] != $_SESSION['login']['id'])
	{
		$query = 'SELECT username'
		       . ' FROM login'
		       . ' WHERE id = ' . $_GET['userid']
		       . ' LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query);
		if(mysql_num_rows($result) > 0){
			$data = mysql_fetch_assoc($result);
			$show_user_username = ucfirst($data['username']) . 's';
			$show_user_id = (int) $_GET['userid'];
			$paths_prefix .= 'userid=' . $show_user_id . '&';
		}
	}
	elseif(isset($_GET['user_id']) && is_numeric($_GET['user_id']) && $_GET['user_id'] != $_SESSION['login']['id'])
	{
		$query = 'SELECT username'
		       . ' FROM login'
		       . ' WHERE id = ' . $_GET['user_id']
		       . ' LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query);
		if(mysql_num_rows($result) > 0){
			$data = mysql_fetch_assoc($result);
			$show_user_username = ucfirst($data['username']) . 's';
			$show_user_id = (int) $_GET['user_id'];
			$paths_prefix .= 'userid=' . $show_user_id . '&';
		}
	}

	$ui_options['title'] = $show_user_username . ' besökare på Hamsterpaj';
	$ui_options['menu_path'] = array('traeffa', 'besoeksloggen');
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['stylesheets'][] = 'my_visitors_joel.css';
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['javascripts'][] = 'my_visitors.js';
	
	$profile_params['user_id'] = $show_user_id;
	$profile = profile_fetch($profile_params);
	
	$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';

	if (userblock_checkblock($show_user_id))
	{
		ui_top();
		echo '<p class="error">IXΘYΣ! Du har blivit blockad, var snel hest så slipper du sånt ;)<br /><em>Visste du förresten att IXΘYΣ betyder Fisk på grekiska?</em></p>';
		ui_bottom();
		exit;
	}
	
	ui_top($ui_options);

	echo profile_mini_page($profile);
	
	if(!login_checklogin())
	{

		$rounded_corners_tabs_options = array();
		$rounded_corners_tabs_options['tabs'][] = array('href' => "#", 'label' => "Inte inloggad!", 'current' => true);
		$rounded_corners_tabs_options['tabs'][] = array('href' => "/register.php", 'label' => "Registrera dig");
		
		rounded_corners_tabs_top($rounded_corners_tabs_options);
		
		echo 'Du måste logga in för att använda den här sidan.<br />' . "\n";
		echo 'Johan kan desutom kommandot för att visa register_teaser-rutan, så han får skriva in det här...';
		
		rounded_corners_tabs_bottom();
		ui_bottom();
		die();
	}
	
		$query = 'SELECT profile_visitors AS total_visits FROM userinfo WHERE userid = ' . $show_user_id;
		$result = mysql_query($query) or report_sql_error($query);
		$data = mysql_fetch_assoc($result);
		echo '<div id="my_visitors_unique_visits">Totalt ' . $data['total_visits'] . ' besökare</div>' . "\n";
	
		$_GET['show'] = isset($_GET['show']) ? $_GET['show'] : 'all';
		$show_what = (in_array($_GET['show'], array('boys', 'girls', 'all'))) ? $_GET['show'] : 'all';
		
		$rounded_corners_tabs_options = array();
		$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . 'show=all', 'label' => 'Alla / Okänt kön', 'current' => ($show_what == 'all'));
		$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . 'show=boys', 'label' => 'Pojkar', 'current' => ($show_what == 'boys'));
		$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . 'show=girls', 'label' => 'Flickor', 'current' => ($show_what == 'girls'));

		rounded_corners_tabs_top($rounded_corners_tabs_options); 

		echo '<div id="my_visitors_picholder"><div id="my_visitors_picholder_expander">' . "\n";		
		$query  = 'SELECT uv.item_id AS visitor_id, l.username, z.spot';
		$query .= ' FROM user_visits AS uv, login AS l, userinfo AS u, zip_codes AS z';
		$query .= ' WHERE uv.user_id = ' . $show_user_id . ' AND l.id = uv.item_id AND uv.type = "profile_visit" AND u.userid = uv.item_id AND z.zip_code = u.zip_code';
		$query .= ' AND is_removed = 0 AND u.image IN(1, 2)';
		if(in_array($show_what, array('boys', 'girls')))
		{
			$query .= ' AND u.gender = "' . (($_GET['show'] == 'boys') ? 'm' : 'f') . '"';
		}
		$query .= ' ORDER BY uv.timestamp DESC';
		$query .= ' LIMIT 35';
	
		$result = mysql_query($query) or die(report_sql_error($query));
	
		
		while($data = mysql_fetch_assoc($result))
		{
			echo '<div class="my_visitors_showinfo_item" id="my_visitors_showinfo_' . $data['visitor_id'] . '">' . "\n";
				echo '<strong>' . $data['username'] . '</strong><br />' . "\n";
				echo '<img src="' . IMAGE_URL . 'images/users/thumb/' . $data['visitor_id'] . '.jpg" alt="' . $data['username'] . '" /><br />' . "\n";
				echo '<address>' . $data['spot'] . '</address><br />' . "\n";
			echo '</div>' . "\n";
		}
		echo '</div></div>' . "\n";
		
		if(mysql_num_rows($result) == 0)
		{
			echo 'Du har inte fått några besök under den här fliken ännu -  men det är inget att vara ledsen över.';
			echo '<br /><a href="/traffa">Klicka här för att komma till träffas förstasida</a>.';
		}
		
		rounded_corners_tabs_bottom();
		
		echo '<input type="hidden" id="my_visitors_show_user_id" value="' . $show_user_id . '" />' . "\n";
		
		rounded_corners_tabs_top();		
		echo '<div id="my_visitors_userinfo_pane">' . "\n";
		echo '	Tips: Dra musen till den högra delen av rutan med alla bilder i här ovanför, så scrollar allt till vänster och du ser fler bilder.<br /><br />Du kan också klicka på en bild - då dyker det upp en större bild och en del annan information om användaren i den här rutan.' . "\n";
		echo '</div>' . "\n";
		rounded_corners_tabs_bottom();

		$rounded_corners_tabs_config = array();
		//$rounded_corners_tabs_config['id'] = 'my_visitors_search';
		$rounded_corners_tabs_config['tabs'][] = array('href' => $paths_prefix, 'label' => 'Visa alla besökare', 'current' => (!isset($_GET['search']) && !isset($_GET['search_type'])));
		$rounded_corners_tabs_config['tabs'][] = array('href' => $paths_prefix . 'search=true', 'label' => 'Sök besökare', 'current' => (isset($_GET['search']) && !isset($_GET['search_type'])));
		if(isset($_GET['search_type']))
		{
			$rounded_corners_tabs_config['tabs'][] = array('href' => '#', 'label' => 'Sökresultat', 'current' => true);
		}
		
		rounded_corners_tabs_top($rounded_corners_tabs_config);
		$query  = 'SELECT DISTINCT l.id AS userid, l.username AS username, l.lastaction AS lastaction, u.birthday AS birthday, u.gender AS gender, z.spot AS spot, uv.timestamp AS visit_time, z.x_rt90, z.y_rt90, u.image AS photo_mode';
		$query .= ' FROM login AS l, userinfo AS u, zip_codes AS z, user_visits AS uv';
		$query .= ' WHERE l.id = u.userid AND z.zip_code = u.zip_code AND uv.type = "profile_visit" AND uv.user_id = ' . $show_user_id . ' AND l.id = uv.item_id AND l.username NOT LIKE "borttagen"';

		if(isset($_GET['search']))
		{
			if(isset($_GET['search_type']) && in_array($_GET['search_type'], array('by_user', 'in_neighbourhood', 'by_spot')))
			{
					if($_GET['search_type'] == 'by_user' && isset($_GET['username']))
					{
						$query .= ' AND l.username LIKE "%' . $_GET['username'] . '%"';
						$view_type_message  = 'Listar ' . $show_user_username . ' besökare vars användarnamn innehöll "' . $_GET['username'] . '".';
					}
					else if($_GET['search_type'] == 'in_neighbourhood')
					{
						if($_SESSION['userinfo']['geo_location'] != "")
						{
							$query .= ' AND z.spot LIKE "' . $_SESSION['userinfo']['geo_location'] . '%"';
							$view_type_message  = 'Listar ' . $show_user_username . ' besökare som bor i ' . $_SESSION['userinfo']['geo_location'] . '.';
						}else{
							$view_type_message .= 'Du måste ange vart du bor under <a href="http://www.hamsterpaj.net/settings.php#optional_info">inställningarna för postnummer</a>!';
							$view_type_disable_user_listing = true;
						}
					}
					else if($_GET['search_type'] == 'by_spot' && isset($_GET['spot']))
					{
						$query .= ' AND z.spot LIKE "' . $_GET['spot'] . '%"';
						$view_type_message  = 'Visar ' . $show_user_username . ' besökare som kommer ifrån ' . $_GET['spot'] . '.';
					}
			}else{
				$view_type_message  = '<form method="get" action="' . $_SERVER['PHP_SELF'] . '">' . "\n";
				$view_type_message .= 'Sök besökare:<br />' . "\n";
				$view_type_message .= '<input type="hidden" name="search" value="true" />' . "\n";
				$view_type_message .= '<input type="radio" name="search_type" id="my_visitors_search_type_by_user" value="by_user" checked="checked" /><label for="my_visitors_search_type_by_user"> Efter användarnamn: </label><input type="text" value="" name="username" id="my_visitors_search_user" /><br />' . "\n";
				$view_type_message .= '<input type="radio" name="search_type" id="my_visitors_search_type_in_neighbourhood" value="in_neighbourhood" /><label for="my_visitors_search_type_in_neighbourhood"> Besökare som bor nära dig.</label><br />' . "\n";
				$view_type_message .= '<input type="radio" name="search_type" id="my_visitors_search_type_by_spot" value="by_spot" /><label for="my_visitors_search_type_by_spot"> Besökare ifrån </label><input type="text" name="spot" id="my_visitors_search_spot" value="" /> (T.ex. Göteborg, Stockholm eller Malmö).<br />' . "\n";
				$view_type_message .= '<input type="submit" value="Sök" class="button_40" />' . "\n";
				$view_type_message .= '</form>' . "\n";
				$view_type_disable_user_listing = true;
			}
		}else{
			$view_type_message = 'Här listar vi ' . $show_user_username . ' 50 senaste besökare. Du kan också <a href="' . $paths_prefix . 'search=true">söka</a> efter besökare.';
		}

		$query .= ' ORDER BY uv.timestamp DESC';
		$query .= ' LIMIT 50';		
		
		echo $view_type_message . '<br /><br />' . "\n";

		if(!isset($view_type_disable_user_listing)){
			$result = mysql_query($query) or report_sql_error($query);
			if(mysql_num_rows($result)>0)
			{
				echo '<ul id="my_visitors_search">' . "\n";
				while($data = mysql_fetch_assoc($result))
				{
					$online = ((int) $data['lastaction'] > time() - 600);
					echo '<li>';
					$user_age_gender  = str_replace(array('u', 'm', 'f'), array('', ' pojke', ' flicka'), $data['gender']);
					$user_age_gender .= (($data['birthday'] == '0000-00-00') ? '' : ' ' . date_get_age($data['birthday']) . ' år');
					$real_rt90_x = $data['y_rt90']; /* Note: rt90_x and rt90_y (GPS-coordinates) */
					$real_rt90_y = $data['x_rt90']; /* are flipped, due to bug in hitta.se       */
					
					echo '<div class="_username_age_gender"><a href="/traffa/profile.php?user_id=' . $data['userid'] . '" id="my_visitors_search_showinfo_' . (in_array((int) $data['photo_mode'], array(1, 2)) ? 'true' : 'false') . '_' . $data['userid'] . '">' . $data['username'] . '</a>' . ((strlen($user_age_gender) > 0) ? ', ' . $user_age_gender : '') . '</div>';
					echo '<div class="_spot">' . (($data['spot'] == '') ? '&nbsp;' : '<a href="javascript:void(0)" id="my_visitors_spot_popup_' . $real_rt90_x . '_' . $real_rt90_y . '_' . $data['userid'] . '">' . $data['spot'] . '</a>') . '</div>';
					echo '<div class="_status_' . ($online ? 'online' : 'offline') . '">' . ($online ? 'Online' : 'Offline') . '</div>';
					echo '<div class="_visit_time">' . fix_time($data['visit_time']) . '</div>';
					echo '<br style="clear: both" />';
					echo '</li>' . "\n";
				}
				echo '</ul>' . "\n";
			}
			else
			{
				echo 'Hittade inga besökare matchande din sökning. <a href="http://www.hamsterpaj.net/traffa/search.php">Klicka här för att söka bland alla användare</a>.';
			}
		}
		rounded_corners_tabs_bottom();
		
	ui_bottom();
?>