<?php
exit;
$sql = 'INSERT INTO abuse (timestamp, reporter, report_type, reference_id, abuse_type, freetext)';
$sql .= ' VALUES (' . time() . ', ' . $_SESSION['login']['id'] . ', "photo", ' . $_GET['photo_id'] . ', "photo", "' . $_GET['reason'] . '")';

?>