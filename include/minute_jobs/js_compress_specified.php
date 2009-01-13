<?php
require_once('../core/common.php');
if(ENVIRONMENT != 'development')
{
	require_once(PATHS_INCLUDE . 'libraries/jsmin.php');
	header('Content-type: text/plain');
	$javascripts_path = PATHS_WEBROOT . 'javascripts/';
	$merged_file_path = PATHS_WEBROOT . 'tmp/javascripts/specified/';
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
	echo sizeof($update_list) . ' of ' . $number_of_files . ' Javascript files added to queue.' . "\n";
	echo 'Updating list...' . "\n\n";
	echo ' ID |   SOURCE   | COMPRESSED | NAME' . str_repeat(' ', $maxlen - 4) . '| STATUS' . "\n";
	$file_id = 1;
	$updated_files = 0;
	$total_saved_bit = 0;
	$start_time = microtime(true);
	foreach($update_list as $file)
	{
		if($file_id < 10) echo ' ';
		if($file_id < 100) echo ' ';
		echo $file_id . ' | ' . filemtime($javascripts_path . $file) . ' | ' . filemtime($merged_file_path . $file) . ' | ' . $file . str_repeat(' ', $maxlen - strlen($file)) . '| ';
		if((filemtime($javascripts_path . $file) >= filemtime($merged_file_path . $file)) || FORCE_UPDATE_JS_LIB)
		{
			$file_contents = file_get_contents($javascripts_path . $file);
			$packed = JSMin::minify($file_contents);
			$process = file_put_contents($merged_file_path . $file, $packed);
			$saved_bit = strlen($file_contents) - strlen($packed);
			if($process)
			{
				echo 'Compress succeed (' . (time() - filemtime($javascripts_path . $file)) . ' seconds old). ' . $saved_bit . ' bits saved';
				$updated_files++;
				$total_saved_bit += $saved_bit;
			}
			else
			{
				echo 'ERROR';
			}
		}
		else
		{
			echo 'Up-to-date';
		}
		echo "\n";
		$file_id++;
	}
	$end_time = microtime(true);
	echo "\n";
	echo $updated_files . ' of ' . sizeof($update_list) . ' files updated in ' . ($end_time - $start_time) . ' seconds. Totally ' . $total_saved_bit . ' bit saved.' . "\n";
	
	if(CLEAR_JS_SPECIFIC_LIB)
	{
		echo "\n";
		echo 'Files in merged path:' . "\n";
		$dir = dir($merged_file_path);
		while(($file = $dir->read()) !== false)
		{
			if(in_array($file, $js_compress_important_files))
			{
				echo $file . '<---------------- Removed (refresh)';
				unlink($merged_file_path . $file);
			}
			else
			{
				echo $file;
			}
			echo "\n";
		}
		$dir->close();
	}
}
?>