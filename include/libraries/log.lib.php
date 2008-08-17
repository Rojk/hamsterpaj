<?php
	function trace($type, $log_text)
	{
		define('LOG_PATH', '/var/log/hamsterpaj/trace/');

		$backtrace = debug_backtrace();
		$file = $backtrace[0]['file'];
		$line = $backtrace[0]['line'];
		
		$filename = substr($file, strrpos($file, '/')+1);
		
		$output = date('H:i:s') . ' ' . $filename . ':' . $line . ': ' . $log_text . "\n";

		file_put_contents(LOG_PATH . $type . '.log', $output, FILE_APPEND);
	}
?>