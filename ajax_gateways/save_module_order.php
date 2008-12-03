<?php
	require('../include/core/common.php');

	$order = $_SERVER['QUERY_STRING'];
	$order = explode('|', $order);
	$_SESSION['module_order'] = $order;

	if(login_checklogin())
	{
		$query = 'UPDATE preferences';
		$query .= ' SET module_order = "' . mysql_escape_string(serialize($order)) . '"';
		$query .= ' WHERE userid = ' . $_SESSION['login']['id'] . ' LIMIT 1';
		$query = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		echo mysql_affected_rows();
	}
?>