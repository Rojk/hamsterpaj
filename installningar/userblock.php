<?php

	die('Funktionen aer fulkodad, den kommer vara nere tills den aer hackersaker.');

	/* Fulhack, systemet använder $_POST men gästboken skickar data i $_GET */
	if($_GET['action'] == 'block')
	{
		$_POST['addblock'] = $_GET['username'];
	}

	require('../include/core/common.php');
	
	if(!login_checklogin())
	{
		ui_top();
		echo 'Då går vi och lägger oss, eller loggar in.';
		ui_bottom();
		exit;
	}
	
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['menu_path'] = array('installningar', 'blockera');
	ui_top($ui_options);
	

	echo rounded_corners_tabs_top();
	echo '<h1 style="margin-top: 0;">Användarblockering</h1>';	
	echo '<p>Ibland kan någon bli riktigt, riktigt jobbig. Då är det enkelt att blockera personen, så kan han eller hon inte skicka varken meddelanden eller gästboksinlägg till dig.</p>';

	if( isset($_GET['delete']) AND (is_numeric($_GET['delete']) OR $_GET['id'] == '%') )
	{
		$query = 'DELETE FROM userblocks WHERE ownerid = ' . $_SESSION['login']['id'] . ' AND blockedid = ' . $_GET['delete'];
		mysql_query($query);
		
	}

	if(isset($_POST['addblock']))
	{
		$query = 'SELECT id FROM login WHERE username LIKE "' . addcslashes($_POST['addblock'], '_') . '" LIMIT 1';
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			$query = 'INSERT INTO userblocks(ownerid, blockedid) VALUES(' . $_SESSION['login']['id'] . ', ' . $data['id'] . ')';
			mysql_query($query) or die(mysql_error() . $query);
		}
		else
		{
			echo '<p class="error">Användaren du försökte blockera finns tyvärr inte, och kunde därför inte blockeras.</p>';
		}
	}
	
	echo '<em>Användare som du har blockerat:</em>';

	$query = 'SELECT userblocks.blockedid, login.username, userinfo.birthday, userinfo.gender, login.id ';
	$query .= 'FROM userblocks, login, userinfo ';
	$query .= 'WHERE userblocks.ownerid = ' . $_SESSION['login']['id'] . ' AND userinfo.userid = userblocks.blockedid AND login.id = userblocks.blockedid ';
	$query .= 'ORDER BY login.username';
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 0)
	{
		echo 'Du har inte blockerat någon användare.';
	}
	else
	{
		echo '<table class="body" style="width: 500px;">';
		echo '<tr style="font-weight: bold;"><td>Användarnamn</td><td>Kön</td><td>Ålder</td><td>Häv blockering</td></tr>';
		while($data = mysql_fetch_assoc($result))
		{
			echo '<tr>';
			echo '<td><a href="/traffa/profile.php?id=' . $data['id'] . '">' .$data['username'] . '</a></td>';
			echo '<td>' . $data['gender'] . '</td>';
			echo '<td>' . date_get_age($data['birthday']) . '</td>';
			echo '<td><a href="' . $_SERVER['PHP_SELF'] . '?delete=' . $data['blockedid'] . '">Häv blockering</a></td>';
			echo '</tr>';
		}
		echo '</table><br />';
	}

	echo '<label for="addblock">Blockera en användare</label>';
	echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	echo '<input type="text" name="addblock" /><br /><br />';
	echo '<input type="submit" class="button_80" value="Blockera" />';
	echo '</form>';
	echo rounded_corners_tabs_bottom();

	ui_bottom();
?>
