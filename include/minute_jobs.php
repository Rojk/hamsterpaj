<?php
if(file_exists('/tmp/minute_jobs.lockfile'))
{
  if(file_exists('/tmp/minute_jobs.lockfile'))
	{
  	if(time() - filemtime('/tmp/minute_jobs.lockfile') > 60) 
		{
			unlink('/tmp/minute_jobs.lockfile');
		}
  }
	exit;
}
$lockfile = fopen('/tmp/minute_jobs.lockfile', 'w');
fwrite($locfile, 'locked');
fclose($lockfile);
set_time_limit(0); //Set the time limit to infinity

include('/storage/www/standard.php');


$dir_handle = opendir(PATHS_INCLUDE . 'minute_jobs/');
while($filename = readdir($dir_handle))
{
	if($filename != '.' && $filename != '..')
	{
		include(PATHS_INCLUDE . 'minute_jobs/' . $filename);
	}	
}

unlink('/tmp/minute_jobs.lockfile');
?>
