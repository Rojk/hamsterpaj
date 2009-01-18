<?php
	require('include/core/common.php');
	//session_start();
	
  require_once($hp_includepath . 'avataradmin-functions.php');
  require_once($hp_includepath . 'admin-functions.php');
	
	$_GET['id'] = intval($_GET['id']);
	
	if (!is_numeric($_GET['id']) && isset($_GET['id']))
	{
		die('FISK ' . $_GET['id']);
	}
	
	if (isset($_GET['refuse']) && is_numeric($_GET['refuse']) && login_checklogin() && is_privilegied('avatar_admin'))
	{
		refuse_image($_GET['refuse'], $_SESSION['login']['username']);
		jscript_alert('Borttaget!');
		jscript_selfclose();
		die();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Visningsbild på Hamsterpaj</title>
<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />
<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />

<style type="text/css">
	@import url('/stylesheets/avatar.css');
	@import url('/stylesheets/shared.css');
</style>

<script type="text/javascript" language="javascript" src="/javascripts/avatar.js"></script>
</head>
<body>
<?php
	$query = 'SELECT user_status FROM userinfo WHERE userid = "' . $_GET['id'] . '" LIMIT 1';
	$result = mysql_query($query);
	$data = mysql_fetch_assoc($result);
	
	echo '<div id="user_status"><p>' . $data['user_status'] . '</p></div>' . "\n";
	$img_path = IMAGE_PATH . 'images/users/full/' . $_GET['id'] . '.jpg';
	echo '<div id="passepartout"><img src="' . IMAGE_URL . 'images/users/full/' . $_GET['id'] . '.jpg?cache_prevention=' . filemtime($img_path) . '" id="user_avatar" /></div>';

	echo '<div id="controls">' . "\n";
	echo '<input type="hidden" id="user_id" value="' . $_GET['id'] . '" />' . "\n";
	echo '<button class="button_100" id="presentation">Presentation</button>' . "\n";
	echo '<button class="button_80" id="guestbook">Gästbok</button>' . "\n";
	if (is_privilegied('avatar_admin'))
	{
		echo '<button class="button_100" id="remove_avatar">Ta bort bild</button>' . "\n";
	}
	echo '</div>' . "\n";
	echo $string_to_remove;
?>
</body>
</html>
