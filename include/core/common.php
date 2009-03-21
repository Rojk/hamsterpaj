<?php
	header('Content-Type: text/html; charset=UTF-8');
	error_reporting(E_ERROR);
	//error_reporting(E_ALL);
	ob_start();
	
	// Sanitize POST and GET data
	$new_post = array();
	$new_get = array();
	
	foreach($_POST AS $key => $value)
	{
		if(!is_array($value))
		{
			$new_post[htmlspecialchars($key)] = htmlspecialchars($value);
		}
	}
	
	foreach($_GET AS $key => $value)
	{
		if(!is_array($value))
		{
			$new_get[htmlspecialchars($key)] = htmlspecialchars($value);
		}
	}
	
	$_POST = $new_post;
	$_GET = $new_get;
	unset($new_post, $new_get);

	define('CORE_PATH', substr(__FILE__, 0, strrpos(__FILE__, '/')+1));

  require_once(CORE_PATH . 'constants.php');
  
  // These libraries are loaded by default, from /include/libraries/xxx.lib.php.
  foreach(array(
  	'debug',
  	'database',
  	'log',
  	'login',
  	'event_log',
  	'cache',
  	'jscript',
  	'file_handling',
  	'parsers',
  	'discussion_forum',
  	'tags',
  	'poll',
  	'ui',
  	'guestbook',
  	'friends',
  	'msnbot'
  ) as $library)
  {
	  require_once(PATHS_LIBRARIES . $library . '.lib.php');
	}
	
	// First here we connect to the database/break for database backup...
	require_once(CORE_PATH . 'database_init.php');

	/* Include all config files */
	$dir = opendir(PATHS_CONFIGS);
	while($file = readdir($dir))
	{
		if($file != '.' && $file != '..' && substr($file, 0, 2) != '._' && $file != 'menu.conf.php')
		{
			require_once(PATHS_CONFIGS . $file);
		}
	}
	require_once(PATHS_CONFIGS . 'menu.conf.php');
	  
 	require(CORE_PATH . 'ip_handling.php');
  
  if(login_checklogin())
  {
  	login_page_impressions();
  }
?>
