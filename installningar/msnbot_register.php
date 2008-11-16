<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/msnbot.lib.php');
	
	$ui_options['title'] = 'Msnbot';
	$ui_options['menu_path'] = array('installningar', 'msnbot');
	
	ui_top($ui_options);
	
	echo '<h1>Vännernotiser på MSN</h1>';
	
	if(login_checklogin())
	{
		if(isset($_GET['msn'], $_GET['salt']) && msnbot_is_valid_msn($_GET['msn']) && msnbot_is_valid_salt(array('salt' => $_GET['salt'], 'msn' => $_GET['msn'])))
		{
			$query = 'UPDATE userinfo SET msnbot_msn = "'. $_GET['msn'] . '" WHERE userid = ' . $_SESSION['login']['id'];
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			$_SESSION['userinfo']['msnbot_msn'] = $_GET['msn'];
			
			echo 'Du är nu registrerad!';
			/*
			$_SESSION['msnbot']['register_msn'] = $_GET['msn'];
			echo 'Välj vad du vill få ett meddelande på MSN om via msnboten:<br />' . "\n";
			echo '<form action="post">' . "\n";
			echo '<ul>' . "\n";
			echo '<li>Bla bla</li>' . "\n";
			echo '</ul>' . "\n";
			echo '<input type="button" value="Spara" class="button_60" />' . "\n";
			echo '</form>' . "\n";
			*/
		}
		else if(isset($_GET['unregister']) && $_GET['unregister'] == $_SESSION['login']['id'])
		{
			$query = 'UPDATE userinfo SET msnbot_msn = "" WHERE userid = ' . $_SESSION['login']['id'];
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			$_SESSION['userinfo']['msnbot_msn'] = '';
			echo 'Du är nu avregistrerad.';
		}
		else
		{
			if($_SESSION['userinfo']['msnbot_msn'] == '')
			{
				echo 'Adda hamsterpaj.net@live.se på MSN och skriv något till honom så svarar han med en länk. Klicka på den (eller kopiera den och öppna i webbläsaren om det inte funkar).';
			}
			else
			{
				echo '<a href="/installningar/msnbot_register.php?unregister=' . $_SESSION['login']['id'] . '">Avregistrera mig ifrån vännernotiser på MSN.</a>';
			}
		}
	}
	else
	{
		echo 'Du måste vara medlem och inloggad på Hamsterpaj för att kunna använda msnboten. Att bli medlem är gratis och tar bara någon minut. Klicka på bli medlem här ovanför eller logga in om du redan har ett konto för att gå vidare. :)';
	}
	ui_bottom();
?>