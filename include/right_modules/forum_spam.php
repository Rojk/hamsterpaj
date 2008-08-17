<?php
	$threads = cache_load('latest_forum_spam');

	$return .= '<ul>' . "\n";
	foreach($threads AS $thread)
	{
		$thread['title'] = (mb_strlen($thread['title'], 'UTF8') > 22) ? htmlspecialchars(mb_substr(htmlspecialchars_decode($thread['title']), 0, 19, 'UTF8')) . '...' : $thread['title'];
		$info = 'I ' . $thread['category_title'] . ' av ' . $thread['username'];
		$return .= '<li>' . date('H:i', $thread['timestamp']) . ' <a title="' . $info . '" href="' . $thread['url'] . '">' . $thread['title'] . '</a></li>' . "\n";
	}
	$return .= '</ul>' . "\n";
?>