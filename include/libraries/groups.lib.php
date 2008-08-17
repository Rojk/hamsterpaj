<?php

function groups_get_action($url)
{
	$explosion = explode('/', $url);
	if($url == '/grupper/ny_grupp.php')
	{
		$request['group_info'] = $_POST;
		$request['action'] = 'create_group';
		return $request;
	}
	if($url == '/grupper/skapa_grupp')
	{
		$request['action'] = 'new_group';
		return $request;
	}
	if(strlen($explosion[2]) > 0)
	{
		$request['action'] = 'view_group';
		$request['group'] = groups_fetch(array('handle' => $explosion[2]));
		if(count($request['group']) == 1)
		{
			$request['group'] = array_pop($request['group']);
		}
		else
		{
			$request['action'] = 'group_not_found';
			$request['group_handle'] = $explosion[2];
		}
		return $request;
	}
	else
	{
		$request['action'] = 'index';
	}
	
	return $request;
}


function groups_fetch($options)
{
	$options['order-by'] = (in_array($options['order-by'], array('id'))) ? $options['order-by'] : 'id';
	$options['order-direction'] = (in_array($options['order-direction'], array('ASC', 'DESC'))) ? $options['order-direction'] : 'ASC';
	$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
	$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 9999;

	$query = 'SELECT g.* FROM groups AS g';
	$query .= ' WHERE 1';
	$query .= (isset($options['founder']) && is_numeric($options['founder'])) ? ' AND g.founder = ' . $options['founder'] : '';
	$query .= (isset($options['handle'])) ? ' AND g.handle LIKE "' . $options['handle'] . '"' : '';
	$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		

	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$groups[$data['id']] = $data;
	}
	
	return $groups;
}

function groups_list($groups)
{
	$output .= '<div class="listgroups">' . "\n";
	foreach($groups AS $group)
	{
		$output .= '<div class="group_thumbnail">' . "\n";
		$output .= '<h2><a href="/grupper/' . $group['handle'] . '/">'. $group['name'] . '</a></h2>' . "\n";
		$output .= $group['member_count'] . ' medlemmar';
		$output .= '</div>' . "\n";
	}
	$output .= '</div><br style="clear: both;" />' . "\n";
	return $output;
}

function groups_add_user_to_group($options)
{
	$query = 'INSERT INTO group_users (group_id, user_id) VALUES("' . $options['group_id'] . '", "' . $options['user_id'] . '")';
	if(mysql_query($query))
	{
		$query = 'UPDATE groups SET member_count = member_count + 1 WHERE id = "' . $options['group_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
}

function groups_remove_user_from_group($options)
{
	$query = 'DELETE FROM group_users WHERE group_id = "' . $options['group_id'] . '" AND user_id = "' . $options['user_id'] . '" LIMIT 1';
	if(mysql_query($query) && mysql_affected_rows() == 1)
	{
		$query = 'UPDATE groups SET member_count = member_count - 1 WHERE id = "' . $options['group_id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
}

function groups_create($options)
{
	$options['founder'] = (!isset($options['founder'])) ? $_SESSION['login']['id'] : $options['founder'];
	$options['handle'] = (!isset($options['handle'])) ? url_secure_string($options['name']) : $options['handle'];
	
	$query = 'INSERT INTO groups(handle, name, description, member_count, created_timestamp, founder, forum_id, auto_join, visible)' . "\n";
	$query .= 'VALUES("' . $options['handle'] . '", "' . $options['name'] . '", "' . $options['description'] . '", 0, ' . time() . ', "' . $options['founder'] . '", 0, "' . $options['auto_join'] . '", "' . $options['visible'] . '")';
	
	mysql_query($query) or report_sql_error($query);
	$options['id'] = mysql_insert_id();
	
	if($options['disable_founder_join'] != true)
	{
		groups_add_user_to_group(array('group_id' => $options['id'], 'user_id' => $options['founder']));
	}
	
	return $options;
}

function groups_create_form($options)
{
	$output .= '<form action="/grupper/ny_grupp.php" method="post">' . "\n";
	$output .= '<h5>Vad ska gruppen heta?</h5>' . "\n";
	$output .= '<input type="text" name="name" value="' . $options['name'] . '" />' . "\n";
	
	$output .= '<h5>Hur skulle du kortfattat beskriva gruppen?</h5>' . "\n";
	$output .= '<textarea name="description">' . $options['description'] . '</textarea>' . "\n";

	$output .= '<h5>Vill du ha en öppen eller sluten grupp?</h5>' . "\n";
	$output .= '<input type="radio" name="visible" value="1" id="group_form_visibilty_control_1" />' . "\n";
	$output .= '<label for="group_form_visibilty_control_1">En <strong>öppen grupp</strong> som syns i sökningar, där alla kan läsa vad som skrivits i gruppen och ansöka till gruppen. Passar bra för intressegrupper, såsom en vegetarian-grupp eller Tokio-Hotel-Emo-grupp</label><br />' . "\n";
	$output .= '<input type="radio" name="visible" value="0" id="group_form_visibilty_control_0" />' . "\n";
	$output .= '<label for="group_form_visibilty_control_0">En <strong>sluten grupp</strong>. Gruppen kommer inte synas i sökresultat, du måste själv skicka ut inbjudningar till alla som vill vara med i gruppen och endast medlemmar kan läsa vad som skrivits. Passar bra för kompis-grupper.</label><br />' . "\n";	

	$output .= '<h5>Vill du godkänna alla som söker till gruppen?</h5>' . "\n";
	$output .= '<input type="radio" name="autojoin" value="1" id="group_form_autojoin_control_1" />' . "\n";
	$output .= '<label for="group_form_autojoin_control_1"><strong>Nej</strong>, acceptera nya medlemmar automatiskt. Detta gäller inte om du valt en sluten grupp.</label><br />' . "\n";
	$output .= '<input type="radio" name="autojoin" value="0" id="group_form_autojoin_control_0" />' . "\n";
	$output .= '<label for="group_form_autojoin_control_0"><strong>Ja</strong>, jag vill själv välja vilka som kan bli medlemmar eller inte.</label><br />' . "\n";
	
	$output .= '<input type="submit" value="Skapa grupp" class="button_100" />' . "\n";
	
	$output .= '</form>' . "\n";

	return $output;
}

function render_group_entries($entries)
{
	$content .= '<ul class="group_entries">' . "\n";
	foreach ($entries AS $entry)
	{
		$content .= '<li>' . "\n";
			$content .= '<img src="http://images.hamsterpaj.net/images/users/thumb/625058.jpg" class="user_avatar" />' . "\n";
				$content .= '<div class="container">' . "\n";
					$content .= '<div class="top_bg">' . "\n";
						$content .= '<div class="bottom_bg">' . "\n";
							$content .= '<div>' . "\n";
								$content .= '<span class="timestamp">' . $entry['timestamp'] . '</span>' . "\n";
								$content .= '<a href="/traffa/profile.php?id=625058">Lef-91</a> ' . "\n";
								$content .= $entry['gender'];
								$content .= $entry['birthday'];
								$content .= '<p>' . "\n";
								$content .= 'Haha, fail' . "\n";
							$content .= '</p>' . "\n";
						$content .= '</div>' . "\n";
					$content .= '</div>' . "\n";
				$content .= '</div>' . "\n";
			$content .= '</div>' . "\n";
		$content .= '</li>' . "\n";
	}
	$content .= '</ul>' . "\n";
}
?>