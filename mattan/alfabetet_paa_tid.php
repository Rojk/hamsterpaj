<?php
	require('../include/core/common.php');

	$ui_options['menu_path'] = array('mattan', 'alfabetet_paa_tid');
	$ui_options['title'] = 'Hamsterpaj.net - Underhållning, community, forum och annat kul :)';

	ui_top($ui_options);
	
	echo rounded_corners_top(array('color' => 'blue'));
	echo '<h1 style="margin-top: 0;">Alfabetet på tid</h1>' . "\n";
	
	if(isset($_GET[$_SESSION['alphabet_anticheat']['score_field']]))
	{
		$submit = $_GET[$_SESSION['alphabet_anticheat']['score_field']];
		$checksum = substr($submit, -1);
		$encrypted = substr($submit, 0, strpos($submit, '_'));
		if(1 || 'min jävla checksumma bara funkade...')
		{
			$encrypted = $encrypted / 2;
			$decrypted = $encrypted - $_SESSION['alphabet_anticheat']['score_encrypt_key'];
			if($decrypted > 2000)
			{
				/*if(round($decrypted/1000, 2) < 8)
				{
					treasure_item(6);
				}*/
			
				$query = 'SELECT score FROM alphabet_scores WHERE user = "' . $_SESSION['login']['id'] . '" LIMIT 1';
				$result = mysql_query($query) or die(report_sql_error($query));
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					if($data['score'] > $decrypted)
					{
						echo '<h2>Sparade ' . round($decrypted/1000, 2) . ' som ditt nya rekord!</h2>';
						$query = 'UPDATE alphabet_scores SET score = "' . $decrypted . '" WHERE user = "' . $_SESSION['login']['id'] . '"';
						mysql_query($query);
					}
					else
					{
						echo '<h2>Du fick ' . round($decrypted/1000, 2) . ' men din gamla tid är ' . round($data['score']/1000, 2) . 's så vi uppdaterade inte highscoren</h2>';
					}
				}
				else
				{
					echo '<h2>Sparade ' . round($decrypted/1000, 2) . ' som din första tid, bättre kan du!</h2>';
					$query = 'INSERT INTO alphabet_scores(user, score) VALUES("' . $_SESSION['login']['id'] . '", "' . $decrypted . '")';
					mysql_query($query);
				}
			}
		}
		
		unset($_SESSION['alphabet_anticheat']['score_encrypt_key']);
		unset($_SESSION['alphabet_anticheat']['score_field']);
	}

?>


<input type="text" onkeypress="return alphabet_keydown(event)" style="color: #ababab; width: 530px; font-family: courier new; font-size: 30px; font-weight: bold;" />

<input type="button" class="button" value="Skicka highscore och försök igen" id="alphabet_submit_button" style="display: none;" />

<?php
	if($_SESSION['login']['id'] < 1)
	{
		echo '<input type="button" class="button" value="Börja om" onclick="window.location = \'alphabet.php\';" />';
	}
	
	$_SESSION['alphabet_anticheat']['access_code'] = substr(md5(time()), 3, 10);
	$_SESSION['alphabet_anticheat']['last_call'] = time();
	echo '<script src="/javascripts/alphabet.php?' . $_SESSION['alphabet_anticheat']['access_code'] . '=' . substr(md5(time), 0, 5) . '"></script>' . "\n";

?>
<div id="javascript_out">
	Klicka i rutan här ovanför och börja skriva alfabetet. Tiden börjar räknas när du har skrivit bokstaven a.
</div>

<?php
	echo rounded_corners_bottom(array('color' => 'blue'));
	ui_bottom();
?>


