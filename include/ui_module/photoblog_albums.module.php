<?php
	global $photoblog_user;
	$options['user'] = $photoblog_user['id'];
	$photoblog_albums = photoblog_categories_fetch($options);
	foreach($photoblog_albums as $photoblog_album)
	{
		if(strlen($photoblog_album['photos']) >= 1)
		{
			$photoblog_album['photos'] = explode(',', $photoblog_album['photos']);
			$options['output'].= '<a href="/fotoblogg/' . $photoblog_user['username'] . '/album/' . $photoblog_album['name'] . '" />' . "\n";
			$options['output'].= '<img src="http://images.hamsterpaj.net/photos/full/' . floor($photoblog_album['photos'][0]['id']/5000) . '/' . $photoblog_album['photos'][0]['id'] . '.jpg" />' . "\n";
			$options['output'].= '<h3>' . $photoblog_album['name'] . '</h3>' . "\n";
			$options['output'].= '</a>' . "\n";
		}
	}
?>