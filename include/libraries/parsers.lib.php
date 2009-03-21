<?php
	// Note to open source developers: not a single one of these old, ugly, functions
	// conforms to the name- and formatting standards. This is because they're really
	// old - some from the very beginnig of Hamsterpaj 2.0!
	
	function url_secure_string($label)
	{
		//$label = strtolower(trim($label));
		$label = trim($label);
		
		$replace =     array(' ', 'Å',  'å',  'Ä',  'ä',  'Ö',  'ö',  'È', 'è', 'É', 'é');
		$replacement = array('_', 'aa', 'aa', 'ae', 'ae', 'oe', 'oe', 'e', 'e', 'e', 'e');
		$label = str_replace($replace, $replacement, $label);
		
		// !!!
		$label = mb_strtolower($label);
		
		$handle = preg_replace('/([^[:lower:]\d_])/', '', $label);
	
		return $handle;
	}
	
	function fix_future_time($timestamp, $day_relative = true, $short_day = false) {
		if($short_day == 'true')
		{
			$days = array('Sön', 'Mån', 'Tis', 'Ons', 'Tors', 'Fre', 'Lör');
		}
		else
		{
			$days = array('Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag');
		}
		
		if(date('Y-m-d') == date("Y-m-d", $timestamp) && $day_relative == true)
		{
			return 'Idag ' . date('H:i', $timestamp);
		}
		elseif(date('Y-m-d', time() + 86400) == date('Y-m-d', $timestamp) && $day_relative == true)
		{
			return 'I morgon ' . date('H:i',$timestamp);
		}
		elseif($timestamp > time() + 86400*5)
		{
			return 'På ' . $days[date('w', $timestamp)] . ' ' . date('H:i', $timestamp);
		}
		elseif(date('Y', $timestamp) == date('Y'))
		{
			return $days[date('w', $timestamp)] . ' ' . date('j/n H:i',$timestamp);
		}
		else
		{
			return date('Y-m-d H:i', $timestamp);
		}
	}

	function fix_time($timestamp, $day_relative = true, $short_day = false)
	{
		if ($timestamp > time())
		{
			return fix_future_time($timestamp, $day_relative, $short_day);
		}
		if($short_day == 'true')
		{
			$days = array('Sön', 'Mån', 'Tis', 'Ons', 'Tors', 'Fre', 'Lör');
		}
		else
		{
			$days = array('Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag');
		}
		
		if(date('Y-m-d') == date("Y-m-d",$timestamp) && $day_relative == true)
		{
			return 'Idag ' . date('H:i', $timestamp);
		}
		elseif(date('Y-m-d', time()-86400) == date('Y-m-d',$timestamp) && $day_relative == true)
		{
			return 'Igår ' . date('H:i',$timestamp);
		}
		elseif($timestamp > time() - 86400*5)
		{
			return 'I ' . $days[date('w', $timestamp)] . 's ' . date('H:i', $timestamp);
		}
		elseif(date('Y', $timestamp) == date('Y'))
		{
			return $days[date('w', $timestamp)] . ' ' . date('j/n H:i',$timestamp);
		}
		else
		{
			return date('Y-m-d H:i', $timestamp);
		}
	}

	function cute_number($number) // Puts spaces in large numbers 222431242 to 222 431 242
	{
		return strrev(chunk_split(strrev($number), 3,' '));
	}
	
	function date_get_age($birthday) // Gets the age from birthday
	{
		$newbday = str_replace("-", "", $birthday);
		$age = floor((date("Ymd") - $newbday)/10000);
		
		if($newbday == 00000000 || $age == 0)
		{
			return false;
		}
		return $age;
	}
	
	function getSmiliesArray()
	{
			$smilies = array(
			array('[angel]', 'angel.gif'),
//			array('[clap]', 'clap.gif'),
			array('[eh]', 'eh.gif'),
			array('[stop]', 'stop.gif'),
//			array('[liar]', 'liar.gif'),
//			array('[no]', 'no.gif'),
//			array('[shhh]', 'shhh.gif'),
//			array('[fatty]', 'fatty.gif'),
//			array(':-X', 'gagged.gif'),
//			array('[whistle]', 'whistle.gif'),
			array(':(', 'sad.gif'),
			array(':@', 'angry.gif'),
			array('[dead]', 'dead.gif'),
			array(';)', 'semikolonparentes.gif'),
//			array('[killed]', 'killed.gif'),
			array(':D', 'kolonD.gif'),
//			array('XD', 'XD.gif'),
			array('O_o', 'O_o.gif'),
			array('[surprised]', 'surprised.gif'),
			array('[onetooth]', 'one_tooth.gif'),
			array('[sur]', 'sur.gif'),
			array('[glasses]', 'glasses.gif'),
			array('[cute]', 'cute.gif'),
//			array('o-)', 'cyklop.gif'),
//			array('[evil]', 'evil.gif'),
//				array(':emo:', 'emo.png'),
			array(':S', 'kolonS.gif'),
			array(':P', 'kolonP.gif'),
		);
		return $smilies;
	}
	
	function listSmilies($adress)
	{
		$smilies = getSmiliesArray();
		for($i = 0; $i < count($smilies); $i++)
		{
			$return .= '<img src="' . IMAGE_URL . '/images/smilies/' . $smilies[$i][1] . '" onclick="javascript: forum_insert_smilie(\'' . $smilies[$i][0] . '\');" alt="' . $smilies[$i][0] . '" /> ';
		}	
		return $return;
	}
	
	function setSmilies($text, $limit = 0)
	{
		if(substr($text, 0, 11) == '[nosmilies]')
		{
			return substr($text, 11);
		}
		
		$smilies = getSmiliesArray();
		$search = array();
		$replace = array();
		foreach($smilies as $index => $smilie)
		{
			$search[$index] = $smilie[0];
			$replace[$index] = '<img src="' . IMAGE_URL . '/images/smilies/' . $smilie[1] . '" alt="" />';
		}
		$text = str_replace($search, $replace, $text);
		return $text;
	}
	
	function unset_smilies($text)
	{
		$smilies = getSmiliesArray();
		
		// Must be done in reverse order (running 'a' --> 'b', 'b' --> 'c' on 'a' would result in 'c' with str_replace).
		$smilies = array_reverse($smilies);
		
		foreach($smilies as $smily)
		{
			$text = str_replace('<img src="' . IMAGE_URL . '/images/smilies/' . $smily[1] . '" alt="" />', $smily[0], $text);
		}
		
		return $text;
	}
	
	function content_check($text)
	{
		$text = strtolower(' ' . $text . ' '); //Lägg till lite mellanslag för att fixa buggen som gör att filtren inte funkar om det saknas tecken innan den förbjudna teckenkombinationen.
		$banned_strings = array(
			'?r=',
			'msn-tools.de/?nr=', 
			'fragbite.com/?userID',
			'?refer=',
			'?ragga=',
			'?ref=',
			'gangstawar',
			'kingsofchaos',
			'referralid=',
			'sexy-lena.com',
			'emocore.se',
			'monstersgame.se',
			'alltgratis.se',
			'?pundare=',
			'rochas.se',
			'th0nd-elajt.no-ip.org',
			'albanau',
			'xth.nu',
			'gamblingcommunity.se',
			'oddsite',
			'adduser.php',
			'liferace',
			'studiotraffic.com',
			'gurk.php/',
			'?Tipsare',
			'clickltad.php?uid',
			'?tipsa',
			'tribalwars',
			'?referral=',
			'?ac=vid&',
			'index.php?ac=main',
			'charles.tk',
			'travian.se',
			'monstersgame',
			'nogg.se',
			'egenbild.se/?i',
			'c.php?uid=',
			'pimpland.se',
			'myminicity.com', 
			'neopets.com',
			'ref.php?user=',
			'?r=',
			'page.php?id=',
			'vinnpris.se', 
			'/skiten/lur.php?id=',
			'sexyemilie',
			'sexye.milie',
			'sexy.emilie',
			'sexy*emilie',
			'milie.com/?id=',
			'sexy-emilie',
			'emilie.com',
			'emilie,com',
			'www.rivality.notlong.com',
			'rivality.com',
			'rivality.notlong',
			'EXgirl007.myhotpicss.com',
			'EXgirl',
			'myhotpicss',
			'ihate'
			);
		foreach($banned_strings AS $banned)
		{
			if(strpos($text, $banned) == true)
			{
				return 'En förbjuden webbadress hittades i ditt meddelande. Var snäll och spamma inte här på hamsterpaj.net';
			}
		}
		$kedjebrev_strings = array(
			'skicka du detta brev till tio personer så kommer den du älskar kyssa dig om sex dagar',
			'skicka till tio av dina vänner',
			'om du skickar till 15 st kommer',
			'denna text måste du skicka till 10 personer',
			'denna text måste du skicka åt 10',
			'du den till 20 personer kommer',
			'om du skickar till 10 st kommer du att',
			'om du bryter den här kedjan kommer du att få kärleksproblem',
			'skickar det till minst 15 pers inom 10 min',
			'Mitt namn är Caroline. Jag dog i en brand för 3 år sedan',
			'You will get kissed on the nearest possible',
			'post this comment to at least 3 videos, you will die within 2 days',
			'a young girl named Jenn was walking down a river',
			'Hejsan jag är en gubbe på 61bast som heter Gösta',
			'Mitt arbete är Cilit-BANG och jag strippar',
			'DONT READ THIS! In',
			'There are 20 angels in',
			'4 video comments',
			'this comment to at least',
			'Hamsterpaj V.I.P',
			'send this to five other videos',
			'this comment on 10 videos in the next hour'
			);
		foreach($kedjebrev_strings AS $kedjebrev)
		{
			if(strpos($text, $kedjebrev) == true)
			{
				return 'Kedjebrev är förbjudna här på hamsterpaj.net. De är bara störande och tillför inget vettigt, det som står i dem är inget annat är ren lögn.';
			}
		}
/*		
		$disturb_strings = array(
			'??????',
			'!!!!!!',
			'!?!?!?!',
			'.......',
			"\n\n\n\n\n\n",
			"\r\n\r\n\r\n\r\n\r\n\r\n\r\n"
			);
		foreach($disturb_strings AS $disturb)
		{
			if(strpos($text, $disturb) == true)
			{
				return 'Ta det lite lugnt med tecknen. Det blir bara jobbigt att läsa med massa punkter, frågetecken, utropstecken eller radbrytningar i rad.';
			}
		}
		*/
		if(strpos($text, 'sms') == true)
		{
			$sms_numbers = array(
				' 75520',
				' 72777',
				'0939-1040800'
				);
			foreach($sms_numbers AS $number)
			{
				if(strpos($text, $number) == true)
				{
					return 'Reklam för SMS-tjänster är förbjudet här på hamsterpaj.net.';
				}
			}
		}
		
		if(strpos($text, 'wowglider') == true)
		{
			return 'Sluta tjata om detta förbannade wowglider. Ingen är intresserad av scam, begrips!';
		}
		
		/* Block posts about "Aprils fool" on the 1st of april every year */
		/*
		$aprils_fool_blocks = array(
				'april', 
				'almanacka', 
				'aprilskämt',
				'almanackan',
				'almenacka',
				'almenackan',
				'a p r i l',
				'kolla datum',
				'a-p-r-i-l',
				'dagens datum',
				'a_p_r_i_l',
				'kolla vilken dag',
				'a.p.r.i.l',
				'4pril',
				'4pr1l'
			);
			foreach($aprils_fool_blocks AS $banned)
			{
				if(strpos($text, $banned) == true)
				{
					return 'Snälla, du kan väl låta bli att avslöja för alla att det är första april idag? Det förstör liksom hela poängen...';
				}
			}
			*/
			
			/* Block everything that has to do with cool-guy or star-mia */
		
		$irritating_fools_blocks = array(
				'cool-guy', 
				'star-mia', 
				'cool_guy',
				'star_mia',
				'cool guy',
				'star mia',
			);
			foreach($irritating_fools_blocks AS $banned)
			{
				if(strpos($text, $banned) == true)
				{
					return 'Nu var det färdigdampat med allt star-mia och cool-guy chatter. Och försöker ni komma runt systemet med stlar-mia lr clool-guy så kan ni vänta er en fet bann. Puss på dig med :) //Lef-91';
				}
			}
			

		return 1;
	}
	
	function duration($time)
	{
		// Note: This is a old function, which doesn't conform to the code standards at all.
		// Please, don't write such nasty code in the future!
		
		// calculate elapsed time (in seconds!)
		$diff = $time;
		$yearsDiff = floor($diff/60/60/24/365);
		$diff -= $yearsDiff*60*60*24*365;
	 	$daysDiff = floor($diff/60/60/24);
	 	$diff -= $daysDiff*60*60*24;
	 	$hrsDiff = floor($diff/60/60);
	 	$diff -= $hrsDiff*60*60;
	 	$minsDiff = floor($diff/60);
	 	$diff -= $minsDiff*60;
		$secsDiff = $diff;
		$doutput='';

		if ($yearsDiff != '0')
		{
			$doutput.=$yearsDiff.' år ';
		}
		
    if ($daysDiff != '0')
    {
      $doutput.=$daysDiff;
      if ($daysDiff == '1')
      {
        $doutput.=' dag ';
      }
      else
      {
        $doutput.=' dagar ';
      }
    }

    if ($hrsDiff != '0')
    {
      $doutput.=$hrsDiff;
      if ($hrsDiff == '1')
      {
        $doutput.= ' timme ';
      }
      else
      {
        $doutput.=' timmar ';
      }
    }

		if ($minsDiff != '0')
		{
      $doutput.=$minsDiff;
      if ($minsDiff == '1')
      {
        $doutput.=' minut';
      }
      else
      {
        $doutput.=' minuter';
      }
		}

		if ($doutput == '')
		{
			$doutput = $secsDiff . ' sekund';
			if ($secsDiff != '1')
			{
				$doutput .= 'er';
			}
		}

    return $doutput;
  }
  
  function rt90_distance($x1, $y1, $x2, $y2)
	{
		$distance = sqrt(pow(($x2-$x1),2) + pow(($y2-$y1),2));
		return $distance;
	}

	function rt90_readable($distance)
	{
		if ($distance > 12000)
		{
			$distance = round($distance/10000,1) . ' mil';
		}
		else if ($distance > 1000)
		{
			$distance = round($distance/1000,1) . ' kilometer';
		}
		else
		{
			$distance = round($distance) . ' meter';
		}
		return $distance;
	}
	
	function shorten_string($string, $max_length, $options)
	{
		// This is probably not multibyte-safe. Used in the old forum I think (libraries/discussions.php)
		if(strlen($string) > $max_length)
		{
			return substr($string, 0, ($max_length - 3)) . '...';
		}
		else
		{
			return $string;
		}
	}
	
	function clickable_links($str)
	{
	
		// Lagra [img] i array
		$matches = array();
		
		$forum_image_regex = '#\[img](.*?)\[/img]#is';
		
		preg_match_all($forum_image_regex, $str, $matches);
		
		// Byta ut [img] mot  markör
		$str = preg_replace($forum_image_regex, '[IMAGE]', $str);
		// GAMMAL #((http://|https://|ftp://|www\.)(www\.)?)([a-zA-Z0-9åäöÅÄÖ$\#_%?&-/=+@.:-~()]{4,})#eis
		// Leta upp länkar
			$str = preg_replace('#((http://|https://|ftp://|www\.)(www\.)?)([a-zA-Z0-9åäöÅÄÖ$\#_%?&-/=+@.:-~()]{4,})(<|\s|\[/|)#eis', 
								"
								('$2' != 'http://' && '$2' != 'https://' && '$2' != 'ftp://') ? 
									'<a href=\"http://$1$4\" target=\"_blank\">' . 
										(strlen('$1$4') > 40 ? substr('$1$4', 0, (strlen('$1$4') / 2)) . '...' . substr('$1$4', -10) 
										: 
										'$1$4') 
									. '</a>' 
								: 
									'<a href=\"$1$4\" target=\"_blank\">' . 
										(strlen('$1$4') > 40 ? substr('$1$4', 0, (strlen('$1$4') / 2)) . '...' . substr('$1$4', -10) 
										: 
										'$1$4') 
									. '</a>'
								", 
							$str
							);
						
		// Lägga tillbaka [img]
		foreach($matches[0] as $row)
		{
			$pos = strpos($str, '[IMAGE]');
			$str = substr_replace($str, $row, $pos, strlen('[IMAGE]'));
		}
		
		return $str;
	}
?>
