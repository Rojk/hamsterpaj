<?php
	function cron_execute($options)
	{
		if(cron_lockfile_check($options['type']))
		{
			cron_lockfile_lock($options['type']);
			$normal_max_exec_time = intval(ini_get('max_execution_time'));
			set_time_limit(0);
			
			$path = PATHS_CRON . $options['type'] . '/';
			$directory = opendir($path);
			while($filename = readdir($directory))
			{
				if(!in_array($filename, array('.', '..')))
				{
					include($path . $filename);
				}
			}
			
			cron_lockfile_unlock($options['type']);
			set_time_limit($normal_max_exec_time);
		}
	}
	
	function cron_lockfile_check($options)
	{
		$lockfile = '/tmp/hamsterpaj_cron/lockfiles/' . $options['type'] . '.lockfile';
		
		if(file_exists($lockfile))
		{
			if(time() - filemtime($lockfile) > 60)
			{
				cron_lockfile_unlock($options['type']);
			}
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function cron_lockfile_lock($options)
	{
		$lockfile = '/tmp/hamsterpaj_cron/lockfiles/' . $options['type'] . '.lockfile';
		
		$handle = fopen($lockfile, 'w');
		fwrite($handle, 'locked');
		fclose($handle);
	}
	
	function cron_lockfile_unlock($options)
	{
		$lockfile = '/tmp/hamsterpaj_cron/lockfiles/' . $options['type'] . '.lockfile';
		unlink($lockfile);
	}
?>