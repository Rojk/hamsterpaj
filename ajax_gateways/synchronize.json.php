<?php
	/*
		It is very important that output is properly escaped.
		If not, we may end upp with a XSS-attack.
		
		
		If you don't know what you're doing, HANDS OFF!!!
	
	
	*/

	require('../include/core/common.php');
	if(isset($_GET['fetch']) && !empty($_GET['fetch']))
	{
		$objects_to_fetch = explode(',', $_GET['fetch']);
		foreach($objects_to_fetch as $object_to_fetch)
		{
			if(in_array($object_to_fetch, array('ui_noticebar_guestbook', 'ui_noticebar_discussion_forum', 'ui_noticebar_groups')))
			{
				$notices = ui_notices_fetch();
			}
			switch($object_to_fetch)
			{				
				case 'ui_noticebar_guestbook':
					$data = $notices['guestbook'];
				break;
				
				case 'ui_noticebar_discussion_forum':
					foreach($_SESSION['forum']['subscriptions'] as $subscription)
					{
						$url = (isset($thread['url'])) ? $thread['url'] : $thread['handle'] . '/sida_1.php';
						$subscriptions[] = '{"title": "' . addslashes($subscription['title']) . '", "url": "' . urlencode($url) . '", "unread_posts": ' . $subscription['unread_posts'] . '}';
					}
					$data = '{"new_notices": ' . $notices['discussion_forum']['new_notices'] . ', "subscriptions": [' . implode(', ', $subscriptions) . ']}';
				break;
				
				case 'ui_noticebar_groups':
					$groups = array();
					foreach($notices['groups']['groups'] as $group_id => $group)
					{
						$groups[] = '{"group_id": ' . $group_id . ', "title": "' . addslashes($group['title']) . '", "unread_messages": ' . $group['unread_messages'] . '}';
					}
					$data = '{"unread_notices": ' . $notices['groups']['unread_notices'] . ', "groups": [' . implode(', ', $groups) . ']}';
				break;
				default: continue 2;
			}
			
			$return[] = '{"' . $object_to_fetch . '": ' . $data . '}';
		}
		
		echo '[' . implode(', ', $return) . ']';
	}
	else
	{
		echo '[]';
	}
?>