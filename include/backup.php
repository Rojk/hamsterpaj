<?php

// I dont know what the fuck this file are doing here, but hey,
// why don't put a small __halt_compiler() in the very beginnig of it?
__halt_compiler();// Like die(), but halts the compiler...


	setlocale(LC_ALL, 'sv_SE.ISO8859-1');
	session_start();
	/* To ip ban user: Use /admin/ban.php */
	include('bannedips.php');
	//include('/home/www/www.hamsterpaj.net/data/heggan/micro.php');
	
	foreach($banned_ips AS $ip)
	{
		if($ip == $_SERVER['REMOTE_ADDR'])
		{
			header('Location: http://disneyland.disney.go.com/disneyland/en_US/home/home?name=HomePage&bhcp=1');
			exit;
		}
	}

	if($_SERVER['REMOTE_ADDR'] == '81.235.156.174')
	{
		echo '<script>alert("Tjockis :( //Johan");</script>';
	}

	function ui_top($options)
	{

		$options['title'] = (isset($options['title'])) ? $options['title'] : 'Hamsterpaj.net - Onlinespel, community, forum och annat kul ;)';
		$options['stylesheets'][] = 'ui_new.css.php';
		$options['stylesheets'][] = 'shared_' . SHARED_CSS_VERSION . '.css';

		if($_SESSION['login']['id'] > 0)
		{
			$options['javascripts'][] = 'stay_online.js';
		}
		$options['javascripts'][] = 'scripts.js';
		$options['javascripts'][] = 'xmlhttp_login.js';
		
		$default_left_modules = array('login.php', 'quicksearch.php', 'members.php');
		if($_SESSION['login']['id'] > 0)
		{
			$default_left_modules[] = 'profile_visits.php';
			$default_left_modules[] = 'latest_visitors.php';
		}

		if ($_SESSION['preferences']['randomizer'] == 'F' || $_SESSION['preferences']['randomizer'] == 'P')
		{
			$default_left_modules[] = 'randomizer.php';
		}
		
		if(isset($options['left_modules']))
		{
			foreach(array_reverse($default_left_modules) AS $module)
			{
				array_unshift($options['left_modules'], $module);
			}
		}
		else
		{
			$options['left_modules'] = $default_left_modules;
		}

		$default_right_modules[] = 'hamstercage_2.php';
		
		if(isset($options['right_modules']))
		{
			foreach(array_reverse($default_right_modules) AS $module)
			{
				array_unshift($options['right_modules'], $module);
			}
		}
		else
		{
			$options['right_modules'] = $default_right_modules;
		}

		$pw_handl = fopen(PATHS_INCLUDE . 'pageviews/' . date('Y-m-d') . '.txt', 'a');
		fwrite($pw_handl, x);
		fclose($pw_handl);

		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
		echo '<head>' . "\n";
		echo '<meta name="description" content="' . $options['meta_description'] . '" />' . "\n";
		echo '<meta name="keywords" content="' . $options['meta_keywords'] . '" />' . "\n";
		echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />' . "\n";
		echo '<title>' . $options['title'] . '</title>' . "\n";
		foreach($options['stylesheets'] AS $stylesheet)
		{
			echo '<link rel="stylesheet" href="/stylesheets/' . $stylesheet . '" type="text/css" media="all" />' . "\n";	
		}
		foreach($options['javascripts'] AS $javascript)
		{
			echo '<script type="text/javascript" language="javascript" src="/javascripts/' . $javascript . '"></script>' . "\n";
		}
		echo '<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
		echo '<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";

		if(isset($options['dom_tt_lib']))
		{
			echo '<script type="text/javascript" language="javascript" src="/include/domLib.js"></script>' . "\n";
			echo '<script type="text/javascript" language="javascript" src="/include/domTT.js"></script>' . "\n";
			echo '<link rel="stylesheet" type="text/css" href="/include/domTT.css" />' . "\n";
			echo '<script type="text/javascript" language="javascript">' . "\n";
			echo 'var domTT_classPrefix = \'domTT\';' . "\n";
			echo 'var domTT_maxWidth = 300;' . "\n";
			echo '</script>' . "\n";
		}
		echo $options['header_extra'];
		echo '</head>' . "\n";

		echo (isset($options['body_extra'])) ? '<body ' . $options['body_extra'] . '>' . "\n" : '<body>' . "\n";

		/* AdToma ad publishing system */
		if(!isset($options['adtoma_category']))
		{
			$options['adtoma_category'] = 'other';
		}
		$adtoma_gender = (in_array($_SESSION['userinfo']['gender'], array('P', 'F'))) ? $_SESSION['userinfo']['gender'] : 'xx';
		$adtoma_age = ($_SESSION['userinfo']['birthday'] != '0000-00-00') ? date_get_age($_SESSION['userinfo']['birthday']) : 'xx';
		$adtoma_birthyear = ($_SESSION['userinfo']['birthday'] != '0000-00-00') ? substr($_SESSION['userinfo']['birthday'], 0, 4) : 'xx';

		echo '<Script language="JavaScript">' . "\n";
		echo 'var CM8Server = "ad.adtoma.com";' . "\n";
		echo 'var CM8Cat = "hp.' . $options['adtoma_category'] . '";' . "\n";
		echo 'var CM8Profile = "hp_age=' . $adtoma_age . '&hp_birthyear=' . $adtoma_birthyear . '&hp_gender=' . $adtoma_gender . '"' . "\n";

		echo '</Script>' . "\n";
		echo '<Script language="JavaScript" src="http://ad.adtoma.com/adam/cm8adam_1_call.js">' . "\n";
		echo '</script>' . "\n";
		echo '<script> CM8ShowAd("Bigbanner") </script>' . "\n";

		include(PATHS_INCLUDE . 'menu-config.php');
		foreach($menu AS $handle => $current_menu)
		{
			if($_SESSION['login']['userlevel'] >= $current_menu['userlevel'])
			{
				$menu_output .= ($handle == $options['current_menu']) ? '<div class="active">' : '<div>';
				$menu_output .= '<a href="' . $current_menu['url'] . '">' . $current_menu['label'] . '</a></div> ';
			}
		}
		foreach($submenu[$options['current_menu']] AS $current_submenu)
		{
			if($_SESSION['login']['userlevel'] >= $current_submenu['userlevel'])
			{
				$submenu_output .= '<a href="' . $current_submenu['url'] . '">' . $current_submenu['label'] . '</a> ';
			}
		}
		echo '<div id="site_container" style="width: 820px; float: left;">' . "\n";
		echo '<div id="menu_main">' . "\n";
		$steve_comments = file(PATHS_INCLUDE . 'steve_comments.txt');
		$comment = substr(addslashes($steve_comments[rand(0, count($steve_comments)-1)]), 0, -1);
		if($_SESSION['login']['id'] != 71372)
		{
			echo '<img src="' . IMAGE_URL . 'images/ui/steve.gif" style="float: right; cursor: pointer;" onclick="alert(\'' . $comment . '\');" />';
		}
		echo $menu_output . "\n";
		echo '</div>' . "\n";
		echo '<div id="main">' . "\n";
		echo '<div id="menu_sub">' . "\n";
		echo $submenu_output . "\n";
		echo '</div>' . "\n";
		
		echo '<div id="top_left">' . "\n";
		echo '<div id="logo">' . "\n";
		if($_GET['id'] == 530471 || $_GET['view'] == 530471)
		{
			echo '<img src="' . IMAGE_URL . 'ui/snopplogo.png" />' . "\n";
		}
		else
		{
		echo '<object type="application/x-shockwave-flash" data="' . IMAGE_URL . 'ui/hp_logo.swf" style="width: 284px; height: 60px;" wmode="opaque"><param name="movie" value="http://images.hamsterpaj.net/ui/hp_logo.swf" /> <param name="wmode" value="opaque"></object>';
//			echo '<object type="application/x-shockwave-flash" data="' . IMAGE_URL . 'ui/hp_logo.swf" wmode="opaque"><param name="movie" value="http://images.hamsterpaj.net/ui/hp_logo.swf" /> <param name="wmode" value="opaque"></object>';
		}
		echo '</div>';
		
		echo '</div>' . "\n";
		echo '<div id="top_right">' . "\n";
		echo '<div id="top_234_ad">';
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'firefox') === false && DISABLE_ADSENSE != true)
		{
			echo '<div style="width: 114px;  float: left;"><h3 style="font-size: 14px; font-weight: bold;">Hamsterpaj funkar bättre med Firefox!</h3></div>';
?>
<div style="width: 120px; float: right;">
<script type="text/javascript"><!--
google_ad_client = "pub-9064365008649147";
google_ad_width = 120;
google_ad_height = 60;
google_ad_format = "120x60_as_rimg";
google_cpa_choice = "CAAQhbj8zwEaCDnuoa36Rn6bKKXjl3Q";
//--></script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
<?php
		}
		else
		{
?>
<script type="text/javascript"><!--
google_ad_client = "pub-9064365008649147";
google_alternate_color = "FFFFFF";
google_ad_width = 234;
google_ad_height = 60;
google_ad_format = "234x60_as";
google_ad_type = "text";
google_ad_channel ="";
google_color_border = "FFFFFF";
google_color_bg = "FFFFFF";
google_color_link = "4C4C4C";
google_color_url = "4C4C4C";
google_color_text = "4C4C4C";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<?php
		}
		echo '</div>' . "\n";
		echo '<div id="top_468_ad">';

		echo '</div>' . "\n";
		if(isset($_SESSION['bubblemessage']) && isset($_SESSION['login']['id']))
		{
			foreach($_SESSION['bubblemessage'] AS $bubblemessage)
			{
				$options['bubblemessage'][] = $bubblemessage;
			}
		}
		unset($_SESSION['bubblemessage']);
		if(isset($options['bubblemessage']))
		{
?>

<div id="bubblecontainer" onclick="this.style.display='none';">
	<div style="background: url('http://images.hamsterpaj.net/ui/bubble/top.png'); width: 242px; height: 32px;">
		<img src="http://images.hamsterpaj.net/images/common/close_small.png" style="float: right; margin-top: 22px; margin-right: 10px;" />
	</div>
	<div style="background: url('http://images.hamsterpaj.net/ui/bubble/background.png'); width: 228px; padding: 7px;">
<?php
	foreach($options['bubblemessage'] AS $bubblemessage)
	{
		echo '<div>' . "\n";
		echo $bubblemessage;
		echo '</div>' . "\n";
	}
?>
	</div>
	<div style="background: url('http://images.hamsterpaj.net/ui/bubble/bottom.png'); width: 242px; height: 18px;">
	</div>
</div>
<?php
		}
		echo '</div>' . "\n";
/* 
	DETTA SKA VI INTE PETA PÅ
	Då detta är under utveckling och bara "testas"
*/
if(rand(1, 3) == 12)
{
	/*
	$status = file_get_contents('/home/www/www.hamsterpaj.net/data/radio/status.txt');
	$info = explode(',', $status);
	$options['right_now'] = 'Just nu spelas ' . $info[6] . ' på Hamster-radion och vi har ' . $info[0] . ' lyssnare | klicka <a href="/radio/">här</a> för att lyssna!';
	*/
	$options['right_now'] = '<b>Hamsterradio ~</b> Nu kan du höra <b>Emma</b> i programmet <b>Myspys</b> ~ <a href="/radio">Besök radiosidan</a>, eller <a href="#" onClick="javascript:window.open(\'http://www.hamsterpaj.net/radio/webplayer/webplayer.php\',\'webplayer\',\'height=170,width=350,top=0,left=0,resizable=no,scrollbars=no\')">starta webbspelaren</a>';
}

		echo '<div id="right_now">' . "\n";
		$weekdays = array('Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag');
		echo $weekdays[date('w')] . date(' j/n H:i: ');
		echo (isset($options['right_now'])) ? $options['right_now'] : file_get_contents(PATHS_INCLUDE . '/message_bar_current.txt');
		echo '</div>' . "\n";

		echo '<div id="main_right">' . "\n";

		echo '</div>' . "\n";

		echo '<div id="main_left">' . "\n";
		foreach($options['left_modules'] AS $module)
		{
			echo '<div class="module">' . "\n";
			include(PATHS_INCLUDE . 'left_modules/' . $module);
			echo '</div>' . "\n";
		}
		echo '</div>' . "\n";
		echo '<div id="content">' . "\n";
	}

	function ui_bottom($options = null)
	{
		global $time_start;
		echo '</div>' . "\n";
		echo '<div id="footer" style="height: 32px;">' . "\n";
		echo 'Kolla in Johans bidrag i tävlingen <a href="http://www.carcasherdotcom-seocontest.se/">carcasherdotcom seocontest</a>!' . "\n";
		//echo '<img src="http://images.hamsterpaj.net/webcows_ad/webcows_bottom_logo.png" style="float: right;" />';
		//echo '<strong style="margin: 2px;">Hamsterpaj hostas på stabila servrar från <a href="http://www.webcows.se/">Webcows</a>. Just nu bjuder dom på två månader gratis webbhotell!</strong>';
		echo '<img id="stay_online_image" style="display: none;" />' . "\n";

		echo '</div>' . "\n"; //Footer div
		echo '</div>' . "\n"; // Close site_frame border

		echo '</div>' . "\n"; //Close main div
		echo '<div style="float: left;"><script> CM8ShowAd("Skyscraper") </script></div>' . "\n";
		echo '</div>' . "\n"; // Close site_container
		
		echo '<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">' . "\n";
		echo '</script>' . "\n";
		echo '<script type="text/javascript">' . "\n";
		echo '_uacct = "UA-531458-1";' . "\n";
		echo 'urchinTracker();' . "\n";
		echo '</script>' . "\n";
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
		echo '</body>' . "\n";
		echo '</html>' . "\n";		

	}

	function insert_avatar($userid, $imgextra = NULL, $random = NULL)
	{
		global $hp_url;
		$output = '<a href="javascript:;" onclick="window.open(\'' . $hp_url . 'avatar.php?id=' . $userid . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=240, height=320\')">';
		
		$output .= '<img src="' . IMAGE_URL . 'images/users/thumb/' . $userid . '.jpg';
		
		if (isset($random)) {
			$output .= '?' . rand();
		}
		$output .= '" border="0" ';
		if (isset($imgextra) && preg_match("/alt/i",$imgextra)) {
			$output .= $imgextra;
		}
		else {
			$output .= 'alt=""'. $imgextra;
		}
		$output .= '/>';
		$output .= '</a>';
		return $output;
	}

	function bubble($text, $timeout = BUBBLE_DISPLAY_TIMEOUT)
	{
		global $_GLOBALS;
		if ($_GLOBALS['bubblepop'] != '1')
		{
			$_GLOBALS['bubblepop'] = '1';
			echo '<script type="text/javascript">' . "\n";
			echo '<!-- //' . "\n";
			echo 'function CloseBubble() {' . "\n";
			echo 'document.getElementById("bubble_main").style.visibility = "hidden";' . "\n";
			echo '}' . "\n";
			if ($timeout != '0')
			{
				echo 'setTimeout("CloseBubble()", ' . $timeout . '000);' . "\n";
			}
			echo '// -->' . "\n";
			echo '</script>' . "\n";
			echo '<div id="bubble_main" onclick="CloseBubble();">' . "\n";
			echo '<table cellpadding="0" cellspacing="0" class="bubble_table">' . "\n";
			echo '<tr><td class="bubble_top"></td></tr>' . "\n";
			echo '<tr><td class="bubble_background">' . "\n";
			echo '<p style="padding:0px;margin:0px;margin-left:3px;margin-right:3px;">' . "\n";
			echo stripslashes($text) . "\n";
			echo '</p>' . "\n";
			echo '<br />';
			echo '<p style="padding:0px;margin:0px;margin-right:3px;text-align:right">';
			echo '<a href="/settings.php">Inställningar »</a>';
			echo '</p>';
			echo '</td></tr>' . "\n";
			echo '<tr><td class="bubble_bottom"></td></tr>' . "\n";
			echo '</table>' . "\n";
			echo '</div>' . "\n";
		}
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
	  $return.= '<span class="droptitletext">' . $title . '</span>' . "\n";
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
    echo '<p class="error">Ett fel på hamsterpaj har inträffat! Utvecklingsansvariga har meddelats om detta, du behöver inte rapportera felet. Vi åtgärdar det snart (om vi kan :P)</p>';
    if($_SESSION['login']['userlevel'] < 1)
    {
      echo '<br />Sessionsdata är inte tillgänglig!';
    }
    elseif($_SESSION['login']['userlevel'] == 5)
    {
      echo '<br />Felsökningsinformation:<br />' . mysql_error();
      echo '<br />Frågan löd:<br />' . $query;
    }
    if(isset($file))
    {
      to_logfile('error', $file, $line, $query);
    }
  }
?>
