<?php

require('../include/core/common.php');
	
if ( ! isset($_GET['url']) || $_GET['url'][0] != '/' )
{
	die('Aaaaand a one a two a one two three four!');
}

$query = 'UPDATE `friends_notices`';
$query .= ' SET `read` = 1';
$query .= ' WHERE `user_id` = "' . $_SESSION['login']['id'] . '" AND `url` = "' . $_GET['url'] . '"';
$query .= ' LIMIT 1';
mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

header('Location: ' . $_GET['url']);