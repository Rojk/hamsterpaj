<?php

require('../include/core/common.php');
$ui_options['menu_path'] = array('installningar', 'byt_namn');
$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';

  if(login_checklogin() != 1)
  {
    header('location: /');
    die();
  }

// We need to use the new method... bla bla bla... I'm tired. Clock is 04:16 after a hard day. Blöh!
die('Denna funktion är temporärt avstängd. Var god gnäll på LordDanne - det är antagligen hans fel.');
ui_top($ui_options);

if(isset($_POST['new_username']))
{
	if(sha1(utf8_decode($_POST['password_old']) . PASSWORD_SALT) != $_SESSION['login']['password_hash'])
	{
		echo '<h1>' . $_POST['password_old'] . '</h1>' . "\n";
		echo sha1(utf8_encode($_POST['password_old']) . PASSWORD_SALT) . '<br />' . sha1($_POST['password_old'] . PASSWORD_SALT) . '<br />' . $_SESSION['login']['password_hash'];
		exit;
		jscript_alert('Du har angivit fel lösenord! Var vänlig försök igen.');
		jscript_go_back();
		die();
	}
	elseif(strlen($_POST['new_username']) > 16 || !preg_match("/^[0-9a-zA-Z_-]+$/i", $_POST['new_username']) || strlen($_POST['new_username']) < 3)
	{
		jscript_alert('Användernamnet får bara bestå av A-z, 0-9, _ och -');
		jscript_go_back();
		die();
	}
	elseif(mysql_num_rows(mysql_query('SELECT id FROM login WHERE username LIKE "' . str_replace('_', '\\_', $_POST['new_username']) . '" LIMIT 1')) != 0)
	{
		$query = 'SELECT id FROM login WHERE username LIKE "' . str_replace('_', '\\_', $_POST['new_username']) . '" LIMIT 1';
		$result = mysql_query($query);
		$data = mysql_fetch_assoc($result);

		if($_SESSION['login']['id'] == $data['id'])
		{
			$query = 'UPDATE login SET username ="' . $_POST['new_username'] . '", lastusernamechange = UNIX_TIMESTAMP(), ';
			$query .= 'lastusername = "' . $_SESSION['login']['username'] . '", lastaction = 0 ';
			$query .= 'WHERE id = ' . $_SESSION['login']['id'] . ' LIMIT 1';
			mysql_query($query) or die(report_sql_error($query));;
			$new_sign='Jag hette tidigare '.$_SESSION['login']['username'];
			$query = 'UPDATE userinfo SET forum_signature ="'.$new_sign.'" ';
			$query .= 'WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
			mysql_query($query);
			log_admin_event('username changed', $_SESSION['login']['username'], $_SESSION['login']['id'], $_SESSION['login']['id'], $_SESSION['login']['id']);
			jscript_alert('Sådärja, du heter numera ' . $_POST['new_username'] . ' på hamsterpaj. Du loggas nu ut.');
			jscript_location('/index.php');
			$_SESSION = null;
			session_destroy();
			die();
		}
		else
		{
			jscript_alert('Användarnamnet är ju redan upptaget smartskaft! Kom på nåt nytt!');
			jscript_go_back();
			die();
		}
	}
	elseif($_SESSION['login']['lastusernamechange'] > time() - 604800)
	{
		jscript_alert('Så ofta kan du inte byta användarnamn, du får inte byta oftare än en gång i veckan!');
		jscript_go_back();
		die();
	}
	else
	{
		$query = 'UPDATE login SET username ="' . $_POST['new_username'] . '", lastusernamechange = UNIX_TIMESTAMP(), ';
		$query .= 'lastusername = "' . $_SESSION['login']['username'] . '", lastaction = 0 ';
		$query .= 'WHERE id = ' . $_SESSION['login']['id'] . ' LIMIT 1';
		mysql_query($query) or die(report_sql_error($query));;
		$new_sign='Jag hette tidigare '.$_SESSION['login']['username'];
		$query = 'UPDATE userinfo SET forum_signature ="'.$new_sign.'" ';
		$query .= 'WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query);
		log_admin_event('username changed', $_SESSION['login']['username'], $_SESSION['login']['id'], $_SESSION['login']['id'], $_SESSION['login']['id']);
		jscript_alert('Sådärja, du heter numera ' . $_POST['new_username'] . ' på hamsterpaj. Du loggas nu ut.');
		jscript_location('/index.php');
		$_SESSION = null;
		session_destroy();
		die();
	}
}
	echo rounded_corners_tabs_top();
	echo '<h1 style="margin-top: 0px;">Byt namn</h1>';
	echo '<p>Nu finns möjligheten att byta användarnamn på hamsterpaj. Du kan bara byta namn en gång i veckan och din signatur låses till ett meddelande om att du bytt namn i en vecka efter bytet.</p>';

	echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	echo '<strong>Nytt användarnamn:</strong><br />';
	echo '<input type="text" name="new_username" class="textbox" maxlength="16" /><br/>';
	echo '<strong>Ditt lösenord:</strong><br/>';
	echo '<input type="password" name="password_old" class="textbox"/><br/><br />';
	echo '<input type="submit" class="button_130" value="Byt användarnamn!" onclick="return confirm(\'Ditt användarnamn är på väg att bytas, vill du fortsätta? Har du stavat rätt?\');" />';
	echo '</form>';
	echo rounded_corners_tabs_bottom();


	ui_bottom();
?>
