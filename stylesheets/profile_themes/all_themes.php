<?php
	/* OPEN_SOURCE */
	
	require('../../include/core/common.php');
	header('Content-type: text/css');
	
	$read_from = PATHS_WEBROOT . 'stylesheets/profile_themes/';
	if(!is_dir($read_from))
	{
		die('/* No such directory. */');
	}
	
	$directory_handle  = opendir($read_from);
	while (($filename = readdir($directory_handle)) !== false)
	{
		if(substr($filename, -4, 4) == '.css')
		{
			//echo '@import url(\'/stylesheets/profile_themes/' . $filename . '?version=' . filemtime($read_from . $filename) . '\');' . "\n";
			echo '/* ' . $filename . ' */' . "\n";
			@readfile($read_from . $filename);
			echo "\n\n";
		}
	}
?>
