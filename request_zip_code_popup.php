<?php
if (isset($_POST['submit']))
{
	if (is_numeric($_POST['zip_code']))
	{
		$query = 'UPDATE userinfo SET zip_code = "' . $_POST['zip_code'] . '" WHERE userid = "' . $_SESSION['login']['id'] . '"';
		mysql_query($query);
	}
	echo 'Tack för ditt postnummer';	
}
else
{
	echo '<form method="post" target="' . $_SERVER['PHP_SELF'] . '">';
	echo '<input type="text" name="zip_code" />';
	echo '<input type="submit" value="Spara!" name="submit">';
	echo '</form>';
}
?>