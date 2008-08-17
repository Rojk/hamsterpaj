<?php
	require('../include/core/common.php');
	
	$query = 'SELECT l.username, u.gender, u.birthday, z.spot, u.image';
	$query .= ' FROM login AS l, userinfo AS u, zip_codes AS z';
	$query .= ' WHERE u.userid = l.id AND z.zip_code = u.zip_code';
	$query .= ' AND l.id = "' . $_GET['user'] . '" LIMIT 1';
	
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
	$data = mysql_fetch_assoc($result);

	echo '<a href="/traffa/profile.php?id=' . $_GET['user'] . '">' . $data['username'] . '</a>' . "\n";
	
	if($data['gender'] == 'm')
	{
		echo ' kille ';
	}
	elseif($data['gender'] == 'f')
	{
		echo ' tjej ';
	}

	if($data['birthday'] != '0000-00-00')
	{
		echo ' ' . date_get_age($data['birthday']) . ' ';
	}

	if(strlen($data['spot']) > 0)
	{
		echo ' från ' . $data['spot'];
	}	
	if($data['image'] == 2 || $data['image'] == 3)
	{
		echo '<br /><img src="' . IMAGE_URL . 'images/users/thumb/' . $_GET['user'] . '.jpg" />' . "\n";
	}
?>