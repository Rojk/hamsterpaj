<?php
	$threads = cache_load('latest_forum_posts');
	
	$return .= '<ul>' . "\n";
	foreach($threads AS $thread)
	{
		$thread['title'] = (mb_strlen($thread['title'], 'UTF8') > 22) ? mb_substr($thread['title'], 0, 19, 'UTF8') . '...' : $thread['title'];
		$info = 'I ' . $thread['category_title'] . ' av ' . $thread['username'];
		$return .= '<li>' . date('H:i', $thread['last_post_timestamp']) . ' <a title="' . $info . '" href="' . $thread['url'] . '">' . $thread['title'] . '</a></li>' . "\n";
	}
	$return .= '</ul>' . "\n";
?>