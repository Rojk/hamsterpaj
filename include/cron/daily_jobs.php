<?php
	require('../core/common.php');
	require(PATHS_LIBRARIES . 'cron.lib.php');
	
	cron_execute(array('type' => 'hourly_jobs'));
?>