<?php
	require('../include/core/common.php');
	
	ui_top();
	
	if(isset($_POST['username']) && strtolower($_POST['username']) == 'borttagen')
	{
		die('Men gå och lägg dig jävla tomte.');
	}
	
	if(login_checklogin())
	{
		echo 'Det kanske låter konstigt, men du måste <a href="/logout.php">logga ut</a> för att byta lösenord.';
	}
	else
	{
		if(isset($_POST['username'], $_POST['old_password'], $_POST['new_password'], $_POST['new_password_repeat']))
		{
			if($_POST['new_password'] == $_POST['new_password_repeat'])
			{
				if($_POST['new_password'] != $_POST['old_password'])
				{
					$query = 'SELECT id FROM login WHERE username = "' . $_POST['username'] . '" AND password_hash = "' . sha1($_POST['old_password'] . PASSWORD_SALT) . '" LIMIT 1';
					$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					if(mysql_num_rows($old_result) == 1)
					{
						$data = mysql_fetch_assoc($result);
						$query = 'UPDATE login SET password_hash = "", password = "' . hamsterpaj_password(utf8_decode($_POST['new_password'])) . '" WHERE id = ' . $data['is'];
						mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
						echo 'Det där gick ju bra, logga in där uppe nu tjockis!';
					}
					else
					{
						echo 'Användaren hittades inte eller så var <i>det gamla lösenordet<i> inte rätt.';
					}
				}
				else
				{
					echo 'Du måste ange ett nytt lösenord. Och lösenordssäkerhet är inte något fjolligt "kanel" som lösenord - det är STORA och små bokstäver blandat med s1ffr0r och krum€|ur€r.';
				}
			}
			else
			{
				echo 'Lösenorden stämmde inte överens med varandra :/. Försök igen.';
			}
		}
		else
		{
			// Fulkod? JAG BRYR MIG FAN INTE SÅHÄR DAGS!
			?>
			<h1>Förnya lösenord</h1>
			<p>
				Du kan ha hamnat på den här sidan av två skäl:
				<ul>
					<li>Du försökte byta lösenord.</li>
					<li>Du hade ett lösenord krypterat med den gamla krypteringen.</li>
				</ul>
				
				Om det är det senare orkar jag inte förklara, bara byt. Klockan är 04:20 och då skriver man inte små söta pedagogiska texter. Punkt. /Joel
			</p>
			
			<p>
				<form method="post">
					Användarnamn: <input type="text" name="username" /><br />
					Gammalt lösenord: <input type="password" name="old_password" /><br />
					Nytt lösenord: <input type="password" name="new_password" /><br />
					Nytt lösenord (Förklaring för blondiner: upprepat) <input type="password" name="new_password_repeat" /><br />
					<input type="submit" value="Byt" /> (&raquo; Blondinförklaring: Tryck på den orange knappen som inte är orange, för det orkar jag inte koda nu).<br />
				</form>
			</p>
			<?php
		}
	}
	
	ui_bottom();
?>