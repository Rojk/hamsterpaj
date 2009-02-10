<?php    
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('traeffa');
	$ui_options['stylesheets'][] = 'traffa_index.css';
	ui_top($ui_options);

	echo rounded_corners_top(array('color' => 'orange'));
?>

<h1 style="margin-top: 0;">Det här är Träffa, stället där du hittar nya hamsterpajare!</h1>
<p>
	Just nu är det mest en massa oranga knappar överallt, du kan ju testa att trycka på några, såsmåningom
	ska det komma lite bilder på användare här också, men det får vi fixa en annan dag.<br />
	Så länge kan du kolla in <a href="/traffa/gallery.php">Galleriet</a> om du vill titta på folks
	visningsbilder.<br />
	Håll till godo! Och du, om du saknar något här så skicka in ett förslag i <a href="/hamsterpaj/foerslag.php">förslagssystemet</a>.
</p><br />

<?php
	echo rounded_corners_bottom(array('color' => 'orange'));
	echo rounded_corners_top(array('color' => 'blue'));
?>

<h2 style="margin-top: 0;">Snacksuget folk just nu</h2>
<div id="chatters">
	<table>
<?php
	if($_POST['action'] == 'delete')
	{
		if(is_privilegied('snacksuget_delete'))
		{
			$timestamp = mysql_real_escape_string($_POST['chat_timestamp']);
			mysql_query('DELETE FROM chatters WHERE timestamp="' . $timestamp . '"');
			echo 'Inlägg borttaget' . "\n";
		}
	}

	$query = 'SELECT l.id, l.username, u.gender, u.birthday, c.description, c.timestamp ';
	$query .= 'FROM login AS l, userinfo AS u, chatters AS c ';
	$query .= 'WHERE u.userid = l.id AND l.id = c.id ';
	$query .= 'ORDER BY c.timestamp DESC LIMIT 20';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		echo '<tr>' . "\n";
		echo '<td>' . '<a href="/traffa/profile.php?id=' . $data['id'] . '">' . $data['username'] . '</a></td>' . "\n";
		echo '<td>' . str_replace('u', '', $data['gender']) . '</td>';
		$age = ($data['birthday'] == '0000-00-00') ? '' : date_get_age($data['birthday']) . 'år';
		echo '<td>' . $age . '</td>';
		echo '<td>' . $data['description'] . '</td>' . "\n";
		if(is_privilegied('snacksuget_delete'))
		{
			echo '<td>' . "\n";
			echo '<form action="/traffa/index.php" method="post" class="chat_delete">' . "\n";
			echo '
<input type="hidden" value="delete" name="action"/>
' . "\n";
			echo '<input type="hidden" value="' . $data['timestamp'] . '" name="chat_timestamp"/>
' . "\n";
			echo '<input type="submit" class="chat_delete" value="x"/>' . "\n";
			echo '
</form>';
			echo '</td>';
		}
		echo '</tr>';
	}
?>
	</table>
</div>
<?php
	if(login_checklogin())
	{
		echo '<input type="button" class="button_130" value="Lägg till mig i listan" onclick="window.open(\'add_to_chatters.php\', \'add_to_chatters\', \'location=no, width=470, height=270\');" style="float: right;" />' . "\n";
	}
	echo '<br style="clear: both;" />' . "\n";
	echo rounded_corners_bottom(array('color' => 'blue'));
?>


<script>
	function open_chat_window(channel)
	{
		<?php
			$url = 'http://ved.hamsterpaj.net/chatt/index.php?';
			if(login_checklogin())
			{
				$url .= 'nick=';
				if(!preg_match("/^[A-Za-z]$/i",substr($_SESSION['login']['username'],0,1)))
				{
					$url.= substr($_SESSION['login']['username'],1,strlen($_SESSION['login']['username']));
				}
				else
				{
					$url .= $_SESSION['login']['username'];
				}
				$url .= '&realname=';
				$url .= urlencode(date_get_age($_SESSION['userinfo']['birthday']) . ';');
				$url .= urlencode($_SESSION['userinfo']['gender'] . ';');
				$url .= urlencode($_SESSION['userinfo']['location'] . ';');
				$url .= urlencode($_SESSION['login']['id'] . ';');
				$url .= urlencode($_SESSION['userinfo']['image'] . ';');
			}
			else
			{
				$url .= 'guest';
			}
			$url .= '&port=53';
			echo 'window.open(\'' . $url . '&chan=\' + channel + \'\', \'chat_window\', \'location=no, width=640, height=478\');' . "\n";
	
		?>
	}
</script>
<?php
	echo rounded_corners_top(array('color' => 'blue'));
?>

	<h2 style="margin-top: 0;">Gå direkt till chatten</h2>
	<input type="button" class="button_150" value="Läs mer om chatten &raquo;" style="float: right; width: 150px;" onclick="window.location='/chat/';" />
	<input type="button" class="button_60" value="#Träffa" onclick="open_chat_window('tr%E4ffa');" />
	<input type="button" class="button_60" value="#Chat" onclick="open_chat_window('chat');" />
	<input type="button" class="button_60" value="#Moget" onclick="open_chat_window('moget');" />
	<input type="button" class="button_100" value="#Frågesport" onclick="open_chat_window('trivia');" />
	<input type="button" class="button_100" value="#Webdesign" onclick="open_chat_window('webdesign');" />
<?php 
	echo rounded_corners_bottom(array('color' => 'blue'));
	echo rounded_corners_top(array('color' => 'blue'));
?>
	<h2 style="margin-top: 0;">Folk online</h2>
	<?php
		foreach(array('F', 'P') AS $gender)
		{
			foreach(array(14, 16, 18, 20, 22) AS $age)
			{
				$unserialized = file_get_contents(PATHS_CACHE . 'online_people/' . $gender . $age . '.phpserialized');
				$people = unserialize($unserialized);
				foreach($people AS $data)
				{
					$map_points .= '<Point X=\'' . $data['y_rt90'] . '\' Y=\'' . $data['x_rt90'] . '\'>';
					$map_points .= '<Name>' . $data['username'] . '</Name>';
					$map_points .= '<IconImage>http://www.hitta.se/images/point.png</IconImage>';
					$map_points .= '<Content><![CDATA[' . $data['gender'] . ' ' . date_get_age($data['birthday']);
					if($data['image'] == 1 || $data['image'] == 2)
					{
						$map_points .= '<br /><a href="http://www.hamsterpaj.net/traffa/hittapunktse_map_link_redirect.php?id=' . $data['userid'] . '"><img src="' . IMAGE_URL . 'images/users/thumb/' . $data['userid'] . '.jpg" /></a>';
					}
					if(login_checklogin())
					{
						$map_points .= '<br />' . rt90_readable(rt90_distance($_SESSION['userinfo']['x_rt90'], $_SESSION['userinfo']['y_rt90'], $data['x_rt90'], $data['y_rt90']));
					}
					$map_points .= ']]></Content>';
					$map_points .= '</Point>';
				}
				
				echo '<form method="post" action="http://www.hitta.se/LargeMap.aspx" target="hittapunktse" onsubmit="window.open(\'\', \'hittapunktse\', \'location=no, width=750, height=500\');" style="display: block; margin: 0px; float: left;">' . "\n";
				echo '<input type="hidden" name="MapPoints" value="<?xml version=\'1.0\' encoding=\'utf-8\'?><MapPoints xmlns=\'http://tempuri.org/XMLFile1.xsd\'>' . $map_points . '</MapPoints>">' . "\n";
			  $display_gender = ($gender == 'P') ? 'Killar' : 'Tjejer';
			  if($_SESSION['userinfo']['gender'] == 'P')
				{
					$age_min = $age - 2;
					$age_max = $age + 1;
				}
				else
				{
					$age_min = $age - 1;
					$age_max = $age + 2;
				}
			  echo '<input type="submit" value="' . $display_gender . ' ' . $age_min . '-' . $age_max . '" class="button_120" />' . "\n";
				echo '</form>' . "\n";
				unset($map_points);
			}
		}
		if(login_checklogin())
	{
		$rt90_close_distance = 5000;
	
		$x_max = $_SESSION['userinfo']['x_rt90'] + $rt90_close_distance;
		$x_min = $_SESSION['userinfo']['x_rt90'] - $rt90_close_distance;
		$y_max = $_SESSION['userinfo']['y_rt90'] + $rt90_close_distance;
		$y_min = $_SESSION['userinfo']['y_rt90'] - $rt90_close_distance;
		
		$query = 'SELECT u.userid, l.username, z.x_rt90, z.y_rt90, u.gender, u.image, u.birthday, l.lastlogon FROM userinfo AS u, login AS l, zip_codes AS z WHERE l.is_removed = 0 ';
		$query .= 'AND l.id = u.userid AND z.zip_code = u.zip_code ';
		$query .= ' AND (z.x_rt90 > ' . $x_min . ' && z.x_rt90 < ' . $x_max . ') ';
		$query .= ' && (z.y_rt90 > ' . $y_min . ' && z.y_rt90 < ' . $y_max . ') LIMIT 100';
		
		$result = mysql_query($query) or die(report_sql_error());
		while($data = mysql_fetch_assoc($result))
		{
			$map_points .= '<Point X=\'' . $data['y_rt90'] . '\' Y=\'' . $data['x_rt90'] . '\'>';
			$map_points .= '<Name>' . $data['username'] . '</Name>';
			$map_points .= '<IconImage>http://www.hitta.se/images/point.png</IconImage>';
			$map_points .= '<Content><![CDATA[' . $data['gender'] . ' ' . date_get_age($data['birthday']);
			if($data['image'] == 1 || $data['image'] == 2)
			{
				$map_points .= '<br /><a href=\'http://www.hamsterpaj.net/traffa/hittapunktse_map_link_redirect.php?id=' . $data['userid'] . '\'><img src=\'http://images.hamsterpaj.net/images/users/thumb/' . $data['userid'] . '.jpg\' /></a>';
			}
			if(login_checklogin())
			{
				$map_points .= '<br />' . rt90_readable(rt90_distance($_SESSION['userinfo']['x_rt90'], $_SESSION['userinfo']['y_rt90'], $data['x_rt90'], $data['y_rt90']));
			}
			$map_points .= ']]></Content>';
			$map_points .= '</Point>';
		}
		
		echo '<form method="post" action="http://www.hitta.se/LargeMap.aspx" target="hittapunktse" onsubmit="window.open(\'\', \'hittapunktse\', \'location=no, width=750, height=500\');">' . "\n";
		echo '<input type="hidden" name="MapPoints" value="<?xml version=\'1.0\' encoding=\'utf-8\'?><MapPoints xmlns=\'http://tempuri.org/XMLFile1.xsd\'>' . $map_points . '</MapPoints>">' . "\n";
	  echo '<input type="submit" value="Hamsterpajare som bor nära dig" class="button" id="neighbors" />' . "\n";
		echo '</form>' . "\n";
	}
	?>
<?php
	echo rounded_corners_bottom(array('color' => 'blue'));
?>

<!--<h2>Nya medlemmar att säga hej till!</h2> -->
<div id="new_members">
	<table>
<?php
	/*
	$query = 'SELECT l.id, l.username, l.regtimestamp, u.gender, u.birthday, z.spot ';
	$query .= 'FROM login AS l, userinfo AS u, zip_codes AS z ';
	$query .= 'WHERE u.userid = l.id AND z.zip_code = u.zip_code AND l.username NOT LIKE "Borttagen" ';
	$query .= 'ORDER BY l.id DESC LIMIT 25';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		echo '<tr>' . "\n";
		echo '<td>' . '<a href="/traffa/profile.php?id=' . $data['id'] . '">' . $data['username'] . '</a></td>' . "\n";
		echo '<td>' . str_replace('u', '', $data['gender']) . '</td>';
		$age = ($data['birthday'] == '0000-00-00') ? '' : date_get_age($data['birthday']) . 'år';
		echo '<td>' . $age . '</td>';
		echo '<td>' . $data['spot'] . '</td>';
		echo '<td>' . duration(time() - $data['regtimestamp']) . '</td>';
		echo '</tr>';
	}*/
?>
	</table>
	
</div>

<div id="scribble_puff">
	<a href="/traffa/klotterplanket.php">
	<img src="<?php echo IMAGE_URL; ?>" style="float: left;" />
	<?php
		echo '<h2>Massa snygga flickor och pojkar på klotterplanket just nu!</h2>' . "\n";
	?>
	<p>
		På klotterplanket skriver Hamsterpajare dygnet runt korta små inlägg. En del letar efter någon
		att prata med, andra småpratar med den som dyker upp och några vill bara klaga på att lektionen
		suger.<br />
		Klotterplanket är till för dig som inte orkar med chatten och som vill se ansiktet på de du 
		pratar med.
	</p>
</a>
</div>

<?php
	ui_bottom();
?>
