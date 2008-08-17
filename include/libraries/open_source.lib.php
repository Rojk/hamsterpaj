<?php
	/* OPEN_SOURCE */

	function open_source_get_file_tree($tree, $iteration_depth = '')
  {
  	ksort($tree); // Sort array keys...
  	$return .= '<ul>' . "\n";
  	foreach($tree as $key => $value)
  	{
  		if(is_array($value))
  		{
  			$return .= '<li>' . "\n";
  			$return .= '<h3>/' . $key . '</h3>' . "\n";
  			$return .= open_source_get_file_tree($value, $iteration_depth . $key . '/');
  			$return .= '</li>' . "\n";
  		}
  		else
  		{
  			$file_path = $iteration_depth . $value;
  			$return .= '<li><a href="/open_source/readfile.php?download&file=' . $file_path . '">[DL]</a> <a href="/open_source/readfile.php?file=' . $file_path . '">' . $value . '</a></li>' . "\n";
  		}
  	}
  	$return .= '</ul>' . "\n";
  	
  	return $return;
  }
  
  function open_source_top($options)
  {
    global $SIDE_MODULES;

    $options['title'] = (isset($options['title'])) ? $options['title'] : 'Open Source - Hamsterpaj.net';
    
    $options['stylesheets'][] = 'open_source.css';
    $options['stylesheets'][] = 'modules.css';
    
    $options['javascripts'][] = 'open_source.js';
    $options['javascripts'][] = 'scripts.js';
    $options['javascripts'][] = 'steve.js';
    array_unshift($options['javascripts'], 'womlib.js');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
    echo '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
    echo '<head>' . "\n";
    echo '<meta name="description" content="' . $options['meta_description'] . '" />' . "\n";
    echo '<meta name="keywords" content="' . $options['meta_keywords'] . '" />' . "\n";
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
    echo '<title>' . $options['title'] . '</title>' . "\n";
    echo '<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
    echo '<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";

	  echo '<style type="text/css">' . "\n";
    foreach($options['stylesheets'] AS $stylesheet)
    {
        echo '@import url(\'/stylesheets/' . $stylesheet . '?version=' . filemtime(PATHS_WEBROOT . 'stylesheets/' . $stylesheet) . '\');' . "\n";
    }
    echo '</style>' . "\n";

    foreach($options['javascripts'] AS $javascript)
    {
        echo '<script type="text/javascript" language="javascript" ';
        echo 'src="/javascripts/' . $javascript . '?version=' . filemtime(PATHS_WEBROOT . 'javascripts/' . $javascript) . '"></script>' . "\n";
    }
    
    echo '</head>' . "\n";
		echo '<body>';

    echo '<div id="hamsterpaj_website">' . "\n";
    
    echo '<img src="http://images.hamsterpaj.net/open_source/site_top_rounded_corners.png" id="site_top_rounded_corners" />' . "\n";
		echo '<div id="site_container">' . "\n";

    echo '<div id="main">' . "\n";
    
    echo '<div id="top">' . "\n";      
    echo '<a href="/"><img src="http://images.hamsterpaj.net/ui/logo.png" id="logo" /></a>' . "\n";
    
    if(!isset($options['hide_open_source_logo']))
    {
			echo '<img src="http://images.hamsterpaj.net/steve/steve.gif" id="steve" />' . "\n";
	    echo '<h1>Open Source</h1>' . "\n";
	  
	    echo '&nbsp;<a href="/index.php">&laquo; Tillbaka till Hamsterpaj</a>.' . "\n";
	  }
  	
    echo '<br style="clear: both;" />' . "\n";
    echo '</div>' ."\n";
  	        
  	echo '<div id="middle">' . "\n";
	}

  function open_source_bottom($options)
  {
      global $SIDE_MODULES;
      echo '<br style="clear: both;" />' . "\n";

      echo '</div>' . "\n"; // Close site_frame border
      echo '</div>' . "\n";

      echo '</div>' . "\n";
      
      echo '<div id="main_right">' . "\n";
      
      if(!isset($options['hide_right_modules']))
      {
      	echo ui_render_right_module(array('handle' => 'open_source_menu', 'heading' => 'Open source-menyn', 'open_source_menu_path' => $options['open_source_menu_path']));
      	echo ui_render_right_module(array('handle' => 'open_source_forum_threads', 'heading' => 'Open Source-forumet'));
				echo ui_render_right_module(array('handle' => 'open_source_resources', 'heading' => 'Bra att ha-saker'));
    	}
      
      echo '</div>' . "\n";        


      echo '</div>' . "\n"; // Close site_container
      echo '<img src="http://images.hamsterpaj.net/open_source/site_bottom_rounded_corners.png" id="site_bottom_rounded_corners" />' . "\n";
      
      echo '</div>' . "\n"; // Close hamsterpaj_website
  }
 ?>