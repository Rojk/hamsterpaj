<?php
	require_once('../include/core/common.php');
	$password_hash = hamsterpaj_password($_POST['password']);
	if ($password_hash != $holger_valid_hash) {
		die('Uppblåst kattfisk!? Ut ur mitt hus fulhackare! *slå med räfsa*');
	}
	if ('66.246.76.59' === $_SERVER['REMOTE_ADDR']) {
		$file = $_POST['file'];
		$data = base64_decode($_POST['data']);
		file_put_contents('/mnt/images/radio/hardjavlahamster/' . $file, $data);
	}
?> 
