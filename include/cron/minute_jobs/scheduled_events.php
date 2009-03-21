<?php
	// This fucks up minute jobs - DO NOT USE IT! require_once('../core/common.php');
	require_once(PATHS_LIBRARIES . 'schedule.lib.php');
	
	schedule_releases_do();
?>
