<?php
/*
	A set of functions to handle our own markup language for forums, guest books etc
*/
	define(CENSOR_TEXT_LEVEL, 3);

	function post_get_timestamp($post_id)
	{
		$query = 'SELECT timestamp FROM posts WHERE id = "' . $matches[2] . '"';
		$result = mysql_query($query) or die(report_sql_error($query));
		if($data = mysql_fetch_assoc($result))
		{
			$timestamp = $data['timestamp'];
		}
		else
		{
			$timestamp = null;
		}
		return $timestamp;
	}

	function markup_parse($text, $options)
	{
		$options['use_funny'] = isset($options['use_funny']) ? $options['use_funny'] : true;

		$tags_allowed = array('<strong>', '<em>', '<u>', '<ul>', '<ol>', '<li>', '<sup>', '<p>', '<h1>', '<h2>', '<br />');
		$tags_temporary = array('[*strong*]', '[*em*]', '[*u*]', '[*ul*]', '[*ol*]', '[*li*]', '[*sup*]', '[*p*]', '[*h1*]', '[*h2*]', '[*br*]');
		$tags_allowed_end = array('</strong>', '</em>', '</u>', '</ul>', '</ol>', '</li>', '</sup>', '</p>', '</h1>', '</h2>');
		$tags_temporary_end = array('[*/strong*]', '[*/em*]', '[*/u*]', '[*/ul*]', '[*/ol*]', '[*/li*]', '[*/sup*]', '[*/p*]', '[*/h1*]', '[*/h2*]');
		$text = str_replace($tags_allowed, $tags_temporary, $text);
		$text = str_replace($tags_allowed_end, $tags_temporary_end, $text);
		$text = strip_tags($text);
		$text = str_replace($tags_temporary, $tags_allowed, $text);
		$text = str_replace($tags_temporary_end, $tags_allowed_end, $text);
		
//		if(644314 == $_SESSION['login']['id'])
//		{
//			$text = str_replace('<br />', "\n", $text);
//		}
		
		//$text = preg_replace('(<\/p><p>)+', '</p><p>', $text);
		//$text = preg_replace('(<br \/>)+', 'hejsan', $text);
				
		if(array_key_exists('spelling', $options))
		{
			$words_string = preg_replace('/[^[:alpha:]åäö]/i', ' ', strip_tags(html_entity_decode($text)));
	
			preg_match_all('/& ([[:alpha:]åäö]+) \d+ \d+: ((([[:alpha:]åäö]+),?\s?)+)/mi',
										 shell_exec('echo "' . $words_string . '" | aspell --lang=sv -a --sug-mode=ultra'),
										 $matches);
			
			$words_misspelled_array = $matches[1];
			for($i = 0; $i < count($words_misspelled_array); $i++)
			{
				$spelling_suggestions_array[$words_misspelled_array[$i]] = preg_split('/[, ]+/', $matches[2][$i]);
			}
		}

		//Answer tags
		preg_match_all('/\[svar:([\w-]+)=(\d*)\](.*)\[\/svar\]/mU', $text, $matches);
		$text = preg_replace(	'/\[svar:([\w-]+)=(\d*)\](.*)\[\/svar\]/mU',
								'<span class="post_reference" id="post_reference_$2-' .
								(isset($options['post_recursive_id']) ? $options['post_recursive_id'] : $options['post_id']) . 
								'"><h3 class="answer_header">Svar till $1s inlägg.</h3></span>' . "\n" . '<div class="post_referred" id="post_referred_$2-' .
								(isset($options['post_recursive_id']) ? $options['post_recursive_id'] : $options['post_id']) . 
								'"></div>' . "\n" . '<div class="answer_text">$3</div>',
								$text);
//		$text = preg_replace(	'/\[svar:([\w-]+)\]([^(\[\/svar)]*)\[\/svar\]/m',
//													'<h3 class="answer_header">Svar till $1s inlägg.</h3><div class="answer_text">$2</div>',
//													$text);
		//Quotes
		$text = preg_replace(	'/\[citat:([\w-]+)=(\d*)\]([^\[]*)\[\/citat\]/m',
																		'<h3 class="quote_header">Citerar ur inlägg av $1.</h3><div class="quote_text">$3</div>',
																		$text);

		//Censur
		if($_SESSION['login']['userlevel'] >= CENSOR_TEXT_LEVEL)
		{
			$text = preg_replace(	'/\[censur:([\w-]+)\]\s*(.*)\[\/censur\]/mU',
														'<span class="censored">$2</span>',
														$text);
			$text = preg_replace(	'/\[censur\]\s*(.*)\[\/censur\]/mU',
														'<span class="censored">$1</span>',
														$text);
		}
		else
		{
			$text = preg_replace(	'/\[censur:([\w-]+)\]\s*(.*)\[\/censur\]/mU',
														'<span class="censored">-------</span>',
														$text);
			$text = preg_replace(	'/\[censur\]\s*(.*)\[\/censur\]/mU',
														'<span class="censored">-------</span>',
														$text);
		}

		//Additions
		$text = preg_replace(	'/\[tillagg:([\w-]+)\]\s*(.*)\[\/tillagg\]/mU',
													'<h3 class="addition_header">Tillägg av $1:</h3><div class="addition_text">$2</div>',
													$text);

		//replace tags
		$tags_orig = array('<h1>', '</h1>', '<h2>', '</h2>');
		$tags_repl = array('<h3>', '</h3>', '<h4>', '</h4>');
		$text = str_replace($tags_orig, $tags_repl, $text);

		//Replace image tags
		$text = preg_replace('/!http:\/\/(\S+)!/','<img src="http://$1" />', $text);
		$text = preg_replace('/!www\.(\S+)!/','<img src="http://www.$1" />', $text);
		$text = preg_replace('/!(\S+\.(se|nu|org|com|net)\/\S+)!/','<img src="http://$1" />', $text);
		
		if(array_key_exists('spelling', $options) && isset($words_misspelled_array))
		{
			$j = 0;
			foreach($words_misspelled_array as $word)
			{
				$text = preg_replace('/(\s)' . $word . '([\s\.!\?:])/m', '$1<miss>' . $j . '<miss_end>$2', $text);
				$j++;
			}
			$j = 0;
			foreach($words_misspelled_array as $word)
			{
				$text = preg_replace('/<miss>' . $j . '<miss_end>/m', '<span onclick="javascript:show_misspelling(\'suggestion_' . $j . '\');" class="misspelling" id="misspelling_' . $j . '">' . $word . '</span>', $text);
				$j++;
			}
			$result['words_misspelled'] = $words_misspelled_array;
			$result['spelling_suggestions'] = $spelling_suggestions_array;
		}
		for($i = 0; $i < count($links1); $i++)
		{
			$text = str_replace('##bild' . $i . '##', '<img src="http://' . $links1[1][$i] . '" />', $text);
		}

		//Lustiga utbyten
		if($options['use_funny'])
		{
			$boring = array('pannkakor');
			$funny  = array('pangkakor');
			$text = str_replace($boring, $funny, $text);
		}
		
		if($options['short_and_clean'])
		{
			$text = strip_tags($text);
		}

        $text = do_clickable($text);
        $text = parse_promoe($text);
                
		if(array_key_exists('spelling', $options) && isset($words_misspelled_array))
		{
			$result['text'] = $text;
			return $result;
		}
		else
		{
			return $text;
		}
	}

// Make hyperlinks clickable
//
function do_clickable($text)
{

        $text = ' '.$text;

        $text = preg_replace('#([\s\(\)>\]!])(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ie', 
        						'\'$1\'.handle_url_tag(\'$2://$3\')', $text);
        $text = preg_replace('#([\s\(\)])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ie', 
        						'\'$1\'.handle_url_tag(\'$2.$3\',\'$2.$3\')', $text);

        return substr($text, 1);
}

//
// Truncate URL if longer than 55 characters (add http:// or ftp:// if missing)
//
function handle_url_tag($url, $link = '')
{

        $full_url = str_replace(' ', '%20', $url);
        if (strpos($url, 'www.') === 0)                 // If it starts with www, we add http://
                $full_url = 'http://'.$full_url;
        else if (strpos($url, 'ftp.') === 0)    // Else if it starts with ftp, we add ftp://
                $full_url = 'ftp://'.$full_url;
        else if (!preg_match('#^([a-z0-9]{3,6})://#', $url, $bah))      // Else if it doesn't start with abcdef://, we add http://
                $full_url = 'http://'.$full_url;

        // Ok, not very pretty :-)
        $link = ($link == '' || $link == $url) ? ((strlen($url) > 55) ? substr($url, 0 , 39).' &hellip; '.substr($url, -10) : $url) : stripslashes($link);

        return '<a href="'.$full_url.'" style="color: #0000A0;" target="_blank">'.$link.'</a>';
}
function parse_promoe($text)
{
	return preg_replace('/!promoe:(\d+)!/', '<a href="/annat/promoe.php?view=$1"><img src="/annat/promoe_png.php?id=$1" /></a>', $text);
}

?>
