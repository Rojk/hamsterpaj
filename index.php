<?php
	/* OPEN_SOURCE */
	
//	require('include/core/common.php');

	$timer['beginning'] = microtime(true);
	require('include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	$timer['after_includes'] = microtime(true);
	$ui_options['stylesheets'][] = 'start.css';
	$ui_options['javascripts'][] = 'start.js';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['title'] = 'Startsidan på Hamsterpaj - Tillfredsställelse utan sex!';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';
	ui_top($ui_options);
	$timer['after_ui_top'] = microtime(true);
	
	$headings[999] = (date('m-d') == '06-20') ? 'Glad midsommar önskar vi på Hamsterpaj!<br />Och grattis säger vi till <a href="/traffa/profile.php?id=15">Heggan</a> som fyller år idag!' : '';
	$headings[998] = (date('m-d') == '11-06') ? 'Idag äter man bakelser i Göteborg, i övriga landet flaggar man!' : '';
	$headings[997] = (date('m-d') == '12-24') ? 'God jul önskar vi på Hamsterpaj!' : '';
	
	$headings[503] = (date('m-d') >= '07-25' && date('m-d') <= '08-03') ? '<span style="color: #f91208;">H</span><span style="color: #fe4e03;">e</span><span style="color: #fcd702;">j </span><span style="color: #236412;">o</span><span style="color: #10388f;">c</span><span style="color: #9721e5;">h </span><span style="color: #f91208;">v</span><span style="color: #fe4e03;">ä</span><span style="color: #fcd702;">l</span><span style="color: #236412;">k</span><span style="color: #10388f;">o</span><span style="color: #9721e5;">m</span><span style="color: #f91208;">m</span><span style="color: #fe4e03;">e</span><span style="color: #fcd702;">n </span><span style="color: #236412;">t</span><span style="color: #10388f;">i</span><span style="color: #9721e5;">l</span><span style="color: #f91208;">l </span><span style="color: #fe4e03;">H</span><span style="color: #fcd702;">a</span><span style="color: #236412;">m</span><span style="color: #10388f;">s</span><span style="color: #9721e5;">t</span><span style="color: #f91208;">e</span><span style="color: #fe4e03;">r</span><span style="color: #fcd702;">p</span><span style="color: #236412;">a</span><span style="color: #10388f;">j</span><span style="color: #9721e5;">!</span>' : '';
	$headings[502] = (date('m-d') >= '08-05' && date('m-d') <= '08-15') ? 'Dags att börja skolan igen, men misströsta inte, www.hamsterpajiskolan.se finns ju :)' : '';
	$headings[501] = (date('m-d') >= '10-01' && date('m-d') <= '10-10') ? 'Snart är det dags att ta fram krattan, hösten har kommit!' : '';
	$headings[500] = (date('m-d') >= '12-05' && date('m-d') <= '12-10') ? 'Kommer julbocken i Gävle brinna i år igen tro?' : '';
	//'Glad påsk, med mycket godis och nyborrade tänder önskar vi på Hamsterpaj'
	
	
	$headings[57] = (date('G') == 1) ? 'Det som står i bibeln glömmer man lätt, men det som står i <a href="/traffa/profile.php?id=13632">skeggis</a> kalsonger, det glömmer man aldrig!' : '';
	// Detta är kul någon dag, absolut inte mer, plocka bort det om du ser det... /Joel
	$headings[56] = (date('G') == 2) ? 'Internet är fullt av <a href="http://www.youtube.com/watch?v=vHftuzzMgEo" target="_blank" style="color: #660033">konstiga människor</a>, eller hur?' : '';
	$headings[55] = (date('G') == 3) ? 'Gå och lägg dig! Du kan väl drömma om <a href="/diskussionsforum/mellan_himmel_och_jord/skraep_och_spam/snoppnaesa_eller_naessnopp/sida_1.php">snoppnäsor</a> eller nåt?' : '';
	$headings[54] = (date('G:i') >= '3:30' && date('G:i') < '4:30') ? 'Klockan 03.00 är det natt och 05.00 är det morgon, men vad är klockan 04.00? (Svar: En halvtimma kvar tills Hamsterpajen backupar sig själv)' : '';
	$headings[53] = (date('G') >= 5 && date('G') < 6) ? 'Det behövs mycket kaffe för att orka upp vid den här tiden' : '';
	$headings[49] = (date('G') >= 1 && date('G') < 6) ? 'I Norrland är myggen så stora att det är skönt när de suger' : '';
	
	$headings[7] = (date('G') >= 11 && date('G') < 13 && in_array(date('w'), array(1, 2, 3, 4, 5))) ? 'Tjena, vad sägs om en <a href="/flashfilmer/topplistan/">kul flashfilm</a> nu under lunchrasten?' : '';
	$headings[51] = (date('G:i') == '13:37') ? 'Är du en sån där 1337 h4xx0r?' : '';
	$headings[50] = (date('G') >= 19 && date('G') < 23) ? 'Välkommen hit, ha en skön kväll' : '';

	$random_quotes = array(
		'Hej och välkommen till Hamsterpaj',
		'"Tritone, det är helt enkelt du. Söt, inslag utav en jordgubbe, mjuk som vanilj och go som en gelégodis."<br />Galten jämför Tritone med alkoläsk från systemet',
		'Hört hos plastikkirurgen:<br />- <a href="http://excds.ath.cx/fun/other/ektapa/ektapa.html" target="_blank">Dr. Ekta Pattar</a> tar emot!',
		'Visst är 65654 ett fint nummer?',
		'Vet du om att Stora mossen ligger mitt emellan Trosa och Fittja?',
		'Visste du att Tradera har <a href="http://www.google.se/search?rlz=&=&q=johan+h%F6glund" target="_blank">låga priser på Johan Höglund</a> just nu?'
	);
	
	$headings[1] = $random_quotes[array_rand($random_quotes)];

	$output .= isset($_GET['ilovemarquee']) ? '<marquee scrolldelay="1" behavior="alternate" direction="right"><div style="width:720px">' : '';

	

	foreach($headings AS $score => $heading)
	{
		if(strlen($heading) > 0 && $score > $heading_max_score)
		{
			$page_heading = $heading;
			$heading_max_score = $score;
		}
	}
	
	
	$output .= '<embed wmode="transparent" style="z-index: 1;" src="http://www.sheeptobechic.com/sheep.swf" width="630" height="375" allowfullscreen="true" />';
	
	
	$output .= '<h1 id="fp_greeting">' . $page_heading . '</h1>' . "\n";
	
	$output .= ($_SERVER['REMOTE_ADDR'] == '217.28.207.226') ? '<h1>Va? Har ni inte slutat ännu? Kullaviksskolan är alltså före er!</h1>' : '';
	$output .= ($_SERVER['REMOTE_ADDR'] == '217.21.232.204') ? '<h2>En blackebergare? Silfverstolpe is watching you!<h2>' : '';
	$output .= ($_SERVER['REMOTE_ADDR'] == '90.224.61.189') ? '<h2>Hej Joar!</h2>' : '';
	
	if(login_checklogin() && date_get_age($_SESSION['userinfo']['birthday']) <= 13)
	{
		$output .= rounded_corners_top(array('color' => 'orange_deluxe'));
			$info2 .= '<img style="float: left; padding: 5px 5px 5px 0;" src="http://images.hamsterpaj.net/13skylt.png" />' . "\n";
			$info2 .= '<h1 style="margin: 0 0 3px 0; font-size: 16px;">Hamsterpaj är ingen barnsida, är du under 13 så använd www.lunarstorm.se</h1>' . "\n";
			$info2 .= '<p style="margin: 0 0 0 0;">Vi som gör Hamsterpaj tycker att medlemmar under 13 år ställer till en massa problem. Om du inte har fyllt 13 borde du läsa vår <a href="http://www.hamsterpaj.net/artiklar/?action=show&id=24">ålderspolicy</a> och fundera på om Hamsterpaj är rätt ställe för dig. Annars rekommenderar vi Lunarstorm, där kan man få häftiga statuspoäng!</p>' . "\n";
			$info2 .= '<div style="clear:both;"></div>' . "\n";
		echo $info2;
		$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'));
	}
	
	// Old...
	if(login_checklogin())
	{
		$photos = photos_fetch(array('limit' => 4, 'order-direction' => 'DESC'));
		$output .= photos_list($photos);
	}
	
	$short_months = array('Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec');	
	$events = query_cache(array('query' => 'SELECT * FROM recent_updates WHERE label NOT LIKE "Generalrepetition" ORDER BY id DESC LIMIT 11'));

	$output .= '<ol id="fp_event_list">' . "\n";
	foreach($events AS $event)
	{
		$output .= '<li class="event">' . "\n";

		$output .= '<div class="timestamp">' . "\n";
		if($event['timestamp'] >= strtotime(date('Y-m-d')))
		{
			$output .= '<span class="label_today">Idag</span>' . "\n";
			$output .= '<span class="time_today">' . date('H:i', $event['timestamp']) . '</span>' . "\n";
		}
		elseif($event['timestamp'] >= strtotime(date('Y-m-d')) - 86400)
		{
			$output .= '<span class="label_yesterday">Igår</span>' . "\n";
			$output .= '<span class="time_yesterday">' . date('H:i', $event['timestamp']) . '</span>' . "\n";
		}
		else
		{
			$output .= '<span class="label_month">' . $short_months[date('n', $event['timestamp'])-1] . '</span>' . "\n";		
			$output .= '<span class="time_date">' . date('j', $event['timestamp']) . '</span>' . "\n";
		}
		$output .= '</div>' . "\n";
		
		$output .= '<div class="entertain_thumb">' . "\n";
		if(in_array($event['type'], array('new_image', 'new_clip', 'new_flash', 'new_game')))
		{
			$handle = substr($event['url'], strrpos($event['url'], '/')+1, -5);
			$output .= '<a href="' . $event['url'] . '"><img src="http://images.hamsterpaj.net/entertain/' . $handle . '.png" /></a>' . "\n";
		}
		elseif($event['type'] == 'new_software')
		{
			$handle = substr($event['url'], strrpos($event['url'], '#')+1);
			$output .= '<a href="' . $event['url'] . '"><img src="http://images.hamsterpaj.net/downloads/icons/' . $handle . '.png" /></a>' . "\n";			
		}
		else
		{
			$output .= '<a href="' . $event['url'] . '"><img src="http://images.hamsterpaj.net/fp_recent_update_thumb_universal.png" alt="Övrig uppdatering" /></a>' . "\n";
		}
		$output .= '</div>' . "\n";
		
		$output .= '<span class="type">' . $RECENT_UPDATES[$event['type']] . '</span>' . "\n";
		
		$output .= '<a href="' . $event['url'] . '" class="title">' . $event['label'] . '</a>' . "\n";

		$output .= '</li>' . "\n";
	}
	$output .= '</ol>' . "\n";
	


	$output .= '<div id="fp_column">' . "\n";
	$output .= '<a href="http://www.hamsterpaj.net/diskussionsforum/hamsterpaj/taevling_15/sida_1.php" title="Till t�vling"><img src="http://images.hamsterpaj.net/karlek3000.png" /></a>' . "\n";	
	$query = 'SELECT * FROM nyheter ORDER BY id DESC LIMIT 1';
	$data = query_cache(array('query' => $query, 'max_delay' => 60));
	if($data[0]['tstamp'] > time() - 86400)
	{
		$data = $data[0];
		$output .= '<div id="news">' . "\n";
		$output .= '<h2>' . $data['title'] . '</h2>' . "\n";
		$output .= '<p>';
		$output .= (strlen($data['body']) > 1000) ? substr($data['body'], 0, 1000) . '...' : $data['body'];
		$output .= '</p>' . "\n";
		$output .= '<p>Skrevs ' . fix_time($data['tstamp']) . ' <a href="' . $data['thread_url'] . '">Diskutera nyheten »</a></p>' . "\n";
		$output .= '</div>' . "\n";
	}	

	/* Spotlight */
	$output .= '<div id="fp_spotlight_area">' . "\n";
		
	$users = cache_load('hetluften');

	$output .= '<div id="fp_spotlight">' . "\n";
	$output .= '<div id="fp_spotlight_scroller">' . "\n";
	foreach($users AS $user)
	{
		$output .= '<div class="fp_spotlight_profile">' . "\n";
		$output .= ui_avatar($user['id'], array('style' => 'float: left; margin-right: 15px; border: 1px solid white;'));
		$output .= '<h2><a href="/traffa/profile.php?id=' . $user['id'] . '">' . $user['username'] . '</a></h2>' . "\n";
		$output .= ($user['gender'] == 'f') ? '<p>Tjej' : '<p>Kille';
		$output .= ($user['birthday'] != '0000-00-00') ? ' ' . date_get_age($user['birthday']) . ' år' : '';
		$output .= (strlen($user['spot']) > 0) ? ' från ' . $user['spot'] . '</p>' : '</p>';
		if(count($user['flags']) > 0)
		{
			$output .= '<ul class="user_flags">' . "\n";
			$flag_count = 0;
			foreach($user['flags'] AS $flag)
			{
				if(strlen($flags_by_id[$flag]) > 0)
				{
					$output .= '<li><img src="' . IMAGE_URL . '/user_flags/' . $flags_by_id[$flag] . '" /></li>' . "\n";
					$flag_count++;
					if($flag_count == 5)
					{
						break;
					}
				}
			}
			$output .= '</ul>' . "\n";
		}
		$output .= '</div>' . "\n";
	}
	$output .= '</div>' . "\n";
	$output .= '</div>' . "\n";


	$output .= '<ul class="fp_users_list">' . "\n";
	$count = 0;
	foreach($users AS $user)
	{
		$output .= '<li><img src="' . IMAGE_URL . 'images/users/thumb/' . $user['id'] . '" class="fp_user_list_thumb" id="fp_user_thumb_' . $count . '" /></li>' . "\n";
		$count++;
	}
	$output .= '</ul>' . "\n";
	
	
	$output .= '<p style="clear: both;">Gå till: <a href="/traffa/age_guess.php">Gissa åldern</a> ' . "\n";
	$output .= '<a href="/traffa/gallery.php">Galleriet</a> ' . "\n";
	$output .= '<a href="/traffa/klotterplanket.php">klotterplanket</a></p>' . "\n";
	$output .= '</div>' . "\n";
	
	/* Info text */
	$output .= '<p>Hamsterpaj är en kul sida med filmer, spel och lite chattfunktioner, till för tonåringar som inte orkar skriva skolarbeten utan slösurfar istället.</p>' . "\n";
	$output .= '<p>Vi håller inte på och tramsar med några betaltjänster eller "nyhetsbrev", allt är gratis. Vi tjänar pengar på annonser och chefen på bygget jobbar även med annat.</p>' . "\n";


	/* Drivers license ad */
	$output .= '<div class="fp_column_ad">' . "\n";
	$output .= '<a href="/mattan/koerkort.php"><img src="' . IMAGE_URL . 'fp_column_ads/drivers_license.png" /></a>' . "\n";
	$output .= '</div>' . "\n";
	
	/* People online, with maps */
	$output .= '<h2>Användare på karta</h2>' . "\n";
	foreach(array('f', 'm') AS $gender)
	{
		$query = 'SELECT l.id, l.username, u.gender, u.birthday, u.image, z.x_rt90, z.y_rt90';
		$query .= ' FROM login AS l, userinfo AS u, zip_codes AS z';
		$query .= ' WHERE u.userid = l.id AND z.zip_code = u.zip_code AND z.zip_code > 0 AND (u.image = 2 OR u.image = 1) AND u.gender = "%GENDER%" AND u.birthday < 1970';
		$query .= ' ORDER BY l.lastaction DESC LIMIT 40';

		$query = str_replace('%GENDER%', $gender, $query);
		
		$people = query_cache(array('query' => $query, 'max_delay' => 600));

		foreach($people AS $data)
		{
			$map_points .= '<Point X=\'' . $data['y_rt90'] . '\' Y=\'' . $data['x_rt90'] . '\'>';
			$map_points .= '<Name>' . $data['username'] . '</Name>';
			$map_points .= '<IconImage>http://www.hitta.se/images/point.png</IconImage>';
			$map_points .= '<Content><![CDATA[' . $data['gender'] . ' ' . date_get_age($data['birthday']);
			if($data['image'] == 1 || $data['image'] == 2)
			{
				$map_points .= '<br /><a href=\'http://www.hamsterpaj.net/hittapunktse_map_link_redirect.php?id=' . $data['id'] . '\'><img src=\'http://images.hamsterpaj.net/images/users/thumb/' . $data['id'] . '.jpg\' /></a>';
			}
			if(login_checklogin())
			{
				$map_points .= '<br />' . rt90_readable(rt90_distance($_SESSION['userinfo']['x_rt90'], $_SESSION['userinfo']['y_rt90'], $data['x_rt90'], $data['y_rt90']));
			}
			$map_points .= ']]></Content>';
			$map_points .= '</Point>';
		}
		
		$output .= '<form method="post" action="http://www.hitta.se/LargeMap.aspx" target="hittapunktse" onsubmit="window.open(\'\', \'hittapunktse\', \'location=no, width=750, height=500\');" style="display: block; margin: 0px; float: left;">' . "\n";
		$output .= '<input type="hidden" name="MapPoints" value="<?xml version=\'1.0\' encoding=\'utf-8\'?><MapPoints xmlns=\'http://tempuri.org/XMLFile1.xsd\'>' . $map_points . '</MapPoints>">' . "\n";

	  $display_gender = ($gender == 'm') ? 'Killar' : 'Tjejer';

	  if($_SESSION['userinfo']['gender'] == 'm')
		{
			$age_min = $age - 2;
			$age_max = $age + 1;
		}
		else
		{
			$age_min = $age - 1;
			$age_max = $age + 2;
		}
		$label = ($gender == 'f') ? 'Tjejer' : 'Killar';
	  $output .= '<input type="submit" value="' . $label . ' på karta" class="button_120" />&nbsp;&nbsp;' . "\n";
		$output .= '</form>' . "\n";
		unset($map_points);
	}

		/* Downloads ad */
	$output .= '<div class="fp_column_ad">' . "\n";
	$output .= '<a href="/mattan/ladda_ner_program.php"><img src="' . IMAGE_URL . 'fp_column_ads/downloads.png" /></a>' . "\n";
	$output .= '</div>' . "\n";
	
	$output .= '</div>' . "\n";

	/* Poll */
	$poll = poll_fetch(array('type' => 'daily'));
	
	if($poll[0]['can_answer'] == 1 && false)
	{
		echo '<a name="poll"></a>' . poll_render($poll[0]);
	}
	else
	{
		$output .= '<br style="clear: both;" /><a name="poll"></a>' . poll_render($poll[0]);
	}
	
	$output .= isset($_GET['ilovemarquee']) ? '</div></marquee>' : '';
	echo $output;
	ui_bottom();
	?>
