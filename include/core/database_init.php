<?php
//	include(PATHS_INCLUDE . 'downtime.php');
//	exit;

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
