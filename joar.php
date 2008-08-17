<?php
	require('include/core/common.php');

	$data = $_GET['password'];
	echo sha1(utf8_decode($data) . PASSWORD_SALT) . "<br />";

	show_source('joar.php'); 
?>