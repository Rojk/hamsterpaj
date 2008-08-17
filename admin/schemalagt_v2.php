<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/schedule_v2.lib.php');
	
	if(is_privilegied('schedule_admin'))
	{
		schedule_admin_parse_request();
	}
	else
	{
		jscript_location('/index.php');
	}
?>