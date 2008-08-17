<?php

	// Shutdown by Joel on Monday 2008-05-26, potential security risk.
	trace('hoppsi_poppsi', __FILE__);
	die();


	/* Vi lagrar antalet klienter i de båda kanalerna i filen clients.txt. Träffa ligger innan chat! */
	$filename = 'clients.txt';

	$content = file_get_contents($filename);
	$clients = explode("\n", $content);

	//print_r($clients);

	if(isset($_GET['traffa']))
	{
		$clients[0] = $_GET['traffa'];
	}
	elseif(isset($_GET['chat']))
	{
		$clients[1] = $_GET['chat'];
	}
	elseif(isset($_GET['moget']))
	{
		$clients[2] = $_GET['moget'];
	}
	elseif(isset($_GET['basketfestival']))
	{
		$clients[3] = $_GET['basketfestival'];
	}
	$new_file_content = $clients[0] . "\n" . $clients[1] . "\n" . $clients[2] . "\n" . $clients[3];
	$file = fopen($filename, 'w+') or die('Couldn\'t open file!');
	fwrite($file, $new_file_content) or die('Couldn\'t write contents!');
	fclose($file);
?>
