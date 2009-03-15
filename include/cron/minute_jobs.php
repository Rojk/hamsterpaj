<?php
	require('../core/common.php');
	require(PATHS_LIBRARIES . 'cron.lib.php');
	
	cron_execute(array('type' => 'minute_jobs'));
?>