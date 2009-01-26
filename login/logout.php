<?php
	require('../include/core/common.php');
	if($_SESSION['login']['id'] > 0)
	{
		$new_info['login']['lastaction'] = 0;

		login_save_user_data($_SESSION['login']['id'], $new_info);
	}

  if (isset($_COOKIE[session_name()]))
  {
    setcookie(session_name(), '', time()-42000, '/');
  }

	session_destroy();

	jscript_go_back();
?>
