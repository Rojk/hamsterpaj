<?php
function ui_new_top($options = array())
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
	$output .= '<div>' . "\n";
	$output .= '	<script type="text/javascript">CM8ShowAd("Bigbanner");</script>' . "\n";
	$output .= '</div>' . "\n";
	
	
	$output .= '	<div id="ui_wrapper">' . "\n";
	$output .= '		<div id="ui_header">' . "\n";
	$output .= '			<h1>' . "\n";
	$output .= '				<a href="/">Hamsterpaj.net</a>' . "\n";
	$output .= '			</h1>' . "\n";
	
	$output .= '			<div id="ui_noticebar">' . "\n";
	$output .= '				<ul>' . "\n";
	
	$notices = ui_notices_fetch();
	
	$output .= '					<li><a id="ui_noticebar_guestbook" href="/traffa/guestbook.php?user_id=' . $_SESSION['login']['id'] . '">' . (($notices['guestbook'] > 0) ? (($notices['guestbook'] == 1) ? 'Ett nytt' : $notices['guestbook'] . ' nya') : 'Gästbok') . '</a></li>' . "\n";
	
	$output .= '					<li>' . "\n";
	$output .= '						<a id="ui_noticebar_forum" href="/diskussionsforum/notiser.php">' . (($notices['discussion_forum'] > 0) ? (($notices['discussion_forum'] == 1) ? 'Ny notis' : $notices['discussion_forum'] . ' nya') : 'Forum') . '</a>' . "\n";
	$output .= '						<ul class="ui_noticebar_info">' . "\n";
	$output .= '							<li class="ui_noticebar_infoheader"><h3>Dina forumnotiser</h3></li>' . "\n";
	$output .= '							<li><a href="#">Bästa skräckfilmen? (<strong>2 nya</strong>)</a></li>' . "\n";
	$output .= '							<li><a href="#">vad heter filmen? (<strong>1 nya</strong>)</a></li>' . "\n";
	$output .= '							<li><a href="#">Motivation (<strong>4 nya</strong>)</a></li>' . "\n";
	$output .= '							<li><a href="#">Roligt klipp (<strong>1 nya</strong>)</a></li>' . "\n";
	$output .= '							<li><a href="#">CS, hur får man stor snopp? (<strong>1222 nya</strong>)</a></li>' . "\n";
	$output .= '						</ul>' . "\n";
	$output .= '					</li>' . "\n";
	
	$output .= '					<li>' . "\n";
	$output .= '						<a id="ui_noticebar_groups" href="/traffa/groupnotices.php">' . (($notices['groups'] > 1) ? (($notices['groups'] == 0) ? 'Ett nytt' : $notices['groups'] . ' nya') : 'Grupper') . '</a>' . "\n";
	$output .= '						<ul class="ui_noticebar_info">' . "\n";
	$output .= '							<li class="ui_noticebar_infoheader"><h3>Dina gruppinl&auml;gg</h3></li>' . "\n";
	$output .= '						</ul>' . "\n";
	$output .= '					</li>' . "\n";
	
	$output .= '					<li>' . "\n";
	$output .= '						<a id="ui_noticebar_events" href="#">Händelser</a>' . "\n";
	$output .= '						<ul class="ui_noticebar_info">' . "\n";
	$output .= '							<li class="ui_noticebar_infoheader"><h3>Dina h&auml;ndelser</h3></li>' . "\n";
	$output .= '						</ul>' . "\n";
	$output .= '					</li>' . "\n";
	
	$output .= '				</ul>' . "\n";
	$output .= '			</div>' . "\n";
	
	$output .= '			<div id="ui_statusbar">' . "\n";
	$output .= '				<a href="#" title="Byt visningsbild">' . "\n";
	$output .= '					<img src="http://images.hamsterpaj.net/images/users/thumb/' . $_SESSION['login']['id'] . '.jpg" alt="" onclick="window.open(\'' . $hp_url . 'avatar.php?id=' . $_SESSION['login']['id'] . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=410, height=600\')"/>' . "\n";
	$output .= '				</a>' . "\n";
	$output .= '				<div id="ui_statusbar_username">' . "\n";
	$output .= '					<strong>' . $_SESSION['login']['username'] . '</strong><span> | </span><a href="">Logga ut</a><br />' . "\n";
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
	$output .= '					<span>' . ((strlen(trim($_SESSION['userinfo']['user_status'])) > 0) ? $_SESSION['userinfo']['user_status'] : 'Ingen status') . '</span>' . "\n";
	$output .= '				</div>' . "\n";
	$output .= '			</div>' . "\n";
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
			if(count($current_menu['children']) > 0)
			{
				$output .= ui_menu_subcategories_fetch($current_menu['children'], $options);
			}
			$output .= '							</ul>' . "\n";
			$output .= '					</li>' . "\n";
		}
	}
	
	$output .= '				</ul>' . "\n";
	$output .= '		</div>' . "\n";
	$output .= '		<div id="ui_content_thin">' . "\n";
	
	/*$output .= 'Rektangel:';
	$output .= '<script type="text/javascript">CM8ShowAd("Rektangel");</script>' . "\n";
	$output .= 'Skyscrape (Höger?):';
	$output .= '<div id="skyscraper">' . "\n";
	$output .= '<script type="text/javascript">CM8ShowAd("Skyscraper");</script>' . "\n";
	$output .= '</div>' . "\n";*/

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
		$output .= '<div class="notice">' . "\n";
		if(isset($notice['timestamp']))
		{
			$output .= '<span class="time">' . date('H:i', $notice['timestamp']) . '</span>' . "\n";
		}
		$output .= $notice['html'];
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
	
	if(isset($options['return']) && $options['return'] == true)
	{
		return $output;
	}
	else
	{
		echo $output;
	}
}

function ui_new_bottom($options = array())
{
	$output .= '<br style="clear: both;" />' . "\n";
	$output .= '</div>' . "\n";
	
	
	$output .= '<div id="ui_modulebar">' . "\n";
	
	$output .= ui_module_render(ui_module_fetch(array(
		'header' => 'Multi-sök',
		'handle' => 'multisearch'
	)));
	
	$output .= ui_module_render(ui_module_fetch(array(
		'header' => 'Vänner online',
		'handle' => 'friends_online'
	)));
	
	$output .= ui_module_render(ui_module_fetch(array(
		'header' => 'Vänner(s)notiser',
		'handle' => 'friends_notices'
	)));
	
	$output .= ui_module_render(ui_module_fetch(array(
		'header' => 'Forumtrådar',
		'handle' => 'latest_threads'
	)));
	
	$output .= ui_module_render(ui_module_fetch(array(
		'header' => 'Inlägg i forumet',
		'handle' => 'latest_posts'
	)));
	
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
		$notices['discussion_forum'] = $_SESSION['forum']['new_notices'];
		$notices['groups'] = $_SESSION['cache']['unread_group_notices'];
		
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
	$output .= '			<div class="ui_module" id="ui_module_' . $options['handle'] . '">' . "\n";
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
	
?>