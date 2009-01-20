<?php
	/* OPEN_SOURCE */
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

function preint_r($array, $bool_return = false)
{
	if ($bool_return)
	{
		$return = '<pre>' . "\n";
		$return .= print_r($array, true);
		$return .= '</pre>' . "\n";
		return $return;
	}
	else
	{
		echo '<pre>' . "\n";
		print_r($array);
		echo '</pre>' . "\n";
	}
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
?>
