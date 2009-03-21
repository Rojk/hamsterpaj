<?php
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'guestbook.lib.php');
	require_once(PATHS_LIBRARIES . 'userblock.lib.php');
	
	if($_POST['action'] == 'insert' && login_checklogin())
	{
		if (userblock_checkblock($_POST['recipient']))
		{
			die('FISK! Du är blockad!');
		}
		$entry['sender'] = $_SESSION['login']['id'];
		$entry['recipient'] = $_POST['recipient'];
		$entry['message'] = utf8_encode($_POST['message']);
		$entry['is_private'] = ($_POST['private'] == 1) ? 1 : 0;
		if(isset($_POST['reply-to']) && is_numeric($_POST['reply-to']))
		{
			$entry['reply-to'] = $_POST['reply-to'];
		}
		guestbook_insert($entry);
		//trace('guestbook', $entry['message']);
	}
	if($_GET['action'] == 'delete' && login_checklogin())
	{
		$query = 'UPDATE traffa_guestbooks SET deleted = 1, `read` = 1 WHERE id = "' . $_GET['entry_id'] . '" AND recipient = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	if($_GET['action'] == 'undelete' && login_checklogin())
	{
		$query = 'UPDATE traffa_guestbooks SET deleted = 0 WHERE id = "' . $_GET['entry_id'] . '" AND recipient = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	if($_GET['action'] == 'private' && login_checklogin())
	{
		$query = 'UPDATE traffa_guestbooks SET is_private = 1 WHERE id = "' . $_GET['entry_id'] . '" AND recipient = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}

	if($_GET['action'] == 'unprivate' && login_checklogin())
	{
		$query = 'UPDATE traffa_guestbooks SET is_private = 0 WHERE id = "' . $_GET['entry_id'] . '" AND recipient = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	if($_GET['action'] == 'zero_unread' && login_checklogin())
	{
		$query = 'UPDATE traffa_guestbooks SET `read` = 1 WHERE recipient = "' . $_SESSION['login']['id'] . '"';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		echo 'Nu ska alla dina inlägg vara markerade som olästa!';
	}
?>
