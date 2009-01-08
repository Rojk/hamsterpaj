<?php
	include('../include/core/common.php');
	if(login_checklogin() && !in_array($_GET['module_id'], $_SESSION['fp_module_votes']))
	{
		$score = ($_GET['vote'] == 'plus') ? 1 : -1;
		$query = 'UPDATE fp_modules SET score = score + "' . $score . '" WHERE id = "' . $_GET['module_id'] . '" LIMIT 1';
		mysql_query($query);
		echo $query;

		$_SESSION['fp_module_votes'][] = $_GET['module_id'];
	}
?>