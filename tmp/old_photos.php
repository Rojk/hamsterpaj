<?php
	include('/storage/www/standard.php');
	ui_top($ui_options);

	if(login_checklogin())
	{
		$query = 'SELECT id FROM photos WHERE owner = "' . $_SESSION['login']['id'] . '"';
		$result = mysql_query($query) or report_sql_error($query);
		
		while($data = mysql_fetch_assoc($result))
		{
			echo '<img src="http://images.hamsterpaj.net/images/photoalbums/images_' . round($data['id']/1000) . '/' . $data['id'] . '_full.jpg" /><br />';
		}
	}
	else
	{
		echo '<h1>Please log in</h1>';
	}

	ui_bottom();
?>