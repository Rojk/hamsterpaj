<?php
//	include(PATHS_INCLUDE . 'downtime.php');
//	exit;

	function debug_trace($message)
	{
		echo $message . '<br />';
		ob_flush();
		flush();
	}
	
	function debug_time($position)
	{
		global $debug_time;
		
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];

		if(count($debug_time) > 0)
		{
			$debug_time[] = array($position => ($mtime - $debug_time[0]['Script start']) );
		}
		else
		{	
			$debug_time[] = array($position => $mtime);
		}
	}

	//Backup is running and slowing down everything between 4.30 and ca 5.00
	if(date('H') == '4' && date('i') > 30)
	{
		include(PATHS_INCLUDE . 'running_backup.html');
		exit;
	}
	if(!mysql_pconnect($db_server, $db_username, $db_password))
	{
		if($_SESSION['max_connection_retries'] > 3)
		{
			include(PATHS_INCLUDE . 'max_connections_max_reloads.html');
			$_SESSION['max_connection_retries'] = 0;
		}
		else
		{
			include(PATHS_INCLUDE . 'max_connections.html');
			$_SESSION['max_connection_retries']++;
		}
		exit;
	}
	$_SESSION['max_connection_retries'] = 0;

	mysql_select_db($db_database) or die ('Can\'t use database: ' . mysql_error());
	mysql_query('SET NAMES utf8');
?>
