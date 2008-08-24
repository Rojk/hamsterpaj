<?php
	require_once('../include/core/common.php');
	
	if ( ! isset($_GET['user_id']) || ! is_numeric($_GET['user_id']) )
	{
		$user_id = 65654;
	}
	else
	{
		$user_id = $_GET['user_id'];
	}
	
	// user
	$query = 'SELECT l.username, l.lastaction, l.lastlogon, u.gender, u.birthday, u.image, u.user_status, u.profile_theme, u.gb_entries, z.spot, u.presentation_text, p.gb_anti_p12';
	$query .= ' FROM login AS l, userinfo AS u, zip_codes AS z, preferences AS p';
	$query .= ' WHERE l.id = ' . $user_id . ' AND u.userid = l.id AND z.zip_code = u.zip_code AND p.userid = l.id';
	$query .= ' AND l.is_removed = 0';
	$query .= ' LIMIT 1';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
	$user = mysql_fetch_assoc($result);

	$gender = array('m' => 'P', 'f' => 'F');
	$gender = (isset($gender[$user['gender']])) ? $gender[$user['gender']] : '';
	$birthday = ($user['birthday'] != '0000-00-00') ? date_get_age($user['birthday']) : '';
	$location = (empty($user['spot'])) ? '' : ' från ' . $user['spot'];
	$online = ($user['lastaction'] > time() - 600) ? 'true' : 'false';
	
	// user flags	
	$query = 'SELECT ufl.* FROM user_flags AS uf, user_flags_list AS ufl';
	$query .= ' WHERE user = ' . $user_id . ' AND ufl.id = uf.flag';
	$query .= ' LIMIT 5';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
	$flags = array();
	while ( $data = mysql_fetch_assoc($result) )
	{
		$flags[] = $data;
	}
	
?>{
	username: '<?php echo $user['username']; ?>',
	user_id: '<?php echo $user_id; ?>',
	user_info: '<?php echo trim($gender . $birthday . $location); ?>',
	status: '<?php echo $user['user_status']; ?>',
	online: <?php echo $online; ?>,
	flags: {
		<?php foreach ( $flags as $flag ): ?>
		'<?php echo $flag['title']; ?>': 'http://images.hamsterpaj.net/user_flags/<?php echo $flag['handle']; ?>.png',
		<?php endforeach; ?>
	}
}