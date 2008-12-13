<?php
	include('include/core/common.php');
	event_log_log('fp_' . $_GET['name']);
	header('Location: ' . html_entity_decode($_GET['url']));
?>
