<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('traffa', 'ortsoek');
	$ui_options['title'] = 'Gratis musik på Hamsterpaj!';
	ui_top($ui_options);

	echo '<form>' . "\n";
	echo '<h5>Stad</h5>' . "\n";
	echo '<input type="text" name="area" /> <input type="submit" value="Sök" class="button_50" />' . "\n";
	echo '</form>' . "\n";

	if(isset($_GET['area']))
	{
		$query = 'SELECT l.username, l.id, u.gender, u.birthday';
		$query .= ' FROM login AS l, zip_codes AS z, userinfo AS u';
		$query .= ' WHERE l.id = u.userid AND u.zip_code = z.zip_code AND z.spot LIKE "' . $_GET['area'] . '%"';
		$query .= ' ORDER BY l.lastlogon DESC LIMIT 50';
		

		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		echo '<ul>' . "\n";
		while($data = mysql_fetch_assoc($result))
		{
			echo '<li><a href="/traffa/profile.php?id=' . $data['id'] . '">' . $data['username'] . '</a> ' . $data['gender'] . date_get_age($data['birthday']) . '</li>' . "\n";
		}
		echo '</ul>' . "\n";
	}
	
	ui_bottom();
?>


