<?php

	require('../include/core/common.php');
	$ui_options['current_menu'] = 'admin';
	$ui_options['stylesheets'][] = 'admin_log.css';
	$ui_options['menu_path'] = array('admin', 'log_view');
	include($hp_includepath . 'admin-functions.php');
	
	if(!is_privilegied('use_ghosting_tools'))
	{
		header('location: /');
		die();
	}
	
	ui_top($ui_options);

	if (isset($_POST['username']))
	{
		preint_r($_POST);
	}

	$numeric = array_key_exists('numeric',$_POST) == 1;
	echo rounded_corners_top();
	echo '<h1 style="margin-top: 0px; padding-top: 2px;">Logg över administrativa händelser</h1>';
	echo '<h3>Filtrera på valfria fält</h3><br />';

	echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	echo '<table class="admin_log" >';
	echo '	<tr>';
	echo '		<td class="event" >Händelse</td>';
	echo '		<td class="comment">Info</td>';
	echo '		<td class="time">Tidpunkt</td>';
	echo '		<td class="admin">Admin</td>';
	echo '		<td class="user">Användare</td>';
	echo '		<td class="item">Item id</td>';
	echo '	</tr>';
	echo '	<tr>';
	echo '		<td class="event" ><input type="text" name="event" value="' . $_POST['event'] . '" /></td>';
	echo '		<td class="comment" ><input type="text" name="value" value="' . $_POST['value'] . '" /></td>';
	echo '		<td><input type="text" name="starttime" value="' . $_POST['starttime'] . '" /></td>';
	echo '		<td><input type="text" name="admin" value="' . $_POST['admin'] . '" /></td>';
	echo '		<td><input type="text" name="user" value="' . $_POST['user'] . '" /></td>';
	echo '		<td><input type="text" name="item" value="' . $_POST['item'] . '" /></td>';
	echo '	</tr>';
	echo '	<tr>';
	echo '		<td></td>';
	echo '		<td></td>';
	echo '		<td><input type="text" name="endtime" value="' . $_POST['endtime'] . '" /></td>';
	echo '		<td></td>';
	echo '		<td></td>';
	echo '		<td></td>';
	echo '	</tr>';
	echo '</table>';
	echo '<input type="checkbox" name="numeric" value="true" id="chk_numeric_ids" ' . ( $numeric ? 'checked="checked"' : '') . ' />';
	echo '<label for="chk_numeric_ids"> Id-nummer istället för namn </label>';
	echo '<input type="checkbox" name="rubish" value="true" id="chk_rubish" ' . ( array_key_exists('rubish',$_POST) ? 'checked="checked"' : '') . ' />';
	echo '<label for="chk_rubish"> Visa inte avatarer och namnbyten </label>';
	echo '<br /><br /><input type="submit" name="submit" value="Filtrera" class="button_60" />';
	echo '</form>';
	echo '<hr />';
	
	if(has_value($_POST['starttime']))
	{
		$starttime = strtotime($_POST['starttime']);
	}
	if(has_value($_POST['endtime']))
	{
		$endtime = strtotime($_POST['endtime']);
	}
	
	$query = 'SELECT * FROM admin_event';
	$query .= ' WHERE';
	$query .= has_value($_POST['event']) ? ' event like "%' . $_POST['event'] . '%"':'';
	$query .= has_value($_POST['value']) ? ' AND value like "%' . $_POST['value'] . '%"':'';
	$query .= has_value($_POST['admin']) ? ' AND admin_id = ' . (is_numeric($_POST['admin']) ? $_POST['admin'] : get_userid_by_name($_POST['admin'])) : '';
	$query .= has_value($_POST['user']) ? ' AND user_id = ' . (is_numeric($_POST['user']) ? $_POST['user'] : get_userid_by_name($_POST['user'])) : '';
	$query .= $starttime ? ' AND timestamp > ' . $starttime : '';
	$query .= $endtime ? ' AND timestamp < ' . $endtime : '';
	$query .= has_value($_POST['item']) ? ' AND item_id =' . $_POST['item'] : '';
	$query .= array_key_exists('rubish',$_POST) ? ' AND event NOT IN ("avatar validated", "username changed")' : '';
	$query .= ' ORDER BY timestamp desc LIMIT 0, 300';
	$query = preg_replace('/WHERE AND/', 'WHERE', $query);
	$query = preg_replace('/WHERE ORDER/', 'ORDER', $query);
	$result = mysql_query($query) or die(report_sql_error($query));
//	echo '<p>sql-frågan: ' . $query . '</p>';
	echo '<table class="admin_log">';
	echo '	<tr>';
	echo '		<td class="event" >Händelse</td>';
	echo '		<td class="comment" >Info</td>';
	echo '		<td class="time" >Tidpunkt</td>';
	echo '		<td class="admin" >Admin</td>';
	echo '		<td class="user" >Användare</td>';
	echo '		<td class="item" >Item id</td>';
	echo '	</tr>';
	while($data = mysql_fetch_assoc($result))
	{
		echo '<tr>';
		echo '	<td class="event" >' . $data['event'] . '</td>';
		echo '	<td class="comment" >' . $data['value'] . '</td>';
		echo '	<td>' . fix_time($data['timestamp']) . '</td>';
		echo '	<td>' . ($numeric ? $data['admin_id'] : get_username_by_id($data['admin_id'])) . '</td>';
		echo '	<td>' . ($numeric ? $data['user_id'] : get_username_by_id($data['user_id'])) . '</td>';
		echo '	<td>' . $data['item_id'] . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo rounded_corners_bottom();
	
	ui_bottom();

	//functions used in this script
	function get_userid_by_name($username)
	{
		//hämta användarid
		$query = 'SELECT id FROM login WHERE username = "'. $username . '"';
		$result = mysql_query($query) or die(report_sql_error($query));
		if($data = mysql_fetch_assoc($result))
			$user_id = $data['id'];
		else
			$user_id = null;
		return $user_id;
	}

	function get_username_by_id($id)
	{
		//hämta användarid
		$query = 'SELECT username FROM login WHERE id = "'. $id . '"';
		$result = mysql_query($query) or die(report_sql_error($query));
		if($data = mysql_fetch_assoc($result))
			$username = $data['username'];
		else
			$username = null;
		return $username;
	}

	function has_value($item)
	{
		return isset($item) && $item != '';
	}
?>
