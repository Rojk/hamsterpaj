<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	$ui_options['title'] = 'Guestbook-hack - Hamsterpaj.net';
	$ui_options['menu_path'] = array('fra', 'guestbook_hack');
	
	$unit_1 = $_GET['unit_1'];
	$unit_2 = $_GET['unit_2'];
	
	if (!is_privilegied('use_ghosting_tools'))
	{
		die("Fel.");
	}
	
	function numberIsset($numbers)
	{
		foreach ($numbers AS $number)
		{
			if (!isset($number))
			{
				$error = 1;
			}
		 	if (!is_numeric($number))
		 	{
				$error = 1;
			}
		}
		if ($error == 1)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	if (isset($unit_1))
	{
		$out .= (isset($_GET['fullscreen'])) ? '' : '<a href="/admin/guestbook_hack.php">Tillbaka</a>';
		if (numberIsset(array($unit_1, $unit_2)))
		{
			$sql = 'SELECT gb.* FROM traffa_guestbooks AS gb
			 WHERE gb.sender = ' . $unit_1 . '
			 AND gb.recipient = ' . $unit_2 . '
			 OR gb.sender = ' . $unit_2 . '
			 AND gb.recipient = ' . $unit_1;
			$sql .= '';
			$sql .= (isset($_GET['from'])) ? ' AND timestamp > ' . $_GET['from'] : '';
			$sql .= ' ORDER BY gb.timestamp DESC LIMIT 2000';
		}
		elseif (isset($unit_1) && $unit_2 == '')
		{
			$sql = 'SELECT gb.* FROM traffa_guestbooks AS gb
			 WHERE gb.sender = ' . $unit_1 . '
			 OR gb.recipient = ' . $unit_1 . '
			 ORDER BY gb.timestamp DESC LIMIT 2000';
		}
		$result = mysql_query($sql) or $out .= rounded_corners(mysql_error(), array('color' => 'orange_deluxe'), true);
		$out .= '<table style="background: #000000; font-family: courier new; color: #00ff00; font-size: 10px;">';
		if (isset($_GET['fullscreen']))
		{
			$out .= '<tr>' . "\n";
			$out .= '<th colspan="10" style="background: #fc8; color: #000;">' . "\n";
			$sql = 'SELECT username FROM login WHERE id = ' . $_SESSION['login']['id'] . ' LIMIT 1';
			$data = mysql_fetch_assoc(mysql_query($sql));
			$out .= 'Frågan utförd ' . date("Y.m.d - H:i.s") . ' (' . time() . ') av ' . $data['username'];
			$out .= '</tr>' . "\n\n";
		}
		$out .= '<tr>' . "\n";
		$out .= '<th valign="top">time</th>' . "\n";
		$out .= '<th valign="top">sender</th>' . "\n";
		$out .= '<th valign="top">recipient</th>' . "\n";
		$out .= '<th valign="top">is_private</th>' . "\n";
		$out .= '<th valign="top">deleted</th>' . "\n";
		$out .= '<th>' . $data['message'] . '</th>' . "\n";
		$out .= '</tr>' . "\n\n";
		while ($data = mysql_fetch_assoc($result))
		{
			$out .= ($zebra == 1) ? '<tr style="background: #222;">' . "\n" : '<tr>' . "\n";
			$zebra = ($zebra == 1) ? $zebra = 0 : $zebra = 1;
			$out .= '<td valign="top">' . date("Y.m.d - H:i.s", $data['timestamp']) . '</td>';
			$out .= '<td valign="top">' . $data['sender'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['recipient'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['is_private'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['deleted'] . '</td>' . "\n";
			$out .= '<td>' . nl2br($data['message']) . '</td>' . "\n";
			$out .= '</tr>' . "\n\n";
		}
		$out .= '</table>' . "\n";
		$sql = 'INSERT INTO admin_event (event, value, timestamp, admin_id, user_id, item_id)
		 VALUES ("guestbook_hack",';
		$sql .= (isset($unit_1) && $unit_2 == '') ? ' "target: ' . $unit_1 . '",' : ' "target: ' . $unit_1 . ' and ' . $unit_2 . '",'; 
		$sql .= ' ' . time() . ','; 
		$sql .= ' ' . $_SESSION['login']['id'] . ',';
		$sql .= ' ' . $unit_1 . ','; 
		$sql .= ' ' . 0 . ')';
		mysql_query($sql) or die($sql . '<br />' . mysql_error() . '<br /><br /> Kontakta <a href="/joar">Joar</a>. det är han som har kodat skiten :S');
		
	}
	else
	{
		$out .= rounded_corners_top($void);
		$out .= '<h2>Hacka GB! (OBS! integritetskränkande)</h2>' . "\n";
		$out .= '<p>Det GÅR att skriva in EN användare i "ID 1"-fältet, 
		men det kan bli en ganska tung process om det är något spamtroll.</p><p>
		Annars är det meningen att man ska följa en privat
			eller borttagen konversation mellan två parter med den här funktionen.
		Kopiera bara user_id och klistra in i fälten, simple as that.</p>' . "\n";
		$out .= '<form method="get">' . "\n";
		$out .= '<label for="unit_1">ID 1</label>' . "\n";
		$out .= '<input type="text" name="unit_1" />' . "\n";
		$out .= '<label for="unit_2">ID 2</label>' . "\n";
		$out .= '<input type="text" name="unit_2" />' . "\n";
		$out .= '<input type="submit" value="FRA!" class="button_60" />' . "\n";
		$out .= '</form>' . "\n";
		$out .= rounded_corners_bottom($void);
	}
	
	echo $out;
?>
	