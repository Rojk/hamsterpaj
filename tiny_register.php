<?php
	require('include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/register.lib.php');
	
	if($_GET['action'] == 'tiny_reg_form_show')
	{
		event_log_log('tiny_reg_form_show');
		exit;
	}
	
	echo $_GET['username'];
	
	echo '<hr />';
	
	$error = false;
	$info = $_GET;
	if(!preg_match("/^[0-9a-zA-Z_-]+$/i", $info['username']))
	{
		$error = true;
		echo '<span style="color: red; font-weight: bold;">Ditt användarnamn innehåller ogiltliga tecken, bara 0-9, A-z, _ och -</span><br />';
	}
	if(strlen($info['username']) > 16)
	{
		$error = true;
		echo '<span style="color: red; font-weight: bold;">Användarnamnet får inte vara längre än 16 tecken!</span><br />';
	}
	if(strlen($info['username']) < 2)
	{
		$error = true;
		echo '<span style="color: red; font-weight: bold;">Användarnamnet får inte vara kortare än två tecken!</span><br />';
	}
	if(register_username_exists($info['username']))
	{
		$error = true;
		echo '<span style="color: red; font-weight: bold;">Användarnamnet är redan upptaget!</span><br />';
	}
	if(strlen($info['password']) < 4)
	{
		$error = true;
		echo '<span style="color: red; font-weight: bold;">Ditt lösenord måste vara minst fyra tecken långt!</span><br />';
	}
	
	if(!$error)
	{
		/* Input from user is OK, create rows in required tables */
		$query = 'INSERT INTO login(username, password, regtimestamp, regip, lastlogon) ';
		$query .= 'VALUES ("' . $_GET['username'] . '", "' . md5(utf8_decode($_GET['password'])) . '", "';
		$query .= time() . '", "' . $_SERVER['REMOTE_ADDR'] . '", "")';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$user_id = mysql_insert_id();
	
		$query = 'INSERT INTO userinfo (userid) VALUES ("' . $user_id . '")';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

		$query = 'INSERT INTO traffa (userid) VALUES ("' . $user_id . '")';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

		$query = 'INSERT INTO preferences (userid) VALUES ("' . $user_id . '")';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		echo '<h2>Ditt konto är skapat!</h2>';
		echo '<p>Kryssa ner den här rutan uppe i högra hörnet och logga sedan in längst upp på sajten!</p>';
		event_log_log('tiny_reg_form_complete');
	}
?>
