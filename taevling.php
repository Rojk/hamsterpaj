<?php
	require('include/core/common.php');
	
	$ui_options['current_menu'] = 'hamsterpaj';
	$ui_options['title'] = 'Tävling - Hamsterpaj.net';
	$ui_options['stylesheets'][] = 'taevling.css';
	$ui_options['stylesheets'][] = 'forms.css';
	


	//Rounded corners
	//$out .= '<div class="rounded_corners" id="bugfix">';
	//$out .= '<img src="http://images.hamsterpaj.net/css_backgrounds/rounded_corners/blue_full_top.png" />';
	try
	{
		if ($_GET['action'] == 'submit')
		{
			$fields_aliases = array(
				'firstname' => 'ditt förnamn',
				'lastname' => 'ditt efternamn',
				'address' => 'din adress',
				'zip_code' => 'din postkod',
				'city' => 'postort',
				'question_a' => 'fråga A',
				'question_b' => 'fråga B'
			);
			$fields_notempty = array(
				'firstname',
				'lastname',
				'address',
				'zip_code',
				'city',
				'question_a',
				'question_b'
			);
			$fields_number_between = array(
				'question_a' => array(0, 4),
				'question_b' => array(0, 4)
			);
			foreach ($fields_notempty as $val)
			{
				if (empty($_POST[$val]))
				{
					throw new Exception('Du glömde att fylla i ' . $fields_aliases[$val] . '.');
				}
			}
			foreach ($fields_number_between as $key => $val)
			{
				if ($_POST[$key] <= $val[0] || $_POST[$key] >= $val[1])
				{
					throw new Exception(ucfirst($fields_aliases[$key]) . ' måste vara ett värde <strong>mellan</strong> <strong>' . $val[0] . '</strong> och <strong>' . $val[1] . '</strong>.');
				}
			}
			/*
			$out .= '<pre>';
			$out .= print_r($_POST, true);
			$out .= '</pre>';
			*/
			$sql = 'INSERT INTO contest_camp_rock SET user_id = 0, firstname = "' . $_POST['firstname'] . '", lastname = "' . $_POST['lastname'] . '", address = "' . $_POST['address'] . '", zip_code = "' . $_POST['zip_code'] . '", city = "' . $_POST['city'] . '", question_a_answer = "' . $_POST['question_a'] . '", question_b_answer = "' . $_POST['question_b'] . '", timestamp = ' . time() . '';
			mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
			$out .= '<h3>Tack för ditt bidrag! :)</h3><br /><a href="/">Till startsidan</a>' . "\n";
			
			/*
			$eric_tavling_sql = 'INSERT INTO eric_tavling SET fname = "' . $_POST['fname'] . '", lname = "' . $_POST['lname'] . '", username = "' . $_POST['username'] . '", address = "' . $_POST['address'] . '", zipcode = "' . $_POST['zipcode'] . '", locality = "' . $_POST['locality'] . '", phone = "' . $_POST['phone'] . '", q1 = "' . $_POST['a'] . '", q2 = "' . $_POST['b'] . '"';
			mysql_query($eric_tavling_sql);
			$out .= 'Tack för att du deltog, du kommer att meddelas om du har vunnit när tävlingen är över.';
			*/
		}
		else
		{
			
			$out .= '<fieldset>' . "\n";
			$out .= '<legend>Tävling!</legend>' . "\n";
			$out .= '<p>För att vara med och tävla om de fina priserna behöver du endast svara på två enkla frågor och fylla i dina uppgifter i formuläret nedan.</p>' . "\n";
			$out .= '<p>Tävlingen pågår fram till och med den 14 december.</p>
			<h3>Priser</h3>' . "\n";
			$out .= '<p>1:a pris: Popcornmaskin, soundtracket till Camp Rock Jonas Brothers-skiva och DVD-filmen Camp Rock<br />
2:a-3:e pris: Soundtracket till Camp Rock samt DVD-filmen Camp Rock<br />
4:e-5:e pris: DVD-filmen Camp Rock</p>' . "\n";

			$out .= '<form action="?action=submit" method="post">';
			$out .= '<table class="form" id="camp_rock_competition">' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<th><label for="firstname">Förnamn <strong>*</strong></label></th>' . "\n";
					$out .= '<td><input type="text" name="firstname" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<th><label for="lastname">Efternamn <strong>*</strong></label></th>' . "\n";
					$out .= '<td><input type="text" name="lastname" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<th><label for="address">Adress <strong>*</strong></label></th>' . "\n";
					$out .= '<td><input type="text" name="address" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<th><label for="zip_code">Postkod <strong>*</strong></label></th>' . "\n";
					$out .= '<td><input type="text" name="zip_code" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<th><label for="city">Postort <strong>*</strong></label></th>' . "\n";
					$out .= '<td><input type="text" name="city" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				/* 
				1. Vad handlar Camp Rock om?
					 1. En campingplats
					X. En tjej på ett musikläger
					2. Ett slagsmål mellan två stenar
					*/

				$out .= '<tr>' . "\n";
					$out .= '<th rowspan="3"><label for="question_a">Vad handlar Camp Rock om? <strong>*</strong></label></th>' . "\n";
					$out .= '<td id="optgroup_top"><input type="radio" id="question_a_1" value="1" name="question_a" /> <label for="a1">1. En campingplats</label></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<td id="optgroup_mid"><input type="radio" id="question_a_2" value="2" name="question_a" /> <label for="a2">X. En tjej på ett musikläger</label></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<td id="optgroup_bottom"><input type="radio" id="question_a_3" value="3" name="question_a" /> <label for="a3">2. Ett slagsmål mellan två stenar</label></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";;
				
				/*
				2. Vad är Jonas Brothers?
				1. Ett tv-program
				X. En filmregissör
				2. Ett musikband
				*/

				$out .= '<tr>' . "\n";
					$out .= '<th rowspan="3"><label for="question_a">Vad är Jonas Brothers? <strong>*</strong></label></th>' . "\n";
				
					$out .= '<td id="optgroup_top"><input type="radio" id="question_b_1" value="1" name="question_b" /> <label for="a1">1. Ett tv-program</label></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<td id="optgroup_mid"><input type="radio" id="question_b_2" value="2" name="question_b" /> <label for="a2">X. En filmregissör</label></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<td id="optgroup_bottom"><input type="radio" id="question_b_3" value="3" name="question_b" /><label for="a3">2. Ett musikband</label></td>' . "\n";
				$out .= '</tr>' . "\n";
				
			$out .= '</table>' . "\n";
			
			$out .= '<input type="submit" id="submit" id="camp_rock_competition_submit" value="Sänd" />' . "\n";
			$out .= '</form>';
			$out .= '<p>Camp Rock i mobilen – SMSa ”CAMP ROCK” till 72790. Kostar som ett vanligt SMS.</p>' . "\n";
			$out .= '</fieldset>' . "\n";
		}
	}
	catch (Exception $error)
	{
		$out .= '<div class="error">';
		$out .= '<p>';
		$out .= $error->getMessage();
		$out .= '</p>';
		$out .= '<p>';
		$out .= '<a href="taevling.php">&laquo; Tillbaka</a>';
		$out .= '</p>';
		$out .= '</div>';
	}
	
	//$out .= '<img src="http://images.hamsterpaj.net/css_backgrounds/rounded_corners/blue_full_bottom.png" />';
	//$out .= '</div>';
	
	
	ui_top($ui_options);
	echo $out;		
	ui_bottom();
?>