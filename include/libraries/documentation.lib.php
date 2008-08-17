<?php
	function open_source_ui_top($options)
	{
		$options['title'] = (isset($options['title'])) ? $options['title'] : 'Hamsterpaj.net - Onlinespel, community, forum och annat kul ;)';

		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
		echo '<head>' . "\n";
		echo '<meta name="description" content="' . $options['meta_description'] . '" />' . "\n";
		echo '<meta name="keywords" content="' . $options['meta_keywords'] . '" />' . "\n";
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
		echo '<title>' . $options['title'] . '</title>' . "\n";
		echo '<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
		echo '<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";

		$options['javascripts'] = array('womlib.js');
		$options['javascripts'][] = 'ui_server_message.js';
		$options['javascripts'][] = 'open_source_ui.js';
		array_unshift($options['javascripts'], 'jquery.js');
		
		$options['stylesheets'][] = 'shared.css';
		$options['stylesheets'][] = 'message.css';
		$options['stylesheets'][] = 'open_source_ui.css';
		$options['stylesheets'][] = 'rounded_corners.css';
		echo "\n\n" . '<!-- Load stylesheets, version is timestamp of last file modification. Current timestamp is: ' . time() . ' -->' . "\n";
		echo '<style type="text/css">' . "\n";
		foreach($options['stylesheets'] AS $stylesheet)
		{
			echo '@import url(\'/stylesheets/' . $stylesheet . '?version=' . filemtime(PATHS_WEBROOT . 'stylesheets/' . $stylesheet) . '\');' . "\n";
		}
		echo '</style>' . "\n";

		echo "\n\n" . '<!-- Load javascripts, version is timestamp of last file modification. -->' . "\n";
		foreach($options['javascripts'] AS $javascript)
		{
			echo '<script type="text/javascript" language="javascript" ';
			echo 'src="/javascripts/' . $javascript . '?version=' . filemtime(PATHS_WEBROOT . 'javascripts/' . $javascript) . '"></script>' . "\n";
		}
		echo $options['header_extra'];
		
		echo '<div id="header">' . "\n";
			echo '<img src="http://images.hamsterpaj.net/open_source/header_logo.png" id="logo" />' . "\n";
			echo '<div id="menu">' . "\n";
				echo '<ul>' . "\n";
					echo '<li>News</li>' . "\n";
					echo '<li>Documentation</li>' . "\n";
					echo '<li>Wiki</li>' . "\n";
				echo '</ul>' . "\n";
			echo '</div>' . "\n";
			
			echo '<div id="pagemap">' . "\n";
				echo '<h1>Documentation</h1> // ' . "\n";
				echo '<h2>Design-functions</h2> // ' . "\n";
				echo '<h3>Containers</h3> // ' . "\n";
				echo '<h4>Text_container</h4>' . "\n";
			echo '</div>' . "\n";
			
		echo '</div>' . "\n";
		
			echo '<div id="large_menu">' . "\n";
				echo '<ul>' . "\n";
					echo '<li>Design-functions</li>' . "\n";
						echo '<ul>' . "\n";
							echo '<li>Containers</li>' . "\n";
								echo '<ul>' . "\n";
									echo '<li>server_message</li>' . "\n";
									echo '<li>text_container</li>' . "\n";
									echo '<li>text_container_tabbed</li>' . "\n";
									echo '<li>user_message</li>' . "\n";
								echo '</ul>' . "\n";
							echo '<li>Modules</li>' . "\n";
								echo '<ul>' . "\n";
									echo '<li>right_module</li>' . "\n";
									echo '<li>profile_module</li>' . "\n";
									echo '<li>latest_events_module</li>' . "\n";
								echo '</ul>' . "\n";
							echo '<li>Functions</li>' . "\n";
								echo '<ul>' . "\n";
									echo '<li>pagination</li>' . "\n";
									echo '<li>search</li>' . "\n";
								echo '</ul>' . "\n";
						echo '</ul>' . "\n";
					echo '<li>Shared-functions</li>' . "\n";
					echo '<li>Library-functions</li>' . "\n";
				echo '</ul>' . "\n";
			echo '</div>' . "\n";
		echo '<div id="content">' . "\n";
	}
	
	function open_source_ui_bottom()
	{
		echo '</div>' . "\n";
		echo '</body>' . "\n";
		echo '</html>' . "\n";
	}
 ?>