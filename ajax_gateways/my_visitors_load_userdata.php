<?php
	require('../include/core/common.php');

	if(!login_checklogin())
	{
		die('Error1');
	}

	$fetch_for_userid = ((isset($_GET['for_userid']) && is_numeric($_GET['for_userid'])) ? (int)$_GET['for_userid'] : $_SESSION['login']['id']);
	
	if($_SESSION['login']['id'] == $fetch_for_userid)
	{
		$fetch_for_username = 'dig';
	}
	else
	{
		$query = 'SELECT username'
		       . ' FROM login'
		       . ' WHERE id = ' . $fetch_for_userid
		       . ' LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query);
		if(mysql_num_rows($result) > 0){
			$data = mysql_fetch_assoc($result);
			$fetch_for_username = $data['username'];
		}
		else
		{
			die('Error: User not found.');
		}
	}

	if(isset($_GET['id']) && is_numeric($_GET['id']) && (int) $_GET['id'] > 0)
	{		
		$query  = 'SELECT l.username, u.user_status, u.gender, u.birthday, z.spot, z.x_rt90, z.y_rt90, uv.count AS total_visits, uv.timestamp AS last_visit, GROUP_CONCAT(DISTINCT f.flag) AS flags';
		$query .= ' FROM user_visits AS uv, userinfo AS u, zip_codes AS z, login AS l';
		$query .= ' LEFT OUTER JOIN user_flags AS f ON l.id = f.user';
		$query .= ' WHERE l.id = ' . $_GET['id'] . ' AND u.userid = l.id AND z.zip_code = u.zip_code AND uv.item_id = l.id AND uv.user_id = ' . $fetch_for_userid . ' AND uv.type = "profile_visit"';
		$query .= ' GROUP BY l.id'; 
		$query .= ' LIMIT 1';

		$result = mysql_query($query) or report_sql_error($query);
		if(mysql_num_rows($result) == 0)
		{
			die('Error2');
		}
		
		$data = mysql_fetch_assoc($result);
		
		$output = array();
		$output['age'] = (($data['birthday'] == '0000-00-00') ? 0 :  date_get_age($data['birthday']));
		/* Some strange conditions... */
		if($output['age'] == ''){ $output['age'] = 0; }
		$output['username'] = htmlentities($data['username']); // Never trust the user (eg. Ekonomi-pär and such).
		$output['have_visited'] = htmlentities($fetch_for_username);
		$output['gender'] = $data['gender'];
		$output['location'] = htmlentities(trim($data['spot']), ENT_QUOTES, 'UTF-8'); // Hmm, I like to escape data...
		$output['total_visits'] = $data['total_visits'];
		$output['last_visit'] = htmlentities(strtolower(fix_time($data['last_visit'])), ENT_QUOTES, 'UTF-8');
		$output['x_rt90'] = $data['x_rt90'];
		$output['y_rt90'] = $data['y_rt90'];
		$output['user_status'] = addslashes(htmlentities($data['user_status'], ENT_QUOTES, 'UTF-8'));
		if($data['flags'] != '')
		{
			$output['flag_array'] = array();
			$flags = explode(',', $data['flags']);
			$flag_count = 0;
			foreach($flags as $flag)
			{
				if(!is_numeric($flag)){ die('Nähedu, det gick inte!'); }
				if((int) $flag > 0 && $flag_count++ < 6){
					
					/* Do not change $flag_query unnecessarily, we're using query_cache()! */
					$flag_query  = 'SELECT handle';
					$flag_query .= ' FROM user_flags_list';
					$flag_query .= ' WHERE id=' . $flag;
					$flag_query .= ' LIMIT 1';
					
					$flag_data = query_cache(array('query' => $flag_query, 'max_delay' => 86400, 'category' => 'user_flags')) or report_sql_error($flag_query);
					$output['flag_array'][] = $flag_data[0]['handle'];
				}
			}
			unset($flag);
			
			$output['flags'] = '["' . implode('", "', $output['flag_array']) . '"]';
		}
		else
		{
			$output['flags'] = '[]';
		}

		$output['everything']  = '"age": ' . $output['age'];
		$output['everything'] .= ', "username": "' . $output['username'] . '"';
		$output['everything'] .= ', "have_visited": "' . $output['have_visited'] . '"';
		$output['everything'] .= ', "gender": "' . $output['gender'] . '"';
		$output['everything'] .= ', "location": "' . $output['location'] . '"';
		$output['everything'] .= ', "total_visits": ' . $output['total_visits'];
		$output['everything'] .= ', "last_visit": "' . $output['last_visit'] . '"';
		$output['everything'] .= ', "x_rt90": ' . $output['x_rt90'];
		$output['everything'] .= ', "y_rt90": ' . $output['y_rt90'];
		$output['everything'] .= ', "user_status": "' . $output['user_status'] . '"';
		$output['everything'] .= ', "user_flags": ' . $output['flags'];

		echo utf8_encode('{ ' . $output['everything'] . ' }');
		//echo '{ ' . $output['everything'] . ' }';
	}else{
		echo 'Eko! Eko! Hakker!';
	}
?>