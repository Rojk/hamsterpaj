<?php
	/* OPEN_SOURCE */
	
	define('EMOLAND', 'off');
	
	$victims[] = 865900;
	// Debug, $victims[] = <secret>; ;)
	
	if (in_array($_SESSION['login']['id'], $victims) && rand(1, 30) == 15)
	{
		header("Location: http://smouch.net/lol/");
		die();
	}
	
	setlocale(LC_ALL, 'sv_SE.ISO8859-1');
	session_start();

	/* To ip ban user: Use /admin/ip_ban_admin.php */
	
	// Se /storage/www/ip_handling.php

	
	function ui_top($options)
	{
		if(isset($_SESSION['new_design']))
		{
			echo ui_new_top($options);
			return;
		}
		
		global $SIDE_MODULES;
		
		if(!isset($options['adtoma_category']))
		{
			$options['adtoma_category'] = 'other';
		}
		define('ADTOMA_CATEGORY', $options['adtoma_category']);

		if(login_checklogin() && rand(1, 5) == 2)
		{
			$query = 'UPDATE login SET lastrealaction="' . time() . '" WHERE id="' . $_SESSION['login']['id'] . '"';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		
		$options['title'] = (isset($options['title'])) ? $options['title'] : 'Hamsterpaj.net - Onlinespel, community, forum och annat kul ;)';

		$options['stylesheets'][] = 'shared.css';
		$options['stylesheets'][] = 'modules.css';
		$options['stylesheets'][] = 'buttons.css';
		$options['stylesheets'][] = 'new_guestbook.css';
		$options['stylesheets'][] = 'rounded_corners.css';
		$options['stylesheets'][] = 'message.css';
		$options['stylesheets'][] = 'domTT.css';
		$options['stylesheets'][] = 'poll.css';

		/* Order:
			jQuery
			Womlib (needs jQuery to work properly!)
			The rest...
		*/

		if(is_array($options['javascripts']))
		{
			array_unshift($options['javascripts'], 'womlib.js');
		}
		else
		{
			$options['javascripts'] = array('womlib.js');
		}
		if($_SESSION['login']['id'] > 0)
		{
			$options['javascripts'][] = 'stay_online.js';
		}
		$options['javascripts'][] = 'ui_server_message.js';
		$options['javascripts'][] = 'scripts.js';
		$options['javascripts'][] = 'steve.js';
		$options['javascripts'][] = 'new_guestbook.js';
		$options['javascripts'][] = 'forum.js';
		$options['javascripts'][] = 'posts.js';
		$options['javascripts'][] = 'abuse_report.js';
		$options['javascripts'][] = 'poll.js';
		array_unshift($options['javascripts'], 'jquery.js');
		$options['javascripts'][] = 'swfobject.js';
		$options['javascripts'][] = 'md5.js';
		$options['javascripts'][] = 'xmlhttp_login.js';
		$options['javascripts'][] = 'xmlhttp.js';
		$options['javascripts'][] = 'fult_dhml-skit_som_faar_bilder_att_flyga.js';
		$options['javascripts'][] = 'wave_effect.js';
		$options['javascripts'][] = 'joels_hackerkod.js';
		$options['javascripts'][] = 'ui.js';
		if(!login_checklogin())
		{
			$options['javascripts'][] = 'tiny_reg_form.js';
			$options['stylesheets'][] = 'tiny_reg_form.css';
		}
		
		if(rand(0, 73) == 50)
		{
			$query = 'UPDATE pageviews SET views = views + 73 WHERE date = "' . date('Y-m-d') . '" LIMIT 1';
			mysql_query($query);
			if(mysql_affected_rows() == 0)
			{
				$query = 'INSERT INTO pageviews (views, date) VALUES(73, "' . date('Y-m-d') . '")';
				mysql_query($query);
			}
		}
		
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
		echo '<head>' . "\n";
		echo '<meta name="description" content="' . $options['meta_description'] . '" />' . "\n";
		echo '<meta name="keywords" content="' . $options['meta_keywords'] . '" />' . "\n";
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
		echo '<title>' . $options['title'] . '</title>' . "\n";
		echo '<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
		echo '<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";

		array_unshift($options['stylesheets'], 'ui.css.php');
		echo "\n\n" . '<!-- Load stylesheets, version is timestamp of last file modification. Current timestamp is: ' . time() . ' -->' . "\n";
		echo '<style type="text/css">' . "\n";
		foreach($options['stylesheets'] AS $stylesheet)
		{
			echo '@import url(\'/stylesheets/' . $stylesheet . '?version=' . filemtime(PATHS_WEBROOT . 'stylesheets/' . $stylesheet) . '\');' . "\n";
		}
		echo '</style>' . "\n";

		echo "\n\n" . '<!-- Load javascripts, version is timestamp of last file modification. -->' . "\n";
		foreach($options['javascripts'] AS $javascript)
		{
			echo '<script type="text/javascript" language="javascript" ';
			echo 'src="/javascripts/' . $javascript . '?version=' . filemtime(PATHS_WEBROOT . 'javascripts/' . $javascript) . '"></script>' . "\n";
		}
		if(isset($options['enable_rte']))
		{
			echo '<script language="javascript" type="text/javascript" src="/javascripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
						<script language="javascript" type="text/javascript">
						tinyMCE.init({
							mode : "exact",
							elements: "post_form_content",
							theme: "advanced",
							theme_advanced_buttons1 : "bold,italic,underline,separator,bullist,numlist,separator,sup,charmap",
							theme_advanced_buttons2 : "",
							theme_advanced_buttons3 : "",
							theme_advanced_resize_horizontal : false,
							theme_advanced_resizing : true,
							theme_advanced_path : false,
							theme_advanced_toolbar_location : "top",
							theme_advanced_statusbar_location : "bottom",
							theme_advanced_toolbar_align : "left",
							auto_reset_designmode : true
						});
						</script>';
		}
		echo "\n\n";

		echo $options['header_extra'];
		echo '</head>' . "\n";
		
		echo (isset($options['body_extra'])) ? '<body ' . $options['body_extra'] . '>' . "\n" : '<body>' . "\n";

		echo '<div id="hamsterpaj_website">' . "\n";
		echo '<!-- Nej, fråga oss inte varför vi har typ tio olika divar som verkar göra samma sak... -->' . "\n";
		echo '<!-- Ad management, adtoma -->' . "\n";
		echo '<script type="text/javascript">' . "\n";

		$adtoma_gender = (in_array($_SESSION['userinfo']['gender'], array('P', 'F'))) ? $_SESSION['userinfo']['gender'] : 'xx';
		$adtoma_age = ($_SESSION['userinfo']['birthday'] != '0000-00-00') ? date_get_age($_SESSION['userinfo']['birthday']) : 'xx';
		$adtoma_birthyear = ($_SESSION['userinfo']['birthday'] != '0000-00-00') ? substr($_SESSION['userinfo']['birthday'], 0, 4) : 'xx';
		echo "\t" . 'var CM8Server = "ad.adtoma.com";' . "\n";
		echo "\t" . 'var CM8Cat = "hp.' . ADTOMA_CATEGORY . '";' . "\n";
		echo "\t" . 'var CM8Profile = "hp_age=' . $adtoma_age . '&amp;hp_birthyear=' . $adtoma_birthyear . '&amp;hp_gender=' . $adtoma_gender . '"' . "\n";
		echo '</script>' . "\n";
		echo '<script language="JavaScript" type="text/javascript" src="http://ad.adtoma.com/adam/cm8adam_1_call.js"></script>' . "\n";
		echo "\n\n";

		echo '<div>' . "\n";
		echo '	<script type="text/javascript">CM8ShowAd("Bigbanner");</script>' . "\n";
		echo '</div>' . "\n";

		echo '<div style="width: 1200px;margin-top: 10px">' . "\n";
		
		if((!login_checklogin() && rand(0, 5) == 4) || (login_checklogin() && rand(0, 30) == 7))
		{
			echo '<a href="/sex_och_sinne/"><img src="http://images.hamsterpaj.net/sex_and_sense/ui_to_new_sex_sense.png" alt="Till nya sex och sinne!" style="margin-left: 670px" /></a>';
		}

		echo '<img src="http://images.hamsterpaj.net/ui/site_top_rounded_corners.png" id="site_top_rounded_corners" />' . "\n";
		echo '<div id="site_container">' . "\n";

		echo '<div id="main">' . "\n";
		echo '<div id="top">' . "\n";
		if(true)
		{
			if ($_SESSION['login']['id'] == 148153 || isset($_GET['illerpaj']))
			{
				echo '<a href="/"><img src="http://images.hamsterpaj.net/illerpaj2.png" id="logo" /></a>' . "\n";
			}
			elseif (NATTPAJ == true)
			{
				echo '<a href="/"><img src="http://images.hamsterpaj.net/nattpaj/nattpaj_logo.png" id="logo" /></a>' . "\n";
			}
			else
			{
				echo '<a href="/"><img src="http://images.hamsterpaj.net/ui/logo.png" id="logo" /></a>' . "\n";
			}
		}
		else
		{
				echo '<div id="logo">' . "\n";
				if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
		{
?>
	<object type="application/x-shockwave-flash" data="http://images.hamsterpaj.net/ui/hamsterpaj_logo.swf" width="320" height="60">
		<param name="movie" value="http://images.hamsterpaj.net/ui/hamsterpaj_logo.swf" />
		<img src="http://images.hamsterpaj.net/logo.png" alt="Hamsterpaj logo" />
	</object>
<?php
		}
		echo '</div>' . "\n";
}
		echo '<div id="login_pane">' . "\n";
		if(login_checklogin())
		{
			echo ui_login_status_bar('page_init');	
		}
		else
		{
			?>
			<form action="/login.php?action=login" method="post" id="login_form">
				<div class="username">
					<h5>Användarnamn</h5> 
					<input type="text" name="username"  />
				</div>
				<div class="password">
					<h5>Lösenord</h5>
					<input type="password" name="password" />
				</div>
			<ul class="login_buttons">
				<li>
						<div class="icon">
							<a onclick="javascript: document.getElementById('login_form').submit();">
								<img src="http://images.hamsterpaj.net/login_bar/login_color.png" alt="Logga in" />
							</a>
						</div>
					<a onclick="javascript: document.getElementById('login_form').submit();">
						Logga in
					</a>
				</li>
				
				<li>
						<div class="icon">
						<a href="/register.php">
							<img src="http://images.hamsterpaj.net/login_bar/register_color.png" alt="Bli medlem" />
						</a>
						</div>
						<a href="/register.php">
							Bli medlem
						</a>
				</li>
			</ul>
			<input type="submit" value="logga in" style="width: 0px; height: 0px; float: left; border: none;" />
			<!-- Submit med enterslag fungerar inte i IE om det inte finns en submit-knapp, dessutom får den inte ha display: none; för då funkar det inte i IE... Skitläsare! -->
			</form>
			<?php
			}
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		
		?>
		
		<div id="search_and_status">
				<div id="quicksearch">
					<form action="/quicksearch.php">
					<input type="text" id="quicksearch_input" class="quicksearch_input" value="Sök Hamsterpajare..." name="search" />
					<input type="hidden" name="type" value="user" />
		
		<?php
		if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
		{
//			echo '<img src="' . IMAGE_URL . 'steve/icon_gun.gif" id="steve_gun"/>' . "\n";
		}
		echo '<input type="submit" value="" class="button_magnifier" />' . "\n";
		echo '</form>' . "\n";
		echo '</div>' . "\n";		

		echo '<div class="status">' . "\n";
		if($_SESSION['disablesteve'] != 1)
		{
			echo '<img src="http://images.hamsterpaj.net/steve/steve.gif" id="steve" />' . "\n";
		}
		if(login_checklogin())
		{
			$status = (strlen(trim($_SESSION['userinfo']['user_status'])) > 0) ? $_SESSION['userinfo']['user_status'] : 'Ingen status';
			echo '<input type="text" id="user_status_input" value="' . stripslashes($status) . '" />' . "\n";
			echo '<button class="button_50" id="user_status_save_button">Spara</button>' . "\n";
		}
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		
		
		echo '<div id="main_left">' . "\n";

	global $menu;
	/* Merge the users additions to the menu array */
	if(isset($options['menu_addition']))
	{
		$menu = array_merge_recursive($menu, $options['menu_addition']);
	}
	foreach($menu AS $handle => $current_menu)
	{
		if(isset($current_menu['is_privilegied']))
		{
			$current_menu['is_privilegied'] = is_array($current_menu['is_privilegied']) ? $current_menu['is_privilegied'] : array($current_menu['is_privilegied']);
			$is_privilegied = false;
			
			foreach($current_menu['is_privilegied'] as $privilegie)
			{
				if(is_privilegied($privilegie))
				{
					$is_privilegied = true;
				}
			}
		}
		else
		{
			$is_privilegied = true;
		}
		if($is_privilegied == true)	
		{	
			$class = ($handle == $options['menu_path'][0]) ? 'menu_active' : 'menu';
			echo '<div class="' . $class . '" id="menu_div_' . $handle . '">' . "\n";
			$target = (isset($current_menu['target'])) ? ' target="' . $current_menu['target'] . '"' : '';
			echo '<h3><a href="' . $current_menu['url'] . '" class="menu_title" id="menu_title_'. $handle . '"' . $target . '>' . $current_menu['label'] . '</a></h3>' . "\n";
			echo '<div class="menu_content">' . "\n";
			echo '<ul>' . "\n";
			$label = (isset($current_menu['index_label'])) ? $current_menu['index_label'] : 'Start';
			$class = (count($options['menu_path']) == 1 && $handle == $options['menu_path'][0]) ? ' class="active"' : '';
			echo '<li' . $class . '><a href="' . $current_menu['url'] . '">' . $label . '</a></li>' . "\n";
			if(count($current_menu['children']) > 0)
			{
				$count_menu_items = ui_menu_recurse($current_menu['children'], $options, 1);
			}
			echo '</ul>' . "\n";
			echo '</div>' . "\n";
			echo '<img src="http://images.hamsterpaj.net/ui/menu/menu_box_open_bottom.png" class="menu_box_open_bottom" />' . "\n";
			echo '<img src="http://images.hamsterpaj.net/ui/menu/menu_box_closed_bottom.png" class="menu_box_closed_bottom" />' . "\n";
			echo '</div>' . "\n";
		}
	}

	$serialized = file_get_contents(PATHS_INCLUDE . 'cache/live_stats.phpserialized');
	$info = unserialize($serialized);

	echo '<img src="http://images.hamsterpaj.net/ui/menu/left_module_top_bottom.png" class="left_module_top" />' . "\n";
	echo '<div class="left_module">' . "\n";	
	echo '<h4>Besökare</h4>' . "\n";
	echo cute_number($info['visitors']) . "\n";

	echo '<h4>Inloggade</h4>' . "\n";
	echo cute_number($info['logged_in']) . "\n";
	
	echo '<h4>Medlemmar</h4>' . "\n";
	echo cute_number($info['members']) . "\n";
	
	echo '<h4>Sidvisningar idag</h4>' . "\n";
	$pageviews = query_cache(array('query' => 'SELECT views FROM pageviews WHERE date = "' . date('Y-m-d') . '" LIMIT 1'));
	echo cute_number($pageviews[0]['views']);
	echo '</div>' . "\n";
	echo '<img src="http://images.hamsterpaj.net/ui/menu/left_module_top_bottom.png" class="left_module_bottom" />' . "\n";

	if(login_checklogin())
	{
		echo '<img src="http://images.hamsterpaj.net/ui/menu/left_module_top_bottom.png" class="left_module_top" />' . "\n";
		echo '<div class="left_module">' . "\n";
		echo '<h4>Minneslapp</h4>' . "\n";
		echo '<textarea id="note">' . "\n";
		echo htmlentities(stripslashes($_SESSION['note']));
		echo '</textarea>' . "\n";
		echo '<input type="button" value="Spara" class="button_50" onclick="note_save()" />' . "\n";				
		echo '</div>' . "\n";
		echo '<img src="http://images.hamsterpaj.net/ui/menu/left_module_top_bottom.png" class="left_module_bottom" />' . "\n";
	}

		echo '</div>' . "\n";		
		echo '<div id="middle">' . "\n";		

		if(isset($_SESSION['notice_message']))
		{
			if(login_checklogin())
			{
				$notices[] = array('html' => $_SESSION['notice_message']);
			}
			unset($_SESSION['notice_message']);
		}

		$data = cache_load('recent_update');
		if($data['timestamp'] > (time() - 1200) && $_SESSION['recent_update_notifier'][$data['id']] < 10)
		{
			global $RECENT_UPDATES;								
			$content = '<span class="time">' . date('H:i', $data['timestamp']) . '</span>' . "\n";
			$content .= '<span class="event">' . $RECENT_UPDATES[$data['type']] . '</span>' . "\n";
			$content .= '<span class="link"><a href="/recent_updates_redirect.php?id=' . $data['id'] . '&url=' . urlencode($data['url']) . '&source=global_notice">' . $data['label'] . '</a></span>' . "\n";
			
			$notices[] = array('html' => $content);
			$_SESSION['recent_update_notifier'][$data['id']]++;
		}

		foreach($notices AS $notice)
		{
			echo '<div class="notice">' . "\n";
			if(isset($notice['timestamp']))
			{
				echo '<span class="time">' . date('H:i', $notice['timestamp']) . '</span>' . "\n";
			}
			echo $notice['html'];
			echo '</div>' . "\n";
		}
	
		echo '<div id="content">' . "\n";
		echo '<script type="text/javascript">CM8ShowAd("Rektangel");</script>' . "\n";
		
		if(isset($_SESSION['posted_gb_to_webmaster']))
		{
			rounded_corners_top(array('color' => 'red'));
			echo 'Men tjockis, det behövs inte mycket IQ för att förstå att användaren Webmaster inte är en riktig människa, utan en BOT som används för att maskineriet bakom Hamsterpaj ska fungera.<br /><br />Om ditt inlägg var speciellt (alltså inte som allt annat som hamnar i Webmasters inkorg, nämligen spam eller hatbrev) är du välkommen vända dig till någon ordningsvakt (se lista bland högermodulerna här till höger&raquo;).';
			rounded_corners_bottom(array('color' => 'red'));
		}
		
		if(login_checklogin())
		{
			if(isset($_SESSION['unread_gb_entries']))
			{
				echo guestbook_list($_SESSION['unread_gb_entries']);
				unset($_SESSION['unread_gb_entries']);
			}
		}
	}

	function ui_menu_recurse($menu, $ui_options, $level)
	{
		$count_menu_items = 0;
		foreach($menu AS $handle => $menu_item)
		{
			$class = ($handle == $ui_options['menu_path'][$level] && !is_array($menu_item['children'])) ? ' class="active"' : '';
			
			// Note: $menu_item['is_privilegied'] might be an array!
			if(isset($menu_item['is_privilegied']))
			{
				$menu_item['is_privilegied'] = is_array($menu_item['is_privilegied']) ? $menu_item['is_privilegied'] : array($menu_item['is_privilegied']);
				$is_privilegied = false;
				
				foreach($menu_item['is_privilegied'] as $privilegie)
				{
					if(is_privilegied($privilegie))
					{
						$is_privilegied = true;
					}
				}
				
				if(!$is_privilegied)
				{
					continue;
				}
			}

				echo '<li' . $class . '><a href="' . $menu_item['url'] . '">' . $menu_item['label'] . '</a>';
				if(count($menu_item['children']) > 0)
				{
					echo "\n";
					echo '<ul>' . "\n";
					ui_menu_recurse($menu_item['children'], $ui_options, $level + 1);
					echo '</ul>' . "\n";
				}
				echo '</li>' . "\n";

		}
	}
	

	function ui_login_status_bar($mode = 'xmlhttp')
	{
		if(login_checklogin())
		{
			if($_SESSION['cache']['lastupdate'] < time() - 20)
			{
				cache_update_all();
			}
			
			$return .= '<ul>' . "\n";

			/* Messages */
			$return .= '<li class="photocomments">' . "\n";
			if($_SESSION['cache']['unread_photo_comments'] >= 1)
			{
				$return .= '<strong>' . $_SESSION['cache']['unread_photo_comments'] . ' <a href="/traffa/photos.php">kommentarer</a></strong><br />';
			}
			else
			{
				$return .= $_SESSION['cache']['unread_photo_comments'] . ' <a href="/traffa/photos.php">kommentarer</a><br />';
			}
			if($_SESSION['notices']['unread_messages'] >= 1)
			{
				$return .= '<strong>' . $_SESSION['notices']['unread_messages'] . ' <a href="/traffa/messages.php">meddelanden</a></strong>';
			}
			else
			{
				$return .= $_SESSION['notices']['unread_messages'] . ' <a href="/traffa/messages.php">meddelanden</a>';
			}
			$return .= '</li>' . "\n\n";
			
			/* Guestbook */
			$msg_text = 'Gästbok';
			$msg_color = 'grey';
			if($_SESSION['notices']['unread_gb_entries'] > 1)
			{
				$msg_text = '<strong>' . $_SESSION['notices']['unread_gb_entries'] . ' nya</strong>';
				$msg_color = 'color';
			}
			elseif($_SESSION['notices']['unread_gb_entries'] == 1)
			{
				$msg_text = '<strong>Nytt inlägg</strong>';
				$msg_color = 'color';
			}
			$return .= '<li>' . "\n";
			$return .= '<a href="/traffa/guestbook.php">' . "\n";
			$return .= '<div class="icon">' . "\n";
			$return .= '<img src="http://images.hamsterpaj.net/login_bar/guestbook_' . $msg_color . '.png" />' . "\n";
			$return .= '</div>' . "\n";
			$return .= $msg_text . "\n";
			$return .= '</a>' . "\n";
			$return .= '</li>' . "\n\n";
			
			/* Forum notices */
			$msg_text = 'Notiser';
			$msg_color = 'grey';
			if($_SESSION['forum']['new_notices'] > 1)
			{
				$msg_text = '<strong>' . $_SESSION['forum']['new_notices'] . ' notiser</strong>';
				$msg_color = 'color';
			}
			elseif($_SESSION['forum']['new_notices'] == 1)
			{
				$msg_text = '<strong>Ny notis</strong>';
				$msg_color = 'color';
			}
			$return .= '<li>' . "\n";
			$return .= '<a href="/diskussionsforum/notiser.php">' . "\n";
			$return .= '<div class="icon">' . "\n";
			$return .= '<img src="http://images.hamsterpaj.net/login_bar/forum_' . $msg_color . '.png" />' . "\n";
			$return .= '</div>' . "\n";
			$return .= $msg_text . "\n";
			$return .= '</a>' . "\n";
			$return .= '</li>' . "\n\n";
			
			/* Group notices */
			$msg_text = 'Gruppinlägg';
			$msg_color = 'grey';
			if($_SESSION['cache']['unread_group_notices'] > 1)
			{
				$msg_text = '<strong>' . $_SESSION['cache']['unread_group_notices'] . ' nya</strong>';
				$msg_color = 'color';
			}
			elseif($_SESSION['cache']['unread_group_notices'] == 1)
			{
				$msg_text = '<strong>Nytt inlägg</strong>';
				$msg_color = 'color';
			}
			$return .= '<li>' . "\n";
			$return .= '<a href="/traffa/groupnotices.php">' . "\n";
			$return .= '<div class="icon">' . "\n";
			$return .= '<img src="http://images.hamsterpaj.net/login_bar/buddies_' . $msg_color . '.png" />' . "\n";
			$return .= '</div>' . "\n";
			$return .= $msg_text . "\n";
			$return .= '</a>' . "\n";
			$return .= '</li>' . "\n\n";
			$return .= '</ul>' . "\n";
			
			$return .= '<div id="user_info">' . "\n";
			$return .= '<a href="/traffa/profile.php?user_id=' . $_SESSION['login']['id'] . '" class="username">' . $_SESSION['login']['username'] . '</a><br />' . "\n";
			
			$return .= '<span class="online_time">Online ';
			$online_secs = time() - $_SESSION['login']['lastlogon'];
			$online_days = floor($online_secs/86400);
			$online_hrs = floor(($online_secs - $online_days*86400)/3600);
			$online_mins = floor(($online_secs%3600)/60);
	
			if($online_days == 1)
			{
				$return .= '1 d, ';
			}
			elseif($online_days > 1)
			{
				$return .= $online_days . ' d ';
			}
			
			if($online_hrs > 0)
			{
				$return .= $online_hrs . ' tim ';
			}
			
			if($online_mins > 0)
			{
				$return .= $online_mins . ' min';
			}
			elseif($online_hrs == 0 && $online_days == 0 && $online_mins == 0)
			{
				$return .= '0 min';
			}
			$return .= '</span>' . "\n";

			$return .= '<a href="/installningar/generalsettings.php" class="settings">' . "\n";
			$return .= 'Inställningar</a>' . "\n";

			$return .= '<a href="/logout.php">' . "\n";
			$return .= 'Logga ut</a>' . "\n";
			$return .= '</div>' . "\n";
		}
		else
		{
			$return = '<h3>Du har blivit utloggad</h3>' . "\n";
		}
		return $return;
	}


	function ui_bottom($options = null)
	{
		if(isset($_SESSION['new_design']))
		{
			echo ui_new_bottom($options);
			return;
		}
		
		global $SIDE_MODULES;
		echo '<br style="clear: both;" />' . "\n";

		if(substr($_SERVER['PHP_SELF'], 0, 18) != '/traffa/photos.php')
		{

		echo '<br style="clear: both;" />' . "\n";
		echo '<div id="internalad_topmargin"></div>' . "\n";
		$latest_post_params = array('color' => 'blue_deluxe');
		$fieldset_line_color = '#cccccc';
		$internal_ad_params = array('color' => 'blue');
		
		$output .= rounded_corners_top($latest_post_params);
			$last_threads = cache_load('latest_forum_threads');
			echo '<fieldset class="fieldset_ad" style="border: 1px solid ' . $fieldset_line_color . '"><legend>Senaste tråden i forumet</legend>' . "\n";
			echo '<h2>' . $last_threads[0]['title'] . '</h2>' . "\n";
			echo '<div style="padding: 10px;">' . "\n";
			echo discussion_forum_parse_output($last_threads[0]['content']);
			echo '</div>' . "\n";
			echo 'Skapad av <a href="/traffa/profile.php?id=' . $last_threads[0]['author'] . '">' . $last_threads[0]['username'] . '</a>, <a href="' . $last_threads[0]['url'] . '">skriv ett svar &raquo;</a>' . "\n";
			echo '</fieldset>';
		$output .= rounded_corners_bottom($latest_post_params);
		
		echo '<br />';
		
		$show_ad = rand(1, 10);		
		switch($show_ad)
		{
		case 1:
			$output .= rounded_corners_top($internal_ad_params);
			echo '<h2 style="margin-top: 0;">Vad hände egentligen den elfte september 2001?</h2>' . "\n";
			echo '<p>Efter attentaten i New York och Pentagon hade den amerikanska regeringen snart hela bilden klar och massmedia förmedlade den villigt till människor över hela världen: Det var terrornätverket al-Qaida under ledning av Osama bin Laden som anfallit det amerikanska folket. Orsaken: De hatar oss för vår frihet.</p>' . "\n";
			echo '<p>Men alla lät sig inte nöjas med officiella förklaringar. Man reagerade på att inga bevis lades fram eller ens ansågs behövas, man ifrågasatte om ett flygplan verkligen kunde få ett hus att störta samman, man upptäckte konstigheter i de officiella förklaringarna.</p>' . "\n";
			echo '<a href="/texter/?article=37">Läs hela artikeln om konspirationsteorierna kring 11 september-dåden här &raquo;</a>' . "\n";
			echo '<br style="clear: both;" />' ."\n";
			$output .= rounded_corners_bottom($internal_ad_params);
		break;
		case 2:
			$output .= rounded_corners_top($internal_ad_params);
			echo '<h2 style="margin-top: 0;">Chatta på IRC, som de riktiga hackerpojkarna!</h2>' . "\n";
			echo '<a href="/chat/"><img src="http://images.hamsterpaj.net/irchat_ad.png" /></a>' . "\n";
			echo '<p>Är du riktigt 1337 kör du förståss irssi och ansluter till <strong>irc.hamsterpaj.net</strong> och joinar <strong>#moget</strong>. Är du inte riktigt lika cool, utan mer som oss andra klickar du på bilden ovanför och väljer sedan en chattkanal.</p>' . "\n";
			echo '<br style="clear: both;" />' ."\n";
			$output .= rounded_corners_bottom($internal_ad_params);
		break;
		case 3:
			$output .= rounded_corners_top($internal_ad_params);
			echo '<h2 style="margin-top: 0;">Gillar du Hamburgare?</h2>' . "\n";
			echo '<a href="/hamburgare/test.php"><img src="http://images.hamsterpaj.net/hamburgers/gruppfoto_638.jpg" /></a>' . "\n";
			echo '<p>Vi gav oss ut på stan och köpte 14 hamburgare från kända kedjor. Kan du gissa vilken burgare som är vilken? <a href="/hamburgare/test.php">Gör vårat Hamburgar-test &raquo;</a></p>' . "\n";
			echo '<br style="clear: both;" />' ."\n";
			$output .= rounded_corners_bottom($internal_ad_params);
		break;
		case 4:
			$output .= rounded_corners_top($internal_ad_params);
			echo '<h2 style="margin-top: 0;"><a href="/mattan/falskt_personnummer.php">Vill du dölja din identitet på Internet?</a></h2>' . "\n";
			echo '<p>Eller är du nyfiken på hur personnummren fungerar? Vi har skrivit en guide till hur du förfalskar ett personnummer och gjort ett behändigt litet script där du väljer vilken identiet du vill ha. På några sekunder har du en påhittad identitet du kan använda på sidor som frågar efter personnummer!<br />Internet skall vara fritt!</p>' . "\n";
			echo '<br style="clear: both;" />' ."\n";
			$output .= rounded_corners_bottom($internal_ad_params);
		break;
		case 5:
			$output .= rounded_corners_top($internal_ad_params);
			echo '<h2 style="margin-top: 0;"><a href="/texter/?article=1">Gör en Cola-fontän med Coca Cola Light och Mentos!</a></h2>' . "\n";
			echo '<p>Med lite Coca Cola och en rulle godis kan man enkelt göra en spektakulär fontän. Det är inte farligt, kostar inte många kronor men kan bli ordentligt kladdigt.</p>' . "\n";
			echo '<br style="clear: both;" />' ."\n";
			$output .= rounded_corners_bottom($internal_ad_params);
		break;
		case 6:
			echo '<a href="http://www.hamsterpaj.net/artiklar/?action=show&id=71"><img style="margin-top: 5px;" src="http://images.hamsterpaj.net/lef-kuriosa_joar.png" /></a>' ."\n";
		break;
		case 7:
			echo '<a href="http://www.hamsterpaj.net/artiklar/?action=show&id=81">' . "\n";
			echo '<img style="margin-top: 5px;" src="http://images.hamsterpaj.net/indoorgolf.png" />' . "\n";
			echo '</a>' . "\n";
		break;
		case 8:
			echo '<a href="http://www.hamsterpaj.net/artiklar/?action=show&id=72">' . "\n";
			echo '<img style="margin-top: 5px;" src="http://images.hamsterpaj.net/globalregering.png" />' . "\n";
			echo '</a>' . "\n";
		break;
		case 9:
			echo '<a href="http://www.hamsterpaj.net/artiklar/?action=show&id=83">' . "\n";
			echo '<img style="margin-top: 5px;" src="http://images.hamsterpaj.net/utomjording.png" />' . "\n";
			echo '</a>' . "\n";
		break;
		case 10:
			echo '<a href="http://www.hamsterpaj.net/artiklar/?action=show&id=84">' . "\n";
			echo '<img style="margin-top: 5px;" src="http://images.hamsterpaj.net/virtuella_glasogon.png" />' . "\n";
			echo '</a>' . "\n";
		break;
		}
		}

		echo '</div>' . "\n"; // Close site_frame border
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		echo '<div id="main_right">' . "\n";
		echo '<div id="skyscraper">' . "\n";
		echo '<script type="text/javascript">CM8ShowAd("Skyscraper");</script>' . "\n";
		echo '</div>' . "\n";
		
		foreach(array('discussion_forum_remove_posts', 'discussion_forum_edit_posts', 'discussion_forum_rename_threads', 'discussion_forum_lock_threads', 'discussion_forum_sticky_threads', 'discussion_forum_move_thread', 'discussion_forum_post_addition', 'avatar_admin') as $privilegie)
		{
			if(is_privilegied($privilegie))
			{
				echo ui_render_right_module(array('handle' => 'administration', 'heading' => 'Administration'));
				break;
			}
		}
		


		if(login_checklogin())
		{
			echo ui_render_right_module(array('handle' => 'profile_visitors', 'heading' => 'Dina besökare'));			
		}
		echo ui_render_right_module(array('handle' => 'forum_threads', 'heading' => 'Forumtrådar'));
		echo ui_render_right_module(array('handle' => 'forum_posts', 'heading' => 'Inlägg i forumet'));
		echo ui_render_right_module(array('handle' => 'online_ovs', 'heading' => 'Inloggade Forumadmins'));
		echo ui_render_right_module(array('handle' => 'ad_1', 'heading' => 'Reklam'));
				
		echo '<div id="right_modules">';
		
		echo '<div style="background: white;">' . "\n";
		echo '<script type="text/javascript" src="http://www.adtrade.net/ad/p/?id=hamsterpaj_1&size=140x350&ad=001" charset="iso-8859-1"></script>';
		echo '</div>' . "\n";
	
		echo '</div>' . "\n";
		echo '</div>' . "\n";		

		echo '</div>' . "\n"; // Close site_container
		echo '</div>' . "\n"; // Close hamsterpaj_website
?>
<!-- START Nielsen//NetRatings SiteCensus V5.2 -->
<!-- COPYRIGHT 2006 Nielsen//NetRatings -->
<script type="text/javascript">
	var _rsCI="hamsterpaj-se";	 /* client ID */ 
	var _rsCG="0";	 /* content group */ 
	var _rsDN="//secure-dk.imrworldwide.com/";	 /* data node */ 
</script>
<script type="text/javascript" src="//secure-dk.imrworldwide.com/v52.js"></script>
<noscript>
	<img src="//secure-dk.imrworldwide.com/cgi-bin/m?ci=hamsterpaj-se&amp;cg=0&amp;cc=1" alt=""/>
</noscript>
<!-- END Nielsen//NetRatings SiteCensus V5.2 -->

<?php
		if(!login_checklogin())
		{
			echo '<div id="tiny_reg_form">' . "\n";
			$regform_suspended = cache_load('register_suspend');
			if($regform_suspended == 'disabled')
			{
				echo '<h1>Du måste vara inloggad för att kunna göra detta!</h1>'.  "\n";
				echo '<p>Just nu är registreringen avstängd, detta är extremt ovanligt. Om du väntar någon timme så borde den vara öppen igen!</p>' . "\n";
			}
			else
			{
				include(PATHS_INCLUDE . 'tiny_reg_form.html');
			}
			echo '</div>' . "\n";	
		}
		
		echo '</body>' . "\n";
		echo '</html>' . "\n";
	}

	function ui_render_right_module($parameters)
	{
		$parameters['heading'] = (isset($parameters['heading'])) ? $parameters['heading'] : $parameters['handle'];
		$style = ($_SESSION['module_states']['module_' . $parameters['handle']] == 'closed') ? ' style="display: none"' : '';
		
		$return .= '<div class="right_module_container">' . "\n";
		$return .= '<h3 class="right_module_heading" id="right_module_heading_' . $parameters['handle'] . '">' . $parameters['heading'] . '</h3>' . "\n";
		$return .= '<div class="right_module" id="right_module_' . $parameters['handle'] . '">' . "\n";
		include(PATHS_INCLUDE . 'right_modules/' . $parameters['handle'] . '.php');
		$return .= '</div>' . "\n";
		$return .= '<img src="http://images.hamsterpaj.net/right_modules/bottom.png" />' . "\n";
		$return .= '</div>' . "\n";

		return $return;
	}

	function insert_avatar($userid, $imgextra = NULL)
	{
		global $hp_url;
		$output = '<a href="javascript:;" onclick="window.open(\'' . $hp_url . 'avatar.php?id=' . $userid . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=410, height=600\')">';
		
		$output .= '<img src="' . IMAGE_URL . 'images/users/thumb/' . $userid . '.jpg?' . filemtime(PATHS_IMAGES . 'users/thumb/' . $userid . '.jpg');
		
		$output .= '" border="0" width="75" height="100" ';
		if (isset($imgextra) && preg_match("/alt/i",$imgextra)) {
			$output .= $imgextra;
		}
		else {
			$output .= 'alt="" '. $imgextra;
		}
		$output .= '/>';
		$output .= '</a>';
		return $output;
	}

	function ui_dropbox($title, $data, $styleinfo, $expanded = NULL)
	{
		$rand = rand(100000, 999999);
		$return = '<div';
		if (array_key_exists('class', $styleinfo))
		{
			$return.= ' class="' . $styleinfo['class'] . '"';
		}
		if (array_key_exists('style', $styleinfo))
		{
			$return.= ' style="' . $styleinfo['style'] . '"';
		}
		$return.= '>' . "\n";
		$return.= '<div class="droptitle" onclick="collapse_expand(\'' . $rand . '\');">' . "\n";
	  	$return.= '<h2 style="margin-top: 0;">' . $title . '</h2>' . "\n";
		$return.= '<img class="dropimage" id="dropbox_image_' . $rand . '" src="/images/';
		if (isset($expanded)) {
			$return.= 'collapse';
		}
		else
			{
			$return.= 'expand';
		}
		$return.= '.png" alt="" />' . "\n";
		$return.= '</div>' . "\n";
		$return.= '<div id="dropbox_' . $rand . '"';
		if (!isset($expanded))
		{
			$return.= ' style="display: none;"';
		}
		$return.= '>' . "\n";
		$return.= $data;
		$return.= '</div>' . "\n";
		$return.= '</div>' . "\n";
		return $return;
	}

function report_sql_error($query, $file = null, $line = null)
  {
    echo '<div class="server_message_error"><h2>Såhär skall det ju inte bli, usch!</h2><p>Ett fel på hamsterpaj har inträffat! Utvecklingsansvariga har meddelats om detta, du behöver inte rapportera felet. Vi åtgärdar det snart (om vi kan :P)</p>';
		echo '<h3 class="server_message_collapse_header" id="server_message_collapse_header_sqlerror">Visa felsökningsinformation</h3>' . "\n";
    echo '<div class="server_message_collapsed_information" id="server_message_collapse_information_sqlerror">' . "\n";
    echo '<br />Felsökningsinformation:<br />' . mysql_error();
    echo '<br />Frågan löd:<br /><p>' . htmlspecialchars($query) . '</p>';
    echo $file . ' #' . $line;
   	echo '<h1>Backtrace</h1>' . "\n";
   	preint_r(debug_backtrace());
		echo '</div></div>' . "\n";
    if(isset($file))
    {
    	echo '<strong>Logging</strong>';
			//log_to_file('sql_error', LOGLEVEL_ERROR, $file, $line, $query);
			trace('sql_errors', $query . ' in ' . $file . ' on line ' . $line);
    }
  }
?>
