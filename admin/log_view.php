<?php

	require('../include/core/common.php');
	$ui_options['current_menu'] = 'admin';
	$ui_options['stylesheets'][] = 'admin_log.css';
	$ui_options['menu_path'] = array('admin', 'log_view');
	require_once(PATHS_LIBRARIES . 'admin.lib.php');
	
	if(!is_privilegied('ov_log'))
	{
		header('location: /');
		die();
	}
	
	$format = isset($_POST['show_csv_format']) ? 'csv' : 'styled';

	if (isset($_POST['username']))
	{
		preint_r($_POST);
	}

	$numeric = array_key_exists('numeric',$_POST) == 1;
	
	if($format == 'styled')
	{
		$output = rounded_corners_top();
		$output .= '<h1 style="margin-top: 0px; padding-top: 2px;">Logg över administrativa händelser</h1>';
		$output .= '<h3>Filtrera på valfria fält</h3><br />';
	
		$output .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
		$output .= '<table class="admin_log" >';
		$output .= '	<tr>';
		$output .= '		<td class="event" >Händelse</td>';
		$output .= '		<td class="comment">Info</td>';
		$output .= '		<td class="time">Tidpunkt</td>';
		$output .= '		<td class="admin">Admin</td>';
		$output .= '		<td class="user">Användare</td>';
		$output .= '		<td class="item">Item id</td>';
		$output .= '	</tr>';
		$output .= '	<tr>';
		$output .= '		<td class="event" ><input type="text" name="event" value="' . $_POST['event'] . '" /></td>';
		$output .= '		<td class="comment" ><input type="text" name="value" value="' . $_POST['value'] . '" /></td>';
		$output .= '		<td><input type="text" name="starttime" value="' . $_POST['starttime'] . '" /></td>';
		$output .= '		<td><input type="text" name="admin" value="' . $_POST['admin'] . '" /></td>';
		$output .= '		<td><input type="text" name="user" value="' . $_POST['user'] . '" /></td>';
		$output .= '		<td><input type="text" name="item" value="' . $_POST['item'] . '" /></td>';
		$output .= '	</tr>';
		$output .= '	<tr>';
		$output .= '		<td></td>';
		$output .= '		<td></td>';
		$output .= '		<td><input type="text" name="endtime" value="' . $_POST['endtime'] . '" /></td>';
		$output .= '		<td></td>';
		$output .= '		<td></td>';
		$output .= '		<td></td>';
		$output .= '	</tr>';
		$output .= '</table>';
		$output .= '<input type="checkbox" name="numeric" value="true" id="chk_numeric_ids" ' . ( $numeric ? 'checked="checked"' : '') . ' />';
		$output .= '<label for="chk_numeric_ids"> Id-nummer istället för namn </label>';
		$output .= '<input type="checkbox" name="rubish" value="true" id="chk_rubish" ' . ( array_key_exists('rubish',$_POST) ? 'checked="checked"' : '') . ' />';
		$output .= '<label for="chk_rubish"> Visa inte avatarer och namnbyten </label>';
		$output .= '<input type="checkbox" name="show_csv_format" value="true" id="chk_show_csv_format" ' . ( array_key_exists('show_csv_format',$_POST) ? 'checked="checked"' : '') . ' />';
		$output .= '<label for="chk_show_csv_format"> Ge mig en sån där CSV-fil istället för något annat skräp (Excel) </label>';
		$output .= '<br /><br /><input type="submit" name="submit" value="Filtrera" class="button_60" />';
		$output .= '</form>';
		$output .= '<hr />';
	}
	
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
//	$output .= '<p>sql-frågan: ' . $query . '</p>';

	switch($format)
	{
		case 'styled':
			$output .= '<table class="admin_log">';
			$output .= '	<tr>';
			$output .= '		<td class="event" >Händelse</td>';
			$output .= '		<td class="comment" >Info</td>';
			$output .= '		<td class="time" >Tidpunkt</td>';
			$output .= '		<td class="admin" >Admin</td>';
			$output .= '		<td class="user" >Användare</td>';
			$output .= '		<td class="item" >Item id</td>';
			$output .= '	</tr>';
		break;
		
		case 'csv':
			$output .= implode("\t", array('Handelse', 'Info', 'Tidpunkt', 'Admin', 'Anvandare', 'Itemid')) . "\n";
		break;
	}
	
	while($data = mysql_fetch_assoc($result))
	{
		switch($format)
		{
			case 'styled':
				$output .= '<tr>';
				$output .= '	<td class="event" >' . $data['event'] . '</td>';
				$output .= '	<td class="comment" >' . $data['value'] . '</td>';
				$output .= '	<td>' . fix_time($data['timestamp']) . '</td>';
				$output .= '	<td>' . ($numeric ? $data['admin_id'] : get_username_by_id($data['admin_id'])) . '</td>';
				$output .= '	<td>' . ($numeric ? $data['user_id'] : get_username_by_id($data['user_id'])) . '</td>';
				$output .= '	<td>' . $data['item_id'] . '</td>';
				$output .= '</tr>';
			break;
			
			case 'csv':
				$row = array(
					'event' => $data['event'],
					'comment' => $data['value'],
					'timestamp' => date('Y-m-d H:i', $data['timestamp']),
					'admin' => ($numeric ? $data['admin_id'] : get_username_by_id($data['admin_id'])),
					'user' => ($numeric ? $data['user_id'] : get_username_by_id($data['user_id'])),
					'item_id' => $data['item_id']
				);
				
				//$row = array_map('md5', $row);
				
				$output .= '' . implode("\t", $row) . '' . "\n";
			break;
		}
	}
	
	if($format == 'styled')
	{
		$output .= '</table>';
		$output .= rounded_corners_bottom();
	}
	
	switch($format)
	{
		case 'styled':
			ui_top($ui_options);
			echo $output;
			ui_bottom();
		break;
		
		case 'csv':
			//header('Content-type: text/plain');
			header('Content-type: text/csv');
			header('Content-disposition: attachment; filename=adminlogg_' . date('Y-m-d_H.i') . '.csv');
			echo $output;
		break;
	}

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