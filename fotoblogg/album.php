<?php
	$ui_options['ui_modules']['photoblog_user'] = 'User';
	$ui_options['ui_modules']['photoblog_albums'] = 'Album';
	$ui_options['ui_modules']['photoblog_calendar'] = 'Kalender';
	
		if ( isset($uri_parts[4]) && preg_match('/^[a-zA-Z0-9-_]+$/', $uri_parts[4]) )
		{
			$albumname = $uri_parts[4];	
			if ( $uri_parts[5] == 'list' )
			{
				$out .= 'Visar album ' . $albumname . 's foton.';
			}
			else
			{
				$out .= 'Visar foton i albumet: ' . $albumname . '';
			}
		}
		else
		{		
				$out .= 'Listar ' . $username . 's foton.';
		}
?>