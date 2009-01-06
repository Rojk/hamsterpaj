<?php
	require('../include/core/common.php');
	
	ui_top();
	
	if(isset($_POST['username']) && strtolower($_POST['username']) == 'borttagen')
	{
		die('Men gå och lägg dig jävla tomte.');
	}
	
	if(login_checklogin())
	{
		jscript_location('/traffa/index.php');
	}
	else
	{
		if(isset($_POST['username'], $_POST['old_password'], $_POST['new_password'], $_POST['new_password_repeat']))
		{
			if($_POST['new_password'] == $_POST['new_password_repeat'])
			{
				if($_POST['new_password'] != $_POST['old_password'])
				{
					$query = 'SELECT id FROM login WHERE password_version = 3 AND username = "' . $_POST['username'] . '" AND password = "' . sha1(utf8_decode($_POST['old_password']) . PASSWORD_SALT) . '" LIMIT 1';
					$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					if(mysql_num_rows($result) == 1)
					{
						$data = mysql_fetch_assoc($result);
						$query = 'UPDATE login SET password_version = 4, password_hash = "", password = "' . hamsterpaj_password(utf8_decode($_POST['new_password'])) . '" WHERE id = ' . $data['id'];
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
				Ditt lösenord var av den gamla typen, vilka vi av säkerhetsskäl byter ut mot den nya typens lösenord.
				För att göra livet surt för elaka hackers (och kanske ditt minne ^^) så måste du byta lösenord.
			</p>
			
			<p>
				(För dig som är säkerhetsintresserad så går vi från saltade SHA1-summor till en ny hemlig algoritm).
			</p>
			
			<p>
				<form method="post">
					Användarnamn: <input type="text" name="username" /><br />
					Gammalt lösenord: <input type="password" name="old_password" /><br />
					Nytt lösenord: <input type="password" name="new_password" /><br />
					Nytt lösenord igen: <input type="password" name="new_password_repeat" /><br />
					<input type="submit" value="Byt" />
				</form>
			</p>
			<?php
		}
	}
	
	ui_bottom();
?>