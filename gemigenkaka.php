<?php
	require('include/core/common.php');
	if(!is_privilegied('igotgodmode')){ die(); }
	echo hamsterpaj_password(utf8_decode($_GET['kaka'])) . '<hr>';
	echo sha1(utf8_decode($_GET['kaka']) . PASSWORD_SALT) . '<hr>';
	echo PASSWORD_SALT;
?>
