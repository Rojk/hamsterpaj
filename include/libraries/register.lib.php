<?php
	function register_username_exists($username)
	{
		$query = 'SELECT id FROM login WHERE username LIKE "' . $username . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query));
		if(mysql_num_rows($result) == 1)
		{
			return true;
		}
		return false;
	}
	
	function register_check($info)
	{
		$return = array();
		if(!preg_match("/^[0-9a-zA-Z_-]+$/i", $info['username']))
		{
			$return['username'] = 'invalid_characters';
		}
		if(strlen($info['username']) > 16)
		{
			$return['username'] = 'too_long';
		}
		if(strlen($info['username']) < 2)
		{
			$return['username'] = 'too_short';
		}
		if(register_username_exists($info['username']))
		{
			$return['username'] = 'already_taken';
		}
		if($info['password'] != $info['password_verify'])
		{
			$return['password'] = 'mismatch';
		}
		if(strlen($info['password']) < 4)
		{
			$return['password'] = 'too_short';
		}
		if($info['rules'] != 'ok')
		{
			$return['rules'] = 'not_ok';
		}
		if(count($return) > 0)
		{
			return $return;
		}
		return true;
	}
	
	function regform_header_p13()
	{
		echo rounded_corners_top(array('color' => 'orange_deluxe'));
		$info2 .= '<img style="float: left; padding: 5px 5px 5px 0;" src="http://images.hamsterpaj.net/13skylt.png" />' . "\n";
		$info2 .= '<h1 style="margin: 0 0 3px 0; font-size: 16px;">Hamsterpaj är ingen barnsida, är du under 13 så använd www.lunarstorm.se</h1>' . "\n";
		$info2 .= '<p style="margin: 0 0 0 0;">Vi som gör Hamsterpaj tycker att medlemmar under 13 år ställer till en massa problem. Om du inte har fyllt 13 borde du läsa vår <a href="http://www.hamsterpaj.net/artiklar/?action=show&id=24">ålderspolicy</a> och fundera på om Hamsterpaj är rätt ställe för dig. Annars rekommenderar vi Lunarstorm, där kan man få häftiga statuspoäng!</p>' . "\n";
		$info2 .= '<div style="clear:both;"></div>' . "\n";
		echo $info2;
		echo rounded_corners_bottom(array('color' => 'orange_deluxe'));
	}
	
	function regform_header_welcome()
	{
		echo rounded_corners_tabs_top();
		echo '<h1>Vad roligt att du vill bli medlem på Hamsterpaj!</h1>' . "\n";
		echo '<p>' . "\n";
		echo 'Att bli medlem är gratis, det finns förresten ingenting som kostar på Hamsterpaj. Vi frågar inte efter' . "\n";
		echo 'ditt personnummer eller din adress och vi lovar att aldrig skicka någon sms- eller e-postreklam till' . "\n";
		echo 'dig. Vi tycker nämnligen om integritet och avskyr spam!</p>' . "\n";
		echo rounded_corners_tabs_bottom();
	}
	
	function regform_header_fail()
	{
		echo rounded_corners_tabs_top();
		echo '<h1>Ooops, någonting blev fel när vi skulle skapa ditt konto!</h1>' . "\n";
		echo '<p>' . "\n";
		echo 'Vi kunde tyvärr inte skapa ett användarkonto åt dig. Konrollera de rödmarkerade fälten och se efter' . "\n";
		echo 'om du har fyllt i fel information någonstans.' . "\n";
		echo '</p>' . "\n";
		echo rounded_corners_tabs_bottom();
	}


	function register_form($values, $errors)
	{
		echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n";
		echo rounded_corners_tabs_top();
		echo '<fieldset>' . "\n";
		
		$class = '';
		$legend = 'Användarnamn';
		if(isset($errors['username']))
		{
			$class = ' class="error"';
			switch($errors['username'])
			{
				case 'too_long':
					$legend = 'Ditt användarnamn får inte vara längre än 16 tecken';
					break;
				case 'too_short':
					$legend = 'Ditt användarnamn får inte vara kortare än två tecken';
					break;
				case 'already_taken':
					$legend = 'Du har valt ett användarnamn som någon redan har';
					break;
				case 'invalid_characters':
					$legend = 'Ditt användarnamn innehåller ogiltliga tecken';
					break;
				default:
					$legend = 'Ogiltligt användarnamn, läs instruktionerna till höger';
					break;
			}
		}
		
		echo '<legend' . $class . '>' . $legend . '</legend>' . "\n";
		echo '<div class="regform_inputs" style="height: 115px;">' . "\n";
		echo '<h4>Välj ett användarnamn</h4>' . "\n";
		echo '<input type="text" name="username" id="regfrm_input_username" value="' . $values['username'] . '"/><br /><br />' . "\n";
		echo '<input type="button" class="button_130" value="Är namnet ledigt?" onclick="loadFragmentInToElement(\'/username_check.php?username=\' + document.getElementById(\'regfrm_input_username\').value, \'regfrm_username_status\');" />' . "\n";
		echo '<div id="regfrm_username_status">' . "\n";
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		echo '<div class="regform_explanation">' . "\n";
		echo '<p>' . "\n";
		echo 'Ditt användarnamn kommer du använda för att logga in på Hamsterpaj, om du vill kan du byta användarnamn' . "\n";
		echo 'efter att du har blivit medlem. Givetvis kan du inte välja ett användarnamn som någon annan redan har' . "\n";
		echo 'valt.' . "\n";
		echo '</p>' . "\n";
		echo '<h4>Ett giltligt användarnamn</h4>' . "\n";
		echo '<ul>' . "\n";
		echo '<li>Består av 2-16 tecken</li>' . "\n";
		echo '<li>Innehåller inga andra tecken än siffror, a-Z, bindestreck och understreck</li>' . "\n";
		echo '<li>Innehåller inga fula ord</li>' . "\n";
		echo '</ul>' . "\n";
		echo '</div>' . "\n";
		echo '</fieldset>' . "\n";
		echo rounded_corners_tabs_bottom();
		
		echo rounded_corners_tabs_top();
		/* Password */
		$class = '';
		$legend = 'Lösenord';
		if(isset($errors['password']))
		{
			$class = ' class="error"';
			switch($errors['password'])
			{
				case 'mismatch':
					$legend = 'De två lösenorden stämmer inte överrens';
					break;
				case 'too_short':
					$legend = 'Ditt lösenord måste vara minst fyra tecken långt';
					break;
			}
		}
		echo '<fieldset>' . "\n";
		echo '<legend' . $class . '>' . $legend .'</legend>' . "\n";
		echo '<div class="regform_inputs">' . "\n";
		echo '<h4>Önskat lösenord</h4>' . "\n";
		echo '<input type="password" name="password" id="regfrm_input_password" value="' . $values['password'] . '" />' . "\n";
		echo '<h4>Skriv lösenordet igen</h4>' . "\n";
		echo '<input type="password" name="password_verify" id="regfrm_input_password_verify" value="' . $values['password_verify"']. '" />' . "\n";
		echo '</div>' . "\n";
				
		echo '<div class="regform_explanation">' . "\n";
		echo '<p>' . "\n";
		echo 'Eftersom det finns en liten risk att du slinter på tangenterna måste du skriva ditt lösenord' . "\n";
		echo 'två gånger, du kan ju inte se vad du skriver för lösenord och det kan ingen annan heller.' . "\n";
		echo '</p>' . "\n";
		echo '</div>' . "\n";
		echo '</fieldset>' . "\n";
		echo rounded_corners_tabs_bottom();
		
		echo rounded_corners_tabs_top();
		echo '<h2>Sunt förnuft, respekt och en trevlig ton på Hamsterpaj</h2>' . "\n";
		echo '<p>' . "\n";
		echo 'Vi tror inte på regler och paragrafer, vi tror att du själv förstår hur man beter sig på ett schysst sätt' . "\n";
		echo 'och att du kan ta ansvar för det du skriver och gör. Men vi vill ändå påpeka några saker:' . "\n";
		echo '</p>' . "\n";
		
		echo '<ul>' . "\n";
		echo '<li>' . "\n";
		echo '<h3>Sex är jättehärligt</h3>' . "\n";
		echo '<p>' . "\n";
		echo 'Ibland vill man prata om det, och det är helt okej, vi har faktiskt ett eget forum för sex. Men, det är' . "\n";
		echo 'inte okej att be andra om sex, lika lite okej som att	ladda upp porrbilder eller att be andra visa sig ' . "\n";
		echo 'lättklädda i webcam.' . "\n";
		echo '</p>' . "\n";
		echo '</li>' . "\n";
			
		echo '<li>' . "\n";
		echo '<h3>Visst finns det folk med fel åsikter</h3>' . "\n";
		echo '<p>' . "\n";
		echo 'När någon har fel ska man ju givetvis förklara hur det ligger till, som att människovärdet inte sitter' . "\n";
		echo 'i hudfärgen eller att tjejer visst är lika duktiga som killar. Men för den sakens skull får man inte vara' . "\n";
		echo 'otrevlig eller häva ur sig förolämpningar. Dessutom hör det till att man förklarar varför man tycker som man' . "\n";
		echo 'tycker och inte använder pajkastningsretorik.' . "\n";
		echo '</p>' . "\n";
		echo '</li>' . "\n";
			
		echo '<li>' . "\n";
		echo '<h3>Vi tycker inte om tävlingslänkar och betalnummer</h3>' . "\n";
		echo '<p>' . "\n";
		echo 'Här på Hamsterpaj får du inte göra reklam för dina egna SMS-tjänster eller skicka runt länkar för att' . "\n";
		echo 'tjäna poäng i spel eller tävlingar.' . "\n";
		echo '</p>' . "\n";
		echo '</li>' . "\n";
		echo '</ul>' . "\n";
		
		$checked = ($values['rules'] == 'ok') ? ' checked="true"' : '';
		echo (isset($errors['rules'])) ? '<h4 class="error">Du måste faktiskt kryssa i att självklarheterna är självklara</h4>' : '';
		echo '<input type="checkbox" name="rules" value="ok" id="regfrm_rules_ok"' .$checked .' />' . "\n";
		echo '<label for="regfrm_rules_ok">' . "\n";
		echo 'Det här är självklarheter för mig, jag lovar att jag kommer uppföra mig schysst här på Hamsterpaj.net!' . "\n";
		echo '</label>' . "\n";
		echo rounded_corners_tabs_bottom();
	
		echo rounded_corners_tabs_top();
		echo '<input type="submit" value="Bli medlem" class="button_90" />' . "\n";
		echo '</form>' . "\n";
		echo rounded_corners_tabs_bottom();
	}
?>