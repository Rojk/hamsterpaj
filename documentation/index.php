<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'documentation.lib.php');
	
			$out .= '<h3>Text_container - ui_text_container_top(), ui_text_container_bottom()</h3>' . "\n";
			$out .= '<p>Creates a container for text</p>' . "\n";
			
			$out .= '<div id="demo_header" class="content_box_header">' . "\n";
			$out .= '<h4>Demo</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="demo" class="content_box">' . "\n";
			$out .= '<div class="example">' . "\n";
				$code = '<?php	
	$out .= ui_text_container_top($options);
	$out .= \'hejsvejs\' . "\n";
	$out .= ui_text_container_bottom($options);
?>';
				$out .= highlight_string($code, true);
				$options['return'] = true;
				$out .= rounded_corners_top($options);
				$out .= 'hejsvejs' . "\n";
				$out .= rounded_corners_bottom($options);
				$out .= '</div>' . "\n";
				
				$out .= '<div class="example">' . "\n";
				$code = '<?php		
	$options[\'color\'] = \'blue_deluxe\';
?>';
				$out .= highlight_string($code, true);
				$options['return'] = true;
				$options['color'] = 'blue_deluxe';
				$out .= rounded_corners_top($options);
				$out .= 'hejsvejs' . "\n";
				$out .= rounded_corners_bottom($options);
				$out .= '</div>' . "\n";
				
				$out .= '<div class="example">' . "\n";
				$code = '<?php	
	$options[\'color\'] = \'orange\';
?>';
				$out .= highlight_string($code, true);
				$options['return'] = true;
				$options['color'] = 'orange';
				$out .= rounded_corners_top($options);
				$out .= 'hejsvejs' . "\n";
				$out .= rounded_corners_bottom($options);
				$out .= '</div>' . "\n";
				
				$out .= '<div class="example">' . "\n";
				$code = '<?php	
	$options[\'color\'] = \'orange_deluxe\';
?>';
				$out .= highlight_string($code, true);
				$options['return'] = true;
				$options['color'] = 'orange_deluxe';
				$out .= rounded_corners_top($options); 
				$out .= 'hejsvejs' . "\n";
				$out .= rounded_corners_bottom($options);
				$out .= '</div>' . "\n";
				
				$out .= '<div class="example">' . "\n";
				$code = '<?php
	$options[\'color\'] = \'white\';
?>';
				$out .= highlight_string($code, true);
				$options['return'] = true;
				$options['color'] = 'white';
				$out .= rounded_corners_top($options);
				$out .= 'hejsvejs' . "\n";
				$out .= rounded_corners_bottom($options);
				$out .= '</div>' . "\n";
				
				$out .= '<div class="example">' . "\n";
				$code = '<?php
	$options[\'color\'] = \'red\';
?>';
				$out .= highlight_string($code, true);
				$options['return'] = true;
				$options['color'] = 'red';
				$out .= rounded_corners_top($options);
				$out .= 'hejsvejs' . "\n";
				$out .= rounded_corners_bottom($options);
				$out .= '</div>' . "\n";
				
				$out .= '<div class="example">' . "\n";
				$code = '<?php
	$options[\'color\'] = \'red_alert_deluxe\';
?>';
				$out .= highlight_string($code, true);
				$options['return'] = true;
				$options['color'] = 'red_alert_deluxe';
				$out .= rounded_corners_top($options);
				$out .= 'hejsvejs' . "\n";
				$out .= rounded_corners_bottom($options);
				$out .= '</div>' . "\n";
			$out .= '</div>' . "\n";

			$out .= '<div id="attributes_header" class="content_box_header">' . "\n";
				$out .= '<h4>Attributes</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="attributes" class="content_box">' . "\n";
				$out .= '<dl>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'Color' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>Blue</strong>, ' . "\n";
							$out .= 'Blue_deluxe, ' . "\n";
							$out .= 'Orange, ' . "\n";
							$out .= 'Orange_deluxe, ' . "\n";
							$out .= 'White, ' . "\n";
							$out .= 'Red, ' . "\n";
							$out .= 'Red_alert_deluxe, ' . "\n";
						$out .= '</dd>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'Dimension' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>Full</strong>' . "\n";
						$out .= '</dd>' . "\n";
				$out .= '</dl>' . "\n";
			$out .= '</div>' . "\n";
				
			$out .= '<div id="source_code_header" class="content_box_header">' . "\n";
			$out .= '<h4>Source code</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="source_code" class="content_box">' . "\n";
				$out .= '<h5>include/ui.conf.php</h5>' . "\n";
				$code = '<?php
	$TEXT_CONTAINER[\'colors\'] = array(\'blue\', \'white\', \'orange\', \'green\', \'red\', \'blue_deluxe\', \'orange_deluxe\', \'red_alert_deluxe\');
	$TEXT_CONTAINER[\'dimensions\'] = array(\'full\');
?>';
					$out .= highlight_string($code, true);
					
					$out .= '<h5>include/lib/ui.lib.php</h5>' . "\n";
					$out .= '<h6>ui_text_container_top()</h6>' . "\n";
					$code = '<?php
	function ui_text_container_top($options)
	{
		$options[\'color\'] = (in_array($options[\'color\'], $ROUNDED_CORNERS[\'colors\'])) ? $options[\'color\'] : \'blue\';
		$options[\'dimension\'] = (in_array($options[\'dimension\'], $ROUNDED_CORNERS[\'dimensions\'])) ? $options[\'dimension\'] : ´\'full\';
		
		$id = (isset($options[\'id\'])) ? \' id="\' . $options[\'id\'] . \'"\': \'\';
		
		$output .= \'<div class="text_container_\' . $options[\'color\'] . \'_\' . $id . \'>\';
		$output .= \'<div class="top">\' . "\n";
		$output .= \'<div class="content">\' . "\n";
		return $output;
	}
?>';
					$out .= highlight_string($code, true);
	
					$out .= '<h6>ui_text_container_bottom()</h6>' . "\n";
					$code = '<?php
	function ui_text_container_bottom()
	{
		$output .= \'</div>\' . "\n";
		$output .= \'</div>\' . "\n";
		$output .= \'</div>\' . "\n";
		return $output;
	} 
?>' . "\n";
					$out .= highlight_string($code, true);
				$out .= '</div>' . "\n";
				
	echo open_source_ui_top($open_source_ui_options);
	echo $out;
	echo open_source_ui_bottom();
?>
