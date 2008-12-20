<?php
function schedule_admin_draw($options)
{
	$options['base_url'] = isset($options['base_url']) ? $options['base_url'] : $_SERVER['PHP_SELF'];
	
	switch($options['what'])
	{
		case 'main':
			$query = 'SELECT id, type, start, end'
						 . ' FROM schedule_schema'
						 . ' ORDER BY type';
			$result = mysql_query($query) or report_sql_error($query);
			
			$output .= 'Medan du glor på det skitfula schemat jag lyckats åstadkomma kan du ju trycka på något i punktlistan nedan:<br />' . "\n";
			$output .= '<ul id="schedule_v2_admin_slot_selector">' . "\n";
			while($data = mysql_fetch_assoc($result))
			{
				$output .= "\t" . '<li id="schedule_v2_admin_select_slot_' . $data['id'] . '">#' . $data['id'] . ': <strong>' . $data['type'] . '</strong> - ' . schedule_week_minutes_readable((int)$data['start']) . ' <strong>till</strong> ' . schedule_week_minutes_readable((int)$data['end']) . '.</li>' . "\n";
			}
			$output .= '</ul>' . "\n";
			
			$output .= '<img src="http://images.hamsterpaj.net/schedule_v2/schema_hours.png" alt="tid" id="schedule_v2_admin_slot_schema_hours" />' . "\n";
			$output .= '<div id="schedule_v2_admin_slot_schema"></div>' . "\n";
			
			$output .= '<div id="schedule_v2_admin_slot_config_container">' . "\n";
			
			$output .= '<h2>Start</h2>' . "\n";
			$output .= '<p>' . "\n";
			$output .= schdule_admin_generate_datechooser(array('id_prefix' => 'schedule_v2_admin_slot_config_start'));	
			$output .= '</p>' . "\n";
			
			
			$output .= '<h2>Slut</h2>' . "\n";
			$output .= '<p>' . "\n";
			$output .= schdule_admin_generate_datechooser(array('id_prefix' => 'schedule_v2_admin_slot_config_end'));
			$output .= '</p>' . "\n";
			
			$output .= '<h2>Övrigt</h2>' . "\n";
			$output .= '<p>' . "\n";
			$output .= 'ID: <div id="schedule_v2_admin_slot_config_id">Laddar...</div><br />' . "\n";
			$output .= 'Typ: ';
			$output .= '<select id="schedule_v2_admin_slot_config_type">' . "\n";
			$available_types = schedule_get_slot_types();
			foreach($available_types as $type)
			{
				$output .= '<option value="' . $type . '">' . $type . '</option>' . "\n";
			}
			$output .= '</select>' . "\n";
			$output .= '<br />' . "\n";
			$output .= 'Start i minuter: <div id="schedule_v2_admin_slot_config_start_week_minutes">Laddar...</div><br />' . "\n";
			$output .= 'Slut i minuter: <div id="schedule_v2_admin_slot_config_end_week_minutes">Laddar...</div><br />' . "\n";
			$output .= '</p>' . "\n";
			
			$output .= '<p>' . "\n";
			$output .= '<button class="button_60" id="schedule_v2_admin_slot_config_save">Spara</button><br />' . "\n";
			$output .= '</p>' . "\n";
			
			$output .= '</div>' . "\n";
			
		break;
		
		
		case 'ajax_fetch_slot':
			if(!isset($options['slot_id']))
			{
				$output .= 'Could not find slot id-parameter.';
				break;
			}
			$query = 'SELECT id, type, start, end'
						 . ' FROM schedule_schema'
						 . ' WHERE id = ' . $options['slot_id']
						 . ' LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query);
	
			$data = mysql_fetch_assoc($result);		
			$output .= '{ "id": ' . $data['id'] . ', "type": "' . $data['type'] . '", "start": ' . $data['start'] . ', "end": ' . $data['end'] . ' }';
		break;
		
		case 'ajax_save_slot':
			$message = 'Sparat!';
			$query = 'UPDATE schedule_schema SET'
			       . '  start = ' . $options['start']
			       . ', end = ' . $options['end']
			       . ', type = "' . $options['type'] . '"'
			       . ' WHERE id = ' . $options['id'];
			if(!mysql_query($query))
			{
				$message = 'MySQL-fel i ' . __FILE__ . ' på rad ' . __LINE__ . '.';
				//$message .= '\n' . mysql_error();
			}
		
			$output .= '<script type="text/javascript" language="javascript">' . "\n";
			$output .= 'alert("' . $message . '");' . "\n";
			$output .= 'window.history.go(-1);' . "\n";
			$output .= '</script>' . "\n";
		break;
		
		case 'create_slot_create':
			$output .= '<h2>Create slot (...som det så fint heter)</h2>';
			$output .= '<form action="' . $options['base_href'] . '?page=main&create_slot_save=true">';
			$output .= '<input type="hidden" name="page" value="main" />';
			$output .= '<input type="hidden" name="create_slot_save" value="true" />';
			$output .= 'Start: ' . schdule_admin_generate_datechooser(array('name_prefix' => 'start'))	 . '<br />' . "\n";
			$output .= 'Slut: ' . schdule_admin_generate_datechooser(array('name_prefix' => 'end'))	 . '<br />' . "\n";
			$output .= 'Typ: ';
			$output .= '<select name="type">';
			$available_types = schedule_get_slot_types();
			foreach($available_types as $type)
			{
				$output .= '<option value="' . $type . '">' . $type . '</option>' . "\n";
			}
			$output .= '</select><br />' . "\n";
			$output .= '<input type="submit" value="Create slot" class="button_120">';
			$output .= '</form>';
		break;
		
		case 'create_slot_save':
			
			$query = 'INSERT INTO'
			       . ' schedule_schema(start, end, type)'
			       . ' VALUES(' . $options['start'] . ', ' . $options['end'] . ', "' . $options['type'] . '")';
			if(mysql_query($query))
			{
				$message = 'Skapade slot med id #' . mysql_insert_id();
			}
			else
			{
				$message = 'MySQL-error in' . __FILE__ . ' on line ' . __LINE__;
			}
			
			$output .= '<script type="text/javascript" language="javascript">' . "\n";
			$output .= 'alert("' . $message . '");' . "\n";
			$output .= '</script>' . "\n";
		break;
		
		default:
			$output .= 'Error in ' . __FILE__ . ' on line ' . __LINE__;
	}
	
	return $output;
}

function schedule_admin_parse_request($options)
{
	$options['source'] = isset($options['source']) ? $options['source'] : $_GET;
	$page = (isset($options['source']['page']) && in_array($options['source']['page'], array('ajax_fetch_slot', 'ajax_save_slot', 'main'))) ? $options['source']['page'] : 'main';
	$call_options['base_url'] = isset($options['base_url']) ? $options['base_url'] : '';
	$call_options['what'] = $page;
	
	$ui_options['javascripts'][] = 'schedule_v2_admin.js';
	$ui_options['stylesheets'][] = 'schedule_v2_admin.css';
	$ui_options['title'] = 'Schemalagt v2 på Hamsterpaj';
	$ui_options['menu_path'] = array('admin');
	
	switch($page)
	{
		case 'ajax_fetch_slot':
		if(isset($options['source']['id']) && is_numeric($options['source']['id']))
		{
			$call_options['slot_id'] = $options['source']['id'];
			echo schedule_admin_draw($call_options);
		}
		else
		{
			schedule_admin_error(array('error' => 'Felaktigt ID skickades med till ajax_fetch_slot!'));
		}
		break;
		
		case 'ajax_save_slot':
		if(
				isset($options['source']['id'], $options['source']['start'], $options['source']['end'], $options['source']['type'])
				&&
				is_numeric($options['source']['id']) && is_numeric($options['source']['start']) && is_numeric($options['source']['end'])
				&&
				in_array($options['source']['type'], schedule_get_slot_types())
			)
		{
			$call_options['id'] = $options['source']['id'];
			$call_options['start'] = $options['source']['start'];
			$call_options['end'] = $options['source']['end'];
			$call_options['type'] = $options['source']['type'];
			echo schedule_admin_draw($call_options);
		}
		break;
		
		case 'main':
			ui_top($ui_options);
			echo rounded_corners_top(array('color' => 'orange'));
			echo 'Note: Schemat är både fult, snett och går en timma fel.';
			echo rounded_corners_bottom();
			
			if(
					isset($options['source']['create_slot_save'])
				&&
					isset($options['source']['start_day'], $options['source']['start_hour'], $options['source']['start_minute'])
				&&
					is_numeric($options['source']['start_day']) && is_numeric($options['source']['start_hour']) && is_numeric($options['source']['start_minute'])
				&&
					isset($options['source']['end_day'], $options['source']['end_hour'], $options['source']['end_minute'])
				&&
					is_numeric($options['source']['end_day']) && is_numeric($options['source']['end_hour']) && is_numeric($options['source']['end_minute'])
				&&
					isset($options['source']['type']) && in_array($options['source']['type'], schedule_get_slot_types())
				)
			{
				$call_options['what'] = 'create_slot_save';
				
				$call_options['start'] = schedule_readable_to_week_minutes($options['source']['start_day'], $options['source']['start_hour'], $options['source']['start_minute']);
				$call_options['end'] = schedule_readable_to_week_minutes($options['source']['end_day'], $options['source']['end_hour'], $options['source']['end_minute']);;
				$call_options['type'] = $options['source']['type'];
				if($call_options['start'] < $call_options['end'])
				{
					$call_options['what'] = 'create_slot_save';
					echo schedule_admin_draw($call_options);
				}
				else
				{
					jscript_alert('Fel: Du måste ange ett slut som är efter din början!');
				}
			}
			
			
			
			$call_options['what'] = 'main';
			echo schedule_admin_draw($call_options);
						
			$call_options['what'] = 'create_slot_create';
			echo schedule_admin_draw($call_options);
			
			ui_bottom();
		break;
	}
}

function schedule_admin_error($options)
{
	$rounded_corners_options['color'] = 'orange';
	echo rounded_corners_top($rounded_corners_options);
	echo 'Ett fel har inträffat: ' . $options['error'];
	echo rounded_corners_bottom($rounded_corners_options);
}

function schedule_week_minutes_readable($minutes, $format = 'readable_string')
{
			$one_hour = 60;
			$one_day = 1440; // Minutes on 24h
			$one_week = $one_day *  7; // Minutes on one week
			$weekdays = explode(', ', 'Måndag, Tisdag, Onsdag, Torsdag, Fredag, Lördag, Söndag');
			
			$output_days = floor(($minutes / $one_week) * 7);
			$minutes_without_days = $minutes - ($one_day * $output_days); // Minutes elapsed this day...
			$output_hours = floor(($minutes_without_days / $one_day) * 24);
			$output_minutes = $minutes_without_days - ($output_hours * 60);
			
			// Some unpretty code for some pretty reading.
			$output_hours = (strlen((string)$output_hours) < 2) ? '0' . $output_hours : $output_hours;
			$output_minutes = (strlen((string)$output_minutes) < 2) ? '0' . $output_minutes : $output_minutes;
			
			switch($format)
			{
				case 'readable_string':
					return $weekdays[$output_days] . ' klockan ' . $output_hours . ':' . $output_minutes;
				break;
				
				case 'date_array':
					$return = array();
					$return['day'] = $output_days;
					$return['hour'] = $output_hours;
					$return['minutes'] = $output_minutes;
					return $return;
				break;
				default: return false;
			}
}

function schedule_readable_to_week_minutes($days, $hours, $minutes)
{
	$one_day = 1440;
	$one_hour = 60;
	return ($days * $one_day) + ($hours * $one_hour) + $minutes;
}

function schdule_admin_generate_datechooser($options)
{
	$return .= '<select' . (isset($options['id_prefix']) ? ' id="' . $options['id_prefix'] . '_day' : '') . (isset($options['name_prefix']) ? ' name="' . $options['name_prefix'] . '_day"' : '') . '">';
	$day_offset = 0;
	foreach(explode(', ', 'Måndag, Tisdag, Onsdag, Torsdag, Fredag, Lördag, Söndag') as $weekday)
	{
		$return .= '<option value="' . $day_offset++ . '">' . $weekday . '</option>';
	}
	$return .= '</select>';
	
	
	
	$return .= '&nbsp;';
	
	
	
	$return .= '<select' . (isset($options['id_prefix']) ? ' id="' . $options['id_prefix'] . '_hour' : '') . (isset($options['name_prefix']) ? ' name="' . $options['name_prefix'] . '_hour"' : '') . '">';
	for($hour = 0; $hour < 24; $hour++)
	{
		$return .= '<option value="' . $hour . '">' . (($hour < 10) ? '0' . $hour : $hour) . '</option>';
	}
	$return .= '</select>';
	
	
	
	$return .= ':';
	
	
	
	$return .= '<select' . (isset($options['id_prefix']) ? ' id="' . $options['id_prefix'] . '_minute' : '') . (isset($options['name_prefix']) ? ' name="' . $options['name_prefix'] . '_minute"' : '') . '">';
	for($minute = 0; $minute < 60; $minute++)
	{
		$return .= '<option value="' . $minute . '">' . (($minute < 10) ? '0' . $minute : $minute) . '</option>';
	}
	$return .= '</select>';
	
	
	return $return;
}

function schedule_get_slot_types()
{
	$query = 'SHOW COLUMNS FROM schedule_schema';
	$result = mysql_query($query) or trace('schedule_v2_mysql_error', 'MySQL-error in' . __FILE__ . ' on line ' . __LINE__ . ' with query ' . $query . ' -error: ' . mysql_error());
	while($data = mysql_fetch_assoc($result))
	{
		if($data['Field'] == 'type' && substr($data['Type'], 0, 5) == 'enum(')
		{
			// enum(' [...] ')
			$types = substr($data['Type'], 6, -2);
			return explode("','", $types);
		}
	}
	
	// On error...
	return false;
}

function schedule_minute_job()
{
	$last_monday = strtotime((date('D') == 'Mon') ? 'Today' : 'last Monday');
	$minutes_passed_this_week = floor((time() - $last_monday) / 60);
	$query = 'SELECT sp.id, sp.type, sp.data, sp.released'
				 . ' FROM schedule_schema AS ss, schedule_pool AS sp'
				 . ' WHERE ss.type = sp.type'
				 . ' AND ss.start <= ' . $minutes_passed_this_week . ' AND ss.end >= ' . $minutes_passed_this_week
				 . ' AND sp.released = 0'
				 . ' ORDER BY rand()';
	$result = mysql_query($query) or report_sql_error($query);
	
	echo $minutes_passed_this_week;
	
	while($data = mysql_fetch_assoc($result))
	{
		preint_r($data);
	}
}
?>