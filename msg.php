<?php

	require('include/core/common.php');
	$ui_options['menu_path'] = array('hamsterpaj');

	/* Allmänna felmeddelanden */
		$msg['error']['t'] = 'Ett fel har uppstått';
		$msg['error']['b'] = 'Ett fel har uppstått. Var god försök igen senare.<br><a href="javascript:history.go(-1);">Klicka här</a> för att gå tillbaka till den sida du kom ifrån.';
	
	/* Felmeddelanden för login/registrering */
		$msg['login_useradded']['t'] = 'Registrering genomförd';
		$msg['login_useradded']['b'] = 'Du är nu medlem på hamsterpaj.net!<br /><br />';
		$msg['login_useradded']['b'].= '<a href="/forum_new/">Till forumet »</a><br />';
    $msg['login_useradded']['b'].= '<a href="/traffa/profile.php?id='.$_SESSION['login']['id'].'">Till din sida »</a><br />';
    $msg['login_useradded']['b'].= '<a href="/settings.php">Till dina inställningar »</a><br />';
    $msg['login_useradded']['b'].= '<a href="/traffa/myprofile.php">Ändra din presentation »</a><br />';
    $msg['login_useradded']['b'].= '<a href="/traffa/">Leta efter andra att chatta med »</a><br />';		

		$msg['login_usernameinuse']['t'] = 'Användarnamnet används redan';
		$msg['login_usernameinuse']['b'] = 'Användarnamnet du angav finns redan registrerat i vår databas. <a href="javascript:history.go(-1);">Klicka här</a> för att gå tillbaka och ange ett annat användarnamn.';
				
		$msg['login_invalidusername']['t'] = 'Användarnamnet är ogiltig';
		$msg['login_invalidusername']['b'] = 'Användarnamnet du angav är inte giltigt. Ett användarnamn kan endast bestå av a-z, A-Z, 0-9 samt bindestreck och understreck. Användarnamnet får inte vara längre en 16 tecken. <a href="javascript:history.go(-1);">Klicka här</a> för att gå tillbaka och ange ett annat användarnamn.';
		
		$msg['login_passwordlength']['t'] = 'Lösenordet är för kort';
		$msg['login_passwordlength']['b'] = 'Lösenordet du angav är för kort. Ditt lösenord måste vara minst 4 tecken långt. <a href="javascript:history.go(-1);">Klicka här</a> för att gå tillbaka och ange ett annat lösenord.';
		
		$msg['login_passwordmatch']['t'] = 'Lösenorden stämmer inte';
		$msg['login_passwordmatch']['b'] = 'Du har inte angivit samma lösenord i båda lösenordsrutorna. <a href="javascript:history.go(-1);">Klicka här</a> för att gå tillbaka och ange lösenorden igen.';
		
		$msg['login_invalidlogin']['t'] = 'Ogiltigt användarnamn eller lösenord';
		$msg['login_invalidlogin']['b'] = 'Du måste ange användarnamn och lösenord för att logga in på siten.';
		
		$msg['login_loginfailed']['t'] = 'Inloggning misslyckades';
		$msg['login_loginfailed']['b'] = 'Det gick inte att logga in med de uppgifter du angav. Detta beror antingen på att du inte angivit korrekt användarnamn och lösenord, eller att användarnamnet inte finns.<br /><br />Har du glömt ditt lösenord? Då finns det inte mycket att göra :(';
		
		$msg['login_logoutfailed']['t'] = 'Utloggning misslyckades';
		$msg['login_logoutfailed']['b'] = 'Det gick inte att logga ut.';
		
		$msg['login_changepassok']['t'] = 'Lösenordet ändrat';
		$msg['login_changepassok']['b'] = 'Ditt lösenord har nu ändrats. Använd ditt nya lösenord nästa gång du loggar in på hamsterpaj.net';
		
		$msg['login_changepasswrongpass']['t'] = 'Felaktigt lösenord';
		$msg['login_changepasswrongpass']['b'] = 'Det lösenord du angav stämmer inte överens med ditt gamla lösenord. <a href="javascript:history.go(-1);">Klicka här</a> för att gå tillbaka och försöka igen.';
		
		$msg['login_changepassmismatch']['t'] = 'Lösenorden stämmer inte';
		$msg['login_changepassmismatch']['b'] = 'De lösenord du angivit stämmer inte. Du måste ange samma lösenord i båda fälten för nytt lösenord. <a href="javascript:history.go(-1);">Klicka här</a> för att gå tillbaka och försöka igen.';
		
		$msg['login_changeinfook']['t'] = 'Frivilliga uppgifter ändrade';
		$msg['login_changeinfook']['b'] = 'Dina frivilliga uppgifter har nu ändrats.';
	
		$msg['login_authcode']['t'] = 'Du har angivit en felaktig säkerhetskod!';
		$msg['login_authcode']['b'] = 'Det verkar som om du skrev fel när du skrev av säkerhetskoden. Tillbaks och gör om!';

		$msg['remove_success']['t'] = 'Ditt konto är nu borttaget!';
		$msg['remove_success']['b'] = 'Ditt konto är nu helt borttaget, och du kan inte få tillbaka det.<br/><br/>Tråkigt att behöva se dig lämna Hamsterpaj, men vi hoppas att du kanske hittar tillbaka hit senare!';
		
	
	if(!isset($msg[$_GET['message']])) {
		header('Location: ' . $hp_url . 'index.php');
	}
	
	ui_top($ui_options);

?>
<p class="title"><?php echo $msg[$_GET['message']]['t']; ?></p>
<p>
<?php echo $msg[$_GET['message']]['b']; ?>
</p>
<?php
	
	ui_bottom();
?>
