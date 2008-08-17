  <?php
  echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px; ">' . "\n";
  
  echo '<script type="text/javascript">' . "\n";
  echo 'function update_iplist() ' . "\n";
  echo '{' . "\n";
  echo '	loadFragmentInToElement(\'/admin/ip_checker.php?lastip=' . $userinfo['login']['lastip'] . '\', \'ip_list\');' . "\n";
  echo '}' . "\n";
  echo 'function update_reglist() ' . "\n";
  echo '{' . "\n";
  echo '	loadFragmentInToElement(\'/admin/ip_checker.php?regip=' . $userinfo['login']['regip'] . '\', \'ip_list\');' . "\n";
  echo '}' . "\n";
  echo '</script>';

  echo '<div>';
  if ($_SESSION['login']['userlevel'] >= USERLEVELS_SHOW_HASH)
  {
  	echo '<div style="float: left; clear: right; width: 40%;"><span style="font-weight: bold;">Lösenordshash:</span><br />' . $userinfo['login']['password'] . '</div>';
  }
  if ($_SESSION['login']['userlevel'] >= USERLEVELS_SHOW_IP)
  {
  	echo '<div style="float: left; clear: right; width: 20%;"><span style="font-weight: bold;">Senaste IP:</span><br /><a href="javascript: void(0);" onclick="update_iplist();">' . $userinfo['login']['lastip'] . '</a><br />' . date('Y-m-d H:i:s', $userinfo['login']['lastlogon']) . '</div>';
  	echo '<div style="float: left; clear: right; width: 20%;"><span style="font-weight: bold;">Reg IP:</span><br /><a href="javascript: void(0);" onclick="update_reglist();">' . $userinfo['login']['regip'] . '</a></div>';
  }
	if ($_SESSION['login']['userlevel'] >= USERLEVELS_SHOW_BIRTHDATE)
	{
		echo '<div style="float: left; clear: right; width: 20%;"><span style="font-weight: bold;">Födelsedatum:</span><br />' . $userinfo['userinfo']['birthday'] . '</div>';
	}
  echo '</div>';
  echo '<br /><br />';
 	echo '<div id="ip_list"></div><br />';

  if ($_SESSION['login']['userlevel'] >= USERLEVELS_GHOST_USER)
  {
  	echo '<strong><a href="/admin/ghost.php?ghost=' . $userinfo['login']['username'] . '">Ghosta</a> </strong>';
  	echo '<strong><a href="/admin/logout_user.php?action=logout&username=' . $userinfo['login']['username'] . '">Logga ut användare</a> </strong>';
  }
  if ($_SESSION['login']['userlevel'] >= USERLEVELS_DELETE_PHOTO)
  {
  	echo '<a href="/avatar.php?id=' . $userinfo['login']['id'] . '&refuse&admin" ';
		echo 'onclick="return confirm(\'Är du säker på att du vill ta bort denna bild?\');" ';
		echo '/><strong>Ta bort avatar</strong></a>';
  }
  if ($_SESSION['login']['userlevel'] >= USERLEVELS_EDIT_PRESENTATION)
  {
  	echo ' <strong><a href="/admin/edit_presentation.php?id='  . $userinfo['login']['id'] . '">Ändra presentation</a></strong> ';
  }
  
  if($_SESSION['login']['userlevel'] >= 3)
  {
  	echo '<input type="button" value="Ta bort" onclick="if(confirm(\'Vill du ta bort den här knäppgöken?\')){window.location=\'/remove_user.php?userid=' . $userinfo['login']['id'] . '\';}" />' . "\n";
  }
  
  if($_SESSION['login']['userlevel'] >= 3)
  {
  	echo '<h1>Aiight, snabbkoll vad för shit användaren hittat på på sajten</h1>' . "\n";
  	echo '<h2>Senast skickade gästboksinlägg</h2>' . "\n";
  	$query = 'SELECT * FROM traffa_guestbooks WHERE sender = "' . $userinfo['login']['id'] . '" AND is_private != 1 ORDER BY id DESC LIMIT 5';
  	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
  	while($data = mysql_fetch_assoc($result))
  	{
  		echo '<strong>' . fix_time($data['timestamp']) . '</strong>' . "\n";
  		echo '<p>' . htmlspecialchars($data['message']) . '</p>' . "\n";
  	}
  	
  	echo '<h2>Senaste inläggen i forumet</h2>' . "\n";
  	$query = 'SELECT * FROM posts WHERE author = "' . $userinfo['login']['id'] . '" ORDER BY id DESC LIMIT 5';
  	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
  	while($data = mysql_fetch_assoc($result))
  	{
  		echo '<strong>' . fix_time($data['timestamp']) . '</strong>' . "\n";
  		echo '<p><a href="' . posts_url_get($data['id']) . '">' . htmlspecialchars($data['content']) . '</a></p>' . "\n";
  	}
  }
  
  ?>
  <div style="clear: both;"></div>
  </div>
