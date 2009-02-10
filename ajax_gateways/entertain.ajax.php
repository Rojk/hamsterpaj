<?php
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'movie_compability.lib.php');
	require_once(PATHS_LIBRARIES . 'entertain.lib.php');
	require_once(PATHS_LIBRARIES . 'schedule.lib.php');
	require_once(PATHS_LIBRARIES . 'rank.lib.php');
	require_once(PATHS_LIBRARIES . 'comments.lib.php');


log_to_file('entertain', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'ajax call registered');
if(count($_GET) > 0)
{
	switch($_GET['action'])
	{
		case 'cancel_upload':
			log_to_file('entertain', LOGLEVEL_INFO, __FILE__, __LINE__, 'upload canceled');
			unset($_SESSION['new_entertain_temp']);
		break;
	}
}

?>