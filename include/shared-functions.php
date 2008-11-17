<?php
	/* OPEN_SOURCE */
	
	function fix_time($timestamp, $day_relative = true, $short_day = false)
	{
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

	function fetch_weekday($time) // Puts the weekday
	{
		$weekdays = array('Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag');
		return $weekdays[date('w', $time)];
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
	
	function birthdaycake($birthday) //Prints a birthday-cake if it's the users birthday
	{
		if($birthday != '0000-00-00' && substr($birthday, -5) == date("m-d", time())){
			return '<img src="' . IMAGE_URL . 'common_icons/cake.png" style="width: 30px; height: 20px; alt="Personen fyller år idag" />';
		}
	}
	
	function dateSplitDate($date) {
		$datearray = explode('-', $date);
		
		to_logfile('notice', __FILE__, __LINE__, 'Deprecated function dateSplitDate() was called', $_SERVER['REQUEST_URI']);
		return $datearray;
	}
	
	function parseContact($contact) {
		$temparray = explode(':', $contact, 2);
		//Delar upp contacthandel i två delar och tar bort ev. html 		
		$contactarray['medium'] = $temparray[0];
		$contactarray['handle'] = strip_tags($temparray[1]);
		
		switch($contactarray['medium']) {
			case 'msn':
				$contactarray['label'] = 'MSN';
				$contactarray['link'] = $contactarray['handle'];
				$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">MSN</acronym>';
				break;
			case 'icq':
				$contactarray['label'] = 'ICQ';
				$contactarray['link'] = $contactarray['handle'];
				$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">ICQ</acronym>';
				break;
			case 'aim':
				$contactarray['label'] = 'AIM';
				$contactarray['link'] = $contactarray['handle'];
				$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">AIM</acronym>';
				break;
			case 'jabber':
				$contactarray['label'] = 'Jabber';
				$contactarray['link'] = $contactarray['handle'];
				$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">Jabber</acronym>';
				break;
			case 'lunar':
				$contactarray['label'] = 'Lunarstorm';
				$contactarray['link'] = '<a href="http://www.lunarstorm.se/' . $contactarray['handle'] . '" target="_blank">' . $contactarray['handle'] . '</a>';
				$contactarray['shortlink'] = '<a href="http://www.lunarstorm.se/' . $contactarray['handle'] . '" target="_blank" title="' . $contactarray['handle'] . '">Lunarstorm</a>';
				break;
			case 'playahead':
				$contactarray['label'] = 'Playahead';
				$contactarray['link'] = $contactarray['handle'];
				$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">Playahead</acronym>';
				break;
			case 'helgon':
				$contactarray['label'] = 'Helgon';
				$contactarray['link'] = $contactarray['handle'];
				$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">Helgon</acronym>';
				break;
			case 'efterfesten':
				$contactarray['label'] = 'Efterfesten';
				$contactarray['link'] = $contactarray['handle'];
				$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">Efterfesten</acronym>';
				break;
			case 'email':
				$contactarray['label'] = 'E-post';
				$contactarray['link'] = '<a href="mailto:' . $contactarray['handle'] . '">' . $contactarray['handle'] . '</a>';
				$contactarray['shortlink'] = '<a href="mailto:' . $contactarray['handle'] . '">E-post</a>';
				break;
 	    case 'skype':
  		  $contactarray['label'] = 'Skype';
     		$contactarray['link'] = $contactarray['handle'];
     		$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">Skype</acronym>';
 	    	break;
			case 'haket':
  		  $contactarray['label'] = 'Haket.com';
     		$contactarray['link'] = $contactarray['handle'];
     		$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">Haket.com</acronym>';
 	    	break;
			case 'sd':
  		  $contactarray['label'] = 'Sockerdricka.nu';
     		$contactarray['link'] = $contactarray['handle'];
     		$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">Sockerdricka.nu</acronym>';
				break;
			case 'myspace':
  		  $contactarray['label'] = 'MySpace';
     		$contactarray['link'] = $contactarray['handle'];
     		$contactarray['shortlink'] = '<acronym title="' . $contactarray['handle'] . '">MySpace</acronym>';
				break;
     break;

			default:
				$contactarray['label'] = NULL;
				$contactarray['link'] = NULL;
				$contactarray['shortlink'] = NULL;
				break;
		}

		return $contactarray;
	}
	
	function listContactMediums($name, $selected) {
		$mediums = array(
			'null'		=> 'Inget',
			'msn'		=> 'MSN',
			'icq'		=> 'ICQ',
			'aim'		=> 'AIM',
			'jabber'	=> 'Jabber',
			'lunar'		=> 'Lunarstorm',
			'playahead'	=> 'Play Ahead',
			'helgon'	=> 'Helgon',
			'efterfesten'	=> 'Efterfesten',
			'email'		=> 'E-post',
			'skype'		=> 'Skype',
			'haket'		=> 'Haket',
			'sd'			=> 'Sockerdricka',
			'myspace'			=> 'MySpace'
		);
		
		if(!array_key_exists($selected, $mediums)) {
			$selected = 'null';
		}
		
		$htmlContactMediums = '<select name="' . $name . '" class="textbox">';
		
		foreach($mediums as $medium => $label) {
			if($medium == $selected) {
				$selectcode = ' selected';
			}
			else {
				$selectcode = '';
			}
			
			$htmlContactMediums .= '<option value="' . $medium . '"' . $selectcode . '>' . $label . '</option>';
		}
		
		$htmlContactMediums .= '</select>';
		
		return $htmlContactMediums;
	}
		
	function getSmiliesArray(){
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
	function listSmilies($adress){
		$smilies = getSmiliesArray();
		for($i = 0; $i < count($smilies); $i++){
			$return .= '<img src="' . IMAGE_URL . '/images/smilies/' . $smilies[$i][1] . '" onclick="javascript: forum_insert_smilie(\'' . $smilies[$i][0] . '\');" alt="' . $smilies[$i][0] . '" /> ';
// För gamla forumet (pre sommaren 2007)
//			$return .= '<img src="/images/smilies/' . $smilies[$i][1] . '" onclick="' . $adress;
//			$return .= '.value = ' . $adress . '.value + \'' . $smilies[$i][0] . '\';" alt="' . $smilies[$i][0] . '" /> ';
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
	
	function setsmilies_old($text, $limit = 25){
	/* Tar emot en sträng och en begränsning av antal smilies i procent,
	   Anges inte denna så defaultar funktionen till 25%. Inlägg med färre
	   än fyra smilies släpps alltid igenom. Nya smilies läggs in rad för
	   rad i arrayen $smilies. Alla bilder ska ligga i /images/smilies.
	*/
		$smilies = getSmiliesArray();


		if(substr($text, 0, 11) == '[nosmilies]')
		{
			return substr($text, 11);
		}

		for($i = 0; $i < count($smilies); $i++){//Räkna antalet smilies
			$smiliesInString = $smiliesInString + substr_count($text, $smilies[$i][0]);
		}
		$nosmilies = $text;
		for($i = 0; $i < count($smilies); $i++){//Plocka bort alla smilies
			$nosmilies = str_replace($smilies[$i][0], NULL, $nosmilies);
		}
		if(100*$smiliesInString/(strlen($nosmilies)+1) > $limit && $smiliesInString > 3){//Om smiliespam upptäcks
			return '<b>Smilies har dolts i detta inlägg</b><br/>' . $nosmilies;
		}
		for($i = 0; $i < count($smilies); $i++){//Byt ut smilies mot bilder
			// If you change this, change line below to...
			$text = str_replace($smilies[$i][0], '<img src="' . IMAGE_URL . '/images/smilies/' . $smilies[$i][1] . '" alt="" />', $text);
		}
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
			'/skiten/lur.php?id='
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

	function userblock_check($owner, $blocked)
	{
		if(is_privilegied('use_ghosting_tools'))
		{
			return 0;
		}
		$query = 'SELECT ownerid FROM userblocks WHERE ownerid = ' . $owner . ' AND blockedid = ' . $blocked . ' LIMIT 1';
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	function duration($time)
	{
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
      if ($daysDiff != '0') {
        $doutput.=$daysDiff;
        if ($daysDiff == '1') {
          $doutput.=' dag ';
        }
        else {
          $doutput.=' dagar ';
        }
      }
      if ($hrsDiff != '0') {
        $doutput.=$hrsDiff;
        if ($hrsDiff == '1') {
          $doutput.= ' timme ';
        }
        else {
          $doutput.=' timmar ';
        }
      }
			if ($minsDiff != '0') {
	      $doutput.=$minsDiff;
	      if ($minsDiff == '1') {
	        $doutput.=' minut';
	      }
	      else {
	        $doutput.=' minuter';
	      }
			}
			if ($doutput == '') {
				$doutput = $secsDiff . ' sekund';
				if ($secsDiff != '1') {
					$doutput .= 'er';
				}
			}
			//to_logfile('notice', __FILE__, __LINE__, 'Deprecated function duration() was called', $_SERVER['REQUEST_URI']);
			//echo jscript_alert('Nu körs duration()');
      return $doutput;
  }

	function generateuniqid()
	{
		return md5(uniqid(rand(), true));
	}

	function message_bar($message)
	{
		$old_msg_bar = file(PATHS_INCLUDE . 'message_bar_current.txt');
		$msg_bar[0] = trim($old_msg_bar[1]);
		$msg_bar[1] = trim($old_msg_bar[2]);
		$msg_bar[2] = time() . ';' . $message;
		unset($msg_bar[3]);
		
		$message_bar = implode("\n", $msg_bar);
		$message_bar_file = fopen(PATHS_INCLUDE . 'message_bar_current.txt', 'w');
		fwrite($message_bar_file, $message_bar);
		fclose($message_bar_file);
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
	if(strlen($string) > $max_length)
	{
		return substr($string, 0, ($max_length - 3)) . '...';
	}
	else
	{
		return $string;
	}
}

function preint_r($array, $label = null)
{
	echo (isset($label)) ? '<h3>' . $label . '</h3>' : '';
	echo '<pre>' . "\n";
	print_r($array);
	echo '</pre>' . "\n";
}

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


function file_extension($filename)
{
	$last_dot = strrpos($filename, '.');
	$extension = strtolower(substr($filename, $last_dot));
	
	return $extension;
}

function tail($options)
{
	$options['line_count'] = (isset($options['line_count'])) ? $options['line_count'] : 10;
	$options['buffer_length'] = (isset($options['buffer_length'])) ? $options['buffer_length'] : 200;
	
	$file = fopen($options['filename'], 'r');
	$filesize = filesize($options['filename']);

	$offset = $filesize - ($options['buffer_length'] * $options['line_count']);		
	while($offset > 0)
	{
		$lines = array();
		$offset = $filesize - ($options['buffer_length'] * $options['line_count']);
		$offset = ($offset < 0) ? 0 : $offset;

		fseek($file, $offset);

  	while(!feof($file))
  	{
			$lines[] = fgets($file);
		}
		
		if(count($lines) > $options['line_count'])
		{
			$lines = array_reverse($lines);
			for($i = 0; $i < $options['line_count']; $i++)
			{
				$return[] = $lines[$i];
			}
			$return = array_reverse($return);
			return $return;
		}
	
		// We didn't read enoguh lines, increase the buffer length and try again
		$options['buffer_length'] = ($options['buffer_length'] / count($lines)) * $options['line_count'] + 1;
	}
	$lines = file($options['filename']);
	return $lines;
}

function clickable_links($str)
{

	// Lagra [img] i array
	$matches = array();
	preg_match_all('#\[img](.*?)\[/img]#is', $str, $matches);
	
	// Byta ut [img] mot  markör
	$str = preg_replace('#\[img](.*?)\[/img]#is', '[IMAGE]', $str);
	
	// Leta upp länkar
		$str = preg_replace('#((http://|https://|ftp://|www\.)(www\.)?)([a-z0-9-åäöÅÄÖ$\#_%\?&\/=\+@\.:-~()]{4,})#eis', 
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
		if($pos = strpos($str, '[IMAGE]'))
		{
			$str = substr_replace($str, $row, $pos, strlen('[IMAGE]'));
		}
	}
	
	return $str;
}
?>
