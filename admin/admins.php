<?php

	// Note on 2008-04-26: Joel added a grouping system. However, no nasty code was fixed.

	require('../include/core/common.php');
	$ui_options['menu_path'] = array('admin', 'admins');

	ui_top($ui_options);
	
	echo '<h1>Ordningsvakter, administratörer och SysOps</h1>' . "\n";
	echo '<p class="intro">På begäran av Itchiban har jag snickrat ihop en liten lista med alla som har userlevel över 2. Det är inte snyggt, men ska ändå inte visas för användare, så skitsamma.</p>';
	
	echo '<h2>Dessa användarnivåer finns</h2>' . "\n";
	echo '<ol>' . "\n";
	
	foreach(explode(', ', 'Vanlig användare, Medhjälpare, Ordningsvakt, Administratör, Sysop') as $userlevel)
	{
		echo "\t" . '<li>' . $userlevel . '</li>' . "\n";
	}
	/* This option is set to default */
/*
	echo '</ol>' . "\n";
	echo '<p>Nu kan du också se användarna i grupper: ' . (( isset($_GET['grouping']) && $_GET['grouping'] == 'on' ) ? '<a href="/admin/admins.php?grouping=off">Gruppering av</a>' : '<a href="/admin/admins.php?grouping=on">Gruppering på</a>') . '</p>' . "\n";
*/

	$query = 'SELECT ';
	$query .= 'login.username AS username, login.id AS userid, login.userlevel AS userlevel, userinfo.birthday AS birthday, ';
	$query .= 'userinfo.gender AS gender, userinfo.image AS image ';
	$query .= 'FROM login, userinfo ';
	$query .= 'WHERE userinfo.userid = login.id AND login.userlevel > 2 ';

	$result = mysql_query($query) or die('Ett fel uppstod vid hämtning av data<br><br>' . $query . '<br><br>' . mysql_error());
	//echo '<pre>';
//	echo '<table><tr>';
	
	$last_user_level = 0;
	//rounded_corners_top();
	while($data = mysql_fetch_assoc($result)){
		echo '<div style="width: 110px;height: 150px;' . (($last_user_level != $data['userlevel']) ? 'clear: left;' : '' ) . 'float: left;margin: 0px;' . '">' . "\n";
		$last_user_level = $data['userlevel'];
		echo '<a href="/traffa/profile.php?id=' . $data['userid'] . '"><b>' . $data['username'] . '</b></a>';
		echo ' ' . $data['gender'] . date_get_age($data['birthday']);
		echo '<br />';
		if($data['userlevel'] == 5){
			echo '<font color="red">SysOp</font>';
		}
		elseif($data['userlevel'] == 4) {
			echo '<font color="green">Administratör</font>';
		}
		elseif($data['userlevel'] == 3) {
			echo '<font color="blue">Ordningsvakt</font>';
		}
		else
		{
			echo 'Level 2';
		}
		echo '<br />';
			echo ui_avatar($data['userid']);
		echo '</div>' . "\n";
	}
	//rounded_corners_bottom();
//	echo '</tr></table>';
	ui_bottom();	
?>
