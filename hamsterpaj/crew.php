<?php
	require('../include/core/common.php');
	$ui_options = array();
	$ui_options['title'] = 'Vi som gör Hamsterpaj';
	$ui_options['menu_path'] = array('hamsterpaj', 'crew');
	
	ui_top($ui_options);
	
	$query  = 'SELECT l.id AS userid, u.image AS image, u.birthday AS birthday, c.real_name AS real_name, c.task AS task, c.info_text AS info_text';
	$query .= ' FROM login AS l, userinfo AS u, crew_members AS c';
	$query .= ' WHERE c.userid = l.id AND u.userid = l.id';
	$result = mysql_query($query) or report_sql_error($query);
	
	echo '<h1 style="font-size: 24px; font-family: georgia, serif;">Vi som gör Hamsterpaj</h1>' . "\n";
	echo '<p>';
	echo 'Vi är ju några stycken som sliter för att siten skall vara en trevlig plats att vistas på och har ni någon gång funderat på vilka som gömmer sig bakom skärmarna kan ni sluta fundera, här är en presentation av de inblandade.';
	echo ' <strong>Buggrapporter och förslag tas i <a href="/hamsterpaj/suggestions.php">förslagslådan</a></strong>!';
	echo '</p>' . "\n";
	
	while($data = mysql_fetch_assoc($result))
	{
		rounded_corners_top();
			echo (( in_array($data['image'], array(1, 2)) ) ? ui_avatar($data['userid'], array('style' => 'float: left; margin: 10px')) : '<img src="' . IMAGE_URL . 'images/noimage.png" alt="Ingen visninsbild" style="float: left;clear: left; margin: 10px" />');
			echo '<div style="float: left; width: 500px;margin-left: 10px">';
			echo '<h2 style="font-size: 20px; font-family: georgia, serif;"><a href="/traffa/profile.php?id=' . $data['userid'] . '">' . $data['real_name'] . '</a> ' . (($data['birthday'] != '0000-00-00') ? date_get_age($data['birthday']) : '') . ' - ' . htmlspecialchars($data['task']) . '</h2>' . "\n";
			echo htmlspecialchars($data['info_text']);
			echo '</div>';
			echo '<br style="clear: both" />';
		rounded_corners_bottom();
	}
	
	ui_bottom();
?>