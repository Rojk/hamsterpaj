<?php
	require_once('../include/core/common.php');
	$query = 'SELECT user_status FROM userinfo WHERE userid = ' . $_GET['id'];
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$data = mysql_fetch_assoc($result);
	echo $data['user_status'];
?>