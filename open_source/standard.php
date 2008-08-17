<?php
	/* OPEN_SOURCE */
	
	/*
		Detta är inte den standard.php som är i drift på servern
		Det här är en kopia av den filen, som normalt ligger i /storage/www/standard.php
		
		Allting är samma (2008-04-14 23:01), bortsett från databasuppgifterna, som är
		ersatta med XXX	
	*/
	
	ob_start();

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

	$db_server = 'XXX';
	$db_username = 'XXX';
	$db_password = 'XXX';
	$db_database = 'XXX';

  require('/home/www/paths.php');
  require_once($hp_includepath . 'constants.php');
	require_once($hp_includepath . 'db-config.php');
  require($hp_includepath . 'login-functions.php');
  require($hp_includepath . 'ui-functions.php');
	require_once(PATHS_INCLUDE . 'libraries/forum-notices.php');
	require_once(PATHS_INCLUDE . 'libraries/posts.php');

  require($hp_includepath . 'shared-functions.php');
	require_once(PATHS_INCLUDE . 'libraries/forum.php');
	require_once(PATHS_INCLUDE . 'libraries/discussion_forum.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/tags.php');
	require_once(PATHS_INCLUDE . 'libraries/log.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/poll.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/treasure_hunt.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/ui.lib.php');

	require_once(PATHS_INCLUDE . 'libraries/event_log.lib.php');
	require_once($hp_includepath . 'logging-functions.php');
	
	

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
	
  require_once($hp_includepath . 'side_modules.php');

  if(isset($_SESSION['ip']) && $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'])
  {
  	echo '<h1>Nånting blev fel!</h1>';
  	echo 'Du är troligen inloggad på en annan dator och därför kan du inte logga in<br />';
  	echo 'Om det inte fungerar att surfa tillbaka till startsidan <a href="http://www.hamsterpaj.net">www.hamsterpaj.net</a><br />';
  	echo 'så kan du prova att stänga ner din webbläsare eller tömma dina kakor<br />';
  	$_SESSION = array();
  	if (isset($_COOKIE[session_name()])) {
   		setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
  	die();
  }
  
  if(login_checklogin())
  {
  	login_page_impressions();
  }
?>
