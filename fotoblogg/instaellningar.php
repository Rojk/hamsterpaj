<?php
	$ui_options['ui_modules']['photoblog_user'] = 'User';
	$ui_options['ui_modules']['photoblog_calendar'] = 'Kalender';
	$ui_options['ui_modules']['photoblog_albums'] = 'Album';
	
	$ui_options['stylesheets'][] = 'colorpicker.css';
	$ui_options['stylesheets'][] = 'colorpicker_layout.css';
	$ui_options['stylesheets'][] = 'forms.css';
		
	$ui_options['javascripts'][] = 'colorpicker.js';
	$ui_options['javascripts'][] = 'photoblog_preferences.js';
	$ui_options['javascripts'][] = 'colorpicker_eye.js';
	$ui_options['javascripts'][] = 'colorpicker_layout.js';
	$ui_options['javascripts'][] = 'colorpicker_utils.js';
				
	$photoblog_preferences = new photoblog_preferences();
	$my_photoblog_preferences = $photoblog_preferences->fetch();
				
				
				
	// Debug $out .= '<div id="test"></div>' . "\n";
	$out .= '<form id="photoblog_preferences_form" action="/fotoblogg/instaellningar/post_settings.php" method="post">' . "\n";
	$out .= '<fieldset>' . "\n";
	$out .= '<legend>INSTÄLLNINGAR</legend>' . "\n";
		$out .= '<table class="form" id="photoblog_preferences_color_table">' . "\n";
			$out .= '<tr>' . "\n";
				$out .= '<th>' . "\n";
					$out .= '<label for="photoblog_is_private">Vakthamster <strong>*</strong></label>' . "\n";
				$out .= '</th>' . "\n";
				$out .= '<td>' . "\n";
					$out .= '<label for="photoblog_preferences_hamster_guard_on_1"><input type="radio" name="photoblog_preferences_hamster_guard_on" id="photoblog_preferences_hamster_guard_on_1"';
					$out .= ($my_photoblog_preferences['hamster_guard_on'] == 1) ? ' checked="checked"' : '';
					$out .= ' value="1" />På</label><br />' . "\n";
					$out .= '<label for="photoblog_preferences_hamster_guard_on_0"><input type="radio" name="photoblog_preferences_hamster_guard_on" id="photoblog_preferences_hamster_guard_on_0"';
					$out .= ($my_photoblog_preferences['hamster_guard_on'] == 0) ? ' checked="checked"' : '';
					$out .= ' value="0" />Av</label>' . "\n";
				$out .= '</td>' . "\n";
			$out .= '</tr>' . "\n";
			$out .= '<tr>' . "\n";
				$out .= '<td colspan="2">' . "\n";
				$out .= '<span style="color: red"><strong>*</strong></span> Om du har Vakthamstern på så kan bara inloggade medlemmar se din fotoblogg.' . "\n";
			$out .= '</td>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '<tr>' . "\n";
			$out .= '<th>' . "\n";
				$out .= '<label for="photoblog_preferences_color_detail">Detaljfärg</label>' . "\n";
			$out .= '</th>' . "\n";
			$out .= '<td>' . "\n";
				$out .= '<div class="colorSelector" id="photoblog_preferences_color_detail_div"><div style="background-color: ' . $my_photoblog_preferences['color_detail'] . ';"/></div></div>' . "\n";
					$out .= '<input type="hidden" name="photoblog_preferences_color_detail" id="photoblog_preferences_color_detail" value="' . $my_photoblog_preferences['color_detail'] . '" />' . "\n";
			$out .= '</td>' . "\n";
			$out .= '</tr>' . "\n";
			$out .= '<tr>' . "\n";
				$out .= '<th>' . "\n";
					$out .= '<label for="photoblog_preferences_color_main">Bakgrund på element</label>' . "\n";
				$out .= '</th>' . "\n";
				$out .= '<td>' . "\n";
					$out .= '<div class="colorSelector" id="photoblog_preferences_color_main_div"><div style="background-color: ' . $my_photoblog_preferences['color_main'] . ';"/></div></div>' . "\n";
					$out .= '<input type="hidden" name="photoblog_preferences_color_main" id="photoblog_preferences_color_main" value="' . $my_photoblog_preferences['color_main'] . '" />' . "\n";
				$out .= '</td>' . "\n";
			$out .= '</tr>' . "\n";
	$out .= '</table>' . "\n";
	$out .= '<input type="submit" value="Spara inställningar" />' . "\n";
	$out .= '</fieldset>' . "\n";
	$out .= '</form>' . "\n";
	switch ($uri_parts[3])
	{
		case 'post_settings.php':
		//$out .= preint_r($_POST);
		$options = array(
			'color_main' => strtoupper($_POST['photoblog_preferences_color_main']),
			'color_detail' => strtoupper($_POST['photoblog_preferences_color_detail']),
			'hamster_guard_on' => $_POST['photoblog_preferences_hamster_guard_on']
		);
		$options_check_strlen_len_6_array = array(
			'color_main',
			'color_detail'
		);
		foreach ($options_check_strlen_len_6_array as $key)
		{
			if (strlen($options[$key]) != 6)
			{
				throw new Exception('Fel i postfunktionen... klaga på <a href="/joar/gb">Joar</a>');
			}
		}
		$photoblog_preferences->save($options);
		header('Location: /fotoblogg/instaellningar/');
		break;
	}
?>