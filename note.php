<?php
	require('include/core/common.php');
	if(login_checklogin())
	{
		$insertquery = 'INSERT INTO notes (id, text) VALUES("' . $_SESSION['login']['id'] . '", "' . $_GET['note'] . '")';
		$updatequery = 'UPDATE notes SET text = "' . $_GET['note'] . '" WHERE id = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		
		$_SESSION['note'] = $_GET['note'];

		mysql_query($insertquery) or mysql_query($updatequery) or die(report_sql_error($query, __FILE__, __LINE__));

	}
?>