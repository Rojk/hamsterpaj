<?php
	require('./include/core/common.php');
	
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['title'] = 'Nybliven medlem på Hamsterpaj!';
		$ui_options['stylesheets'][] = 'register.css';

	ui_top($ui_options);

	function regform_header()
	{
		echo '<h1>Nu är du alltså medlem på ännu en sån där internetsajt. Välkommen hit!</h1>' . "\n";
		echo '<p>' . "\n";
		echo 'Nu har du ett fungerande konto på ungdomssajten som startades av tre tonårskillar i ett' . "\n";
		echo 'klassrum 2003. Mycket har hänt sedan dess och nu vill vi tillsammans med dig skapa Sveriges ' . "\n";
		echo 'bästa mötesplats för tonåringar!<br />' . "\n";
		echo 'Har du något förslag, tycker du att något är krångligt eller skulle du vilja ändra på någonting här' . "\n";
		echo 'så är det bara att skriva en rad i forumet.' . "\n";
		echo '</p>' . "\n";
	
		echo '<h2>Vi skulle vilja be om lite mer information om dig</h2>' . "\n";
		echo '<p>' . "\n";
		echo 'Om vi får veta lite mer om dig, så som vilket kön du har, vart du bor och hur gammal du är så' . "\n";
		echo 'kan vi leta fram innehåll som passar dig, dessutom får vi bra statistik så vi vet vad vi ska' . "\n";
		echo 'satsa på.<br />' . "\n";
		echo 'Dessutom så vill andra medlemmar här veta lite vilka det är dom pratar med.' . "\n";
		echo '</p>' . "\n";
	}
	
	function regform_fail()
	{
		echo '<h1>Ooops, nu blev någonting fel här!</h1>' . "\n";
		echo '<p>' . "\n";
		echo 'Vi kunde inte spara dina uppgifter, vi har markerat det som inte stämde i röd text.' . "\n";
		echo '</p>' . "\n";
	}
	
	function regform_settings($values, $errors)
	{
		echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n";
		echo '<fieldset>' . "\n";
		$legend = 'När är du född?';
		if(isset($errors['birth_date']))
		{
			$class = ' class="error"';
			$legend = 'Kontrollera att du valde ett giltligt datum';
		}
		echo '<legend' . $class . '>' . $legend . '</legend>' . "\n";
		echo '<div class="regform_inputs">' . "\n";
		
		echo '<select name="birth_year">' . "\n";
		for($i = 1940; $i < date('Y'); $i++)
		{
			$selected = ( ($i == 1991 && !isset($values['birth_year'])) || $i == $values['birth_year']) ? ' selected="true"' : '';
			echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>' . "\n";
		}
		echo '</select>' . "\n\n";

		echo '<select name="birth_month">' . "\n";
		for($i = 1; $i <= 12; $i++)
		{
			$selected = ($i == $values['birth_month']) ? ' selected="true"' : '';
			$i = ($i < 10) ? '0' . $i : $i;
			echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>' . "\n";
		}
		echo '</select>' . "\n\n";
		
		echo '<select name="birth_day">' . "\n";
		for($i = 1; $i <= 31; $i++)
		{
			$selected = ($i == $values['birth_day']) ? ' selected="true"' : '';
			$i = ($i < 10) ? '0' . $i : $i;
			echo '<option value="' . $i . '">' . $i . '</option>' . "\n";
		}
		echo '</select>' . "\n\n";
		
		echo '</div>' . "\n";
		
		echo '<div class="regform_explanation">' . "\n";
		echo 'Om du roar dig med att säga att du är 75 år gammal, fast du är typ 14 så kommer' . "\n";
		echo 'du inte att få ha någon visningsbild, dessutom förstör du för oss.' . "\n";
		echo '</div>' . "\n";
		echo '</fieldset>' . "\n";
	
		echo '<fieldset>' . "\n";
		echo '<legend>Kan du kissa rakt fram?</legend>' . "\n";
		$selected[$values['gender']] = ' checked="true"';
		echo '<input type="radio" id="gender_m" name="gender" value="m"' . $selected['m'] .' />' . "\n";
		echo '<label for="gender_m">Ja, och när det är vinter kan vi killar skriva vårt namn i snön!</label>' . "\n";
		echo '<br />' . "\n";
		echo '<input type="radio" id="gender_f" name="gender" value="f"' . $selected['f'] .'/>' . "\n";
		echo '<label for="gender_f">Nej, men vi tjejer brukar i alla fall inte tävla om vem som kan fisa högst...</label>' . "\n";
		echo '</fieldset>' . "\n";
	
		echo '<fieldset>' . "\n";
		$legend = 'Vart bor du någonstans?';
		$class = '';
		if(isset($errors['zip_code']))
		{
			$class = ' class="error"';
			$legend = 'Postnummret du angav finns inte i vårat register, ange ett vanligt svenskt postnummer';
		}
		echo '<legend' . $class . '>' . $legend . '</legend>' . "\n";
		echo '<div class="regform_inputs">' . "\n";
		echo '<h4>Postnummer, utan mellanslag</h4>' . "\n";
		echo '<input type="text" name="zip_code" value="' . $values['zip_code'] . '"/>	' . "\n";
		echo '</div>' . "\n";
		echo '<div class="regform_explanation">' . "\n";
		echo 'Ditt postnummer visar bara i vilket område du bor i, inte vilket hus eller på vilken adress du bor.' . "\n";
		echo 'Andra kommer att kunna se en karta med mitten av ditt postnummerområde markerat, men dom kommer inte' . "\n";
		echo 'att kunna se exakt vart du bor.' . "\n";
		echo '</div>' . "\n";
		echo '</fieldset>' . "\n";
	
		echo '<input type="submit" name="submit_button" value="Spara uppgifterna" class="button" />' . "\n";
		echo '</form>' . "\n";
	}
	
	function regform_check($info)
	{
		/* Check birthday */
		if(!checkdate($info['birth_month'], $info['birth_day'], $info['birth_year']))
		{
			$return['birth_date'] = 'error';
		}
		elseif($info['birth_year'] < 1940)
		{
			$return['birth_date'] = 'error';
		}
		elseif($info['birth_year'] > date('Y'))
		{
			$return['birth_date'] = 'error';		
		}
		
		/* Check zip_code */
		if(!is_numeric($info['zip_code']))
		{
			$return['zip_code'] = 'error';
		}
		$query = 'SELECT spot FROM zip_codes WHERE zip_code = "' . $info['zip_code'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) != 1)
		{
			$return['zip_code'] = 'error';			
		}
		
		if(count($return) > 0)
		{
			return $return;
		}
		return true;
	}
	
	if(!login_checklogin())
	{
		echo 'Nu gick något fel, du loggades inte in...';
		trace('register_error', 'register.php acsessed by not logged on user...');
	}
	else
	{
		if(isset($_POST['submit_button']))
		{
			$check = regform_check($_POST);
			if($check === true)
			{
				unset($data);
				$data['userinfo']['gender'] = $_POST['gender'];
				$data['userinfo']['zip_code'] = $_POST['zip_code'];
				$data['userinfo']['birthday'] = $_POST['birth_year'] . '-' . $_POST['birth_month'] . '-' . $_POST['birth_day'];
				login_save_user_data($_SESSION['login']['id'], $data);
				
				session_merge($data);
				
				/*$alert = 'Tackar! Nu skickar vi dig till en introduktionssida som berättar mer om Hamsterpaj,\\n';
				$alert .= 'vad man gör här och hur sidan fungerar, du måste inte läsa den om du inte vill.';
				jscript_alert($alert);
				jscript_location('/hamsterpaj/introduction.php');*/
				jscript_alert('Eftersom Lef inte gjort klart välkommen-sidan ännu så kan vi inte skicka dig till den. Hursomhelst så är du välkommen till hamsterpaj, och vi skickar dig nu till startsidan för träffa.\\n\\nDet skulle dessutom vara kul för folk att veta vem du är, klicka på Inställningar i menyn så kan du ladda upp en bild på dig själv eller göra din egna presentation.\\n\\nÅter igen; välkommen!');
				jscript_location('/traffa/');
			}
			else
			{
				regform_fail();
				regform_settings($_POST, $check);
			}
		}
		else
		{
			regform_header();
			regform_settings();
		}
	}
	
	ui_bottom();

?>


