<?php

require('../include/core/common.php');
require_once(PATHS_INCLUDE . 'libraries/posts.php');
require_once(PATHS_INCLUDE . 'libraries/markup.php');
//require_once($hp_includepath . '/libraries/markup.php');
//require_once($hp_includepath . '/libraries/games.lib.php');
//require_once($hp_includepath . '/libraries/schedule.lib.php');
//require_once(PATHS_INCLUDE . 'libraries/tips.lib.php');

$ui_options['menu_path'] = array('admin', 'ov_list');

ui_top($ui_options);

if(!is_privilegied('crew_see_register'))
{
	echo 'inte för dig...';
}
else
{/*'user_id' => 'User id', */
	$keys_1 = array('firstname' => 'Förnamn/Mobilnr', 'surname' => 'Efternamn/Födelsedag', 'email' => 'E-post/MSN', 'streetaddress' => 'Adress');
	$keys_2 = array('phone_ov', 'birthday', 'msn', 'zip_code');
	$query = 'SELECT l.id as user_id, username, firstname, surname, email, msn, streetaddress, zip_code, birthday, visible_level, phone_ov';
	$query .= ' FROM userinfo u, login l';
	$query .= ' WHERE u.userid=l.id AND l.userlevel > 2 ORDER BY surname ASC';
	$result = mysql_query($query);
	while($data = mysql_fetch_assoc($result))
	{
		$ovs[] = $data;
	}
	
	echo '<h1>Ordningsvakter och deras adresser mm</h1>' . "\n";
	echo '<table>' . "\n";
	echo '<tr>' . "\n";
	foreach($keys_1 as $key => $label)
	{
		echo '<td>' . $label . '</td>' . "\n";
	}
	echo '</tr>' . "\n";
	foreach($ovs as $ov)
	{
		$zebra = ($zebra == true) ? false : true;
		if($ov['visible_level'] == 0)
		{
			$ov['visible_level'] = 5;
		}
		if($ov['visible_level'] <= $_SESSION['login']['userlevel'])
		{
			$out = ($zebra) ? '<tr style="background: #EEF">' : '<tr>';
			echo $out;
			echo '<td rowspan="2">' . ui_avatar($ov['user_id']) . '</td>';
			foreach($keys_1 as $key => $label)
			{
				if ($key == 'firstname')
				{
					echo '<td>' . $ov[$key] . ' (<a href="/traffa/quicksearch.php?username=' . $ov['username'] . '">' . $ov['username'] . '</a>)</td>' . "\n";
				}
				else
				{
					echo '<td>' . $ov[$key] . '</td>' . "\n";
				}
			}
			echo '</tr>' . "\n";
			$out = ($zebra) ? '<tr style="background: #EEF">' : '<tr>';
			echo $out;
			foreach($keys_2 as $key)
			{
				echo '<td>' . $ov[$key] . '</td>' . "\n";
			}
			echo '</tr>' . "\n";
			echo '<tr><td></td></tr>';
		}
	}
	echo '</table>' . "\n";
}	

ui_bottom();

?>
