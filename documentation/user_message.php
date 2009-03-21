<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'documentation.lib.php');

			$out .= '<h3>Server_message - ui_server_message()</h3>' . "\n";
			$out .= '<p>Creates a warning/notification</p>' . "\n";
			
			$out .= '<div id="demo_header" class="content_box_header">' . "\n";
			$out .= '<h4>Demo</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="demo" class="content_box">' . "\n";
			
			$out .= '<div class="example">' . "\n";
				$code = '<?php	
	$options[\'type\'] = \'notification\';
	$options[\'title\'] = \'This is a notification\';
	$options[\'message\'] = \'More information about the notification\';
	$out .= ui_server_message($options);
?>';
				$out .= highlight_string($code, true);
				$options['user_id'] = 625058;
				$out .= message_top($options);
				$out .= '<p>Here u put some text</p>' . "\n";
				$out .= '<p>Here u put some more text</p>' . "\n";
				$out .= message_bottom();
			$out .= '</div>' . "\n";
			
			$out .= '<div class="example">' . "\n";
				$code = '<?php	
	$options[\'type\'] = \'warning\';
	$options[\'title\'] = \'This is a warning\';
	$options[\'message\'] = \'More information about the warning\';
	$out .= ui_server_message($options);
?>';
				$out .= highlight_string($code, true);
				$out .= '<div class="server_message_warning">' . "\n";
				$out .= '<h2>This is a warning</h2>' . "\n";
				$out .= '<p>More information about the warning</p>' . "\n";
				$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
				

			$out .= '</div>' . "\n";
			
			$out .= '<div id="attributes_header" class="content_box_header">' . "\n";
				$out .= '<h4>Attributes</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="attributes" class="content_box">' . "\n";
				$out .= '<dl>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'Type' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>Standard</strong>, ' . "\n";
							$out .= 'Unread, ' . "\n";
						$out .= '</dd>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'User_id' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>Integrer</strong>' . "\n";
						$out .= '</dd>' . "\n";
				$out .= '</dl>' . "\n";
			$out .= '</div>' . "\n";
				
			$out .= '<div id="source_code_header" class="content_box_header">' . "\n";
			$out .= '<h4>Source code</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="source_code" class="content_box">' . "\n";
				$out .= '<h5>include/configs/ui.conf.php</h5>' . "\n";
				$code = '<?php
	$UI_USER_MESSAGE[\'types\'] = array(\'standard\', \'unread\');
?>';
					$out .= highlight_string($code, true);
					
					$out .= '<h5>include/lib/ui.lib.php</h5>' . "\n";
					$out .= '<h6>ui_user_message_top()</h6>' . "\n";
					$code = '<?php
function ui_user_message_top($options)
	{
		$options[\'type\'] = (in_array($options[\'type\'], $UI_USER_MESSAGE[\'types\'])) ? $options[\'type\'] : \'standard\';
		
			$output .= \'<li class="message">\' . "\n";
				$output .= \'<div class="\' . $options[\'type\'] . \'">\' . "\n";
					$output .= ui_avatar($options[\'user_id\']) . "\n";
					$output .= \'<div class="container">\' . "\n";
						$output .= \'<div class="top_bg">\' . "\n";
							$output .= \'<div class="bottom_bg">\' . "\n";
								$coutput .= \'<div>\' . "\n";
		return $output;
	}
?>';
					$out .= highlight_string($code, true);
					
					$out .= '<h6>ui_user_message_bottom()</h6>' . "\n";
					$code = '<?php
function ui_user_message_bottom($options)
	{
								$coutput .= \'</div>\' . "\n";
							$coutput .= \'</div>\' . "\n";
						$coutput .= \'</div>\' . "\n";
					$coutput .= \'</div>\' . "\n";
				$coutput .= \'</div>\' . "\n";
			$output .= \'</li>\' . "\n";
		return $output;
	}
?>';
					$out .= highlight_string($code, true);
				$out .= '</div>' . "\n";
?>

<?php
	echo open_source_ui_top($open_source_ui_options);
	echo $out;
	echo open_source_ui_bottom();
?>
