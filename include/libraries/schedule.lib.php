<?php
// Schedule library

require_once(PATHS_LIBRARIES . 'contests.lib.php');
require_once(PATHS_LIBRARIES . 'fun_images.lib.php');
require_once(PATHS_LIBRARIES . 'poll.lib.php');
//require_once(PATHS_LIBRARIES . 'sex_and_sense.lib.php');
require_once(PATHS_LIBRARIES . 'sex_sense.lib.php');

function schedule_event_add($options)
{
	/*
	options			comment
	item_id			optional
	type
	data <-- Serialized!
	release
	*/
	
	$query = 'INSERT INTO scheduled_events (' .
				(isset($options['item_id']) ? 'item_id, ' : '') .
				'type, data, `release`) VALUES ("' .
				(isset($options['item_id']) ? ($options['item_id'] . '", "') : '') .
				$options['type'] . '", "' .
				mysql_real_escape_string($options['data']) . '", "' .
				$options['release'] . '")';
	return mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
}

function schedule_event_update($options)
{
	/*
	options			comment
	type
	data
	release
	*/

	$query = 'UPDATE scheduled_events';
	$query .= ' SET';
	$query .= (isset($options['data'])) ? ' data = "' . mysql_real_escape_string($options['data']) . '", ' : '';
	$query .= ' `release`="' . $options['release'] . '"';
	if(isset($options['id']))
	{
		$query .= ' WHERE id = "' . $options['id'] . '"';
	}
	else
	{
		$query .= ' WHERE item_id="' . $options['item_id'] . '" AND type="' . $options['type'] . '"';
	}
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	switch($options['type'])
	{
		case 'new_game':
		case 'new_image':
		case 'new_flash':
		case 'new_clip':
		case 'new_software':
			$query = 'UPDATE entertain_items SET `release` = "' . $options['release'] . '"' .
			' WHERE id="' . $options['item_id'] . '" AND entertain_type="' . substr($options['type'], 4) . '"';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		break;
	}
	
}

function schedule_event_fetch($options)
{
	/*
	option		array support	comment
	item_id			no			give only one type if item_id is used
	type			yes			array support only if item_id is not set
	release_before	no			default is time()
	release_after	no
	released					default is 0 = no
	*/
	
	if(isset($options['type']))
	{
		// (This will be shifted again if type isn't an array...)
		$type = is_array($options['type']) ? $options['type'] : array($options['type']);
	}
	$released = isset($options['released']) ? $options['released'] : 0;
	if(isset($options['release_before']))
	{
		$options['release_before'] = $options['release_before'];
	}
	
	$query = 'SELECT * FROM scheduled_events WHERE 1';
	if(!isset($options['fetch_released_and_unreleased']))
	{
		$query .= ' AND released="' . $released . '"';
	}
 	if(isset($options['item_id']))
	{
		$query .= ' AND item_id = "' . $options['item_id'] . '"';
		$query .= ' AND type = "' .  (is_array($type) ? array_shift($type) : $type) . '"';
	}
	elseif(isset($type))
	{
		$query .= ' AND type IN ("' . implode('", "', $type) . '")';
	}
	if(isset($options['release_after']))
	{
		$query .= ' AND `release` > "' . $options['release_after'] . '"';
	}
	if(isset($options['release_before']))
	{
		$query .= ' AND `release` < "' . $options['release_before'] . '"';
	}
	$query .= ' ORDER BY `release` DESC';
	if(isset($options['limit']) && is_numeric($options['limit']))
	{
		$query .= ' LIMIT ' . $options['limit'];
	}
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$return[] = $data;
	}
	//trace('schedule', $query);
	return $return;
}

function schedule_event_list($options)
{
//	preint_r($options['events']);
	echo '<div class="schedule_event_list">' . "\n";
	echo '<table class="schedule_event_table">' . "\n";
	$options['events'] = array_reverse($options['events']);
	$day = '';
	foreach($options['events'] as $event)
	{
		$event_data = unserialize($event['data']);
		$new_day = date('Y-m-d', $event['release']);
		if($day != $new_day)
		{
			echo '<tr><td>';
			echo '<h2>' . $new_day . '</h2>' . "\n";
			echo '</td></tr>';
			$day = $new_day;
		}
		echo '<tr>' . "\n";
		echo '<td><strong><a href="' . $event_data['url'] . '">' . $event_data['title'] . '</a></strong></td>' . "\n";
		echo '<td>' . $event['type'] . '</td>' . "\n";
		echo '<td>';
		if($options['editable'])
		{
			echo '<form action="/admin/schemalagt.php">' . "\n";
			echo '<input type="hidden" name="id" value="' . $event['id'] . '"/>' . "\n";
			echo '<input type="text" name="release" value="';
		}
		echo date('Y-m-d H:i', $event['release']);
		if($options['editable'])
		{
			echo '"/><input type="submit" value="Ok" />' . "\n";
			echo '</form>' . "\n";
		}
		echo '</td>' . "\n";
		echo '<td>' . ($event['released'] == 1 ? 'Släppt' : 'Inte släppt') . '</td>' . "\n";
		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";
	echo '</div>' . "\n";
}

function schedule_releases_do($options)
{
	/*
	option		array support	comment
	item_id				yes
	type			yes
	release_after	no
	*/

	//trace('schedule', 'Init');

	$options['release_before'] = time();
	$options['released'] = 0;
	$events = schedule_event_fetch($options);
	foreach($events as $event)
	{
		//trace('schedule', 'Releasing ' . $event['type']);
		$data = unserialize($event['data']);

		switch($event['type'])
		{
			case 'todays_discussion':
				trace('todays_discussion_break', 'Tried to release todays_discussion in ' . __FILE__ . ' on line ' . __LINE__);
				break;
			case 'contest':
				contests_create($data);
				$url = '/taevlingar/';
				$label = $data['title'];
				break;
			case 'poll':
				$poll_handle = poll_create($data);
				
				$poll = poll_fetch(array('handle' => $poll_handle));
				
				$post['content'] = '[poll:' . $poll[0]['id'] . ']';
				$post['forum_id'] = 78;
				$post['title'] = 'Undersökning: ' . $poll[0]['question'];
				$post['mode'] = 'new_thread';
				$post['author'] = 2348;// Webmaster

				$thread_id = discussion_forum_post_create($post);
				$comment_url = forum_get_url_by_post($thread_id);

				$query = 'UPDATE poll SET comment_url = "' . $comment_url . '" WHERE id = "' . $poll[0]['id'] . '"';

				mysql_query($query);				

				$url = '/index.php#poll';
				$label = $data['question'];
				break;
				
			/* Old sex and sense
			case 'sex_sense':
				$entry_id = sex_sense_create($data);
				$entry = sex_sense_fetch(array('id' => $entry_id));
				
				$url = '/sex_och_sinne/' . $entry[0]['category'] . '/' . $entry[0]['handle'] . '.html';
				$label = $entry[0]['title'];

				break;*/
			case 'sex_sense':
				try
				{
					$entries = sex_sense_fetch_posts($data['fetch_item_options']);
					if(count($entries) != 1)
					{
						throw new Exception('Fel 1 i schedule_releae! Base64(serialize): ' . base64_encode(serialize($data)));
					}
					
					$entry = array_pop($entries);
					$query = 'UPDATE sex_questions SET is_released = 1 WHERE id = ' . $entry['id'];
					
					$label = $entry['title'];
					
					$url = '/sex_och_sinne/';
			  	$categories = sex_sense_fetch_categories(array('category_id' => $entry['category_id']));
			  	foreach($categories as $category_tree)
			  	{
			  		$category = array_pop($category_tree);
			  		$url .= $category['category_handle'] . '/';
			 		}
			  	$url .= $entry['handle'] . '.html';
			  	
			  	// Forum thread creation (main thread)
			  	unset($thread);
			  	$thread['author'] = '876354';
					$thread['title'] = $entry['title'];
					$thread['mode'] = 'new_thread';
					$thread['forum_id'] = '102';
					$thread['content'] = $entry['question'];
					$thread_id = discussion_forum_post_create($thread);
			  	
			  	$query = 'UPDATE sex_questions SET forum_post_id = ' . $thread_id . ', is_released = 1 WHERE id = ' . $entry['id'];
			  	mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			  	
			  	
			  	// Create forum posts and guestbook notifications for all answers.
			  	sex_sense_answer_distribute(array('post_id' => $entry['id']));
				}
				catch(Exception $error)
				{
					trace('sex_sense_schedule', $error->getMessage());
				}
				break;
			case 'music_guess':
					$query = 'INSERT INTO music_guess_songs (secret_id, artist, song, alternate_spellings, timestamp, artist_score, song_score)' . "\n";
					$query .= ' VALUES("' . $data['secret_id'] . '", "' . $data['artist'] . '", "' . $data['song'] . '", "' . addslashes(serialize($data['alternate_spellings'])) . '", "' . time() . '", "' . $data['artist_score'] . '", "' . $data['song_score'] . '")';
					
					mysql_query($query) or trace('sql_error', $query . ' ' . mysql_error());

					$url = '/mattan/gissa_laaten.php';
					$label = 'Ny låt!';
				break;
			case 'survey':
				survey_create($data);
				$survey = survey_fetch(array('type' => 'front_page'));
				cache_save('fp_survey', $survey);
				$url = '/index.php#survey';
				$label = $data['question'];
				break;
			case 'new_image':
			case 'new_clip':
			case 'new_flash':
			case 'new_background':
			case 'new_software':
			case 'new_prank':
			case 'new_music':
			case 'new_game':
				$url = $data['url'];
				$label = $data['title'];
			break;
		}
		$query = 'INSERT INTO recent_updates (type, label, timestamp, url)' . ' VALUES("' .
					$event['type'] . '", "' .
					$label . '", "' .
					$event['release'] . '", "' .
					$url . '")';
		$query . '<br />';
		if(!mysql_query($query))
		{
			report_sql_error($query, __FILE__, __LINE__);
		}
		else
		{
			$query = 'UPDATE scheduled_events SET released = 1 WHERE id="' . $event['id'] . '"';
			if(!mysql_query($query))
			{
				report_sql_error($query, __FILE__, __LINE__);
			}
			else
			{
				log_to_file('scheduled_events', LOGLEVEL_INFO, __FILE__, __LINE__, 
							'released  ' . $event['type'] . ' id: ' . $event['id'] . ' ' . date('Y-m-d H:i', $release));
			}
		}
	}
}

/*
function schedule_releases_list()
{
	$events = schedule_events_get();
	
	foreach()
}

*/


function schedule_release_get($options)
{
	/*
	returns a random time within the next free slot for type
	option		comment
	type		give me a free slot for this type
	after		give me the first free slot after this timestamp
	*/
	
	global $schedule_slots;

	if(!array_key_exists($options['type'], $schedule_slots))
	{
		return false;
	}
	$type = $options['type'];
	$slots = $schedule_slots[$type];
	$num_of_slots = count($slots);
	$slot = 0;
	$day = 0; /* Offset, days counting from today */
	$midnight = strtotime(date('Y-m-d'));
	$time = max($options['after'], time());
	unset($free_slot);
	
	
	$debug = 'time: ' . $time . ' ' . date('Y-m-d H:i', $time) . "\n";
	$debug .=  'after: ' . $options['after'] . ' ' . date('Y-m-d H:i', $options['after']) . "\n";
	$debug .=  'type: ' . $type . "\n";
	$debug .=  date('Y-m-d H:i', $slots[$slot]['end'] + $day * 86400) . "\n";
	
	/* Find the next slot, regardless if it's occupied or not */
	while(($slots[$slot]['start'] + ($day * 86400)) <= $time)
	{
		$debug .=  'slot before time <br />';
		$slot++;
		if($slot >= $num_of_slots)
		{
			$day++;
			$slot = 0;
		}
	}
	$debug .=  'Find nearest slot after day #' . $day . ', slot #' . $slot . "\n";
	
	$query = 'SELECT `release` FROM scheduled_events WHERE type="' . $type . '" AND `release` > "' . $time . '" ORDER BY `release` ASC';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$debug .=  'Items in qeue: ' . mysql_num_rows($result) . "\n";
	if($data = mysql_fetch_assoc($result))
	{
		$release = $data['release'];
		$debug .=  'before loop, release: ' . $release . ' ' . date('Y-m-d H:i', $release) . "\n";
		while(!isset($free_slot))
		{
			if($release < $slots[$slot]['start'] + ($day * 86400)) /* Released before current slot */
			{
				$debug .=  'Released before current slot' . "\n";
				if($data = mysql_fetch_assoc($result))
				{
					$release = $data['release'];
				}
				else
				{
					$free_slot = $slot;
				}
			}
			elseif($release < $slots[$slot]['end'] + $day * 86400) /* Released during current slot */
			{
				$slot++;
				if($slot >= $num_of_slots)
				{
					$day++;
					$slot = 0;
				}
				if($data = mysql_fetch_assoc($result))
				{
					$release = $data['release'];
				}
				else
				{
					$free_slot = $slot;
				}
			}
			else /* Released after current slot */
			{
				$free_slot = $slot;
			}
		}
	}
//	preint_r($slots);
	$debug .=  'slot: ' . $slot . "\n";
	$debug .=  'day: ' . $day . "\n";
	log_to_file('schedule', LOGLEVEL_DEBUG, __FILE__, __LINE__, $debug);
	return $slots[$slot]['start'] + $day * 86400 + rand(0, $slots[$slot]['end'] - $slots[$slot]['start']);
}

?>