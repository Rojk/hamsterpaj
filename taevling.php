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
			$out .= 'Tack för ditt bidrag! :)' . "\n";
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
				$out .= '<tr>' . "\n";
					$out .= '<th rowspan="3"><label for="question_a">Fråga A <strong>*</strong></label></th>' . "\n";
					$out .= '<td id="optgroup_top"><label for="a1">Svar 1</label> <input type="radio" id="question_a_1" value="1" name="question_a" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<td id="optgroup_mid"><label for="a2">Svar 2</label> <input type="radio" id="question_a_2" value="2" name="question_a" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<td id="optgroup_bottom"><label for="a3">Svar 3</label> <input type="radio" id="question_a_3" value="3" name="question_a" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";;
				
				$out .= '<tr>' . "\n";
					$out .= '<th rowspan="3"><label for="question_a">Fråga B <strong>*</strong></label></th>' . "\n";
				
					$out .= '<td id="optgroup_top"><label for="a1">Svar 1</label> <input type="radio" id="question_b_1" value="1" name="question_b" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<td id="optgroup_mid"><label for="a2">Svar 2</label> <input type="radio" id="question_b_2" value="2" name="question_b" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<td id="optgroup_bottom"><label for="a3">Svar 3</label> <input type="radio" id="question_b_3" value="3" name="question_b" /></td>' . "\n";
				$out .= '</tr>' . "\n";
				
			$out .= '</table>' . "\n";
			
			$out .= '<input type="submit" id="submit" id="camp_rock_competition_submit" value="Sänd" />' . "\n";
			$out .= '</form>';
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