<?php
	/*
	function online_ovs($level)
	{
		//Preliminary stylesheet.
		$out .= '<style type="text/css">
		.online_ovs ul
		{
		margin: 0px;
		padding: 0px;
		list-style: none;
		}
		.online_ovs li
		{
		margin: 0px;
		list-style-type: none;
		}
		</style>';
		$nextlevel = $level + 1;
		$out .= '<div class="online_ovs">';
		$userlevel_fetch = ($level == 3) ? "userlevel = 3 OR userlevel = 4" : "userlevel = 5";
		$query = query_cache(array('query' => 'SELECT userlevel, id, username, lastaction FROM login WHERE ' . $userlevel_fetch . ' ORDER BY lastaction DESC LIMIT 5'));
		$out .= '<ul>' . "\n";
		foreach($query AS $row)
		{
			if($row['lastaction'] > time() - 600)
			{
				$out .= '<li>' . "\n";
				$out .= '- ';
				$out .= '<a href="/traffa/guestbook.php?view=' . $row['id'] . '">' . $row['username'] . '</a><br />' . "\n";
				$out .= '</li>' . "\n";
				
				$many[] = 'random data. saved just for the sake of the count :)';
			}
		}
		$out .= '</ul></div>' . "\n";
		$count = count($many);
		if ($count > 0)
		{
			return $out;
		}
		else
		{
			return false;
		}
	}
	
	
		if (online_ovs(3))
		{
			$return .= online_ovs(3);
		}
		elseif (online_ovs(4))
		{
			$return .= online_ovs(4);
		}
		elseif (online_ovs(5))
		{
			$return .= online_ovs(5);
		}
		else
		{
			$title = 'Ingen';
		}
		$return .= '<div style="height: 2px;"></div><a href="/admin/online_ovs.php">Alla inloggade OV\'s</a>';
	*/
	
	$query = 'SELECT DISTINCT(p.user) AS user_id, l.username FROM login AS l, privilegies AS p WHERE l.id = p.user AND p.value = 0 AND p.privilegie IN ("discussion_forum_remove_posts", "discussion_forum_edit_posts", "discussion_forum_rename_threads", "discussion_forum_lock_threads", "discussion_forum_sticky_threads", "discussion_forum_move_thread", "discussion_forum_post_addition") AND l.lastaction > UNIX_TIMESTAMP() - 600 ORDER BY l.lastaction DESC limit 7';
	$result = query_cache(array('query' => $query));
	foreach($result as $data)
	{
		$return .= '<li>' . "\n";
		$return .= '- ';
		$return .= '<a href="/traffa/guestbook.php?view=' . $data['user_id'] . '">' . $data['username'] . '</a><br />' . "\n";
		$return .= '</li>' . "\n";
	}
?>