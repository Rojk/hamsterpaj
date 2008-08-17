<?php
	function rounded_corners_top($options, $return = false)
	{
		global $ROUNDED_CORNERS;
		
		$ROUNDED_CORNERS['last_top_call'] = $options;

		$options['color'] = (in_array($options['color'], $ROUNDED_CORNERS['colors'])) ? $options['color'] : 'blue';
		$options['dimension'] = (in_array($options['dimension'], $ROUNDED_CORNERS['dimensions'])) ? $options['dimension'] : 'full';
		
		$style = (isset($options['style'])) ? ' style="' . $options['style'] . '"': '';
		$id = (isset($options['id'])) ? ' id="' . $options['id'] . '"': '';
		$content_id = (isset($options['id'])) ? ' id="' . $options['id'] . '_content"': '';
		
		$output .= "\n\n";
		if(!isset($options['new_layout_beta']))
		{		
			$output .= '<!-- Rounded corners div. Color: ' . $color . ', dimension: ' . $dimension . '-->' . "\n";
			$output .= '<div class="rounded_corners"' . $style . $id .'>' . "\n";
			$output .= '<img src="' . IMAGE_URL . 'css_backgrounds/rounded_corners/' . $options['color'] . '_' . $options['dimension'] . '_top.png"  class="rounded_corners_top_image" />' . "\n";
			$output .= '<div class="rounded_corners_' . $options['color'] . '_' . $options['dimension'] . '"' . $content_id . '>' . "\n";
		}
		else
		{
			$output .= '<div class="rounded_corners_beta_' . $options['color'] . '"' . $style . $id . '>';
			$output .= '<div class="top">' . "\n";
			$output .= '<div class="content">' . "\n";
		}
		
		
		if($return || $options['return'])
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}
	
	function rounded_corners_bottom($options, $return = false)
	{
		global $ROUNDED_CORNERS;
		
		if(isset($ROUNDED_CORNERS['last_top_call']) && !empty($ROUNDED_CORNERS['last_top_call']))
		{
			$options = array_merge($ROUNDED_CORNERS['last_top_call'], $options);
			$ROUNDED_CORNERS['last_top_call'] = array();
		}

		$options['color'] = (in_array($options['color'], $ROUNDED_CORNERS['colors'])) ? $options['color'] : 'blue';
		$options['dimension'] = (in_array($options['dimension'], $ROUNDED_CORNERS['dimensions'])) ? $options['dimension'] : 'full';

		if(!isset($options['new_layout_beta']))		
		{
			$output .= '</div>' . "\n";
			$output .= '<img src="' . IMAGE_URL . 'css_backgrounds/rounded_corners/' . $options['color'] . '_' . $options['dimension'] . '_bottom.png" class="rounded_corners_bottom_image"/>' . "\n";
			$output .= '</div>' . "\n\n";
		}
		else
		{
			$output .= "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
		}
		
		if($return || $options['return'])
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}

	function rounded_corners($content, $options, $do_return)
	{
		$return .= rounded_corners_top($options, $do_return);
		$return .= $content;
		$return .= rounded_corners_bottom($options, $do_return);
		if($do_return)
		{
			return $return;
		}
		else
		{
			echo $return;
		}
	}
	
	function rounded_corners_tabs_top($options, $return = false)
	{
		global $ROUNDED_CORNERS;

		$options['color'] = (in_array($options['color'], $ROUNDED_CORNERS['colors'])) ? $options['color'] : 'blue';
		$options['dimension'] = (in_array($options['dimension'], $ROUNDED_CORNERS['dimensions'])) ? $options['dimension'] : 'full';
		
		$style = (isset($options['style'])) ? ' style="' . $options['style'] . '"': '';
		$id = (isset($options['id'])) ? ' id="' . $options['id'] . '"': '';
		$content_id = (isset($options['id'])) ? ' id="' . $options['id'] . '_content"': '';
		
		if(isset($options['tabs']))
		{
			foreach($options['tabs'] as $tab)
			{
				$tab_id = (isset($tab['id'])) ? ' id="' . $tab['id'] . '"' : '';
				$tab_current = (isset($tab['current']) && $tab['current'] == true) ? ' class="_current"' : '';

				$tabs_output .= '<div class="_tab">' . "\n";
				$tabs_output .= '<div class="_left">&nbsp;</div>' . "\n";
				$tabs_output .= '<div class="_label">';
				$tabs_output .= '<a href="' . $tab['href'] . '"' . $tab_id . $tab_current . '>' . $tab['label'] . '</a></div>' . "\n";
				$tabs_output .= '<div class="_right">&nbsp;</div>' . "\n";
				$tabs_output .= '</div>' . "\n";
			}
		}else{
			$tabs_output = '<!-- No tabs loaded -->' . "\n";
		}
				
		$output .= "\n\n";
		$output .= '<!-- Rounded corners div with tabs. Color: ' . $color . ', dimension: ' . $dimension . '-->' . "\n";
		$output .= '<div class="rounded_corners_tabs_' . $options['dimension'] . '_' . $options['color'] . '"' . $style . $id .'>' . "\n";
		$output .= $tabs_output;
		$output .= '<div class="_top">&nbsp;</div>' . "\n";
		$output .= '<div class="_content"' . $content_id . '>' . "\n";
		
		if($return || $options['return'])
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}
	
	function rounded_corners_tabs_bottom($options, $return = false)
	{	
		$output .= "\n" . '<br style="clear: both" />' . "\n";
		$output .= '</div>' . "\n";
		$output .= '<div class="_bottom">&nbsp;</div>' . "\n";
		$output .= '</div>' . "\n\n";
		if($return || $options['return'])
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}
	
	function message_top($options)
	{
		if(!isset($options['type']))
		{
			$options['type'] = 'standard';
		}
		$content .= '<li class="message">' . "\n";
		$content .= '<div class="' . $options['type'] . '">' . "\n";
			$content .= ui_avatar($options['user_id']) . "\n";
				$content .= '<div class="container">' . "\n";
					$content .= '<div class="top_bg">' . "\n";
						$content .= '<div class="bottom_bg">' . "\n";
							$content .= '<div>' . "\n";
		return $content;
	}
	
	function message_bottom()
	{
						$content .= '</div>' . "\n";
					$content .= '</div>' . "\n";
				$content .= '</div>' . "\n";
			$content .= '</div>' . "\n";
			$content .= '</div>' . "\n";
		$content .= '</li>' . "\n";
		return $content;
	}
	
	function ui_avatar($user_id, $options)
	{
		if(!is_numeric($user_id))
		{
			return 'Avatar id not numeric, aborting...';
		}
		$img_path = IMAGE_PATH . 'images/users/thumb/' . $user_id . '.jpg';
		$style = (isset($options['style'])) ? ' style="' . $options['style'] . '"' : '';
		if (file_exists($img_path))
		{
			return '<img src="' . IMAGE_URL . 'images/users/thumb/' . $user_id . '.jpg?cache_prevention=' . filemtime($img_path) . '" class="user_avatar"' . $style . ' />' . "\n";
		}
		else
		{
			return '<img src="' . IMAGE_URL . '/images/users/no_image_mini.png" class="user_avatar"' . $style . ' />' . "\n";
		}
	}
	
?>