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
	
	/*
	This is a new (2007-04-24) logging system for hamsterpaj.net. The old one was 
	the single function 'to_logfile' in login-functions.php.
	This new system supports logging for different subsystems in different directories
	and also more different levels of logging.
	
	levels:
	LOGLEVEL_DEBUG	- used only for debugging purposes
	LOGLEVEL_INFO 	- information about an event that is normal, not an error
	LOGLEVEL_WARN 	- warning about something that should not happen but is not an error in code
	LOGLEVEL_ERROR	- an error has occured, something the code couldn't handle

	categories, this is now the same as a subsystem or a group of subsystems
	ex:
	forum	- logs from the forum system and possibly subsystems belonging to forum
	amuse	-	logs form amuse system
	messaging - the messaging system
	main - front page and general logs
	etc...
	
	*/

	define('LOGLEVEL_DEBUG', 3);
	define('LOGLEVEL_INFO', 2);
	define('LOGLEVEL_WARN', 1);
	define('LOGLEVEL_ERROR', 0);

	$loglevels['front_page']		=	LOGLEVEL_ERROR;
	$loglevels['main']				=	LOGLEVEL_ERROR;
	$loglevels['old_log_system']	=	LOGLEVEL_ERROR;
	$loglevels['forum']				=	LOGLEVEL_ERROR;
	$loglevels['amuse']				=	LOGLEVEL_ERROR;
	$loglevels['messaging']			=	LOGLEVEL_ERROR;
	$loglevels['default']			=	LOGLEVEL_ERROR;
	$loglevels['logging']			=	LOGLEVEL_ERROR;
	$loglevels['henrik']			=	LOGLEVEL_ERROR;
	$loglevels['johan']				=	LOGLEVEL_ERROR;
	$loglevels['distribute']		=	LOGLEVEL_ERROR;
//	$logids['distribute']			= array(644314);
	$loglevels['games']				=	LOGLEVEL_ERROR;
	$loglevels['tips']				=	LOGLEVEL_ERROR;
	$loglevels['film']				=	LOGLEVEL_ERROR;
	$loglevels['films']				=	LOGLEVEL_ERROR;
	$loglevels['scheduled_events']	=	LOGLEVEL_ERROR;
	$loglevels['rank']				=	LOGLEVEL_ERROR;
	$loglevels['comments']			=	LOGLEVEL_ERROR;
	$loglevels['highscore_games']	=	LOGLEVEL_DEBUG;
	$loglevels['deprecated']		=	LOGLEVEL_DEBUG;
	$loglevels['admin']				=	LOGLEVEL_ERROR;
	$loglevels['entertain']			=	LOGLEVEL_DEBUG;
	$logids['entertain']			= array(644314);

	$loglevel_names = array( 0 => 'error', 1 => 'warning', 2 => 'info', 3 => 'debug');

	/*
	This is the function to use if you want to make a log entry. You supply 
	a	category, the name of the subsystem that wants to make the log entry,
	a level, the level of importance for this log entry, if this is equally or
		more important than current log level for this subsystem an entry will be made,
	a file name, __FILE__ I guess,
	a line number, __LINE__ is it?,
	a description, some information usable for error tracking and
	a serialized object or something that can be printed in the log entry.
	*/
	function log_to_file($category, $level, $file, $line, $description, $serialized)
	{
		global $loglevels;
		global $loglevel_names;
		global $logids;
		if(isset($loglevels[$category]))
		{
			if($level > $loglevels[$category])
			{
				return;
			}
		}
		else
		{
			if($level > $loglevels['default'])
			{
				return;
			}
		}
		
		/*
		$output = date('Y-m-d H:i:s') . "\t" .  $loglevel_names[$level] . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_SESSION['login']['id'] . "\t";
		$output .= $file . "\t" . $line . "\t" . $description .  "\t" . $_SERVER['HTTP_REFERER'] . "\t" . $_SERVER['REQUEST_URI'] . "\t" . $serialized. "\n";
		if(!is_dir(PATHS_LOGS . $category))
		{
			mkdir(PATHS_LOGS . $category);
		}
		if(isset($logids[$category]))
		{
			if(in_array($_SESSION['login']['id'], $logids[$category]))
			{
				$ext = $_SESSION['login']['id'];
			}
		}
		$handle = fopen(PATHS_LOGS . $category . '/' . date('Y-m-d') . (isset($ext) ? '.' . $ext : '') . '.log', 'a');
		fwrite($handle, $output);
		fclose($handle);
		*/
	}

	function log_admin_event($event, $data , $admin_id, $user_id, $item_id )
	{
		$query = 'INSERT INTO admin_event (event, value, timestamp, admin_id, user_id, item_id) ';
		$query .= 'VALUES("' . $event . '", "' . $data . '", UNIX_TIMESTAMP(), "' . $admin_id . '", "' . $user_id . '", "' . $item_id . '")';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
?>