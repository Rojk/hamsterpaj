<?php
	function login_dologin($options)
	{	
		if(!isset($options['method']))
		{
			throw new Exception('No login method specified.');
		}
		
		if(isset($options['username']) && strtolower($options['username']) == 'borttagen')
		{
			header('Location: http://disneyworld.disney.go.com/wdw/index?bhcp=1');
			throw new Exception('Username CANNOT be "borttagen"!');
		}
		
		$query = 'SELECT id FROM login WHERE is_removed = 0';
		
		switch($options['method'])
		{
			case 'ghost':
				if(isset($options['username']))
				{
					$query .= ' AND username = "' . $options['username'] . '"';
				}
				else
				{
					throw new Exception('No username was set!');
				}
			break;
			
			case 'username_and_password':
				if(isset($options['username']) && isset($options['password']))
				{
					$options['password'] = utf8_decode($options['password']);
					$query .= ' AND password_version = 4 AND username = "' . $options['username'] . '" AND password = "' . hamsterpaj_password($options['password']) . '"';
				}
				else
				{
					throw new Exception('No username or password was set!');
				}
			break;
		
			default:
				throw new Exception('Invalid login method.');
		}
			
		$query .= ' LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) > 0)
		{
			$data = mysql_fetch_assoc($result);
			$user_id = $data['id'];
			
			// * Fetch neccessary data from login, userinfo, preferences and traffa-tables and unserialize...
			$_SESSION = array_merge($_SESSION, login_load_user_data($user_id, array(
				'login' => array('id', 'lastlogon', 'username', 'password', 'userlevel', 'regtimestamp', 'lastusernamechange', 'session_id', 'lastaction', 'lastip', 'regip', 'quality_level', 'quality_level_expire'),
				'userinfo' => array('contact1', 'contact2', 'gender', 'birthday', 'image', 'image_ban_expire', 'forum_signature', 'zip_code', 'forum_quality_rank', 'parlino_activated', 'cell_phone', 'firstname', 'surname', 'email', 'streetaddress', 'msn', 'visible_level', 'phone_ov', 'user_status', 'gbrss'),
				'preferences' => array('bubblemessage_visitors', 'allow_hotmessages', 'activate_current_action', 'enable_hetluft', 'randomizer', 'left_login_module', 'enable_shoutbox', 'module_states', 'module_order', 'forum_enable_smilies', 'forum_subscribe_on_create', 'forum_subscribe_on_post','gb_anti_p12'),
				'traffa' => array('firstname', 'profile_modules')
			), __FILE__, __LINE__));
			$_SESSION['module_states'] = unserialize($_SESSION['preferences']['module_states']);
			$_SESSION['module_order'] = unserialize($_SESSION['preferences']['module_order']);
			//$_SESSION['preferences']['forum_favourite_categories'] = unserialize($_SESSION['preferences']['forum_favourite_categories']);
			
			// * Update fields in logon related to the login...
			if($options['method'] != 'ghost')
			{
				$login_time = time();
				$query = 'UPDATE login SET lastlogon = ' . $login_time . ', lastip = "' . $_SERVER['REMOTE_ADDR'] . '", session_id = "' . session_id() . '" WHERE id = "' . $user_id . '" LIMIT 1';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__); 
				$_SESSION['login']['lastlogon'] = $login_time;
				$_SESSION['login']['lastip'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['login']['session_id'] = session_id();

				event_log_log('user_log_on');
				if($_SESSION['login']['lastlogon'] < strtotime(date('Y-m-d')))
				{
					event_log_log('user_unique_log_on');
				}
			}
						
			// * Set some special/initial parametrers...
			$_SESSION['cache']['lastupdate'] = 0;
			switch($options['method'])
			{
				case 'ghost':
					$_SESSION['ghost'] = true;
				break;
				
				case 'username_and_password':
					$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
					$_SESSION['login']['lastlogon'] = time();
				break;
			}
			
			// * Fetch guestbook notices...
			$guestbook_query = 'SELECT COUNT(id) AS unread FROM traffa_guestbooks WHERE recipient = ' . $user_id . ' AND `read` =  0 AND deleted = 0';
			$guestbook_result = mysql_query($guestbook_query) or report_sql_error($guestbook_query, __FILE__, __LINE__);
			$guestbook_data = mysql_fetch_assoc($guestbook_result);
			$_SESSION['notices']['unread_gb_entries'] = $guestbook_data['unread'];

			// * Fetch group notices...
			$_SESSION = array_merge($_SESSION, login_load_group_data($user_id, array('groups_members' => array('groupid'))));

			// * Fetch friends notices...
			$_SESSION['friends'] = friends_fetch_online_smart(array('user_id' => $user_id));
			
			// * Fetch visitors from "my visitors"
			$query  = 'SELECT DISTINCT(uel.remote_user_id) AS id, uel.timestamp, l.username';
			$query .= ' FROM user_event_log AS uel, login AS l, userinfo AS u';
			$query .= ' WHERE uel.action = "profile_visit" AND uel.user = "' . $user_id . '" AND l.id = uel.remote_user_id AND (u.image = 1 OR u.image = 2) AND u.userid = uel.remote_user_id';
			$query .= ' GROUP BY uel.remote_user_id ORDER BY timestamp DESC LIMIT 8';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			$_SESSION['visitors_with_image'] = array();
			while($data = mysql_fetch_assoc($result))
			{
				$_SESSION['visitors_with_image'][] = $data;
			}
			
			// * Fetch privilegies...
			$query = 'SELECT privilegie, value FROM privilegies WHERE user = "' . $_SESSION['login']['id'] . '"';
			$result = mysql_query($query);
			while($data = mysql_fetch_assoc($result))
			{
				$_SESSION['privilegies'][$data['privilegie']][is_numeric($data['value']) ? intval($data['value']) : $data['value']] = true;
			}
			
			// * Log the logon to the database...
			$query  = 'INSERT INTO login_log (user_id, logon_time, impressions, ip, ghost)';
			$query .= ' VALUES(' . $user_id . ', ' . time() . ', 0, ' . ip2long($_SERVER['REMOTE_ADDR']) . ', "' . ($options['method'] == 'ghost' ? 'YES' : 'NO') . '")';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			// * Cache some info about the users visits to categories. This is used to calculate new threads and category-subscriptions
			$query = 'SELECT * FROM forum_category_visits WHERE user_id = "' . $user_id . '"';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			while($data = mysql_fetch_assoc($result))
			{
				$_SESSION['forum']['categories'][$data['category_id']] = $data;
			}
		}
		else if($options['method'] == 'username_and_password')
		{
			$query = 'SELECT id FROM login WHERE password_version = 3 AND username = "' . $options['username'] . '" AND password = "' . sha1($options['password'] . PASSWORD_SALT) . '" LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			if(mysql_num_rows($result) == 1)
			{
				throw new Exception('<h2>Du använder ett lösenord baserat på det gamla lösenordssystemet. Av säkerhetsskäl måste du byta, det gör du <a href="/installningar/renew_password.php" style="font-weight: bold">på den här sidan &raquo;</a></h2>');
			}
			else
			{
				throw new Exception('Det gick inte att logga in med de uppgifter du angav. Detta beror antingen på att du inte angivit korrekt användarnamn och lösenord, eller att användarnamnet inte finns.<br /><br />Har du glömt ditt lösenord? Då finns det inte mycket att göra :(');
			}
		}
		else
		{
			throw new Exception('Login failed: User not found or password incorrect.');
		}
	}
	
	function login_checklogin()
	{
		if(isset($_SESSION['login']['id']))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	function is_privilegied($privilegie, $item_id = 'ANY')
	{
		if(isset($_SESSION['privilegies']['igotgodmode'][0]))
		{
			return true;
		}
		return ($item_id == 'ANY') ? isset($_SESSION['privilegies'][$privilegie]) : (isset($_SESSION['privilegies'][$privilegie][$item_id]) || isset($_SESSION['privilegies'][$privilegie][0]));
	}
	
	function userblock_check($owner, $blocked)
	{
		if(is_privilegied('use_ghosting_tools'))
		{
			return 0;
		}
		$query = 'SELECT ownerid FROM userblocks WHERE ownerid = ' . $owner . ' AND blockedid = ' . $blocked . ' LIMIT 1';
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
function cache_update_all()
{
	cache_update_bookmarks();
	cache_update_lastaction();
	cache_update_groups();
	cache_update_forum_notices();
	if ($_SESSION['login']['userlevel'] == 3 || $_SESSION['login']['userlevel'] == 4)
	{
		cache_update_forum_reported();
	}

	discussion_forum_reload_all();
	cache_update_photo_comments();
}

function cache_update_photo_comments()
{
	$query = 'SELECT SUM(unread_comments) AS unread_comments FROM user_photos WHERE user = "' . $_SESSION['login']['id'] . '"';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$data = mysql_fetch_assoc($result);
	$_SESSION['cache']['unread_photo_comments'] = $data['unread_comments'];
}

function cache_update_forum_notices()
{
	$_SESSION['cache']['forum_notices'] = forum_notices_count();
}

function cache_update_bookmarks()
{
// Det här har väl med gamla forumet att göra !?!?!?
/*
	$notices = 0;
	$query = 'SELECT t.posts - n.posts_last AS diff ';
	$query .= 'FROM forum_notices AS n, forum_threads AS t ';
	$query .='WHERE n.user = "' . $_SESSION['login']['id'] . '" AND n.status != 0 AND t.id = n.thread ';
	$result = mysql_query($query) or die(report_sql_error($query));
	while($data = mysql_fetch_assoc($result))
	{
		if($data['diff'] > 0 && $data['diff'] < 1000)
		{
			$notices += $data['diff'];
		}
	}
	$query = 'SELECT COUNT(*) AS replies FROM forum_replies WHERE reply_to = "' . $_SESSION['login']['id'] . '"';
	$result = mysql_query($query) or die(report_sql_error($query));
	$data = mysql_fetch_assoc($result);
	$notices += $data['replies'];

	$_SESSION['cache']['unread_forum_bookmarks'] = $notices;
*/
/*	if($notices == 1)
	{
		$_SESSION['bubblemessage'][] = 'Du har en oläst forumsnotis!<br />Klicka <a href"/forum_new/notices.php">här</a> för att kolla dina forumnotiser!';
	}
	elseif($notices > 1)
	{
		$_SESSION['bubblemessage'][] = 'Du har ' . $notices . ' olästa forumsnotiser!<br />Klicka <a href"/forum_new/notices.php">här</a> för att kolla dina forumnotiser!';
	}
*/
}

function cache_update_lastaction()
{
	if (!isset($_SESSION['ghost'])) {
		$sql = 'UPDATE login SET lastaction = "' . time() . '" WHERE id = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($sql) or die('Ett fel uppstod när databasfrågan kördes: ' . mysql_error());
		$_SESSION['cache']['lastupdate'] = time();
		$_SESSION['login']['lastaction'] = time();
	}
}

function login_remove_user($user_id)
{
	$sql = 'SELECT username FROM login WHERE id = ' . $user_id . ' LIMIT 1';
	$result = mysql_query($sql);
	$data = mysql_fetch_assoc($result);
	
	if (strtolower($data['username']) == 'borttagen')
	{
		throw new Exception('Inte så hastigt!<br />Det räcker med att ta bort användaren <strong>en</strong> gång :D');
	}
	$query = 'UPDATE login SET lastusernamechange = ' . time() . ', lastusername = "' . $data['username'] . '", username = "Borttagen", is_removed = 1 WHERE id = "' . $user_id . '" LIMIT 1';
	mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
}

function session_add_key($sessid, $key, $value)
{
	$file_handle = fopen('/var/lib/php/session2/sess_' . $sessid, 'a');
	fwrite($file_handle, $key . '|s:' . strlen($value) . ':"' . $value . '";');
}

	function session_unserialize($data)
	{
		$vars=preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\|/', $data ,-1 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		for($i=0; $vars[$i]; $i++)
		{
			$result[$vars[$i++]]=unserialize($vars[$i]);
		}
		return $result;
	}

	function session_serialize($string)
	{
		$retstring = '';
		foreach($string as $name => $value)
		{
			$retstring .= $name . '|' . serialize($string[$name]);
		}
		 return $retstring;
	}

	function session_load($session_id)
	{
		$data = file_get_contents('/var/lib/php/session2/sess_' . $session_id);
		return session_unserialize($data);
	}

	function session_save($session_id, $data)
	{
		$data = session_serialize($data);
		$file_handle = fopen('/var/lib/php/session2/sess_' . $session_id, 'w');
		fwrite($file_handle, $data);
		fclose($file_handle);
	}

	/* Takes an array and injects it into the SESSION array. Replaces the values in SESSION if key already exists. 
		Input array MUST be in format $newdata['key1']['key2'] = 'Value'. Eg. $newdata['userinfo']['gender'] = 'P';
		Does ONLY support this format, plain variables or multi-multi-dimensional arrays will probably break the session values. */
	function session_merge($newdata)
	{
		foreach($newdata AS $table => $fields)
		{
			$_SESSION[$table] = array_merge($_SESSION[$table], $fields);
		}
	}

	function login_load_user_data($userid, $data, $file = null, $line = null)
	{
		if(!is_numeric($userid))
		{
			to_logfile('debug', __FILE__, __LINE__, 'login_load_user_data() called without userid', print_r(debug_backtrace(), true));
		}
		$query = 'SELECT ';
		foreach($data AS $table => $fields)
		{
			foreach($fields AS $field_name)
			{
				$query .= $table . '.' . $field_name . ' AS ' . $table . '__' . $field_name . ', ';
				if($table == 'userinfo' && $field_name == 'zip_code')
				{
					$zip_codes = 'active';
					$query .= 'zip_codes.spot AS userinfo__geo_location, zip_codes.x_rt90 AS userinfo__x_rt90, zip_codes.y_rt90 AS userinfo__y_rt90, ';
				}
			}
		}
		$query .= 'null '; /* We select a NULL value so that the last comma doesn't create a syntax error */
		$query .= 'FROM ';

		$walked_tables = 0;
		if($zip_codes == 'active')
		{
			$query .= 'zip_codes, ';
		}
		foreach(array_keys($data) AS $table)
		{
			$query .= $table;
			$walked_tables++;
			if(count($data) > $walked_tables)
				{
				$query .= ',';
			}
			$query .= ' ';
		}

		$query .= 'WHERE ';

		$walked_tables = 0;
		foreach(array_keys($data) AS $table)
		{
		if($table == 'login')
			{
				$query .= 'login.id = ' . $userid . ' ';
			}
			else
			{
				$query .= $table . '.userid = ' . $userid . ' ';
			}
			$walked_tables++;
			if(count($data) > $walked_tables)
			{
				$query .= 'AND';
			}
			$query .= ' ';
		}
		if($zip_codes == 'active')
		{
			$query .= 'AND zip_codes.zip_code = userinfo.zip_code ';
		}
		$query .= 'LIMIT 1';

		$file = (isset($file)) ? $file : __FILE__;
		$line = (isset($line)) ? $line : __LINE__;
		$result = mysql_query($query) or die(report_sql_error($query, $file, $line));
		if(mysql_num_rows($result) == 0)
		{
			return false;
		}
		$data = mysql_fetch_assoc($result);
		unset($data['NULL']);
		foreach($data AS $key => $value)
		{
			$explosion = explode('__', $key);
			$return[$explosion[0]][$explosion[1]] = $value;
		}

		return $return;
	}
	function login_save_user_data($userid, $data, $debug = null)
	{
			/* To use this function: 
			$newdata['login'] = array('username' => 'pelle');
			$newdata['userinfo'] = array('gender' => 'P', 'hetluft' => 1, 'forum_signature' => 'Min dator är \\"mirkkig\\"');
			$newdata['preferences'] = array('forum_posts_per_page' => 25);
			login_save_user_data($_SESSION['login']['id'], $newdata);
			-- Plase make sure that double qoutes ARE escaped! --
		*/ 
	
		if($userid == 3)
		{
			log_to_file('login_save_user_data', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'Saving user data', print_r($data, true));
		}

		foreach($data AS $tablename => $fields)
		{
			$query = 'UPDATE ' . $tablename . ' SET ';
			$fieldwalk = 0;
			foreach($fields AS $field => $value)
			{
				$query .= $field . ' = "' . $value . '"';
				$fieldwalk++;
				if($fieldwalk < count($fields))
				{
					$query .= ',';
				}
				$query .= ' ';
			}
			$query .= 'WHERE ';
			if($tablename == 'login')
			{
				$query .= 'id ';
			}
			else
			{
				$query .= 'userid ';
			}
			$query .= '= ' . $userid;
			mysql_query($query) or die(report_sql_error($query,  __FILE__, __LINE__));
		}
	}
	/* function to load the groups a user is a member of */
	function login_load_group_data($userid, $data)
	{
		$query = 'SELECT ';
		foreach($data AS $table => $fields)
		{
			foreach($fields AS $field_name)
			{
				$query .= $table . '.' . $field_name . ' AS ' . $table . '__' . $field_name . ', ';
			}
		}
		$query .= 'null '; /* We select a NULL value so that the last comma doesn't create a syntax error */
		$query .= 'FROM ';

	foreach(array_keys($data) AS $table)
		{
			$query .= $table;
			$query .= ' ';
		}
		$query .= 'WHERE ';
		$query .= $table . '.userid = ' . $userid . ' ';
		$query .= 'AND ' . $table . '.approved = 1';

		$result = mysql_query($query) or die(report_sql_error($query));
		$i = 0;
		while($data = mysql_fetch_assoc($result))
		{
			unset($data['NULL']);
			
			foreach($data AS $key => $value)
			{
				$explosion = explode('__', $key);
				$return[$explosion[0]][$value] = $value;
        $i++;
			}
		}
		
		if(count($return) == 0)
		{
			$return = array('groups_members' => array());
		}
		
		return $return;
	}


function to_logfile($category, $file, $line, $description, $id = null, $id_2 = null)
{
	log_to_file('deprecated', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'Function called: to_logfile()');
	/* Allowed categories are:
		* debug - Outdated functions shall use this, so we can see if they are still in use.
		* admin - This is used to log all admin actions
		* notice - 
		* error - Errors that needs to be fixed, unexpected behavior from scripts, files missing
	*/
/*
	$output = date('Y-m-d H:i:s') . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_SESSION['login']['id'] . "\t";
	$output .= $file . "\t" . $line . "\t" . $description . "\t" . $id . "\t" . $id_2 . "\t" . $_SERVER['HTTP_REFERER'] . "\t" . $_SRVER['REQUEST_URI'] . "\n";
	$handle = fopen(PATHS_LOGS . $category . '/' . date('Y-m-d') . '.log', 'a');
	fwrite($handle, $output);
	fclose($handle);
*/
	log_to_file('old_log_system', LOGLEVEL_DEBUG, $file, $line, $description, $id . "\t" . $id_2);
}


function cache_update_groups()
{
	$_SESSION['cache']['group_notices'] = array();
	$_SESSION['cache']['unread_group_notices'] = 0;

	if(!empty($_SESSION['groups_members']))
	{
		$query = 'SELECT groups_list.groupid, groups_list.message_count, groups_members.read_msg, groups_list.name FROM groups_members, groups_list ';
		$query .= 'WHERE groups_members.groupid IN(' . implode(', ', $_SESSION['groups_members']) . ') AND groups_list.groupid = groups_members.groupid';
		$query .= ' AND groups_members.userid =' . $_SESSION['login']['id'] . ' AND groups_members.notices = "Y"';
		$result = mysql_query($query) or report_sql_error($query);
		
		while($data = mysql_fetch_assoc($result))
		{
			$message_count = $data['message_count'] - $data['read_msg'];
			$_SESSION['cache']['unread_group_notices'] += $message_count;
			$_SESSION['cache']['group_notices'][$data['groupid']] = array('unread_messages' => $message_count, 'title' => $data['name'], 'groupid' => $data['groupid']);
		}
	}
}

function cache_update_forum_reported()
{
	log_to_file('deprecated', LOGLEVEL_INFO, __FILE__, __LINE__, 'cache_update_forum_reported()');
}

function login_page_impressions()
{
	$query = 'UPDATE login_log SET impressions = impressions + 1 WHERE user_id = "' . $_SESSION['login']['id'] . '" && logon_time = "';
	$query .= $_SESSION['login']['lastlogon'] . '" LIMIT 1';
	//mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
}

function login_onlinestatus($lastaction, $lastrealaction)
{
	$onlinestatus['offline']['handle']	= 'offline';
	$onlinestatus['offline']['label']	= '';
	$onlinestatus['idle']['handle']	= 'idle';
	$onlinestatus['idle']['label']	= '(Inte vid datorn)';
	$onlinestatus['online']['handle']	= 'online';
	$onlinestatus['online']['label']	= '(Online)';
	$time = time();
	if($lastaction < $time - 2 * 60 )
	{
		return $onlinestatus['offline'];
	}
	else
	{
		if($lastrealaction > $time - 10 * 60 )
		{
			return $onlinestatus['online'];
		}
		else
		{
			return $onlinestatus['idle'];
		}
	}
}

?>