<?php
	foreach(array('discussion_forum_remove_posts', 'discussion_forum_edit_posts', 'discussion_forum_rename_threads', 'discussion_forum_lock_threads', 'discussion_forum_sticky_threads', 'discussion_forum_move_thread', 'discussion_forum_post_addition') as $privilegie)
	{
		if(is_privilegied($privilegie))
		{
			$query = 'SELECT COUNT(*) AS reports FROM abuse WHERE reply_timestamp = 0';
			$data = array_pop(query_cache(array('query' => $query, 'category' => 'other', 'max_delay' => 60)));
			
			if($data['reports'] == 0)
			{
				$return .= 'Inga nya rapporter';
			}
			elseif ($data['reports'] > 1)
			{
				$return .= '<a href="/admin/abuse.php">' . $data['reports'] . ' nya rapporter</a>' . "\n";
			}
			else
			{
				$return .= '<a href="/admin/abuse.php">En ny rapport</a>' . "\n";
			}
			
			break;// IMPORTANT!
		}
	}
	
	if(is_privilegied('avatar_admin'))
	{
		/* Some pre-historic script from admin/avatarer.php... */
		$filecontent = file_get_contents(PATHS_WEBROOT . 'admin/validator.txt');
		$filerows = explode("\n", $filecontent);
		$timestamp = $filerows[0];
		$userid = $filerows[1];
		$checktime = time();
		if (($timestamp + 90) < $checktime)
		{
			$query = 'SELECT COUNT(*) AS new_images FROM userinfo WHERE image = 1';
			$result_data = query_cache(array('query' => $query, 'max_delay' => 60));
			$new_images = $result_data[0]['new_images'];
			
			if($new_images == 0)
			{
				$return .= '<br />----------------------';
				$return .= '<br />Inga nya bilder';
			}
			elseif($new_images > 1)
			{
				$return .= '<br />----------------------';
				$return .= '<br /><a href="/admin/avatarer.php">' . $new_images . ' nya bilder att validera &raquo;</a>';
			}
			else
			{
				$return .= '<br />----------------------';
				$return .= '<br /><a href="/admin/avatarer.php">En ny bild att validera &raquo;</a>';
			}
		}
	}
?>