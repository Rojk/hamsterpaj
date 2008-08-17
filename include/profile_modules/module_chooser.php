<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";
	echo '<a name="modules"></a>';
/*
	Av någon jävla anledning görs dubbla requests ibland, en request utan POST-data.
	Därför, typ.
*/
	if($_GET['action'] == 'change_module_order' && $_SERVER['REQUEST_METHOD'] != 'GET')
	{
		for($i = 1; $i <= PROFILE_MAX_MODULES; $i++)
		{
			$module_info = $modules[$_POST['module_' . $i]];
			if($module_info['blocked'] != true && $module_info['userlevel_use'] <= $_SESSION['login']['userlevel'] && $module_info['active'] == true)
			{
				$new_data['traffa']['profile_modules'] .= $_POST['module_' . $i] . ',';
			}
		}
		$new_data['traffa']['color_theme'] = (is_numeric($_POST['color_theme'])) ? $_POST['color_theme'] : 1;
		$new_data['traffa']['profile_modules'] = substr($new_data['traffa']['profile_modules'], 0, -1); /* Remove the last comma sign */
		login_save_user_data($_SESSION['login']['id'], $new_data);
		session_merge($new_data);
		jscript_location($_SERVER['PHP_SELF']);
	}

	echo '<div style="float: left; width: 250px;">' . "\n";
	echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=change_module_order" method="post">' . "\n";
	for($i = 1; $i <= PROFILE_MAX_MODULES; $i++)
	{
		echo '<h2>Modul #' . $i . '</h2>' . "\n";
		echo '<select name="module_' . $i . '">' . "\n";
		echo '<option value="none">Avstängd</option>' . "\n";
		foreach($modules AS $module_id => $current)
		{
			if($current['blocked'] != true && $current['userlevel_use'] <= $_SESSION['login']['userlevel'] && $current['active'] == true)
			{
				echo '<option value="' . $module_id . '"';
				echo ($display_modules[$i-1] == $module_id) ? ' selected="selected"' : null;
				echo '>' . $current['title'] . '</option>' . "\n";
			}
		}
		echo '</select>' . "\n";
	}
	echo '</div><div style="float: left; width: 300px;">';
	echo '<strong>Välj färg:</strong>';
	echo '<select name="color_theme" onchange="document.getElementById(\'profile_color_preview\').src = \'' . IMAGE_URL . 'images/profile_color_previews/\' + this.value + \'.png\';">';
	$colors[1] = array('label' => 'Ljusblå', 'hex' => '#c9ddf9');
	$colors[2] = array('label' => 'Blålila', 'hex' => '#d1c9f9');
	$colors[3] = array('label' => 'Lila', 'hex' => '#efc9f9');
	$colors[4] = array('label' => 'Lila-rosa', 'hex' => '#f9c9e7');
	$colors[5] = array('label' => 'Rosa', 'hex' => '#f9c9c9');
	$colors[6] = array('label' => 'Sandfärgad', 'hex' => '#f9e6c9');
	$colors[7] = array('label' => 'Gulgrön', 'hex' => '#f0f9c9');
	$colors[8] = array('label' => 'Grön', 'hex' => '#d4f9c9');
	$colors[9] = array('label' => 'Grön', 'hex' => '#c9f9dc');
	$colors[10] = array('label' => 'Turkos', 'hex' => '#c9f9f8');
	
	foreach($colors AS $id => $info)
	{
		echo '<option value="' . $id . '" style="background: ' . $info['hex'] . ';"';
		if($id == $userinfo['traffa']['color_theme'])
		{
			echo ' selected="true"';
		}
		echo '>' . $info['label'] . '</option>';
	}
	echo '</select>';
	echo '<br />';
	echo '<strong>Så här kan det se ut:<br /></strong>';
	echo '<img src="' . IMAGE_URL . 'images/profile_color_previews/' . $userinfo['traffa']['color_theme'] . '.png" id="profile_color_preview" style="border: 1px solid black; margin: 5px;"/>';
		echo '</div>';
	echo '<br style="clear: both;" />';
	echo '<input type="submit" value="Spara ordning och färgval" class="button" />' . "\n";
	echo '</form>';
?>
</div>