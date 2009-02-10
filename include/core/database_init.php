<?php
	if(ENVIRONMENT == 'downtime')
	{
		require(PATHS_WEBROOT . 'downtime.php');
		exit;
	}
	
	//Backup is running and slowing down everything between 4.30 and ca 5.00
	if(date('H') == '4' && date('i') > 30)
	{
		echo database_error_create(array('type' => 'running_backup'));
		exit;
	}
	
	if(!mysql_pconnect($db_server, $db_username, $db_password))
	{
		$_SESSION['database_connection_retries'] = isset($_SESSION['database_connection_retries']) ? $_SESSION['database_connection_retries'] : 0;
			
		echo database_error_create(array('type' => 'connection_error', 'try_again' => ($_SESSION['database_connection_retries'] < 4)));
		
		$_SESSION['database_connection_retries']++;
		exit;
	}
	
	unset($db_server, $db_username, $db_password);
	unset($_SESSION['database_connection_retries']);

	mysql_select_db($db_database) or die ('Can\'t use database: ' . mysql_error());
	mysql_query('SET NAMES utf8');
?>
