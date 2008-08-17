<?php
	/* If you enable text-align, font-size, font-family or color, ALL four properties
		MUST be enabled, IN THIS ORDER: text-align, font-size, font-family, color.
		Failure to do so will result in broken <table> HTML code and a broken layout. 
		
		Same goes for borders. If you enable border-top, border-bottom, border-left or border-right
		ALL four borders MUST be enabled, IN THAT SPECIFIC ORDER!
	*/
	$objects['presentation']['type'] = 'presentation';
	$objects['presentation']['title'] = 'Presentation';
	$objects['presentation']['properties'] = array('background', 'text-align', 'font-size', 'font-family', 'color', 'padding');

	$objects['h5']['type'] = 'h5';
	$objects['h5']['title'] = 'Rubrik';
	$objects['h5']['properties'] = array('text-align', 'font-size', 'font-family', 'color');

	$objects['h6']['type'] = 'h6';
	$objects['h6']['title'] = 'Underrubrik';
	$objects['h6']['properties'] = array('text-align', 'font-size', 'font-family', 'color');

	$objects['text1']['type'] = 'text';
	$objects['text1']['title'] = 'Text typ 1';
	$objects['text1']['properties'] = array('text-align', 'font-size', 'font-family', 'color');

	$objects['text2']['type'] = 'text';
	$objects['text2']['title'] = 'Text typ 2';
	$objects['text2']['properties'] = array('text-align', 'font-size', 'font-family', 'color');

	$objects['text3']['type'] = 'text';
	$objects['text3']['title'] = 'Text typ 3';
	$objects['text3']['properties'] = array('text-align', 'font-size', 'font-family', 'color');

	$objects['box1']['type'] = 'div';
	$objects['box1']['title'] = 'Box typ 1';
	$objects['box1']['properties'] = array('background', 'text-align', 'font-size', 'font-family', 'color', 'padding', 'border-top', 'border-bottom', 'border-left', 'border-right');

	$objects['box2']['type'] = 'div';
	$objects['box2']['title'] = 'Box typ 2';
	$objects['box2']['properties'] = array('background', 'text-align', 'font-size', 'font-family', 'color', 'padding', 'border-top', 'border-bottom', 'border-left', 'border-right');

	$objects['box3']['type'] = 'div';
	$objects['box3']['title'] = 'Box typ 3';
	$objects['box3']['properties'] = array('background', 'text-align', 'font-size', 'font-family', 'color', 'padding', 'border-top', 'border-bottom', 'border-left', 'border-right');
	

	$cfg_properties['text-aligns'][] = array('value' => 'left', 'label' => 'Vänsterställd');
	$cfg_properties['text-aligns'][] = array('value' => 'right', 'label' => 'Högerställd');
	$cfg_properties['text-aligns'][] = array('value' => 'center', 'label' => 'Centrerad');
	$cfg_properties['text-aligns'][] = array('value' => 'justify', 'label' => 'Marginaljusterad');

	$cfg_properties['font-sizes'] = array(10, 12, 14, 16, 18, 20);

	$cfg_properties['colors'] = array('#000000', '#333333', '#666666', '#999999', '#cccccc', '#ffffff', '#620000', '#aa1619', '#f5393d', '#f3c3c4', '#82370a', '#d0642a', '#ff630f', '#84750e', '#e3ce41', '#ffde05', '#fff8c5', '#4a670b', '#aee22c', '#e8ffa7', '#41be02', '#c0eda9', '#0bab73', '#70cead', '#b9f7e2', '#0e4c6a', '#0b81b5', '#7ebad4', '#8dddff', '#0a0a5e', '#7879da', '#5e2294', '#d19dff', '#d035ef', '#4a2052', '#7e1463', '#e28acd', '#366d9a', '#8de60a', '#ffd75d', '#253553', '#f9e6c9');
	$cfg_properties['font-families'][] = array('label' => 'Arial', 'value' => 'arial, sans-serif');
	$cfg_properties['font-families'][] = array('label' => 'Arial Black', 'value' => 'arial black, sans-serif');
	$cfg_properties['font-families'][] = array('label' => 'Comic Sans MS', 'value' => 'comic sans ms, sans-serif');
	$cfg_properties['font-families'][] = array('label' => 'Courier', 'value' => 'courier, serif');
	$cfg_properties['font-families'][] = array('label' => 'Georgia', 'value' => 'georgia, serif');
	$cfg_properties['font-families'][] = array('label' => 'Impact', 'value' => 'impact, sans-serif');
	$cfg_properties['font-families'][] = array('label' => 'Times New Roman', 'value' => 'times new roman, serif');
	$cfg_properties['font-families'][] = array('label' => 'Trebuchet MS', 'value' => 'trebuchet ms, sans-serif');
	$cfg_properties['font-families'][] = array('label' => 'Verdana', 'value' => 'verdana, helvetica, sans-serif');

	$cfg_properties['border-styles'][] = array('label' => 'Solid', 'value' => 'solid');
	$cfg_properties['border-styles'][] = array('label' => 'Streckad', 'value' => 'dashed');
	$cfg_properties['border-styles'][] = array('label' => '3D', 'value' => 'ridge');
	$cfg_properties['border-styles'][] = array('label' => 'Dubbel', 'value' => 'double');


	$cfg_properties['paddings'] = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

	$cfg_properties['border-widths'] = array(0, 1, 2, 3, 5, 6, 7, 8, 9, 10);

	$cfg_defaults['background'] = '#ffffff';
	$cfg_defaults['color'] = '#000000';
	$cfg_defaults['text-align'] = 'left';
	$cfg_defaults['font-size'] = '14px';
	$cfg_defaults['font-family'] = 'arial, verdana, helvetica, sans-serif';
	$cfg_defaults['padding'] = '3px';
	$cfg_defaults['border-top'] = 'none';
	$cfg_defaults['border-right'] = 'none';
	$cfg_defaults['border-left'] = 'none';
	$cfg_defaults['border-bottom'] = 'none';
	
	
	function freetext_module_security_check($input, $value)
	{
		GLOBAL $objects;
		
		$allowed_objects = array('presentation', 'h5', 'h6', 'text1', 'text2', 'text3', 'box1', 'box2', 'box3');
		
		if(!preg_match('/^(' . implode('|', $allowed_objects) . ')_([a-z-]+)$/', $input, $matches))
		{
			trace('profiles', 'Error with handle: ' . $input);
			return false;
		}
		
		$object = $matches[1];
		$property = $matches[2];
		
		if(!in_array($property, $objects[$object]['properties']))
		{
			trace('profiles', 'Error with property: ' . $property);
			return false;
		}
		
		
		if(strpos($value, '.php') || strpos($value, '.asp') || strpos($value, '.asp'))
		{
			trace('profiles', 'Error when setting value for user ' . $_SESSION['login']['username'] . ': ' . $property . '->' . $value);
			return false;
		}
		
		return true;
	}
	
	if(isset($_GET['update']))
	{
		foreach($_POST AS $input => $value)
		{
			if($input != 'freetext' && freetext_module_security_check($input, $value))
			{
				$object = substr($input, 0, strpos($input, '_'));
				$property = substr($input, strpos($input, '_')+1);
				$stylesheet[$object][$property] = $value;
			}
		}
		
		$fjortis_strings = array();
		$fjortis_strings[] = 'Varför började du läsa? Såg du inte överskriften?';
		$fjortis_strings[] = 'Du ringer nummer-upplysningen för att få numret till 112';
		$fjortis_strings[] = '5. om du vill sova i min säng';
		$fjortis_strings[] = '2. Om du vill ha mig som KK';
		$fjortis_strings[] = '^^^^###^^^###^^^^#####^^^^^###^^^###^^^';
		$fjortis_strings[] = 'Flickan står på stranden,';
		
		foreach($fjortis_strings AS $string)
		{
			if(strpos(strtolower($_POST['freetext']), strtolower($string)) !== false)
			{
				jscript_alert('Men din fucking FJOOOORTIS. www.lunarstorm.se är en bra sida för dig som vill kopiera samma text som alla andra har och lägga på din presentation!');
				jscript_alert('Din presentation kunde inte uppdateras.');
				jscript_go_back();
				exit;
			}
		}
		
		
		$serialized = mysql_real_escape_string(serialize($stylesheet));
		$query = 'UPDATE traffa_freetext SET stylesheet = "' . $serialized . '", freetext = "' . $_POST['freetext'] . '" WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_affected_rows() == 0)
		{
			$query = 'SELECT userid FROM traffa_freetext WHERE userid ="' . $_SESSION['login']['id'] . '" LIMIT 1';
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0)
			{
				$query = 'INSERT INTO traffa_freetext(userid, stylesheet, freetext) VALUES(' . $_SESSION['login']['id'] . ', "' . $serialized . '", "' . $_POST['freetext'] . '")';
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			}
		}
		jscript_location('/traffa/profile.php');
		exit;
		//$freetext = htmlspecialchars($_POST['freetext']);
	}
	else
	{
		$query = 'SELECT freetext, stylesheet FROM traffa_freetext WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
		$freetext = $data['freetext'];
		$stylesheet = unserialize($data['stylesheet']);
	}

	foreach($objects AS $object_id => $object_info)
	{
		$object_types[$object_info['type']][] = $object_id;
	}

	function draw_color_control($id, $property, $onclick = null)
	{
		global $cfg_properties;
		echo '<div class="color_picker" id="' . $id . '_' . $property . '_color_control">' . "\n";
		echo '<img src="' . IMAGE_URL . 'images/common/close_small.png" style="float: right; cursor: pointer; width: 16px; height: 16px;" onclick="document.getElementById(\'' . $id . '_' . $property . '_color_control\').style.display = \'none\';" />';
		foreach($cfg_properties['colors'] AS $color)
		{
			if(isset($onclick))
			{
				echo '<div style="background: ' . $color . ';" class="color_chooser_square" onclick="' . str_replace('%COLOR%', $color, $onclick) . '"></div>' . "\n";
			}
			else
			{
				echo '<div style="background: ' . $color . ';" class="color_chooser_square" onclick="update_values(\'' . $id . '\', \'' . $property . '\', \'' . $color . '\'); document.getElementById(\'' . $id . '_' . $property . '_preview\').style.background = \'' . $color . '\';"></div>' . "\n";
			}
		}
		echo '</div>';
	}

	function draw_background_image_control($id)
	{
		global $cfg_properties;
		
	}

	function draw_control($property, $object_id)
	{
		global $cfg_properties, $stylesheet, $cfg_defaults;
		$value = (strlen($stylesheet[$object_id][$property]) > 0) ? $stylesheet[$object_id][$property] : $cfg_defaults[$property];
		switch($property)
		{
			case 'background':
					echo 'Bakgrundsfärg: <div id="' . $object_id . '_' . $property . '_preview" class="settings_color_preview" ';
					echo 'onclick="document.getElementById(\'' . $object_id . '_' . $property . '_color_control\').style.display = \'block\';" ';
					echo 'style="background: ' . $value . ';"></div><br />' . "\n";
					draw_color_control($object_id, $property);
				break;
			case 'text-align':
				echo '<select name="' . $object_id . '_' . $property . '" onchange="update_values(\'' . $object_id . '\', \'' . $property . '\', this.value);">' . "\n";
				foreach($cfg_properties['text-aligns'] AS $option)
				{
					echo '<option value="' . $option['value'] . '"';
					echo ($option['value'] == $value) ? ' selected="true"' : null;
					echo '>' . $option['label'] . '</option>' . "\n";
				}
				echo '</select><br />' . "\n";
				break;
			case 'font-size':
				echo '<select name="' . $object_id . '_' . $property . '" onchange="update_values(\'' . $object_id . '\', \'' . $property . '\', this.value);">' . "\n";
				foreach($cfg_properties['font-sizes'] AS $size)
				{
					echo '<option value="' . $size . '" ';
					echo ($size == $value) ? ' selected="true"' : null;
					echo '>' . $size . '</option>' . "\n";
				}
				echo '</select><br />' . "\n";
				break;
			case 'font-family':
				echo '<select name="' . $object_id . '_' . $property . '" onchange="update_values(\'' . $object_id . '\', \'' . $property . '\', this.value);">' . "\n";
				foreach($cfg_properties['font-families'] AS $family)
				{
					echo '<option value="' . $family['value'] . '" style="font-family: ' . $family['value'] . ';"';
					echo ($family['value'] == $value) ? ' selected="true"' : null;
					echo '>' . $family['label'] . '</option>' . "\n";
				}
				echo '</select><br />' . "\n";
				break;
			case 'color':
					echo '<div id="' . $object_id . '_' . $property . '_preview" class="settings_color_preview" ';
					echo 'onclick="document.getElementById(\'' . $object_id . '_' . $property . '_color_control\').style.display = \'block\';"';
					echo ' style="background: ' . $value . ';"';
					echo '></div>' . "\n";
					draw_color_control($object_id, $property);
				break;
			case 'padding':
				echo '<h4>Innermarginal (padding)</h4>' . "\n";
				echo '<select name="' . $object_id . '_' . $property . '" onchange="update_values(\'' . $object_id . '\', \'' . $property . '\', this.value);">' . "\n";
				foreach($cfg_properties['paddings'] AS $padding)
				{
					echo '<option value="' . $padding . '"';
					echo ($padding == $value) ? ' selected="true"' : null;
					echo '>' . $padding . '</option>' . "\n";
				}
				echo '</select><br />' . "\n";				
				break;
			case 'border-top':
			case 'border-left':
			case 'border-right':
			case 'border-bottom':
				$border_properties = explode(' ', $value);
				$border_width = substr($border_properties[0], 0, -2);
				$border_style = $border_properties[1];
				$border_color = $border_properties[2];
				echo '<table>' . "\n";
				echo '<tr><th>Bredd</th><th>Utseende</th><th>Färg</th></tr>' . "\n";
				echo '<tr>' . "\n";
				echo '<td>' . "\n";
				echo '<select id="' . $object_id . '_' . $property . '_width" onchange="update_values(\'' . $object_id . '\', \'' . $property . '\', this.value + \'px \' + document.getElementById(\'' . $object_id . '_' . $property . '_style\').value + \' \' + document.getElementById(\'' . $object_id . '_' . $property . '_color\').value);">' . "\n";
				foreach($cfg_properties['border-widths'] AS $width)
				{
					echo '<option value="' . $width . '"';
					echo ($width == $border_width) ? ' selected="true"' : null;
					echo '>' . $width . '</option>' . "\n";
				}
				echo '</select><br />' . "\n";
				echo '</td><td>' . "\n";
				echo '<select id="' . $object_id . '_' . $property . '_style" onchange="update_values(\'' . $object_id . '\', \'' . $property . '\', document.getElementById(\'' . $object_id . '_' . $property . '_width\').value + \'px \' + this.value + \' \' + document.getElementById(\'' . $object_id . '_' . $property . '_color\').value);">' . "\n";
				foreach($cfg_properties['border-styles'] AS $style)
				{
					echo '<option value="' . $style['value'] . '"';
					echo ($style['value'] == $border_style) ? ' selected="true"' : null;
					echo '>' . $style['label'] . '</option>' . "\n";
				}
				echo '</select><br />' . "\n";
				echo '</td><td>' . "\n";
				
				echo '<div class="settings_color_preview" id="' . $object_id . '_' . $property . '_color_control_preview" style="background: ';
				echo (strlen($border_color) > 0) ? $border_color : $cfg_defaults['border']['color'];
				echo ';" onclick="document.getElementById(\'' . $object_id . '_' . $property . '_color_control\').style.display = \'block\';"';
				echo '></div>' . "\n";
				
				echo '</td></tr></table>' . "\n";
				echo '<input type="hidden" id="' . $object_id . '_' . $property . '_color" />' . "\n";
				draw_color_control($object_id, $property, 'document.getElementById(\'' . $object_id . '_' . $property . '_color\').value=\'%COLOR%\'; update_values(\'' . $object_id . '\', \'' . $property . '\', document.getElementById(\'' . $object_id . '_' . $property . '_width\').value + \'px \' + document.getElementById(\'' . $object_id . '_' . $property . '_style\').value + \' %COLOR%\'); document.getElementById(\'' . $object_id . '_' . $property . '_color_control_preview\').style.background=\'%COLOR%\';');
				break;
		}
	}

?>

<style>
.color_chooser_square
{
	width: 10px;
	height: 10px;
	float: left;
	margin: 5px;
	cursor: pointer;
	border: 1px solid #656565;
}

.color_chooser_square:hover
{
	border: 1px solid white;
}

.color_picker
{
	width: 150px;
	height: 150px;
	display: none;
	border-top: 3px solid #c8c8c8;
	border-left: 3px solid #c8c8c8;
	border-right: 3px solid #4e4e4e;
	border-bottom: 3px solid #4e4e4e;
	position: absolute;
	background: #e0dfe3;
}

.settings_color_preview
{
	width: 10px;
	height: 10px;
	border: 1px solid black;
	position: relative;
	clear: none;
	cursor: pointer;
}

#preview_container
{
	width: 245px;
	height: 200px;
	float: right;
}

#settings_container
{
	float: left;
	width: 385px;
}

h4
{
	margin: 2px;
}

th
{
	text-align: left;
}
</style>

<script>
	function update_values(object, property, value)
	{
		//alert('Setting ' + property + ' to ' + value + ' for object ' + object);
		document.getElementById(object + "_" + property + "_input").value = value;
		switch(property)
		{
			case "background":
				if(value.substr(0, 1) == "#")
				{
					document.getElementById(object + '_preview').style.background = value;
				}
				else
				{
					document.getElementById(object + '_preview').style.background = "url('" + value + "')";
				}
				break;
			case "color":
				document.getElementById(object + '_preview').style.color = value;
				break;
			case "text-align":
				document.getElementById(object + '_preview').style.textAlign = value;
				break;
			case "font-size":
				document.getElementById(object + '_preview').style.fontSize = value + 'px';
				break;
			case "font-family":
				document.getElementById(object + '_preview').style.fontFamily = value;
				break;
			case "padding":
				document.getElementById(object + '_preview').style.padding = value + 'px';
				break;
			case "border-top":
				document.getElementById(object + '_preview').style.borderTop = value;
				break;
			case "border-left":
				document.getElementById(object + '_preview').style.borderLeft = value;		
				break;
			case "border-right":
				document.getElementById(object + '_preview').style.borderRight = value;		
				break;
			case "border-bottom":
				document.getElementById(object + '_preview').style.borderBottom = value;		
				break;
		}
	}

	function view_settings(object)
	{
		for(var i = 0; i < document.getElementById('preview_container').childNodes.length; i++)
		{
			if(document.getElementById('preview_container').childNodes[i].nodeName == 'DIV')
			{
				document.getElementById(document.getElementById('preview_container').childNodes[i].id).style.display = 'none';
			}
		}
		document.getElementById(object + '_preview_container').style.display = 'block';

		for(var i = 0; i < document.getElementById('settings_container').childNodes.length; i++)
		{
			if(document.getElementById('settings_container').childNodes[i].nodeName == 'DIV')
			{
				document.getElementById(document.getElementById('settings_container').childNodes[i].id).style.display = 'none';
			}
		}
		document.getElementById(object + '_properties').style.display = 'block';
	}
</script>

<?php
	echo '<div class="grey_faded_div">' . "\n";
	echo '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&update" method="post">' . "\n";
	echo '<h2>Din presentation</h2>' . "\n";
	echo '<textarea name="freetext" style="width: 99%; height: 200px;">' . $freetext . '</textarea>' . "\n";
	echo '</div>' . "\n";

	echo '<div class="grey_faded_div" style="margin-top: 15px; overflow: hidden;">' . "\n";
	echo '<h2>Ändra utseende för ';
	echo '<select onchange="view_settings(this.value);">' . "\n";
	echo '<optgroup>Välj objekt</optgroup>' . "\n";
	foreach(array_keys($objects) AS $object_id)
	{
		echo '<option value="' . $object_id . '">' . $objects[$object_id]['title'] . '</option>' . "\n";
	}
	echo '</select>';
	echo '</h2>';

	echo '<div id="settings_container">' . "\n";
?>
<script language="javascript">
	function freetext_customize_show_border_properties(object_id, border)
	{
		document.getElementById(object_id + "_border-top_control").style.display = "none";
		document.getElementById(object_id + "_border-left_control").style.display = "none";
		document.getElementById(object_id + "_border-right_control").style.display = "none";
		document.getElementById(object_id + "_border-bottom_control").style.display = "none";
		
		document.getElementById(object_id + "_border-" + border + "_control").style.display = "block";
		
		document.getElementById(object_id + "border_top_link").style.fontWeight = 'normal';
		document.getElementById(object_id + "border_left_link").style.fontWeight = 'normal';
		document.getElementById(object_id + "border_right_link").style.fontWeight = 'normal';
		document.getElementById(object_id + "border_bottom_link").style.fontWeight = 'normal';
		
		document.getElementById(object_id + "border_" + border + "_link").style.fontWeight = 'bold';
	}
</script>
<?php
	foreach($object_types AS $object_type => $object_ids)
	{
		foreach($object_ids AS $object_id)
		{
			echo '<div id="' . $object_id . '_properties" style="display: none;">' . "\n";
			foreach($objects[$object_id]['properties'] AS $property)
			{
				switch($property)
				{
					case 'text-align':
						echo '<table>';
						echo '<tr><th>Justering</th><th>Storlek</th><th>Typsnitt</th><th>Färg</th></tr>';
						echo '<tr><td>';
						draw_control($property, $object_id);
						echo '</td><td>';
						break;
					case 'font-size':
					case 'font-family':
						draw_control($property, $object_id);
						echo '</td><td>';
						break;
					case 'color':
						draw_control($property, $object_id);
						echo '</td></tr></table>';
						break;
					case 'border-top':
						echo '<span style="cursor: pointer;" id="' . $object_id . 'border_top_link" onclick="freetext_customize_show_border_properties(\'' . $object_id . '\', \'top\')">Övre kant</span>&nbsp;&nbsp;&nbsp;&nbsp; ';
						echo '<span style="cursor: pointer;" id="' . $object_id . 'border_left_link" onclick="freetext_customize_show_border_properties(\'' . $object_id . '\', \'left\')">Vänster kant</span>&nbsp;&nbsp;&nbsp;&nbsp; ';
						echo '<span style="cursor: pointer;" id="' . $object_id . 'border_right_link" onclick="freetext_customize_show_border_properties(\'' . $object_id . '\', \'right\')">Höger kant</span>&nbsp;&nbsp;&nbsp;&nbsp; ';
						echo '<span style="cursor: pointer;" id="' . $object_id . 'border_bottom_link" onclick="freetext_customize_show_border_properties(\'' . $object_id . '\', \'bottom\')">Nedre kant</span>&nbsp;&nbsp;&nbsp;&nbsp; ';
					case 'border-bottom':
					case 'border-left':
					case 'border-right':
							echo '<div id="' . $object_id . '_' . $property . '_control" style="display: none;">';
							draw_control($property, $object_id);
							echo '</div>' . "\n";
						break;
					default:
						draw_control($property, $object_id);
						break;
				}
			}
			echo '</div>';
		}
	}
	echo '</div>' . "\n";

	echo '<div id="preview_container">' . "\n";
	echo '<h3>Förhandsgranskning</h3>' . "\n";
	foreach($object_types AS $object_type => $object_ids)
	{
		foreach($object_ids AS $object_id)
		{
			echo '<div style="display: none;" id="' . $object_id . '_preview_container">' . "\n";
			echo '<div id="' . $object_id . '_preview" style="';
			foreach($objects[$object_id]['properties'] AS $property)
			{
				echo ' ' . $property . ': ';
				echo (strlen($stylesheet[$object_id][$property]) > 0) ? $stylesheet[$object_id][$property] : $cfg_defaults[$property];
				echo ';';
			}
			echo '">' . "\n";
			switch($objects[$object_id]['type'])
			{
				case 'presentation':
						echo 'Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
					break;
				case 'text':
						echo 'Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor';
					break;
				case 'h5':
					echo 'Lorem Ipsum';
					break;
				case 'h6':
					echo 'Finibus Bonorum et Malorum';
					break;
				case 'div':
					echo 'Finibus Bonorum et Malorum laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi';
					break;
			}
			echo '</div>' . "\n";
			echo '</div>';
		}
	}	
	echo '</div>' . "\n";
	echo '<br style="clear: both;" />' . "\n";
	echo '</div>' . "\n";

	foreach($object_types AS $object_type => $object_ids)
	{
		foreach($object_ids AS $object_id)
		{
			foreach($objects[$object_id]['properties'] AS $property)
			{
				$value = $stylesheet[$object_id][$property];
				echo '<input type="hidden" name="' . $object_id . '_' . $property . '" id="' . $object_id . '_' . $property . '_input" value="' . $value . '" />' . "\n";
			}
		}
	}
	echo '<input type="submit" value="Spara ändringar &raquo;" class="button" />';
	echo '</form>';
?>

	<script language="javascript">
		function show_documentation(chapter)
		{
			document.getElementById('documentation_introduction').style.display = 'none';
			document.getElementById('documentation_header').style.display = 'none';
			document.getElementById('documentation_subheader').style.display = 'none';
			document.getElementById('documentation_boxes').style.display = 'none';
			document.getElementById('documentation_texts').style.display = 'none';
			document.getElementById('documentation_bold').style.display = 'none';
			document.getElementById('documentation_italic').style.display = 'none';
			document.getElementById('documentation_underline').style.display = 'none';
			document.getElementById('documentation_sup').style.display = 'none';
			document.getElementById('documentation_' + chapter).style.display = 'block';
		}
	</script>

	<div id="traffa_freetext_documentation" class="grey_faded_div" style="min-height: 200px;">
		<h2>Visa hjälpavsnitt: 
			<select onchange="show_documentation(this.value);">
				<option value="introduction">Introduktion</option>
				<option value="header">Rubrik</option>
				<option value="subheader">Underrubrik</option>
				<option value="boxes">Boxar</option>
				<option value="texts">Text-typer</option>
				<option value="bold">Fet</option>
				<option value="italic">Kursiv</option>
				<option value="underline">Understruken</option>
				<option value="sup">Upphöjd</option>
			</select>
		</h2>
		<div id="documentation_introduction">
			För att göra din presentation mer personlig kan du använda en bunt olika taggar. Här nedanför ser du några taggar:<br />
			<span style="font-family: courier new, courier, terminal, monospace">&lt;länk&gt;, &lt;text typ=1&gt;, &lt;understruken&gt;, &lt;box typ=2&gt;, &lt;fet&gt;, &lt;upphöjd&gt;</span><br />
			Alla taggar måste ha en avslutningstagg, den ser ut precis som start-taggen, men har ett snedstreck framför. För boxar, länkar och text-taggar skrivs bara det första ordet.<br />
			<table><tr><th>Tagg</th><th>Avlsutning</th></tr>
				<tr><td>&lt;länk&gt;</td><td>&lt;/länk&gt;</td></tr>
				<tr><td>&lt;text typ=2&gt;</td><td>&lt;/text&gt;</td></tr>
				<tr><td>&lt;fet&gt;</td><td>&lt;/fet&gt;</td></tr>
			</table>
			Du kan anpassa utseendet på taggarna<br />
			<span style="font-family: courier new, courier, terminal, monospace">&lt;rubrik&gt;, &lt;underrubrik&gt;, &lt;text&gt;</span> och <span style="font-family: courier new, courier, terminal, monospace">&lt;box&gt;</span><br />
			För att ändra utseende på en tagg, välj den i rutan här ovanför.<br />
			För taggarna <span style="font-family: courier new, courier, terminal, monospace">&lt;box&gt;</span> och <span style="font-family: courier new, courier, terminal, monospace">&lt;text&gt;</span>
			kan du skapa upp till tre olika typer; 1, 2 och 3. För välja vilken typ du vill använda skriver du 
			<span style="font-family: courier new, courier, terminal, monospace">typ=<strong>2</strong></span> inuti taggen. Du kan byta tvåan mot en etta eller trea.
		</div>
		<div id="documentation_boxes">
			För att lägga en box på din presentation skriver du:<br />
			<span style="font-family: courier new, courier, terminal, monospace">
				&lt;box typ=<strong>1</strong>&gt;Välkommen&lt;/box&gt;
			</span><br />
			Nu har gu gjort en box med texten "Välkommen". Du kan ändra boxens utseende genom att välja
			"Box typ 1" i rutan ovanför denna.<br /><br />
			Du har totalt tre olika boxar du kan använda: 1, 2 och 3. För att använda box nummer två istället skriver du:<br />
			<span style="font-family: courier new, courier, terminal, monospace">
				&lt;box typ=<strong>2</strong>&gt;Välkommen&lt;/box&gt;
			</span>
			<br /><br />
			Givetvis kan du använda samma box flera gånger.<br />
			Du kan göra länkar, nya boxar och använda olika text-typer inuti en box.
		</div>
		<div id="documentation_header">
			För att skapa en rubrik skriver du:<br />
			<span style="font-family: courier new, courier, terminal, monospace">
				&lt;rubrik&gt;Hamsterpartaj&lt;/rubrik&gt;
			</span><br />
			Nu har du skapat en rubrik med texten "Hamsterpartaj".<br />
			För att ändra färg, typsnitt eller storlek på rubriken, välj "Rubrik" i rutan ovanför denna.<br />
			Observera att du <strong>inte</strong> ska använda boxar innanför rubrik-taggarna!
		</div>
		<div id="documentation_subheader">
			För att skapa en underrubrik skriver du:<br />
			<span style="font-family: courier new, courier, terminal, monospace">
				&lt;rubrik&gt;Emediate&lt;/rubrik&gt;
			</span><br />
			Nu har du skapat en rubrik med texten "Emediate".<br />
			För att ändra färg, typsnitt eller storlek på rubriken, välj "Underrubrik" i rutan ovanför denna.<br />
			Observera att du <strong>inte</strong> ska använda boxar innanför underrubrik-taggarna!
		</div>
		<div id="documentation_texts">
			Du kan skapa upp till tre olika text-typer att skriva med. För att anpassa en text-typ, välj Text typ 1, 2 eller 3 i rutan ovanför denna.<br />
			För att använda en text-typ skriver du:<br />
			<span style="font-family: courier new, courier, terminal, monospace">&lt;text typ=2&gt;Några viktiga vener är lungvenen, portvenen övre hålvenen och nedre hålvenen.&lt;/text&gt;</span><br />
			I detta exemplet är Text typ 2 grön med typsnittet Comic Sans MS och i storlek 14.<br />
			<span style="font-family: comic sans ms, sans-serif; color: #65b009; font-size: 14px;">Några viktiga vener är lungvenen, portvenen övre hålvenen och nedre hålvenen.</span><br />
			<br />
			Du kan använda taggar som fet och understruken mellan två text-taggar:<br />
			<span style="font-family: courier new, courier, terminal, monospace">&lt;text typ=2&gt;Jag gillar bullar med &lt;fet&gt;mjölk&lt;/fet&gt; och hallonsyltt!&lt;/text&gt;</span><br />
			<span style="font-family: comic sans ms, sans-serif; color: #65b009; font-size: 14px;">Jag gillar kakor med <strong>mjölk</strong> och hallonsylt!</span>
		</div>
		<div id="documentation_bold">
			För att skriva med <strong>fet</strong> text använder du taggen &lt;fet&gt;:<br />
			<span style="font-family: courier new, courier, terminal, monospace">Jag är en &lt;fet&gt;dator&lt;/fet&gt; som luktar skinka!</span><br />
			Resultatet blir:<br />
			Jag är en <strong>dator</strong> som luktar skinka.
			<br /><br />
			Om du vill kan du blanda fet-taggen med exempelvis taggen för understruken text:<br />
			<span style="font-family: courier new, courier, terminal, monospace">&lt;understruken&gt;Darin har &lt;fet&gt;inte&lt;/fet&gt; tand&lt;/understruken&gt;ställning.</span><br />
			<u>Darin har <strong>inte</strong> tand</u>ställning.
		</div>
		<div id="documentation_underline">
		För att skriva med <u>understruken</u> text använder du taggen &lt;understruken&gt;:<br />
			<span style="font-family: courier new, courier, terminal, monospace">Jag kör över en &lt;understruken&gt;gammal&lt;/understruken&gt; tant, aiight!</span><br />
			Resultatet blir:<br />
			Jag kör över en <u>gammal</u> tant, aiight!
			<br /><br />
			Om du vill kan du blanda understruken-taggen med exempelvis taggen för upphöjd text:<br />
			<span style="font-family: courier new, courier, terminal, monospace">&lt;understruken&gt;Lingonveckan, är det när alla &lt;upphöjd&gt;kommunister&lt;/upphöjd&gt;&lt;/understruken&gt; demonstrerar?.</span><br />
			<u>Lingonveckan, är det när alla <sup>kommunister</sup></u> demonstrerar?
		</div>
		<div id="documentation_italic">
			För att skriva med <em>kursiv</em> text använder du taggen &lt;kursiv&gt;:<br />
			<span style="font-family: courier new, courier, terminal, monospace">Heggan äter &lt;kursiv&gt;ganska&lt;/kursiv&gt; lite jämfört med Johan.</span><br />
			Resultatet blir:<br />
			Heggan äter <em>ganska</em> lite jämfört med Johan.
			<br /><br />
			Om du vill kan du blanda kursiv-taggen med exempelvis taggen för fet text:<br />
			<span style="font-family: courier new, courier, terminal, monospace">Någon som minns &lt;kursiv&gt;&lt;fet&gt;THM 268&lt;/fet&gt;&lt;/kursiv&gt;?</span><br />
			Någon som minns <em><strong>THM 268</strong></em>?
		</div>
		<div id="documentation_sup">
			För att skriva med <sup>upphöjd</sup> text använder du taggen &lt;upphöjd&gt;:<br />
			<span style="font-family: courier new, courier, terminal, monospace">Pythagoras sats säger att c&lt;upphöjd&gt;2&lt;/upphöjd&gt; = a&lt;upphöjd&gt;2&lt;/upphöjd&gt; + b&lt;upphöjd&gt;2&lt;/upphöjd&gt; när vinkeln ab är 90&lt;upphöjd&gt;o&lt;/upphöjd&gt;.</span><br />
			Resultatet blir:<br />
			Pythagoras sats säger att c<sup>2</sup> = a<sup>2</sup> + b<sup>2</sup> när vinkeln ab är 90<sup>o</sup>.
			<br /><br />
			Om du vill kan du blanda upphöd-taggen med exempelvis taggen för fet text:<br />
			<span style="font-family: courier new, courier, terminal, monospace">
				a&lt;upphöjd&gt;2&lt;/upphöjd&gt; = b&lt;upphöjd&gt;2&lt;/upphöjd&gt; + c&lt;upphöjd&gt;2&lt;/upphöjd&gt; - 2bc * cosA. Detta gäller &lt;fet&gt;&understruken&gt;alla&lt;/understruken&gt;&lt;/fet&gt; trianglar.
				</span><br />
				a<sup>2</sup> = b<sup>2</sup> + c<sup>2</sup> - 2bc * cosA. Detta gället <strong><u>alla</u></strong> trianglar.
		</div>		
		<script language="javascript">
			show_documentation('introduction');
		</script>
	</div>
