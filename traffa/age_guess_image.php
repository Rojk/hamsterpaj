<?php

	header('Content-type: image/jpeg');
	
	include('../include/core/common.php');
	
	$sql = 'SELECT id FROM login WHERE MD5(id) = ' . $_GET['user_hash'];
	
	$result = mysql_query($sql) or die('Dummer, det här fungerar ju inte.');
	
	$data = mysql_fetch_assoc($result);
	
	$out = readfile(IMAGE_PATH . 'images/users/full/' . $data['id'] . '.jpg') or die('FISKROJK! BOM-SPÖKENA JAGAR MIG') && preint_r($data);
	
	echo $out;