<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/live_chat.lib.php');

	unset($event);
	unset($options);
	
	if(isset($_GET['message']) && login_checklogin() && strlen($_GET['message']) > 0)
	{
		if($_SESSION['live_chat']['last_message'][0]['md5'] == md5($_GET['message']))
		{
			exit;
		}
		
		$explosion = explode('_', $_GET['chatroom']);
		$event['type'] = $explosion[0];
		$event['reference_id'] = $explosion[1];

		$event['event_type'] = 'message';
		$event['username'] = $_SESSION['login']['username'];
		$event['message'] = utf8_encode(stripslashes($_GET['message']));
		
	  $pattern = "/[\n]+$/";
	  $replacement = "\n";
	 	$event['message'] = preg_replace($pattern, $replacement, $event['message']);
		
		if($_SESSION['login']['username'] == 'mrsten')
		{
			$event['message'] = 'Kämpa mot rasism och främlingsfientlighet! http://www.antifa.org/ http://www.revfront.org/';
		}

		event_log_log('live_chat_message');

		live_chat_event($event);

		$session['md5'] = md5($_GET['message']);
		$session['timestamp'] = time();
		array_unshift($_SESSION['live_chat']['last_message'], $session);
		if(count($_SESSION['live_chat']['last_message']) > 5)
		{
			array_pop($_SESSION['live_chat']['last_message']);
		}
		
		// Create a notice to all members that somebody is writing, if it is long since latest
		$latest_message = cache_load('live_chat_latest_message');
		if($event['reference_id'] = 4 && $latest_message < (time() - 15*60)) // If it is the normal klotterplank room and half an hour since last message
		{
			$cache_data['id'] = md5($_GET['message'] . time()); // We create a fake-id
			// Save latest messagetime to cache.
			$cache_data['timestamp'] = time();
			$cache_data['author'] = $event['username'];
			cache_save('live_chat_new_message', $cache_data);
		}
		
		cache_save('live_chat_latest_message', time());
	}
	else
	{
		foreach(array_keys($_GET) AS $chatroom)
		{
			unset($event);
			if($chatroom != 'cache_prevention')
			{
				$explosion = explode('_', $chatroom);
				$options['type'] = $explosion[0];
				$options['reference_id'] = $explosion[1];

				if(login_checklogin())
				{
					live_chat_log_update($options);
				}

				$options['min_id'] = (isset($_SESSION['live_chat']['chatrooms'][$chatroom]['last_id'])) ? $_SESSION['live_chat']['chatrooms'][$chatroom]['last_id'] : 0;
				$events = live_chat_load_events($options);

				foreach($events AS $key => $event)
				{
					$_SESSION['live_chat']['chatrooms'][$chatroom]['last_id'] = max($_SESSION['live_chat']['chatrooms'][$chatroom]['last_id'], $event['id']);
					if(strpos(strtolower($event['message']), strtolower($_SESSION['login']['username'])) !== false)
					{
						$event['highlight'] = true;
					}
					$events[$key] = $event;
				}
				$chatrooms[] = array('chatroom' => $chatroom, 'events' => $events);
			}
		}
		
		echo json_encode($chatrooms);
	}
?>