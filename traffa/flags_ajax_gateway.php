<?php
	$locked_flags = array('mh', 'sysop', 'ov', 'admin', 'guldstjaerna', 'nord');

	require('../include/core/common.php');
	foreach($_GET AS $field => $value)
	{
		if(in_array(strtolower($field), $locked_flags) || in_array(strtolower($value), $locked_flags))
		{
			die('Jag vill köpa nåt annat, typ en AK, handgranat, kevlar och hjälm - sen är det bra så');
		}
		if($field == 'action')
		{
			continue;
		}
		if($value == 'disabled')
		{
			$query = 'DELETE FROM user_flags WHERE user = "' . $_SESSION['login']['id'] . '" AND flag IN(SELECT id FROM user_flags_list WHERE handle LIKE "' . $field . '%")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		elseif($value == 'enable') /* This is a checkbox */
		{
			$query = 'SELECT id FROM user_flags_list WHERE handle = "' . $field . '" LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			if(mysql_num_rows($result) == 1)
			{
				$data = mysql_fetch_assoc($result);
				$query = 'INSERT INTO user_flags (user, flag) VALUES("' . $_SESSION['login']['id'] . '", "' . $data['id'] . '")';
				mysql_query($query);
			}
		}
		else /* For radio buttons */
		{
			$query = 'DELETE FROM user_flags WHERE user = "' . $_SESSION['login']['id'] . '" AND flag IN(SELECT id FROM user_flags_list WHERE `group` = "' . $field . '")';
			
			mysql_query($query);
			
			$query = 'SELECT id FROM user_flags_list WHERE `group` = "' . $field . '" AND handle = "' . $value . '" LIMIT 1';
			$result = mysql_query($query);
			$data = mysql_fetch_assoc($result);
				
			$query = 'INSERT INTO user_flags (user, flag) VALUES("' . $_SESSION['login']['id'] . '",  "' . $data['id'] . '")';
			mysql_query($query);
		}
	}

	if($_GET['hacker'] == 'enable')
	{
		echo '<h1>Gratulerar</h1>' . "\n";
		echo '<p>Du har fulat iväg data och fått scriptet att aktivera en flagga som inte går att välja. Just denna gången är luckan öppnad bara för denna flaggan, tidigare gick det att få en sysop-flagga på samma sätt.<br />Liknande luckor finns överallt på nätet, det är särskilt kul när det är poäng i en tävling som skickas in ;)<br />Men du, gör inget allt för dumt, sabotage är olagligt.</p>' . "\n"; 
	}

?>