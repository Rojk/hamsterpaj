<?php
	include 'include/core/common.php';
	if(!in_array(strtolower($_SESSION['login']['username']), array('joar', 'heggan', 'joel', 'lef-91', 'anoosmonkey', 'iphone', 'soode', 'rojk', 'johan', 'ace', 'entrero')))
	{
		return;
	}

	if(isset($_SESSION['new_design']))
	{
		unset($_SESSION['new_design']);
	}
	else
	{
		$_SESSION['new_design']  = true;
	}
?>
