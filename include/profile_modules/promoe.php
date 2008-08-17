<?php
	include(PATHS_INCLUDE . 'libraries/promoe.lib.php');
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";


	$query = 'SELECT * FROM promoes WHERE owner = "' . $userinfo['login']['id'] . '" ORDER BY id DESC';
	$result = mysql_query($query) or die(report_sql_error());
	if(mysql_num_rows($result) > 0)
	{
		while($promoe = mysql_fetch_assoc($result))
		{
			$promoes[] = $promoe;
		}
		
		echo promoe_thumbs_list('Promoes', $promoes);
	}
	elseif($_SESSION['login']['id'] == $userinfo['login']['id'])
	{
		echo '<h1>Du har inte ritat någon Promoe ännu!</h1>' . "\n";
		echo '<a href="/annat/promoe_editor.php">Rita en Promoe här!</a>' . "\n";
	}
	else
	{
		echo 'Tråkmånsen ' . $userinfo['login']['username'] . ' har inte ritat någon Promoe ännu...' . "\n";
		echo '<br /><br />Men men, det här är ju revolutionens tid, det är dags att börja konvertera vegetarianer' . "\n";
	}
?>

</div>