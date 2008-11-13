<?php
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
	
	?>