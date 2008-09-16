<?php
	include 'include/core/common.php';

	if(isset($_SESSION['new_design']))
	{
		unset($_SESSION['new_design']);
	}
	else
	{
		$_SESSION['new_design']  = true;
	}
	
	header('location: /');
?>
