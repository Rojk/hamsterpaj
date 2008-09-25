<?php
function ui_top($options = array())
{
	/* Den här raden skapades när nya ui_top skapades. Låt den vara kvar, så kommer
	   man ha något att le åt av nostalgiska syften. 2008-08-15, Joel.
	*/
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
	if(login_checklogin() && rand(1, 5) == 2)
	{
		$query = 'UPDATE login SET lastrealaction="' . time() . '" WHERE id="' . $_SESSION['login']['id'] . '"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
		
	$options['adtoma_category'] = isset($options['adtoma_category']) ? $options['adtoma_category'] : 'other';
	define('ADTOMA_CATEGORY', $options['adtoma_category']);
	
	$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' . "\n";
	$output .= '	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	$output .= '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
	$output .= '	<head>' . "\n";
	
	$output .=  '<meta name="description" content="' . $options['meta_description'] . '" />' . "\n";
	$output .=  '<meta name="keywords" content="' . $options['meta_keywords'] . '" />' . "\n";
	$output .=  '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
	
	$options['title'] = (isset($options['title'])) ? $options['title'] : 'Hamsterpaj.net - Onlinespel, community, forum och annat kul ;)';
	$output .= '		<title>' . $options['title'] . '</title>' . "\n";
	$output .= '		<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
	$output .= '		<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";

	$options['javascripts'] = (isset($options['javascripts']) && is_array($options['javascripts'])) ? $options['javascripts'] : array();
	$options['stylesheets'] = (isset($options['stylesheets']) && is_array($options['stylesheets'])) ? $options['stylesheets'] : array();
	
	// Javascripts (Order: jQuery, Womlib (needs jQuery to work properly!), synchronize, The rest...)
	$options['javascripts'] = array_merge(array(
		'jquery.js',
		'womlib.js',
		'jquery.dimensions.js',
		'jquery-ui.js',
		'jquery.cookie.js',
		'synchronize.js'
	), $options['javascripts']);
	$options['javascripts'][] = 'ui_server_message.js';
	$options['javascripts'][] = 'scripts.js';
	$options['javascripts'][] = 'steve.js';
	$options['javascripts'][] = 'new_guestbook.js';
	$options['javascripts'][] = 'forum.js';
	$options['javascripts'][] = 'posts.js';
	$options['javascripts'][] = 'abuse_report.js';
	$options['javascripts'][] = 'poll.js';
	$options['javascripts'][] = 'swfobject.js';
	$options['javascripts'][] = 'md5.js';
	$options['javascripts'][] = 'xmlhttp_login.js';
	$options['javascripts'][] = 'xmlhttp.js';
	$options['javascripts'][] = 'fult_dhml-skit_som_faar_bilder_att_flyga.js';
	$options['javascripts'][] = 'wave_effect.js';
	$options['javascripts'][] = 'joels_hackerkod.js';
	$options['javascripts'][] = 'ui.js';
	$options['javascripts'][] = 'ui_modules.js';
	$options['javascripts'][] = 'ui_business_card.js';
	$options['javascripts'][] = 'ui_multisearch.js';
	if(!login_checklogin())
	{
		$options['javascripts'][] = 'tiny_reg_form.js';
	}
	if($_SESSION['login']['id'] > 0)
	{
		$options['javascripts'][] = 'stay_online.js';
	}
	
	// Stylesheets
	array_unshift($options['stylesheets'], 'ui.css');
	$options['stylesheets'][] = 'tiny_reg_form.css';
	$options['stylesheets'][] = 'shared.css';
	$options['stylesheets'][] = 'modules.css';
	$options['stylesheets'][] = 'buttons.css';
	$options['stylesheets'][] = 'new_guestbook.css';
	$options['stylesheets'][] = 'rounded_corners.css';
	$options['stylesheets'][] = 'message.css';
	$options['stylesheets'][] = 'domTT.css';
	$options['stylesheets'][] = 'poll.css';
	$options['stylesheets'][] = 'ui_modules/friends_online.css';
	$options['stylesheets'][] = 'ui_modules/friends_notices.css';
	$options['stylesheets'][] = 'ui_modules/forum_threads.css';
	$options['stylesheets'][] = 'ui_modules/forum_posts.css';
	
	// Remove duplicates
	$options['stylesheets'] = array_unique($options['stylesheets']);
	$options['javascripts'] = array_unique($options['javascripts']);
	
	$output .= '<style type="text/css">' . "\n";
	foreach($options['stylesheets'] as $stylesheet)
	{
		$output .= '@import url(\'/stylesheets/' . $stylesheet . '?version=' . filemtime(PATHS_WEBROOT . 'stylesheets/' . $stylesheet) . '\');' . "\n";
	}
	$output .= '</style>' . "\n";

	foreach($options['javascripts'] as $javascript)
	{
		$output .= '<script type="text/javascript" language="javascript" src="/javascripts/' . $javascript . '?version=' . filemtime(PATHS_WEBROOT . 'javascripts/' . $javascript) . '"></script>' . "\n";
	}
	
	$output .= $options['header_extra'];
	
	$output .= '</head> ' . "\n";
	
	$output .= '<body' . (isset($options['body_extra']) ? ' ' . $options['body_extra'] : '') . '>' . "\n";
	
	$output .= '<script type="text/javascript">' . "\n";
	$adtoma_gender = (in_array($_SESSION['userinfo']['gender'], array('P', 'F'))) ? $_SESSION['userinfo']['gender'] : 'xx';
	$adtoma_age = ($_SESSION['userinfo']['birthday'] != '0000-00-00') ? date_get_age($_SESSION['userinfo']['birthday']) : 'xx';
	$adtoma_birthyear = ($_SESSION['userinfo']['birthday'] != '0000-00-00') ? substr($_SESSION['userinfo']['birthday'], 0, 4) : 'xx';
	$output .= "\t" . 'var CM8Server = "ad.adtoma.com";' . "\n";
	$output .= "\t" . 'var CM8Cat = "hp.' . ADTOMA_CATEGORY . '";' . "\n";
	$output .= "\t" . 'var CM8Profile = "hp_age=' . $adtoma_age . '&amp;hp_birthyear=' . $adtoma_birthyear . '&amp;hp_gender=' . $adtoma_gender . '"' . "\n";
	$output .= '</script>' . "\n";
	$output .= '<script language="JavaScript" type="text/javascript" src="http://ad.adtoma.com/adam/cm8adam_1_call.js"></script>' . "\n";
	
	// A big notice-bar shown on top, 60px height.
	$full_page_notice = '<h2>Hamsterpaj kör nu för första gången den nya designen ("Amanda"), som en del av Hamsterpaj version 3.</h2>';
	$full_page_notice .= '<span>Har du hittat några buggar eller fel i nya designen? Vänligen rapportera dem i <a href="/hamsterpaj/suggestions.php">förslagslådan</a>.</span>';
	$full_page_notice_id = 'dynamic01ochumbaaerkaera'; //Set this to a unique ID for this notice
	
	// Don't remove those lines
	if(isset($full_page_notice) && $_COOKIE[$full_page_notice_id] != 'closed')
	{
		$output .= '<div id="ui_full_page_notice" class="' . $full_page_notice_id . '">' . "\n";
		$output .= '<img src="" alt="[close]" id="ui_full_page_notice_close" />' . "\n";
			$output .= $full_page_notice . "\n";
		$output .= '</div>' . "\n";
	}
	
	$output .= '<div>' . "\n";
	$output .= '	<script type="text/javascript">CM8ShowAd("Bigbanner");</script>' . "\n";
	$output .= '</div>' . "\n";
	
	
	$output .= '	<div id="ui_wrapper">' . "\n";
	$output .= '		<div id="ui_header">' . "\n";
	$output .= '			<h1>' . "\n";
	$output .= '				<a href="/">Hamsterpaj.net</a>' . "\n";
	$output .= '			</h1>' . "\n";
	
	if ( login_checklogin() )
	{
		$output .= '			<div id="ui_noticebar">' . "\n";
		$output .= '				<ul>' . "\n";
		
		$notices = ui_notices_fetch();
		
		$output .= '					<li>' . "\n";
		$output .= '						<a id="ui_noticebar_guestbook' . ($notices['guestbook'] > 0 ? '_active' : '') . '" href="/traffa/guestbook.php?user_id=' . $_SESSION['login']['id'] . '">';
		$output .=								(($notices['guestbook'] > 0) ? (($notices['guestbook'] == 1) ? 'Ett nytt' : $notices['guestbook'] . ' nya') : 'Gästbok');
		$output .= '						</a>';
		$output .= '					</li>' . "\n";
		
		$output .= '					<li id="ui_noticebar_forum_container">' . "\n";
		$output .= '						<a id="ui_noticebar_forum' . ($notices['discussion_forum']['new_notices'] > 0 ? '_active' : '') . '" href="/diskussionsforum/notiser.php">';
		$output .=								(($notices['discussion_forum']['new_notices'] > 0) ? (($notices['discussion_forum']['new_notices'] == 1) ? 'Ny notis' : $notices['discussion_forum']['new_notices'] . ' nya') : 'Forum');
		$output .= '						</a>' . "\n";
		$output .= '						<ul class="ui_noticebar_info">' . "\n";
		$output .= '							<li class="ui_noticebar_infoheader"><h3>Dina forumnotiser</h3></li>' . "\n";
		foreach($notices['discussion_forum']['subscriptions'] as $subscription)
		{
			$output .= '							<li><a href="' . $subscription['url'] . '">' . $subscription['title'] . ' (<strong>' . $subscription['unread_posts'] . ' nya</strong>)</a></li>' . "\n";
		}
		$output .= '						</ul>' . "\n";
		$output .= '					</li>' . "\n";
		
		$output .= '					<li id="ui_noticebar_groups_container">' . "\n";
		$output .= '						<a id="ui_noticebar_groups' . ($notices['groups']['unread_notices'] > 0 ? '_active' : '') . '" href="/traffa/groupnotices.php">';
		$output .= 								(($notices['groups']['unread_notices'] >= 1) ? (($notices['groups']['unread_notices'] == 1) ? 'Ett nytt' : $notices['groups']['unread_notices'] . ' nya') : 'Grupper');
		$output .= '						</a>' . "\n";
		$output .= '						<ul class="ui_noticebar_info">' . "\n";
		$output .= '							<li class="ui_noticebar_infoheader"><h3>Dina gruppinl&auml;gg</h3></li>' . "\n";
		foreach($notices['groups']['groups'] as $group_id => $group)
		{
			$output .= '							<li><a href="/traffa/groups.php?action=goto&groupid=' . $group_id . '">' . (($group['unread_messages'] > 0) ? '<strong>' : '') . $group['title'] . ' (' . $group['unread_messages'] . ' nya)' . (($group['unread_messages'] > 0) ? '</strong>' : '') . '</a></li>' . "\n";
		}
		$output .= '						</ul>' . "\n";
		$output .= '					</li>' . "\n";
		
		$output .= '					<li>' . "\n";
		$output .= '						<a id="ui_noticebar_events' . (($notices['photo_comments'] + $notices['messages'] > 0) ? '_active' : '') . '" href="/traffa/events.php">' . (($notices['photo_comments'] + $notices['messages'] >= 1) ? (($notices['photo_comments'] + $notices['messages'] == 1) ? 'En ny' : ($notices['photo_comments'] + $notices['messages']) . ' nya') : 'Händelser') . '</a>' . "\n";
		$output .= '						<ul class="ui_noticebar_info">' . "\n";
		$output .= '							<li class="ui_noticebar_infoheader"><h3>Dina h&auml;ndelser</h3></li>' . "\n";
		$output .= '						</ul>' . "\n";
		$output .= '					</li>' . "\n";
		
		$output .= '				</ul>' . "\n";
		$output .= '			</div>' . "\n";
		
		$output .= '			<div id="ui_statusbar">' . "\n";
		$output .= '				<a href="#" title="Byt visningsbild">' . "\n";
		$output .= '					<img src="http://images.hamsterpaj.net/images/users/thumb/' . $_SESSION['login']['id'] . '.jpg" alt="" onclick="window.open(\'/avatar.php?id=' . $_SESSION['login']['id'] . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=410, height=600\')"/>' . "\n";
		$output .= '				</a>' . "\n";
		$output .= '				<div id="ui_statusbar_username">' . "\n";
		$output .= '					<a href="/traffa/profile.php?user_id=' . $_SESSION['login']['id'] . '"><strong>' . $_SESSION['login']['username'] . '</strong></a><span> | </span><a href="/logout.php">Logga ut</a><br />' . "\n";
		$output .= '				</div>' . "\n";
		$output .= '				<div id="ui_statusbar_logintime">' . "\n";
	
		$online_secs = time() - $_SESSION['login']['lastlogon'];
		$online_days = floor($online_secs/86400);
		$online_hrs = floor(($online_secs - $online_days*86400)/3600);		
		$online_mins = floor(($online_secs%3600)/60);
	
		$time_online_readable = ($online_days == 1) ? '1 d, ' : (($online_days > 1) ? $online_days . ' d ' : '');
		$time_online_readable .= ($online_hrs > 0) ? $online_hrs . ' tim ' : '';
		$time_online_readable .= ($online_mins > 0) ? $online_mins . ' min' : (($online_hrs == 0 && $online_days == 0 && $online_mins == 0) ? '0 min' : '');
	
		$output .= '					<span>' . $time_online_readable . '</span>' . "\n";
		$output .= '				</div>' . "\n";
		$output .= '				<div id="ui_statusbar_forumstatus">' . "\n";
		$output .= '					<span title="' . $_SESSION['userinfo']['user_status'] . '">' . ((strlen(trim($_SESSION['userinfo']['user_status'])) > 0) ? ((mb_strlen($_SESSION['userinfo']['user_status'], 'UTF8') > 22) ? mb_substr($_SESSION['userinfo']['user_status'], 0, 19, 'UTF8') . '...' : $_SESSION['userinfo']['user_status']) : 'Ingen status') . '</span>' . "\n";
		$output .= '				</div>' . "\n";

		$output .= '			</div>' . "\n";
	}
	else
	{
		$output .= '			<div id="ui_login">' . "\n";
		$output .= '				<form action="/login.php?action=login" method="post">' . "\n";
		$output .= '					<p><label><strong>Användarnamn:</strong><br /><input id="ui_login_username" type="text" name="username" /></label></p>' . "\n";
		$output .= '					<p><label><strong>Lösenord:</strong><br /><input id="ui_login_password" type="password" name="password" /></label></p>' . "\n";
		$output .= '					<p><input id="ui_login_submit" type="submit" value="Logga in" /></p>' . "\n";
		$output .= '				</form>' . "\n";
		$output .= '				<p id="ui_login_register"><a href="/register.php"><button>Registrera</button></a></p>';
		$output .= '			</div>' . "\n";
	} // end login_checklogin
	
	$output .= '		</div>' . "\n";
	$output .= '		<div id="ui_menu">' . "\n";
	$output .= '				<ul>' . "\n";
	
	
	
	
	global $menu;
	
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
			$target = (isset($current_menu['target'])) ? ' target="' . $current_menu['target'] . '"' : '';
			$output .= '					<li>' . "\n";
			$output .= '						<a href="' . $current_menu['url'] . '" class="root-a"' . $target . '>' . $current_menu['label'] . '</a>' . "\n";
			$output .= '							<ul>' . "\n";
			$output .= '								<li><a href="' . $current_menu['url'] . '">Start</a>' . "\n";
			if(count($current_menu['children']) > 0)
			{
				$output .= ui_menu_subcategories_fetch($current_menu['children'], $options);
			}
			$output .= '							</ul>' . "\n";
			$output .= '					</li>' . "\n";
		}
	}
	
	$output .= '				</ul>' . "\n";
	$output .= '<img src="http://images.hamsterpaj.net/steve/steve.gif" id="steve" />' . "\n";
	$output .= '		</div>' . "\n";

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
		$content = '<span class="ui_notice_time">' . date('H:i', $data['timestamp']) . '</span>' . "\n";
		$content .= '<span class="ui_notice_event">' . $RECENT_UPDATES[$data['type']] . '</span>' . "\n";
		$content .= '<span class="ui_notice_link"><a href="/recent_updates_redirect.php?id=' . $data['id'] . '&url=' . urlencode($data['url']) . '&source=global_notice">' . $data['label'] . '</a></span>' . "\n";
		
		$noticemessages[] = array('html' => $content);
		$_SESSION['recent_update_notifier'][$data['id']]++;
	}

	foreach($noticemessages AS $noticemessage)
	{
		$output .= '<div id="ui_notice">' . "\n";
		if(isset($noticemessage['timestamp']))
		{
			$output .= '<span class="ui_notice_time">' . date('H:i', $noticemessage['timestamp']) . '</span>' . "\n";
		}
		$output .= $noticemessage['html'];
		$output .= '</div>' . "\n";
	}
	
	if(login_checklogin())
	{
		if(isset($_SESSION['unread_gb_entries']))
		{
			$output .= guestbook_list($_SESSION['unread_gb_entries']);
			unset($_SESSION['unread_gb_entries']);
		}
	}
	
	$output .= '		<div id="ui_content">' . "\n";
	
	$output .= '<script type="text/javascript">CM8ShowAd("Rektangel");</script>' . "\n";
	
	if(isset($options['return']) && $options['return'] == true)
	{
		return $output;
	}
	else
	{
		echo $output;
	}
}

function ui_bottom($options = array())
{
	$output .= '<br style="clear: both;" />' . "\n";
	$output .= '</div>' . "\n";
	
	$output .= '<div id="skyscraper">' . "\n";
	$output .= '<script type="text/javascript">CM8ShowAd("Skyscraper");</script>' . "\n";
	$output .= '</div>' . "\n";
	
	$output .= '<div id="ui_modulebar">' . "\n";
	
	$modules = array(
		'multisearch' => 'Multi-sök',
		'latest_threads' => 'Forumtrådar',
		'latest_posts' => 'Inlägg i forumet'
	);
	
	if ( login_checklogin() )
	{
		$modules['friends_online'] = 'Vänner online';
		$modules['friends_notices'] = 'Vänner(s)notiser'; 
	}
	
	foreach(array('discussion_forum_remove_posts', 'discussion_forum_edit_posts', 'discussion_forum_rename_threads', 'discussion_forum_lock_threads', 'discussion_forum_sticky_threads', 'discussion_forum_move_thread', 'discussion_forum_post_addition') as $privilegie)
	{
		if (is_privilegied($privilegie))
		{
			$ui_administration_module_show = true;
		}
	}
	
	if ($ui_administration_module_show === true)
	{
		$modules['administration'] = 'Administration';
	}
	
	if ( is_array($_SESSION['module_order']) && count($_SESSION['module_order']) == count($modules))
	{
		foreach ( $_SESSION['module_order'] as $handle )
		{
			if ( isset($modules[$handle]) )
			{
				$output .= ui_module_render(ui_module_fetch(array(
					'header' => $modules[$handle],
					'handle' => $handle
				)));
			}
		}
	}
	else
	{
		foreach ( $modules as $handle => $header )
		{
			$output .= ui_module_render(ui_module_fetch(array(
				'header' => $header,
				'handle' => $handle
			)));
		}
	}
	
	$output .= '		</div>' . "\n";
	$output .= '	<div id="ui_break"></div> ' . "\n";
	$output .= '	</div>' . "\n";
	
	$output .= '<div style="background: white;">' . "\n";
	$output .= '<script type="text/javascript" src="http://www.adtrade.net/ad/p/?id=hamsterpaj_1&size=140x350&ad=001" charset="iso-8859-1"></script>';
	$output .= '</div>' . "\n";
	
	$output .= '<!-- START Nielsen//NetRatings SiteCensus V5.2 -->' . "\n";
	$output .= '<!-- COPYRIGHT 2006 Nielsen//NetRatings -->' . "\n";
	$output .= '<script type="text/javascript">' . "\n";
	$output .= '	var _rsCI="hamsterpaj-se";	 /* client ID */' . "\n";
	$output .= '	var _rsCG="0";	 /* content group */' . "\n";
	$output .= '	var _rsDN="//secure-dk.imrworldwide.com/";	 /* data node */' . "\n";
	$output .= '</script>' . "\n";
	$output .= '<script type="text/javascript" src="//secure-dk.imrworldwide.com/v52.js"></script>' . "\n";
	$output .= '<noscript>' . "\n";
	$output .= '	<img src="//secure-dk.imrworldwide.com/cgi-bin/m?ci=hamsterpaj-se&amp;cg=0&amp;cc=1" alt=""/>' . "\n";
	$output .= '</noscript>' . "\n";
	$output .= '<!-- END Nielsen//NetRatings SiteCensus V5.2 -->' . "\n";
	
	if(!login_checklogin())
	{
		$output .= '<div id="tiny_reg_form">' . "\n";
		$regform_suspended = cache_load('register_suspend');
		if($regform_suspended == 'disabled')
		{
			$output .= '<h1>Du måste vara inloggad för att kunna göra detta!</h1>'.  "\n";
			$output .= '<p>Just nu är registreringen avstängd, detta är extremt ovanligt. Om du väntar någon timme så borde den vara öppen igen!</p>' . "\n";
		}
		else
		{
			$output .= file_get_contents(PATHS_INCLUDE . 'tiny_reg_form.html');
		}
		$output .= '</div>' . "\n";	
	}
	
	$output .= '</body>' . "\n";
	$output .= '</html>' . "\n";
	
	if(isset($options['return']) && $options['return'] == true)
	{
		return $output;
	}
	else
	{
		echo $output;
	}
}

function ui_menu_subcategories_fetch($menu, $ui_options)
{
	foreach($menu as $handle => $menu_item)
	{
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

		$return .= '<li' . $class . '><a href="' . $menu_item['url'] . '">' . $menu_item['label'] . '</a>' . "\n";
		if(count($menu_item['children']) > 0)
		{
			$return .= '<ul>' . "\n";
			$return .= ui_menu_subcategories_fetch($menu_item['children'], $ui_options);
			$return .= '</ul>' . "\n";
		}
		$return .= '</li>' . "\n";
	}
	
	return $return;
}

function ui_notices_fetch()
{
	if(login_checklogin())
	{
		$notices = array();
		if($_SESSION['cache']['lastupdate'] < time() - 20)
		{
			cache_update_all();
		}
		
		$notices['guestbook'] = $_SESSION['notices']['unread_gb_entries'];
		$notices['discussion_forum'] = array('new_notices' => $_SESSION['forum']['new_notices'], 'subscriptions' => array());
		foreach($_SESSION['forum']['subscriptions'] as $subscription)
		{
			if($subscription['unread_posts'] > 0)
			{
				$notices['discussion_forum']['subscriptions'][] = $subscription;
			}
		}
		
		$notices['groups'] = array('unread_notices' => $_SESSION['cache']['unread_group_notices'], 'groups' => $_SESSION['cache']['group_notices']);
		
		$notices['photo_comments'] = $_SESSION['cache']['unread_photo_comments'];
		$notices['messages'] = $_SESSION['notices']['unread_messages'];
		
		return $notices;
	}
	else
	{
		throw new Exception('Du har blivit utloggad. Logga in igen =D');
	}		
}

function ui_module_fetch($options)
{
	include(PATHS_INCLUDE . 'ui_module/' . $options['handle'] . '.module.php');
	return $options;
}

function ui_module_render($options)
{
	$state = (isset($_SESSION['module_states'][$options['handle']])) ? $_SESSION['module_states'][$options['handle']] : 'max';
	$class = ($state == 'min') ? 'ui_module_state_min': 'ui_module_state_max';
	$output .= '			<div class="ui_module ' . $class . '" id="ui_module_' . $options['handle'] . '">' . "\n";
	$output .= '				<div class="ui_module_header">' . "\n";
	$output .= '					<h2>' . $options['header'] . '</h2>' . "\n";
	$output .= '				</div>' . "\n";
	$output .= '				<div class="ui_module_content">' . "\n";
	$output .= $options['output'];
	$output .= '				</div>' . "\n";
	$output .= '			</div>	' . "\n";
	
	return $output;
}










































	function rounded_corners_top($options, $return = false)
	{
		global $ROUNDED_CORNERS;
		
		$ROUNDED_CORNERS['last_top_call'] = $options;

		$options['color'] = (in_array($options['color'], $ROUNDED_CORNERS['colors'])) ? $options['color'] : 'blue';
		$options['dimension'] = (in_array($options['dimension'], $ROUNDED_CORNERS['dimensions'])) ? $options['dimension'] : 'full';
		
		$style = (isset($options['style'])) ? ' style="' . $options['style'] . '"': '';
		$id = (isset($options['id'])) ? ' id="' . $options['id'] . '"': '';
		$content_id = (isset($options['id'])) ? ' id="' . $options['id'] . '_content"': '';
		
		$output .= "\n\n";
		if(!isset($options['new_layout_beta']))
		{		
			$output .= '<!-- Rounded corners div. Color: ' . $color . ', dimension: ' . $dimension . '-->' . "\n";
			$output .= '<div class="rounded_corners"' . $style . $id .'>' . "\n";
			$output .= '<img src="' . IMAGE_URL . 'css_backgrounds/rounded_corners/' . $options['color'] . '_' . $options['dimension'] . '_top.png"  class="rounded_corners_top_image" />' . "\n";
			$output .= '<div class="rounded_corners_' . $options['color'] . '_' . $options['dimension'] . '"' . $content_id . '>' . "\n";
		}
		else
		{
			$output .= '<div class="rounded_corners_beta_' . $options['color'] . '"' . $style . $id . '>';
			$output .= '<div class="top">' . "\n";
			$output .= '<div class="content">' . "\n";
		}
		
		
		if($return || $options['return'])
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}
	
	function rounded_corners_bottom($options, $return = false)
	{
		global $ROUNDED_CORNERS;
		
		if(isset($ROUNDED_CORNERS['last_top_call']) && !empty($ROUNDED_CORNERS['last_top_call']))
		{
			$options = array_merge($ROUNDED_CORNERS['last_top_call'], $options);
			$ROUNDED_CORNERS['last_top_call'] = array();
		}

		$options['color'] = (in_array($options['color'], $ROUNDED_CORNERS['colors'])) ? $options['color'] : 'blue';
		$options['dimension'] = (in_array($options['dimension'], $ROUNDED_CORNERS['dimensions'])) ? $options['dimension'] : 'full';

		if(!isset($options['new_layout_beta']))		
		{
			$output .= '</div>' . "\n";
			$output .= '<img src="' . IMAGE_URL . 'css_backgrounds/rounded_corners/' . $options['color'] . '_' . $options['dimension'] . '_bottom.png" class="rounded_corners_bottom_image"/>' . "\n";
			$output .= '</div>' . "\n\n";
		}
		else
		{
			$output .= "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
		}
		
		if($return || $options['return'])
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}

	function rounded_corners($content, $options, $do_return)
	{
		$return .= rounded_corners_top($options, $do_return);
		$return .= $content;
		$return .= rounded_corners_bottom($options, $do_return);
		if($do_return)
		{
			return $return;
		}
		else
		{
			echo $return;
		}
	}
	
	function rounded_corners_tabs_top($options, $return = false)
	{
		global $ROUNDED_CORNERS;

		$options['color'] = (in_array($options['color'], $ROUNDED_CORNERS['colors'])) ? $options['color'] : 'blue';
		$options['dimension'] = (in_array($options['dimension'], $ROUNDED_CORNERS['dimensions'])) ? $options['dimension'] : 'full';
		
		$style = (isset($options['style'])) ? ' style="' . $options['style'] . '"': '';
		$id = (isset($options['id'])) ? ' id="' . $options['id'] . '"': '';
		$content_id = (isset($options['id'])) ? ' id="' . $options['id'] . '_content"': '';
		
		if(isset($options['tabs']))
		{
			foreach($options['tabs'] as $tab)
			{
				$tab_id = (isset($tab['id'])) ? ' id="' . $tab['id'] . '"' : '';
				$tab_current = (isset($tab['current']) && $tab['current'] == true) ? ' class="_current"' : '';

				$tabs_output .= '<div class="_tab">' . "\n";
				$tabs_output .= '<div class="_left">&nbsp;</div>' . "\n";
				$tabs_output .= '<div class="_label">';
				$tabs_output .= '<a href="' . $tab['href'] . '"' . $tab_id . $tab_current . '>' . $tab['label'] . '</a></div>' . "\n";
				$tabs_output .= '<div class="_right">&nbsp;</div>' . "\n";
				$tabs_output .= '</div>' . "\n";
			}
		}else{
			$tabs_output = '<!-- No tabs loaded -->' . "\n";
		}
				
		$output .= "\n\n";
		$output .= '<!-- Rounded corners div with tabs. Color: ' . $color . ', dimension: ' . $dimension . '-->' . "\n";
		$output .= '<div class="rounded_corners_tabs_' . $options['dimension'] . '_' . $options['color'] . '"' . $style . $id .'>' . "\n";
		$output .= $tabs_output;
		$output .= '<div class="_top">&nbsp;</div>' . "\n";
		$output .= '<div class="_content"' . $content_id . '>' . "\n";
		
		if($return || $options['return'])
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}
	
	function rounded_corners_tabs_bottom($options, $return = false)
	{	
		$output .= "\n" . '<br style="clear: both" />' . "\n";
		$output .= '</div>' . "\n";
		$output .= '<div class="_bottom">&nbsp;</div>' . "\n";
		$output .= '</div>' . "\n\n";
		if($return || $options['return'])
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}
	
	function message_top($options)
	{
		if(!isset($options['type']))
		{
			$options['type'] = 'standard';
		}
		$content .= '<li class="message">' . "\n";
		$content .= '<div class="' . $options['type'] . '">' . "\n";
			$content .= ui_avatar($options['user_id']) . "\n";
				$content .= '<div class="container">' . "\n";
					$content .= '<div class="top_bg">' . "\n";
						$content .= '<div class="bottom_bg">' . "\n";
							$content .= '<div>' . "\n";
		return $content;
	}
	
	function message_bottom()
	{
						$content .= '</div>' . "\n";
					$content .= '</div>' . "\n";
				$content .= '</div>' . "\n";
			$content .= '</div>' . "\n";
			$content .= '</div>' . "\n";
		$content .= '</li>' . "\n";
		return $content;
	}
	
	function ui_avatar($user_id, $options)
	{
		if(!is_numeric($user_id))
		{
			return 'Avatar id not numeric, aborting...';
		}
		$img_path = IMAGE_PATH . 'images/users/thumb/' . $user_id . '.jpg';
		$style = (isset($options['style'])) ? ' style="' . $options['style'] . '"' : '';
		if (file_exists($img_path))
		{
			return '<img src="' . IMAGE_URL . 'images/users/thumb/' . $user_id . '.jpg?cache_prevention=' . filemtime($img_path) . '" class="user_avatar"' . $style . ' />' . "\n";
		}
		else
		{
			return '<img src="' . IMAGE_URL . '/images/users/no_image_mini.png" class="user_avatar"' . $style . ' />' . "\n";
		}
	}
	
	function ui_server_message($options)
	{
		$options['title'] = isset($options['title']) ? $options['title'] : 'Title saknas';
		$options['collapse_link'] = isset($options['collapsed_link']) ? $options['collapse'] : 'Visa mer information';
		$options['collapse_id'] = rand(100000, 999999);
		$options['type'] = isset($options['type']) ? $options['type'] : 'notification';

		$output .= '<div class="' . $options['type'] . '">' . "\n";
			$output .= '<h2>' . $options['title'] . '</h2>' . "\n";
			$output .= '<p>' . $options['message'] . '</p>' . "\n";
			if (isset($options['collapse_information']))
			{
				$output .= '<h3 class="server_message_collapse_header" id="server_message_collapse_header_' . $options['collapse_id'] . '">' . $options['collapse_link'] . '</h3>' . "\n";
				$output .= '<div class="server_message_collapsed_information" id="server_message_collapse_information_' . $options['collapse_id'] . '">';
					$output .= $options['collapse_information'] . "\n";
				$output .= '</div>' . "\n";
			}
		$output .= '</div>' . "\n";
		return $output;
	}
	
?>