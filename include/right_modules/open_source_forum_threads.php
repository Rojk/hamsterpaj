<?php
	$threads = cache_load('latest_forum_open_source_threads');
	
	$return .= '<ul>' . "\n";
	foreach($threads AS $thread)
	{
		$thread['title'] = (strlen($thread['title']) > 22) ? substr($thread['title'], 0, 19) . '...' : $thread['title'];
		$info = 'I ' . $thread['category_title'] . ' av ' . $thread['username'];
		$return .= '<li>' . date('H:i', $thread['timestamp']) . ' <a title="' . $info . '" href="' . $thread['url'] . '">' . $thread['title'] . '</a></li>' . "\n";
	}
	$return .= '</ul>' . "\n";
?>