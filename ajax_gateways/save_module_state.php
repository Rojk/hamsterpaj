<?php
	require('..include/core/common.php');

	$_SESSION['module_states'][$_GET['module']] = $_GET['state'];

	if(login_checklogin())
	{
		$serialized = serialize($_SESSION['module_states']);
		$query = 'UPDATE preferences SET module_states = "' . mysql_escape_string($serialized) . '"WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
?>