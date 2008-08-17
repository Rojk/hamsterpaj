<?php
	// Can be found in: http://www.hamsterpaj.net/open_source/readfile.php?file=open_source/standard.php
	require('../include/core/common.php');

	/* OPEN_SOURCE */
	
	define('FILE', '/storage/www/www.hamsterpaj.net/data/' . $_GET['file']);
	
	if(!isset($_GET['file']))
	{
		die('Please call with argument ?file=path/to/file');
	}
	
	if(!file_exists(FILE))
	{
		die('File not found');
	}
	
	if(filesize(FILE) > 100000)
	{
		die('Filesize too large');
	}
	
	$content = file_get_contents(FILE);
	
	if(strpos($content, '/* OPEN_SOURCE */') !== false)
	{
		if(isset($_GET['download']))
		{
			header('Content-type: application/force-download');
			readfile(FILE);
		}
		else
		{
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
			echo '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
			echo '<head>' . "\n";
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
			echo '<title>Visar ' . FILE . ' p㟈amsterpaj</title>' . "\n";
			echo '<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
			echo '<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
			
			echo '</head>' . "\n";
			
			echo '<body>' . "\n";
			
			echo '<fieldset>' . "\n";
			echo '<legend>' . FILE . ' (' . filesize(FILE) . ' bytes)</legend>' . "\n";
			
			echo 'Last changed: ' . date('Y-m-d H:i:s', filemtime(FILE)) . '<br />';
			//echo 'MD5 sum: ' . md5($content) . '<br />';
			echo 'Rows: ' . count(explode("\n", $content)) . '<br />' . "\n";
			echo '<a href="/open_source/readfile.php?download=true&file=' . $_GET['file'] . '">Download this file</a>';
			
			echo '</fieldset>' . "\n";
			
			$highlighted_string = highlight_file(FILE, true);
			
			if(isset($_GET['active_code']))
			{
				$follow_functions = array('ui_top', 'ui_bottom');
				
				$pattern = '/\<span style=\"color: #0000BB\">(' . implode('|', $follow_functions) . ')\<\/span\>/';
				$replacement = '<a href="/open_source/find_function.php?function=$1">$1</a>';
				$highlighted_string = preg_replace($pattern, $replacement, $highlighted_string);
			}
			
			echo $highlighted_string;
			
			echo '</body>' . "\n";
			echo '</html>' . "\n";
		}
	}
	else
	{
		die('Access denied');
	}

?>