<?php
	include("/storage/www/standard.php");
	
	header("Content-type: text/plain");
	echo "test\n";
	$sql = 'SELECT username FROM login WHERE username NOT LIKE "Borttagen" ORDER BY username ASC LIMIT 100000';
	$result = mysql_query($sql) or die(mysql_error());
	while ($data = mysql_fetch_assoc($result))
	{
		//echo "test\n";
		$un = $data['username'];
		$names[substr($un, 0, 1)][] = $un;
	}
	//preint_r($names);
	foreach ($names AS $key => $sorted_name_array)
	{
		//echo $key . "lol\n";
		$out .= '<?php' . "\n";
		$out .= '// THIS IS AN ARRAY FOR EVERY USERNAME IN THE DATABASE AT ' . date("Y-m-d H:i") . ' THAT STARTS WITH THE LETTER "' . $key . '"' . "\n";
		$out .= '$names = ' . "\n";
		$out .= 'array(';
		foreach ($sorted_name_array AS $k => $name)
		{
			$out .= ($done_first_post[$key]) ? ',' . "\n" : '';
			$out .= '"' . $name . '"';
			$done_first_post[$key] = true;
		}
		$done_first_post[$key] = false;
		$out .= ');' . "\n";
		$out .= '// END OF THE LETTER ' . $key . "\n";
		$out .= '?>' . "\n";
		file_put_contents('/storage/www/www.hamsterpaj.net/data/traffa/suggest_usernamefiles/' . $key . '.php', $out);
	}
	echo "klar";
?>