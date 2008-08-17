<?php

	/*	This function return an array of discussion ids along with the title
			and number of unread posts. The field 'types' in $options is an array
			of the kinds of notices that should be returned. Insert the types that you
			want receive, 'watches', 'responses', 'notices' and 'subscriptions'.
			*/
	function forum_notices_get($options)
	{
		$query['watches'] = 'SELECT id, (a.posts - b.posts) AS unread FROM';
		$query['watches'] .= ' ( (SELECT id, posts';
		$query['watches'] .= ' FROM discussion_watches w, discussions d';
		$query['watches'] .= ' WHERE w.user_id = ' . $_SESSION['login']['id'];
		$query['watches'] .= ' AND w.discussion_id = d.id';
		$query['watches'] .= ' AND d.deleted != 1) AS a';
		$query['watches'] .= ' LEFT OUTER JOIN';
		$query['watches'] .= ' (SELECT discussion_id, posts';
		$query['watches'] .= ' FROM posts_read';
		$query['watches'] .= ' WHERE user_id = ' . $_SESSION['login']['id'] . ') AS b';
		$query['watches'] .= ' ON';
		$query['watches'] .= ' a.id = b.discussion_id)';
		$query['watches'] .= ' WHERE (a.posts - b.posts) > 0';

		$query['responses'] = 'SELECT p.id post_id, d.id as id, title, count(post_id) unread FROM discussions d, posts p, notices n';
		$query['responses'] .= ' WHERE n.user_id = ' . $_SESSION['login']['id'] . ' AND p.id = n.post_id';
		$query['responses'] .= ' AND d.id = p.discussion_id AND n.type = "response"';
		$query['responses'] .= ' AND d.deleted != 1';
		$query['responses'] .= ' GROUP BY d.id ORDER BY p.id ASC';

		$query['notices'] = 'SELECT p.id post_id, d.id as id, title, count(post_id) unread FROM discussions d, posts p, notices n';
		$query['notices'] .= ' WHERE n.user_id = ' . $_SESSION['login']['id'] . ' AND p.id = n.post_id';
		$query['notices'] .= ' AND d.id = p.discussion_id AND n.type = "notice"';
		$query['notices'] .= ' AND d.deleted != 1';
		$query['notices'] .= ' GROUP BY d.id ORDER BY p.id ASC';
		
		
		$query['subscriptions'] = 'SELECT id, title, label, (a.posts - b.posts) as unread';
		$query['subscriptions'] .= ' FROM';
		$query['subscriptions'] .= ' ((SELECT d.id AS id, d.title AS title, t.label AS label, d.posts AS posts';
		$query['subscriptions'] .= ' FROM discussions d, object_tags ot, tags t, discussion_subscriptions s';
		$query['subscriptions'] .= ' WHERE s.user_id = ' . $_SESSION['login']['id'];
		$query['subscriptions'] .= ' AND s.tag_id = ot.tag_id';
		$query['subscriptions'] .= ' AND ot.object_type = "discussion"';
		$query['subscriptions'] .= ' AND ot.tag_id = t.id';
		$query['subscriptions'] .= ' AND ot.reference_id = d.id';
		$query['subscriptions'] .= ' AND d.deleted != 1';
		$query['subscriptions'] .= ' GROUP BY d.id) AS a';
		$query['subscriptions'] .= ' LEFT OUTER JOIN';
		$query['subscriptions'] .= ' (SELECT discussion_id, posts';
		$query['subscriptions'] .= ' FROM posts_read';
		$query['subscriptions'] .= ' WHERE user_id = ' . $_SESSION['login']['id'] . ') AS b';
		$query['subscriptions'] .= ' ON a.id = b.discussion_id)';
		$query['subscriptions'] .= ' WHERE (a.posts - b.posts) > 0';
								
		//Set user id to logged in user if not set
		if(isset($options['user_id']))
		{
			$user_id = $options['user_id'];
		}
		else
		{
			$user_id = $_SESSION['login']['id'];
		}

		$return = array();
		foreach($options['types'] as $type)
		{
			/*	Send query and fetch result in the same way for all kinds of notices
					This require that all the sql queries above deliver the same
					column names in the response, that is: 'id', 'title' and 'unread'
			*/
/*
			if(5 == $_SESSION['login']['userlevel'])
			{
				echo '<p>' . $query[$type] . '</p>';
			}

*/
			$result = mysql_query($query[$type]) or die(report_sql_error($query));
	 		while($data = mysql_fetch_assoc($result))
			{
				$return[$type]['discussions'][$data['id']]['id']		= $data['id'];
				$return[$type]['discussions'][$data['id']]['post_id']	= $data['post_id'];
				$return[$type]['unread'] += $data['unread'];
			}
		}
/*
		if(in_array($_SESSION['login']['id'], array(685862, 644314)))
		{
			preint_r($return);
		}
*/
		return $return;
	}
	
	function forum_notices_count($user)
	{
		$fetch['types'] = array('watches', 'responses', 'notices', 'subscriptions');
		$notices = forum_notices_get($fetch);
		return $notices['watches']['unread'] + $notices['responses']['unread'] + $notices['notices']['unread'] + $notices['subscriptions']['unread'];
	}

?>
