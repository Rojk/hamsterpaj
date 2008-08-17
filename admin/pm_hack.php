<?php
	require('../include/core/common.php');
	
	$ui_options['title'] = 'Meddelande-hack :: Hamsterpaj.net';
	$ui_options['menu_path'] = array('fra', 'pm_hack');
	
	if (!is_privilegied('use_ghosting_tools'))
	{
		echo '<h2>Fü Fü, inte fyska.</h2>';
		exit;
	}
	
	$sess = $_SESSION;
	$get = $_GET;
	if (isset($_GET['id1']))
	{
		if (strlen($get['id2']) > 0)
		{
			$sql .= 'SELECT * FROM messages_new';
			$sql .= ' WHERE sender = ' . $get['id1'] . ' AND recipient = ' . $get['id2'];
			$sql .= ' OR sender = ' . $get['id2'] . ' AND recipient = ' . $get['id1'];
			$sql .= ' ORDER BY timestamp DESC';
		}
		else
		{
			$sql .= 'SELECT * FROM messages_new';
			$sql .= ' WHERE sender = ' . $get['id1'];
			$sql .= ' OR recipient = ' . $get['id1'];
			$sql .= ' ORDER BY timestamp DESC';
		}
		$result = mysql_query($sql);
		
		$out = '<table>' . "\n";
		$out .= '<tr>' . "\n";
		$out .= '<th colspan="10" style="background: #fc8;">' . "\n";
		$sql = 'SELECT username FROM login WHERE id = ' . $sess['login']['id'] . ' LIMIT 1';
		$data = mysql_fetch_assoc(mysql_query($sql));
		$out .= 'Frågan utförd ' . date("Y.m.d - H:i.s") . ' (' . time() . ') av ' . $data['username'];
		$out .= '</tr>' . "\n\n";
		$out .= '<tr style="background: #eee;">' . "\n";
		$out .= '<td><h3>ID</h3></td>' . "\n";
		$out .= '<td><h3>Sender</h3></td>' . "\n";
		$out .= '<td><h3>Recipient</h3></td>' . "\n";
		$out .= '<td><h3>Timestamp</h3></td>' . "\n";
		$out .= '<td><h3>Title</h3></td>' . "\n";
		$out .= '<td><h3>Message</h3></td>' . "\n";
		$out .= '<td><h3>Discussion</h3></td>' . "\n";
		$out .= '<td><h3>Recipient status</h3></td>' . "\n";
		$out .= '<td><h3>Sender status</h3></td>' . "\n";
		$out .= '<td><h3>Mass message ID</h3></td></tr>' . "\n";
		while ($data = mysql_fetch_assoc($result))
		{
			$out .=  ($zebra == 1) ? '<tr style="background: #eee;">' . "\n" : '<tr>' . "\n";
			$zebra = ($zebra == 1) ? $zebra = 0 : $zebra = 1;
			$out .= '<td valign="top">' . $data['id'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['sender'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['recipient'] . '</td>' . "\n";
			$out .= '<td valign="top">' . date("Y.m.d - H:i.s", $data['timestamp']) . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['title'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['message'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['discussion'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['recipient_status'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['sender_status'] . '</td>' . "\n";
			$out .= '<td valign="top">' . $data['mass_message_id'] . '</td>' . "\n";
			$out .= '</tr>' . "\n\n";
		}
		$out .= '</table>' . "\n";
	//ui_top($ui_options);
	//echo utf8_decode($out);
	echo $out;
	//ui_bottom();
	}
	else
	{
	?>
	<h3>Kolla upp skummisars meddelanden.</h3>
	<form action="/admin/pm_hack.php" method="get">
	ID1 : <input type="text" name="id1" /> ID2: <input type="text" name="id2" /><input type="submit" value="sekz"></form>
	<?
	}
?>
