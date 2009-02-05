<?php
//require_once('../core/common.php');
if(false)//ENVIRONMENT != 'development')
{
	require_once(PATHS_INCLUDE . 'libraries/jsmin.lib.php');
	header('Content-type: text/plain');
	$do_update = false; //Default: false. Change to true to update merged file irrespective of last JavaScript change.
	unset($merge_input);
	$javascripts_path = PATHS_WEBROOT . 'javascripts/';
	$merged_file_path = PATHS_STATIC . 'javascripts/merged.js';
	$merged_file_last_update = filemtime($merged_file_path);
	$last_javascript_update = 0;
	$maxlen = 45;
	
	echo 'Merged file last updated on unix_timestamp: ' . $merged_file_last_update . ' (' . (time() - $merged_file_last_update) . ' seconds ago)' . "\n";
	echo "\n";
	
	foreach ($js_compress_important_files as $file)
	{
		if (file_exists($javascripts_path . $file) && preg_match('/\.js$/', $file))
		{
			echo 'Javascript matched | ' . $file . str_repeat(' ', $maxlen - strlen($file)) . '|';
			$last_javascript_update = ($last_javascript_update < filemtime($javascripts_path . $file)) ? filemtime($javascripts_path . $file) : $last_javascript_update;
			if (filemtime($javascripts_path . $file) > $merged_file_last_update)
			{
				echo ' UPDATED, ' . (time() - filemtime($javascripts_path . $file)) . ' seconds old';
			}
			else
			{
				echo ' up-to-date';
			}
		}
		else
		{
			echo 'File ignored       | ' . $file;
		}
		echo "\n";
	}
	
	echo "\n";
	
	if($last_javascript_update > $merged_file_last_update)
	{
		$do_update = true;
	}
	else
	{
		echo 'No update necessary.' . "\n";
	}
	
	if($do_update)
	{
		if (!is_writeable($merged_file_path))
		{
			throw new Exception('The merged file destination is not writeable');
		}

		$time_before = microtime(true);
		foreach ($js_compress_important_files as $file)
		{
			if (file_exists($javascripts_path . $file) && preg_match('/\.js$/', $file))
			{
				$merge_input .= file_get_contents($javascripts_path . $file) . "\n";
			}
			else
			{
				echo 'Ignored file ' . $file . ' in compress-stack.' . "\n";
			}
		}
		$packed = JSMin::minify($merge_input);
		$time_after = microtime(true);
		$elapsed_time = $time_after - $time_before;
		$unpacked_size = strlen($merge_input);
		$packed_size = strlen($packed);
		$ratio_size = round($unpacked_size - $ratio_size);
		$process = file_put_contents($merged_file_path, $packed);
		echo $process ? ('Merge finished successfully.' . "\n" . 'Time elapsed: ' . $elapsed_time . ' seconds, bits saved: ' . $ratio_size . ' bit.') : 'An error appeared while merging JavaScript library.';
	}
}
?>