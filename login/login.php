<?php
	require('../include/core/common.php');
	
	if(isset($_POST['username'], $_POST['password']))
	{
		try
		{
			login_dologin(array(
				'username' => $_POST['username'],
				'password' => $_POST['password'],
				'method' => 'username_and_password'
			));
			
			if(strpos($_SERVER['HTTP_REFERER'], 'login.php') !== false)
			{
				header('Location: /index.php');
			}
			else
			{
			  if(strlen($_SERVER['HTTP_REFERER']) > 5)
			  {
			      header('Location: ' . $_SERVER['HTTP_REFERER']);
			  }
			  else
			  {
			      header('Location: /index.php');
			  }
			}
			
			exit;
		}
		catch(Exception $error)
		{
			$ui_options['title'] = 'Inloggningen misslyckades.';
			ui_top($ui_options);
			echo '<h1>Ett fel inträffade när du skulle logga in!</h1>' . "\n";
			echo $error->getMessage();
			ui_bottom();
		}
	}
?>