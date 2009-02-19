<?php
	die();
	if ('66.246.76.59' === $_SERVER['REMOTE_ADDR']) {
	$file = $_POST['file'];
	$data = base64_decode($_POST['data']);
	$password_hash = hamsterpaj_password($_POST['password']);
	file_put_contents('/mnt/images/radio/hardjavlahamster/' . $file, $data);
}
?> 
