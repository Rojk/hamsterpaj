<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('admin', 'forumstatus');
	
	if(!is_privilegied('forum_userlabel_admin'))
	{
		header('location: /');
		die();
	}
	
	$out .= '<h1>Ändra forumsstatus</h1> <br style="clear: both;" />';
	$out .= rounded_corners_top();
	$out .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=view">' . "\n";
	$out .= '<br /><h2> Användarnamn</h2><input type="text" name="username" />' . "\n";
	$out .= '<input type="submit" value="Visa" class="button_60" />';
	$out .= '</form>' . "\n";
	$out .= '<br style="clear: both;" />';
	$out .= '<a href="?action=list">Visa alla forumstatusar</a>' . "\n"; 
	
	switch(isset($_GET['action']) ? $_GET['action'] : 'home')
	{			
	case 'view':		
		if(isset($_POST['username']) && strtolower($_POST['username']) != 'borttagen')
		{
			$query = 'SELECT l.id, l.username, u.forum_userlabel FROM login AS l, userinfo AS u WHERE username LIKE "' .  str_replace('_', '\\_', $_POST['username']) . '" AND l.id = u.userid';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			if(mysql_num_rows($result) == 1)
			{
				$data = mysql_fetch_assoc($result);
				$user_id = $data['id'];
				$username = $data['username'];
				$forum_userlabel = $data['forum_userlabel'];
			
				$out .= '<h2>Användare: ' . $username . '</h2>' . "\n";
				$out .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=change">' . "\n";
				$out .= '<input type="text" name="forum_userlabel" value="' . $forum_userlabel . '" />' . "\n";
				$out .= '<input type="hidden" name="user_id" value="' . $user_id . '" />' . "\n";
				$out .= '<input type="submit" value="Ändra" class="button_60" />';
				$out .= '</form>' . "\n";
			}
			else
			{
				$out .= 'Användarnamnet finns inte, försök igen.' . "\n";
			}			
		}		
		break;
		
	case 'list':
		$query = 'SELECT l.id, u.userid, l.username, u.forum_userlabel FROM login AS l, userinfo AS u WHERE l.id = u.userid AND u.forum_userlabel != "" AND l.is_removed = "0" ORDER BY u.forum_userlabel';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$out .= '<table>' . "\n";
		$out .= '<tr>' . "\n";
		$out .= '<th>Användarnamn</th>' . "\n";
		$out .= '<th>Forumstatus</th>' . "\n";
		$out .= '</tr>' . "\n";
		while($data = mysql_fetch_assoc($result))
		{				
			$userlabel = $data['forum_userlabel'];
			if(!isset($assigned[$userlabel]))
			{
				$assigned[$userlabel] = '%VALUE%';
				$border .= 'style="border-top: solid;"' . "\n";
			}
			$out .= '<tr>' . "\n";
			$out .= '<td ' . $border . '>' . $data['username'] . '</td>' . "\n";
			$out .= '<td ' . $border . '>' . $userlabel . '</td>' . "\n";
			$out .= '</tr>' . "\n";
			$border = "";
		}
		$out .= '</table>' . "\n";
		break;
		
	case 'change':
		$forum_userlabel = $_POST['forum_userlabel'];
		$user_id = $_POST['user_id'];
		
		$query = 'UPDATE userinfo SET forum_userlabel = "' . $forum_userlabel . '" WHERE userid = ' . $user_id . ' LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$out .= 'Ändrat';
	}
	
	$out .= rounded_corners_bottom();
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>	