<?php
	$common_path = substr(__FILE__, 0, strrpos(__FILE__, '/')+1) . '../core/common.php';
	require($common_path);
	require(PATHS_LIBRARIES . 'cron.lib.php');
	cron_execute(array('type' => 'minute_jobs'));
?>
