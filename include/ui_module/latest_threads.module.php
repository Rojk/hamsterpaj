<?php
/*
	$threads = cache_load('latest_forum_threads');
	
	$options['output'] .= '<ul>' . "\n";
	foreach($threads AS $thread)
	{
		$thread['title'] = (mb_strlen($thread['title'], 'UTF8') > 22) ? htmlspecialchars(mb_substr(htmlspecialchars_decode($thread['title']), 0, 19, 'UTF8')) . '...' : $thread['title'];
		$info = 'I ' . $thread['category_title'] . ' av ' . $thread['username'] . ': ' . $thread['title'];
		$options['output'] .= '<li>' . date('H:i', $thread['timestamp']) . ' <a title="' . $info . '" href="' . $thread['url'] . '">' . $thread['title'] . '</a></li>' . "\n";
	}
	$options['return'] .= '</ul>' . "\n";
	
	if(!(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') || $_SESSION['disablesteve'] == 1))
	{
			$options['output'] .= '<img src="' . IMAGE_URL . 'steve/icon_gun.gif" id="steve_gun" />' . "\n";
	}
	*/
?>