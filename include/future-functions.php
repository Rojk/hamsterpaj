<?php
setlocale(LC_ALL, 'sv_SE');
function profile_future_draw_text($input_text, $date_value, $date_endvalue)
{
		$days = array(0 => 'Söndag', 1 => 'Måndag', 2 => 'Tisdag', 3 => 'Onsdag', 4 => 'Torsdag', 5 => 'Fredag', 6 => 'Lördag');
		$months = array(1 => 'Januari', 2 => 'Februari', 3 => 'Mars', 4 => 'April', 5 => 'Maj', 6 => 'Juni', 7 => 'Juli', 8 => 'Augusti', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'December');
		
		if (strlen($input_text) > 0 && (substr($input_text, 0, 4) == 'Till' ||  substr($input_text, 0, 4) == 'Till') || $input_text == 'Under året')
		{
			return ' - ' . $input_text . ' ';	
		}
		else if ($input_text == 'Ej valt' || $date_value == '0000-00-00')
		{
			return ' - Okänd tidpunkt' . ' ';
		}

		else if ($date_endvalue != '0000-00-00')
		{
			
			$future_time = strtotime($date_value);
			$future_endtime = strtotime($date_endvalue);
			foreach($days AS $key => $label)
			{ 
				if (date('w', $future_time) == $key)
				{
					$date_text .= $label . ' ';
				}
			}
			
			$date_text .= date('d', $future_time) . ' ';
			
			
			foreach($months AS $key => $label)
			{
				if (date('n', $future_time) == $key)
				{
					$date_text .= $label . ' ';
				}
			}
			
			$date_text .= date('Y', $future_time) . ' ';
			
			$date_endtext .= date('d', $future_endtime) . ' ';
			foreach($months AS $key => $label)
			{
				if (date('n', $future_time) == $key)
				{
					$date_endtext .= $label . ' ';
				}
			}
			
			$date_endtext .= date('Y', $future_time) . ' ';
			
			$diffday = ($future_time - time())/86400;
			if ($diffday > 1)
			{
				return ' - ' . $date_text  . ' till ' . $date_endtext . ' (' . ceil($diffday) . ' dagar kvar)	';
			}
			else if (ceil($diffday) == 1)
			{
				return ' - ' . $date_text  . ' till ' . $date_endtext . ' (' . ceil($diffday) . ' dag kvar)	';
			}
			else if (ceil($diffday) == 0)
			{
				return  ' - Idag till ' . $date_endtext;
			}
			return ' - ' . $date_text  . ' till ' . $date_endtext;
		}
		else
		{
			$future_time = strtotime($date_value);
			
			foreach($days AS $key => $label)
			{ 
				if (date('w', $future_time) == $key)
				{
					$date_text .= $label . ' ';
				}
			}
			$date_text .= date('d', $future_time) . ' ';
			
			
			foreach($months AS $key => $label)
			{
				if (date('n', $future_time) == $key)
				{
					$date_text .= $label . ' ';
				}
			}
			$date_text .= date('Y', $future_time) . ' ';
			
			//$date_text = strftime('%A %e %B %Y', $future_time) . ' ';
			$diffday = ($future_time - time())/86400;
			if ($diffday > 1)
			{
				return ' - ' . $date_text . ' (' . ceil($diffday) . ' dagar kvar)	';
			}
			else if(ceil($diffday) == 1)
			{
				return ' - ' . $date_text . ' (' . ceil($diffday) . ' dag kvar)	';
			}
			else if (ceil($diffday) == 0)
			{
				return  ' - Idag';
			}
			return ' - ' . $date_text;
		}

}

?>
