<?php

require('../include/core/common.php');
	
if ( ! isset($_GET['url']) || $_GET['url'][0] != '/' || ! login_checklogin() || ! is_numeric($_GET['friend_id']) )
{
	die('Aaaaand a one a two a one two three four!');
}

$_GET['url'] = html_entity_decode($_GET['url']);

$query = 'UPDATE `friends_notices`';
$query .= ' SET `read` = "1"';
$query .= ' WHERE `user_id` = "' . $_SESSION['login']['id'] . '" AND `friend_id` = "' . $_GET['friend_id'] . '" AND `url` = "' . $_GET['url'] . '"';
$query = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

$_SESSION['friends_actions_lastupdate'] = 0;

header('Location: ' . $_GET['url']);