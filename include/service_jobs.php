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
?>
