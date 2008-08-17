#!/usr/bin/php -q
<?php
	require('/storage/www/standard.php');

	$dir_handle = opendir(PATHS_INCLUDE . 'daily_jobs/');
	while($filename = readdir($dir_handle))
	{
		if($filename != '.' && $filename != '..')
		{
			include(PATHS_INCLUDE . 'daily_jobs/' . $filename);
		}	
	}


	/* ehm, tar väl bort "mina besök" eller nåt. */
	mysql_query('DELETE FROM traffa_visits WHERE tstamp < UNIX_TIMESTAMP() - 1209600');

	
	/* Rensar forum_read_only */
	mysql_query('DELETE FROM forum_read_only WHERE expire < UNIX_TIMESTAMP()');
	
	/* Statistik
	include($hp_includepath . 'generate_stats.php');	 */


	
	/*$year = date('Y');
	$year = $year - 16;
	$query = 'SELECT id FROM login, userinfo WHERE birthday = "' . $year . date('-m-d') . '"';
	$result = mysql_query($query);
	
	while ($data = mysql_fetch_assoc($result))
	{
		$userids[] = $data['id'];
	}
	messages_send(2348, $userids, $title, $message, $allowhtml = 0, $mass_message_id = 0) //change mass_message_id
	*/
	/*
	$query = 'UPDATE amuse_items SET activated = "yes", timestamp = UNIX_TIMESTAMP() WHERE activated = "no" AND status = "1" ORDER BY id ASC LIMIT 3';
	mysql_query($query);
	amuse_regenerate_fp_module();
	*/
	
?>
