<?php
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	require('../include/core/common.php');

	
	$q = $_GET["q"];
	if (strlen($q) > 0)
	{
		include ('/storage/www/www.hamsterpaj.net/data/traffa/suggest_usernamefiles/' . substr($q, 0, 1) . '.php');
	}
	//preint_r($names);
	
	$q = $_GET["q"];
	
	if (strlen($q) > 0)
	{
		foreach ($names AS $un)
		{
			if (stristr($q, $un))
			{
				$out .= $un . '<br />';
			}
		}
	}
	//echo '2';
	if ($out == "")
	{
		$out = "Hittade ingen :(";
	}
	echo $out;
?>