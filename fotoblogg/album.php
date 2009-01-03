<?php
	$ui_options['ui_modules']['photoblog_user'] = 'User';
	$ui_options['ui_modules']['photoblog_albums'] = 'Album';
	$ui_options['ui_modules']['photoblog_calendar'] = 'Kalender';
	
		if ( isset($uri_parts[4]) && preg_match('/^[a-zA-Z0-9-_]+$/', $uri_parts[4]) )
		{
			$albumname = $uri_parts[4];	
			$out .= 'Visar album ' . $albumname . 's foton.';
				global $photoblog_user;
				$options['name'] = $albumname;
				$photoblog_album = photoblog_categories_fetch($options);
				
				preint_r($photoblog_album, true);
		}
		else
		{		
				$out .= 'Listar ' . $photoblog_user['username'] . 's album.';
				global $photoblog_user;
				$options['user'] = $photoblog_user['id'];
				$photoblog_albums = photoblog_categories_fetch($options);
				
				foreach($photoblog_albums as $photoblog_album)
				{
					if(count($photoblog_album['photos']) >= 1)
					{
						$out .= '<a href="/fotoblogg/' . $photoblog_user['username'] . '/album/' . $photoblog_album['name'] . '" />' . "\n";
						$out .= '<img src="http://images.hamsterpaj.net/photos/full/' . floor($photoblog_album['photos']['id'][0]/5000) . '/' . $photoblog_album['photos']['id'][0] . '.jpg" />' . "\n";
						$out .= '<h3>' . $photoblog_album['name'] . '</h3>' . "\n";
						$out .= '</a>' . "\n";
					}
				}
		}
?>