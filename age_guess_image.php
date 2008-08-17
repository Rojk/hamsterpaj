<?php
	header('Content-type:image/jpeg');
	header('Content-Length: ' . filesize('/mnt/images/images/users/full/' . $_SESSION['age_guess']['current_user'] . '.jpg'));
	$binary = file_get_contents('/mnt/images/images/users/full/' . $_SESSION['age_guess']['current_user'] . '.jpg');
	echo $binary;
?>
