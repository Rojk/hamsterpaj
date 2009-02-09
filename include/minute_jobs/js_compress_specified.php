<?php
if(ENVIRONMENT != 'development')
{
	require_once(PATHS_LIBRARIES . 'jsmin.lib.php');
	header('Content-type: text/plain');
	$javascripts_path = PATHS_WEBROOT . 'javascripts/';
	$merged_file_path = PATHS_STATIC . 'javascripts/specified/';
	$update_list = array();
	$number_of_files = 0;
	$maxlen = 30;
	$dir = dir($javascripts_path);
	while(($file = $dir->read()) !== false)
	{
		if(preg_match('/\.js$/', $file))
		{
			if(!in_array($file, $js_compress_important_files))
			{
				array_push($update_list, $file);
			}
			$number_of_files++;
		}
	}
	$dir->close();
	$out .= sizeof($update_list) . ' of ' . $number_of_files . ' Javascript files added to queue.' . "\n";
	$out .= 'Updating list...' . "\n\n";
	$out .= ' ID |   SOURCE   | COMPRESSED | NAME' . str_repeat(' ', $maxlen - 4) . '| STATUS' . "\n";
	$file_id = 1;
	$updated_files = 0;
	$total_saved_bit = 0;
	$start_time = microtime(true);
	foreach($update_list as $file)
	{
		if($file_id < 10) $out .= ' ';
		if($file_id < 100) $out .= ' ';
		$out .= $file_id . ' | ' . filemtime($javascripts_path . $file) . ' | ' . filemtime($merged_file_path . $file) . ' | ' . $file . str_repeat(' ', $maxlen - strlen($file)) . '| ';
		if(filemtime($javascripts_path . $file) >= filemtime($merged_file_path . $file))
		{
			$file_contents = file_get_contents($javascripts_path . $file);
			$packed = JSMin::minify($file_contents);
			$process = file_put_contents($merged_file_path . $file, $packed);
			$saved_bit = strlen($file_contents) - strlen($packed);
			if($process)
			{
				$out .= 'Compress succeed (' . (time() - filemtime($javascripts_path . $file)) . ' seconds old). ' . $saved_bit . ' bits saved';
				$updated_files++;
				$total_saved_bit += $saved_bit;
			}
			else
			{
				$out .= 'ERROR';
			}
		}
		else
		{
			$out .= 'Up-to-date';
		}
		$out .= "\n";
		$file_id++;
	}
	$end_time = microtime(true);
	$out .= '' . "\n";
	$out .= $updated_files . ' of ' . sizeof($update_list) . ' files updated in ' . ($end_time - $start_time) . ' seconds. Totally ' . $total_saved_bit . ' bit saved.' . "\n";
}
?>
