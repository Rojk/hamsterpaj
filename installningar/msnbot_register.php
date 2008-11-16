<?php
	require('../include/core/common.php');
	
	$ui_options['title'] = 'Msnbot';
	$ui_options['menu_path'] = array('installningar', 'msnbot');
	
	ui_top($ui_options);
	if(login_checklogin())
	{
		if(isset($_GET['msn'], $_GET['salt']) && msnbot_is_valid_msn($_GET['msn']) && msnbot_is_valid_salt($_GET['salt']))
		{
			$_SESSION['msnbot']['register_msn'] = $_GET['msn'];
			echo 'Välj vad du vill få ett meddelande på MSN om via msnboten:<br />' . "\n";
			echo '<form action="post">' . "\n";
			echo '<ul>' . "\n";
			echo '<li>Bla bla</li>' . "\n";
			echo '</ul>' . "\n";
			echo '<input type="button" value="Spara" class="button_60" />' . "\n";
			echo '</form>' . "\n";
		}
		else
		{
			echo 'Adda hamsterpaj.net@live.se på MSN och skriv något till honom så svarar han med en länk. Klicka på den (eller kopiera den och öppna i webbläsaren om det inte funkar).';
		}
	}
	else
	{
		echo 'Du måste vara medlem och inloggad på Hamsterpaj för att kunna använda msnboten. Att bli medlem är gratis och tar bara någon minut. Klicka på bli medlem här ovanför eller logga in om du redan har ett konto för att gå vidare. :)';
	}
	ui_bottom();
?>