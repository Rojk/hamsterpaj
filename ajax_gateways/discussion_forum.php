<?php
	require('../include/core/common.php');
	include(PATHS_INCLUDE . 'libraries/discussion_forum.lib.php');

	if($_GET['action'] == 'direct_link_fetch' && isset($_GET['post_id']) && is_numeric($_GET['post_id']))
	{
		$link = forum_get_url_by_post($_GET['post_id']);
		if($link === false)
		{
			 die('Länkfel, meddela SysOP. Rad ' . __LINE__ . ' fil ' . __FILE__);
		}
		else
		{
			die('http://www.hamsterpaj.net' . $link);
		}
	}


	if($_GET['action'] == 'remove_post' && forum_security(array('action' => 'remove_post', 'post_id' => $_GET['post_id'])))
	{
		echo 'Removing post';
		$posts = discussion_forum_post_fetch(array('post_id' => $_GET['post_id']));
		if(count($posts) != 1)
		{
			trace('forum_remove_error', __FILE__ . ':' . __LINE__ . ' data: ' . print_r($_GET, true));
			exit;
		}
		$post = array_pop($posts);
		
		discussion_forum_remove_post(array('post_id' => $_GET['post_id'], 'removal_comment' => $_GET['removal_comment']));
		
		$message  = 'Hej, ditt inlägg i forumet med titeln "%TITLE%" har tagits bort.' . "\n";
		$message .= 'Ordningsvakten som tog bort ditt inlägg heter %REMOVERS_USERNAME% och gjorde följande notering:' . "\n\n";
		$message .= '-----' . "\n";
		$message .= '%REMOVAL_COMMENT%' . "\n";
		$message .= '-----' . "\n\n";
		$message .= 'Här är ditt inlägg:' . "\n";
		$message .= '-----' . "\n";
		$message .= '%CONTENT%' . "\n";
		$message .= '-----' . "\n\n";
		$message .= 'Har du några frågor så ta det med någon ordningsvakt, du hittar sådana i modulen "Inloggade Ordningsvakter" till höger.' . "\n";
		$message .= 'Detta är inte hela världen, men det är kanske bäst att du chillar lite extra i framtiden.' . "\n\n";
		$message .= '/Webmaster';
		$guestbook_message = array(
			'sender' => 2348,
			'recipient' => intval($post['author']),
			'message' => mysql_real_escape_string(str_replace(
				array('%TITLE%',      '%CONTENT%',      '%REMOVAL_COMMENT%',      '%REMOVERS_USERNAME%'),
				array($post['title'], $post['content'], $_GET['removal_comment'], $_SESSION['login']['username']),
				$message
			))
		);
		
		preint_r($guestbook_message);
		
		guestbook_insert($guestbook_message);
		log_admin_event('post removed', $post['removal_comment'], $_SESSION['login']['id'], $post['author'], $_GET['post_id']);
	}

	if($_GET['action'] == 'unremove_post' && forum_security(array('action' => 'unremove_post', 'post_id' => $_GET['post_id'])))
	{
		discussion_forum_remove_post(array('post_id' => $_GET['post_id'], 'mode' => 'unremove'));
	}
	
	if($_GET['action'] == 'vote' && login_checklogin() && is_numeric($_GET['thread_id']))
	{
		$query = 'UPDATE forum_read_posts SET has_voted = 1 WHERE thread_id = "' . $_GET['thread_id'] . '" AND user_id = "' . $_SESSION['login']['id'] . '" AND has_voted = 0';
		mysql_query($query);
		if(mysql_affected_rows() == 1)
		{
			$operand = ($_GET['vote'] == 'positive') ? '+' : '-'; 
			$query = 'UPDATE forum_posts SET score = score ' . $operand . ' 1 WHERE id = "' . $_GET['thread_id'] . '"';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
	}
	
	/* Thread subscriptions */
	if($_GET['action'] == 'add_thread_subscription')
	{
		$query = 'UPDATE forum_read_posts SET subscribing = "true" WHERE user_id = "' . $_SESSION['login']['id'] . '" AND thread_id = "' . $_GET['thread_id'] . '" LIMIT 1';
		mysql_query($query);
		
		$thread = array_pop(discussion_forum_post_fetch(array('post_id' => $_GET['thread_id'])));
		$_SESSION['forum']['subscriptions'][$_GET['thread_id']] = $thread;
	}
	if($_GET['action'] == 'remove_thread_subscription')
	{
		$query = 'UPDATE forum_read_posts SET subscribing = "false" WHERE user_id = "' . $_SESSION['login']['id'] . '" AND thread_id = "' . $_GET['thread_id'] . '" LIMIT 1';
		mysql_query($query);
		unset($_SESSION['forum']['subscriptions'][$_GET['thread_id']]);
	}
	
	/* Category subscriptions */
	if($_GET['action'] == 'add_category_subscription')
	{
		$query = 'UPDATE forum_category_visits SET subscribing = 1 WHERE user_id = "' . $_SESSION['login']['id'] . '" AND category_id = "' . $_GET['category_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

		$_SESSION['forum']['categories'][$_GET['category_id']]['subscribing'] = 1;
		discussion_forum_reload_category_subscriptions();
	}
	if($_GET['action'] == 'remove_category_subscription')
	{
		$query = 'UPDATE forum_category_visits SET subscribing = 0 WHERE user_id = "' . $_SESSION['login']['id'] . '" AND category_id = "' . $_GET['category_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

		$_SESSION['forum']['categories'][$_GET['category_id']]['subscribing'] = 0;
		discussion_forum_reload_category_subscriptions();
	}	
	
	/* Sticky or unsticky */
	if($_GET['action'] == 'setsticky' && is_privilegied('discussion_forum_sticky_threads') && is_numeric($_GET['post_id']))
	{
		$query = 'UPDATE forum_posts SET sticky = 1 WHERE id = "' . $_GET['post_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	if($_GET['action'] == 'unsticky' && is_privilegied('discussion_forum_sticky_threads') && is_numeric($_GET['post_id']))
	{
		$query = 'UPDATE forum_posts SET sticky = 0 WHERE id = "' . $_GET['post_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	/* Locking or unlocking threads... */
	if($_GET['action'] == 'lock_thread' && is_privilegied('discussion_forum_lock_threads') && is_numeric($_GET['post_id']))
	{
		$query = 'UPDATE forum_posts SET locked = 1 WHERE id = "' . $_GET['post_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	if($_GET['action'] == 'unlock_thread' && is_privilegied('discussion_forum_lock_threads') && is_numeric($_GET['post_id']))
	{
		$query = 'UPDATE forum_posts SET locked = 0 WHERE id = "' . $_GET['post_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	/* Renaming posts (threads). Fix a function for this later on... */
	if($_GET['action'] == 'rename_post' && is_privilegied('discussion_forum_rename_threads') && is_numeric($_GET['post_id']))
	{
		$query = 'UPDATE forum_posts SET title = "' . $_GET['new_title'] . '" WHERE id = "' . $_GET['post_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
?>