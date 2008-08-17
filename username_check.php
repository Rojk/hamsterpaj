<?php
	require('./include/core/common.php');
	
	function check_name($username)
	{
		$query = 'SELECT username FROM login WHERE username LIKE "' . addcslashes($username, '_') .'"';
		$result = mysql_query($query);	
		if (mysql_num_rows($result) > 0)
		{
			echo '<strong><span style="color: red;">' . utf8_encode($username) . ' är upptaget</span></strong>';
		}
		else
		{
			echo '<strong><span style="color: green;">' . utf8_encode($username) . ' är ledigt</span></strong>';
		}
	}

if (!preg_match("/^[0-9a-zA-Z_-]+$/i", $_GET['username']) || strlen($_GET['username']) > 16 || strlen($_GET['username']) < 2)
{
	echo '<strong> Du har inte valt ett giltligt användarnamn, du får bara använda A-z och _ </strong>';
}
else
{
	check_name($_GET['username']);
}
?>
