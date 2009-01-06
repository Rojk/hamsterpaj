<?php
	require('include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/register.lib.php');
	
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['title'] = 'Bli medlem på Hamsterpaj.net';
	
	$ui_options['stylesheets'][] = 'register.css';

	ui_top($ui_options);
	
	$register_suspend = cache_load('register_suspend');
	if($register_suspend == 'disabled')
	{
		echo '<h1>Registreringsfunktionen är tillfälligt avstängd</h1>' . "\n";
		echo '<p>Det här händer jäkligt sällan, men nu har vi stängt av registreringen - det går inte att skapa konton på Hamsterpaj just nu. Funktionen är antagligen igång inom en timma, titta hit igen då!</p>' . "\n";
		ui_bottom();
		exit;
	}

	if(login_checklogin())
	{
		echo '<h1>Du kan inte skapa en ny användare när du redan är inloggad!</h1>';
		ui_bottom();
		exit;
	}

	if(isset($_POST['username']))
	{
		$data_ok = register_check($_POST);
		if($data_ok !== true)
		{
			regform_header_fail();
			register_form($_POST, $data_ok);
		}
		else
		{
			event_log_log('classic_reg_form_sign_up');
			/* Input from user is OK, create rows in required tables */
			$query = 'INSERT INTO login(username, password, regtimestamp, regip, lastlogon) ';
			$query .= 'VALUES ("' . $_POST['username'] . '", "' . hamsterpaj_password(utf8_decode($_POST['password'])) . '", "';
			$query .= time() . '", "' . $_SERVER['REMOTE_ADDR'] . '", "")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$user_id = mysql_insert_id();
	
			$query = 'INSERT INTO userinfo (userid) VALUES ("' . $user_id . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

			$query = 'INSERT INTO traffa (userid) VALUES ("' . $user_id . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

			$query = 'INSERT INTO preferences (userid) VALUES ("' . $user_id . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			
			/* Rows created, log on the user */
			try
			{
				login_dologin(array(
					'username' => $_POST['username'],
					'password' => $_POST['password'],
					'method' => 'username_and_password'
				));
					
				/* Redirect to welcome page asking the user for more information */
				jscript_alert('Du kan numera känna dig som en riktig Hamsterpajare!\nVi loggar in dig på ditt konto nu.');
				jscript_location('/registered.php');
			}
			catch(Exception $error)
			{
				jscript_alert('Något gick ganska snett under registreringen. Felet har loggats.');
				echo $error->getMessage();
				trace('registration_login_failed', $error);
			}
		}
	}
	else
	{
		regform_header_p13();
		regform_header_welcome();
		register_form();
		event_log_log('classic_reg_form_load');
	}
	
	ui_bottom();
?>


