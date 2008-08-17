<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	
	//----------------------
	$ui_options['menu_path'] = array('installningar', 'profil');
	require(PATHS_INCLUDE . 'traffa-definitions.php');

	//$ui_options['javascripts'][] = 'zip_codes.js';
	$ui_options['javascripts'][] = 'settings.js';
	
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'settings.css';
	$ui_options['stylesheets'][] = 'profile_themes/all_themes.php';
	//-------------------
	
	$ui_options['title'] = 'Ändra presentation på Hamsterpaj';
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'profile_presentation_change.css';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['stylesheets'][] = 'flags_customize.css';
	
	$ui_options['javascripts'][] = 'profile.js';
	$ui_options['javascripts'][] = 'flags-customize.js';

	ui_top($ui_options);
	if(login_checklogin())
	{
		if(is_privilegied('edit_presentation') && isset($_GET['admin_change']) && is_numeric($_GET['admin_change'])) // Replace with privilegies later...
		{
			$user_id = $_GET['admin_change'];
		}
		else
		{
			$user_id = $_SESSION['login']['id'];
		}
		
		if($_POST['action'] == 'profile_theme')
		{
			$query = 'UPDATE userinfo SET profile_theme = "' . $_POST['theme'] . '" WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query);
			$_SESSION['userinfo']['profile_theme'] = $_POST['theme'];
		}
		
		if(isset($_POST['presentation_text']))
		{
			$output .= profile_presentation_save(array('user_id' => $user_id, 'presentation_text' => $_POST['presentation_text']) );
		}

		$rounded_corners_tabs_options = array();
		if ($_GET['action'] == "theme_select")
		{
						
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '/installningar/profilesettings.php', 'label' => 'Ändra presentationen');
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=theme_select', 'label' => 'Byt tema', 'current' => TRUE);
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=flags_select', 'label' => 'Välj flaggor');
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '/traffa/profile.php', 'label' => 'Till min presentation');
			$output .= rounded_corners_tabs_top($rounded_corners_tabs_options); 
			
			$query = 'SHOW COLUMNS FROM userinfo';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			while($data = mysql_fetch_assoc($result))
			{
				if($data['Field'] == 'profile_theme' && substr($data['Type'], 0, 5) == 'enum(')
				{
					$types = substr($data['Type'], 6, -2);
					$profile_themes = explode("','", $types);
				}
			}
		
			//$out .= '<h2>Byt profiltema</h2>' . "\n";
			
			$profile = profile_fetch(array('user_id' => $_SESSION['login']['id']));
			$profile['profile_top_id'] = 'theme_preview';
			$out .= profile_mini_page($profile);
			
			$out .= '<form method="post" class="settings_theme">' . "\n";
			$out .= '<input type="hidden" name="action" value="profile_theme" />';
			$out .= '<ul>' . "\n";
			foreach($profile_themes AS $theme)
			{
				$out .= '<li>' . "\n";
				$out .= '<img src="' . IMAGE_URL . 'profile_themes/' . $theme . '/preview.png" id="preview_' . $theme . '" class="theme_preview" />' . "\n";
				$checked = ($theme == $_SESSION['userinfo']['profile_theme']) ? ' checked="checked"' : '';
				$out .= '<input type="radio" name="theme" value="' . $theme . '"' . $checked . ' />' . "\n";
				$out .= '</li>' . "\n";
			}
			$out .= '</ul>' . "\n";
			$out .= '<br style="clear: both;" /><input type="submit" value="Spara" class="button_50" />' . "\n";
			$out .= '<a href="/open_source/theme_creation.php">Gör ett eget tema</a>,';
			$out .= ' <a href="/profilteman/">Testa ditt egna tema</a> (avancerat)';
			$out .= '</form>' . "\n" . '';
		
			//$output .= $out;
			echo $out;
			$output .= rounded_corners_tabs_bottom();
			
		}
		elseif ($_GET['action'] == "flags_select")
		{
						
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '/installningar/profilesettings.php', 'label' => 'Ändra presentationen');
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=theme_select', 'label' => 'Byt tema');
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=flags_select', 'label' => 'Välj flaggor', 'current' => TRUE);
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '/traffa/profile.php', 'label' => 'Till min presentation');
			$output .= rounded_corners_tabs_top($rounded_corners_tabs_options); 
			
			$out .= '<div id="flags_customize">' . "\n";
			$categories['politics']['label'] = 'Politik';
			$categories['countries']['label'] = 'Länder';	
			$categories['lifestyle']['label'] = 'Livsstil';
			$categories['religion']['label'] = 'Livsåskådning';
			$categories['sports']['label'] = 'Sporter';
			
			$query = 'SELECT ufl.handle FROM user_flags_list AS ufl, user_flags AS uf WHERE uf.user = "' . $_SESSION['login']['id'] . '" AND ufl.id = uf.flag';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			while($data = mysql_fetch_assoc($result))
			{
				$user_flags[] = $data['handle'];
			}

			$out .=  '<ul id="flags_customize_navigation">' . "\n";
			foreach($categories AS $handle => $category)
			{
				$out .=  '<li id="flags_nav_' . $handle . '" class="' . $handle . '"  onclick="flags_customize_navigation_click();flags_nav_chosen_tab=this.innerHTML;">' . $category['label'] . '</li>' . "\n";
			}
			$out .=  '</ul><br style="clear: both;" />' . "\n";
		

		
			foreach($categories AS $handle => $category)
			{
				$style = ($i == 0) ? ' style="display: block;"' : '';
				$out .=  '<div id="flags_form_' . $handle . '"' . $style . ' class="flags_customize_category">' . "\n";	
				$out .=  '<form action="javascript: flags_customize_submit(\'flags_form_' . $handle . '\');">' . "\n";
				$out .=  '<h2>' . $category['label'] . '</h2>' . "\n";	

				$current_group = '';
				$query = 'SELECT handle, label, `group`, category FROM user_flags_list WHERE category = "' . $handle . '" ORDER BY `group`, label ASC';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				
			


				for($i = 0; $data = mysql_fetch_assoc($result); $i++)
				{
					if(strlen($current_group) > 0 && $data['group'] != $current_group)
					{
						$current_group = '';
						$out .=  '	</ul>' . "\n";
						$out .=  '</fieldset>' . "\n";
					}
					
					if(strlen($data['group']) > 0 && $data['group'] != $current_group)
					{
						$current_group = $data['group'];
						$out .=  '<fieldset>' . "\n";
						
						if($data['group'] == 'political_party')
						{
							$data_group = 'Politiskt parti';
						}
						elseif($data['group'] == 'country')
						{
							$data_group = 'Medborgare i';
						}
						elseif($data['group'] == 'major_religions')
						{
							$data_group = 'Livsåskådning';
						}
						
			
						$out .=  '	<legend>' . $data_group . '</legend>' . "\n";
						$out .=  '		<ul>' . "\n";
						$out .=  '		<li><input type="radio" name="' . $data['group'] . '" value="none" />Ingen</li>' . "\n";
					}
					
					$checked = (in_array($data['handle'], $user_flags)) ? ' checked="checked"' : '';
					
					if(strlen($data['group']) > 0)
					{
						$out .=  '		<li>' . "\n";
						$out .=  '			<input type="radio" name="' . $data['group'] . '" value="' . $data['handle'] . '" id="input_' . $data['group'] . '_' . $data['handle'] . '"' . $checked . ' />' . "\n";
						$out .=  '			<label for="input_' . $data['group'] . '_' . $data['handle'] . '">' . $data['label'] . '</label>' . "\n";
						$out .=  '		</li>' . "\n";
					}
					else
					{
						$out .=  '		<input type="checkbox" name="' . $data['handle'] . '" value="enable" id="input_' . $data['handle'] . '"' . $checked . ' />' . "\n";
						$out .=  '		<label for="input_' . $data['handle'] . '">' . $data['label'] . '</label><br />' . "\n";
					}
				}
				$out .=  '<input type="submit" value="Spara" />' . "\n";
				$out .=  '</form>' . "\n";
				$out .=  '</div>' . "\n";
			}
			$out .= '<div id="flags_customize_message" style="color: red; font-weight: bold;"></div>' . "\n";	
			$out .= '</div>' . "\n";	
			

			echo $out;
			$output .= rounded_corners_tabs_bottom();
			
		}
		else
		{
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '/installningar/profilesettings.php', 'label' => 'Ändra presentationen', 'current' => TRUE);
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=theme_select', 'label' => 'Byt tema');
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?action=flags_select', 'label' => 'Välj flaggor');
			$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '/traffa/profile.php', 'label' => 'Till min presentation');
			
			$output .= rounded_corners_tabs_top($rounded_corners_tabs_options); 
			$profile = profile_fetch( array('user_id' => $user_id) );
			echo profile_presentation_change_form($profile);
			$output .= rounded_corners_tabs_bottom();
		}
		
		//$rounded_corners_tabs_options['tabs'][] = array('href' => $paths_prefix . '?show=boys', 'label' => 'Förhandsgranska');

		//$output .= rounded_corners_tabs_top($rounded_corners_tabs_options); 
		//$profile = profile_fetch( array('user_id' => $user_id) );
		//echo profile_presentation_change_form($profile);
		//echo "test";
		//$output .= rounded_corners_tabs_bottom();
	}
	else
	{
		$output .= 'Men tjockis, du måste vara inloggad för att se den här sidan!';
	}
	echo $output;
	ui_bottom();
	
	
?>