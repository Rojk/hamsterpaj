<?php
	header('Content-Type: text/html; charset=UTF-8');
	error_reporting(E_ERROR);
	//error_reporting(E_ALL);
	ob_start();
	
	// Sanitize POST and GET data
	foreach($_POST AS $key => $value)
	{
		if(!in_array($key, $skip_post))
		{
			$_POST[$key] = htmlspecialchars($value);
		}
	}
	
	foreach($_GET AS $key => $value)
	{
		if(!in_array($key, $skip_get))
		{
			$_GET[$key] = htmlspecialchars($value);
		}
	}

	define('CORE_PATH', substr(__FILE__, 0, strrpos(__FILE__, '/')+1));

  require_once(CORE_PATH . 'constants.php');
	require_once(CORE_PATH . 'database_init.php');
	require_once($hp_includepath . 'logging-functions.php');
  require(PATHS_INCLUDE . 'libraries/login.lib.php');

	// Needs to be loaded before ui-functions.php (tele2 fullscreen ad)
	require_once(PATHS_INCLUDE . 'libraries/event_log.lib.php');

  require($hp_includepath . 'ui-functions.php');
  require(PATHS_INCLUDE . 'libraries/cache.lib.php');
  require(PATHS_INCLUDE . 'libraries/jscript.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/forum-notices.php');
	require_once(PATHS_INCLUDE . 'libraries/posts.php');

  require($hp_includepath . 'shared-functions.php');
  
	require_once(PATHS_INCLUDE . 'libraries/forum.php');
	require_once(PATHS_INCLUDE . 'libraries/discussion_forum.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/tags.php');
	require_once(PATHS_INCLUDE . 'libraries/log.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/poll.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/ui.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/guestbook.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/friends.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/msnbot.lib.php');
	

	/* Include all config files */
	$dir = opendir(PATHS_INCLUDE . 'configs/');
	while($file = readdir($dir))
	{
		if($file != '.' && $file != '..' && substr($file, 0, 2) != '._' && $file != 'menu.conf.php')
		{
			include(PATHS_INCLUDE . 'configs/' . $file);
		}
	}
	include(PATHS_INCLUDE . 'configs/menu.conf.php');
	  
 	require(PATHS_INCLUDE . 'core/ip_handling.php');
  
  if(login_checklogin())
  {
  	login_page_impressions();
  }
?>
