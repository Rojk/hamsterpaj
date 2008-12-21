<?php
	require('../include/core/common.php');
	if(!login_checklogin())
	{
		echo 'Members only';
		exit;
	}
?>
<html>
	<head>
		<title>Svara</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<div id="main" style="width: auto; height: auto; padding: 3px;">
		<?php
			$query = 'SELECT timestamp FROM chatters WHERE id = "' . $_SESSION['login']['id'] . '" LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			if(mysql_num_rows($result) > 0)
			{
				$data = mysql_fetch_assoc($result);
				if($data['timestamp'] > (time()-600))
				{
					die('Du kan inte lägga till dig själv på listan oftare än var tionde minut!');
				}
			}
			if(isset($_POST['description']))
			{
				if(isset($data['timestamp']))
				{
					$query = 'UPDATE chatters SET timestamp = "' . time() . '", description = "' . htmlspecialchars($_POST['description']);
					$query .= '" WHERE id = "' . $_SESSION['login']['id'] . '" LIMIT 1';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
				else
				{
					$query = 'INSERT INTO chatters (id, timestamp, description) VALUES("' . $_SESSION['login']['id'] . '", ';
					$query .= time() . ', "' . htmlspecialchars($_POST['description']) . '")';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
				echo '<script>' . "\n";
				echo 'opener.window.location = \'/traffa/index.php\';' . "\n";
				echo 'window.close();' . "\n";
				echo '</script>' . "\n";
				exit;
			}
			else
			{
				echo '<h3>Lägg till dig själv på listan med snacksuget folk</h3>' . "\n";
				echo '<p>' . "\n";
				echo 'Tillsammans med ditt användarnamn kan du skriva en kort text där du förklarar vem du vill prata med eller vad du vill prata om.';
				echo '</p>' . "\n";
				echo '<h4>Text efter ditt namn (max 75 tecken)</h4>' . "\n";
				echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n";
				echo '<input type="text" class="textbox" name="description" maxlength="75" style="width: 370px;" />' . "\n";
				echo '<input type="submit" class="button" value="Lägg till" />' . "\n";
				echo '</form>' . "\n";
				echo '<h5 style="margin-top: 3px; margin-bottom: 2px;">Exempel på bra texter</h5>' . "\n";
				echo '<ul style="list-style-type: none; padding: 0px; font-style: italic;">' . "\n";
				echo '<li>Söker nån som är bra på matte! (Matte C, gymnasiet)</li>' . "\n";
				echo '<li>Vill bara prata, helst nån mogen kille 13-15 år</li>' . "\n";
				echo '<li>Nån som kan hjälpa mig med min presentation?</li>' . "\n";
				echo '<li>Nån tjej som också blivit dumpad...?</li>' . "\n";
				echo '</ul>' . "\n";
			}
		?>
		</div>
	</body>
</html>